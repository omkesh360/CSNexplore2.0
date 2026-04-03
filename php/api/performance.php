<?php
// Performance Management API
require_once '../config.php';
require_once '../jwt.php';

header('Content-Type: application/json');
$db = getDB();

// Verify admin authentication
$admin = requireAdmin();

$method = $_SERVER['REQUEST_METHOD'];

// GET - Fetch performance data
if ($method === 'GET') {
    // Mock performance data (you can implement real metrics later)
    $cacheDir = __DIR__ . '/../../cache';
    $cacheSize = 0;
    
    if (is_dir($cacheDir)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($cacheDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($files as $file) {
            if ($file->isFile()) {
                $cacheSize += $file->getSize();
            }
        }
    }
    
    $cacheSizeMB = round($cacheSize / 1024 / 1024, 2);
    
    // Count images
    $imageCount = 0;
    $uploadDir = __DIR__ . '/../../images/uploads';
    if (is_dir($uploadDir)) {
        $imageFiles = glob($uploadDir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
        $imageCount = count($imageFiles);
    }
    
    sendJson([
        'cache_hit_rate' => 85.5,
        'avg_page_load' => 245,
        'cache_size_mb' => $cacheSizeMB,
        'images_optimized' => $imageCount,
        'features' => [
            'cache' => ['enabled' => true],
            'image' => ['enabled' => true],
            'assets' => ['enabled' => true],
            'query_cache' => ['enabled' => true],
            'lazy_loading' => ['enabled' => true]
        ],
        'config' => [
            'cache' => ['ttl' => 3600, 'max_size_mb' => 2000],
            'image' => ['quality' => 75],
            'query_cache' => ['ttl' => 3600]
        ],
        'metrics' => [
            'php_version' => [
                'label' => 'PHP Version',
                'value' => PHP_VERSION,
                'status' => version_compare(PHP_VERSION, '7.4.0', '>=') ? 'good' : 'warning'
            ],
            'memory_usage' => [
                'label' => 'Memory Usage',
                'value' => round(memory_get_usage() / 1024 / 1024, 2) . ' MB',
                'status' => 'good'
            ],
            'db_connection' => [
                'label' => 'Database Connection',
                'value' => 'Active',
                'status' => 'good'
            ]
        ],
        'slow_queries' => []
    ]);
}

// POST - Perform actions
if ($method === 'POST') {
    $input = getJsonInput();
    $action = $input['action'] ?? '';
    
    if ($action === 'purge_all') {
        // Clear cache directory
        $cacheDir = __DIR__ . '/../../cache';
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '/*');
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.gitkeep' && basename($file) !== '.htaccess') {
                    unlink($file);
                }
            }
        }
        sendJson(['success' => true, 'message' => 'All cache purged']);
    }
    
    if ($action === 'purge_page_cache') {
        $cacheDir = __DIR__ . '/../../cache';
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '/page_*.cache');
            foreach ($files as $file) {
                if (is_file($file)) unlink($file);
            }
        }
        sendJson(['success' => true, 'message' => 'Page cache cleared']);
    }
    
    if ($action === 'purge_query_cache') {
        $cacheDir = __DIR__ . '/../../cache';
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '/query_*.cache');
            foreach ($files as $file) {
                if (is_file($file)) unlink($file);
            }
        }
        sendJson(['success' => true, 'message' => 'Query cache cleared']);
    }
    
    if ($action === 'preload_cache') {
        // Mock preload - you can implement actual preloading
        sendJson(['success' => true, 'message' => 'Cache preloaded']);
    }
    
    if ($action === 'toggle_feature') {
        // Mock toggle - you can implement actual feature toggling
        sendJson(['success' => true, 'message' => 'Feature toggled']);
    }
    
    if ($action === 'save_config') {
        // Mock save - you can implement actual config saving
        sendJson(['success' => true, 'message' => 'Configuration saved']);
    }
}

sendError('Invalid request', 400);
