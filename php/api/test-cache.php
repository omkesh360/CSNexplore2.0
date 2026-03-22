<?php
/**
 * Test Cache System
 * 
 * Generates test cache data to verify the performance page is working
 */

header('Content-Type: application/json');

// Load required classes
require_once __DIR__ . '/../performance-optimizer/CacheBackend.php';
require_once __DIR__ . '/../performance-optimizer/cache/PageCache.php';

use PerformanceOptimizer\Cache\PageCache;

$config = require __DIR__ . '/../performance-optimizer/config.php';

$pageCache = new PageCache(
    $config['cache']['directory'],
    $config['cache']['max_size_mb'] ?? 2000,
    $config['cache']['ttl'] ?? 3600
);

// Generate test cache entries
$testPages = [
    ['url' => '/', 'content' => '<html><body>Home Page</body></html>'],
    ['url' => '/blogs', 'content' => '<html><body>Blogs Page</body></html>'],
    ['url' => '/listing.php?type=stays', 'content' => '<html><body>Stays Listing</body></html>'],
    ['url' => '/listing.php?type=cars', 'content' => '<html><body>Cars Listing</body></html>'],
    ['url' => '/about.php', 'content' => '<html><body>About Page</body></html>'],
];

$cached = 0;
foreach ($testPages as $page) {
    $queryParams = [];
    if (strpos($page['url'], '?') !== false) {
        list($url, $query) = explode('?', $page['url'], 2);
        parse_str($query, $queryParams);
    } else {
        $url = $page['url'];
    }
    
    if ($pageCache->setByUrl($url, $queryParams, $page['content'], 3600, ['test'])) {
        $cached++;
    }
}

// Update stats with test data
$cacheDir = $config['cache']['directory'];
$statsFile = $cacheDir . '/performance_stats.json';

$stats = [];
if (file_exists($statsFile)) {
    $stats = json_decode(@file_get_contents($statsFile), true) ?: [];
}

// Add some test stats
$stats['cache_hits'] = ($stats['cache_hits'] ?? 0) + 50;
$stats['cache_misses'] = ($stats['cache_misses'] ?? 0) + 10;
$stats['images_optimized'] = ($stats['images_optimized'] ?? 0) + 25;
$stats['total_original_size'] = ($stats['total_original_size'] ?? 0) + (5 * 1024 * 1024); // 5MB
$stats['total_optimized_size'] = ($stats['total_optimized_size'] ?? 0) + (3 * 1024 * 1024); // 3MB
$stats['total_webp_size'] = ($stats['total_webp_size'] ?? 0) + (2.5 * 1024 * 1024); // 2.5MB
$stats['last_updated'] = time();

@file_put_contents($statsFile, json_encode($stats, JSON_PRETTY_PRINT));

// Get cache stats
$cacheStats = $pageCache->getStats();

echo json_encode([
    'success' => true,
    'message' => "Generated $cached test cache entries",
    'cache_stats' => $cacheStats,
    'performance_stats' => $stats
], JSON_PRETTY_PRINT);
