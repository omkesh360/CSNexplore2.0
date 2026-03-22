<?php
/**
 * Performance Optimizer Bootstrap
 * 
 * Initializes and manages all performance optimization features.
 * Include this file at the top of your pages to enable caching.
 */

// Load configuration
$perfConfig = require __DIR__ . '/config.php';

// Only proceed if caching is enabled
if (!($perfConfig['cache']['enabled'] ?? false)) {
    return;
}

// Load required classes
require_once __DIR__ . '/CacheBackend.php';
require_once __DIR__ . '/cache/PageCache.php';

use PerformanceOptimizer\Cache\PageCache;

// Initialize PageCache
$pageCache = new PageCache(
    $perfConfig['cache']['directory'],
    $perfConfig['cache']['max_size_mb'] ?? 2000,
    $perfConfig['cache']['ttl'] ?? 3600
);

// Get current URL and query params
$currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
$queryParams = $_GET ?? [];

// Try to serve from cache
$cachedContent = $pageCache->getByUrl($currentUrl, $queryParams);

if ($cachedContent !== null) {
    // Cache HIT - serve cached content
    $pageCache->setStatusHeader();
    
    // Update stats
    updateCacheStats('hit');
    
    echo $cachedContent;
    exit;
} else {
    // Cache MISS - start output buffering to capture page content
    updateCacheStats('miss');
    
    // Start output buffering
    ob_start();
    
    // Register shutdown function to save page to cache
    register_shutdown_function(function() use ($pageCache, $currentUrl, $queryParams, $perfConfig) {
        // Get buffered content
        $content = ob_get_contents();
        
        // Only cache if we have content and no errors
        if ($content && !headers_sent() && http_response_code() === 200) {
            // Determine cache tags based on URL
            $tags = [];
            if (strpos($currentUrl, '/blog') !== false) {
                $tags[] = 'blog';
            } elseif (strpos($currentUrl, '/listing') !== false) {
                $tags[] = 'listing';
            }
            
            // Save to cache
            $pageCache->setByUrl(
                $currentUrl,
                $queryParams,
                $content,
                $perfConfig['cache']['ttl'] ?? 3600,
                $tags
            );
        }
    });
}

/**
 * Update cache statistics
 */
function updateCacheStats($type) {
    $cacheDir = __DIR__ . '/../../cache';
    $statsFile = $cacheDir . '/performance_stats.json';
    
    // Ensure directory exists
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }
    
    // Load existing stats
    $stats = [];
    if (file_exists($statsFile)) {
        $stats = json_decode(@file_get_contents($statsFile), true) ?: [];
    }
    
    // Initialize if needed
    if (!isset($stats['cache_hits'])) {
        $stats['cache_hits'] = 0;
    }
    if (!isset($stats['cache_misses'])) {
        $stats['cache_misses'] = 0;
    }
    
    // Update stats
    if ($type === 'hit') {
        $stats['cache_hits']++;
    } else {
        $stats['cache_misses']++;
    }
    
    $stats['last_updated'] = time();
    
    // Save stats
    @file_put_contents($statsFile, json_encode($stats, JSON_PRETTY_PRINT));
}
