<?php

/**
 * Performance Optimizer Configuration
 * 
 * Central configuration file for all performance optimization features
 * including caching, image optimization, asset minification, and query caching.
 */

return [
    'cache' => [
        'enabled' => true,
        'ttl' => 3600,
        'max_size_mb' => 500,
        'directory' => __DIR__ . '/../../cache',
    ],
    'image' => [
        'enabled' => true,
        'quality' => 75,
        'enable_webp' => true,
        'variants' => [
            'thumbnail' => ['width' => 150, 'height' => 150],
            'medium' => ['width' => 400, 'height' => 400],
            'large' => ['width' => 800, 'height' => 800],
        ],
    ],
    'assets' => [
        'enabled' => true,
        'minify_css' => true,
        'minify_js' => true,
        'minify_html' => true,
        'combine_css' => true,
        'combine_js' => true,
    ],
    'query_cache' => [
        'enabled' => true,
        'ttl' => 3600,
        'backend' => 'file',
        'redis_host' => 'localhost',
        'redis_port' => 6379,
    ],
    'preloader' => [
        'enabled' => true,
        'schedule' => 'daily',
        'pages' => ['/', '/blogs', '/attractions', '/stays', '/cars', '/bikes'],
    ],
    'cdn' => [
        'enabled' => false,
        'url' => '',
        'cache_control' => 'public, max-age=31536000',
    ],
    'monitoring' => [
        'enabled' => true,
        'track_metrics' => true,
        'slow_query_threshold_ms' => 500,
    ],
];
