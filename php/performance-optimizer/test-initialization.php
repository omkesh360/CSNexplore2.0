<?php

/**
 * Performance Optimizer Initialization Test
 * 
 * This script tests the initialization of the performance optimizer system.
 * It verifies that all database tables are created correctly.
 */

require_once __DIR__ . '/../config.php';

use PerformanceOptimizer\PerformanceOptimizer;
use PerformanceOptimizer\DatabaseInitializer;

try {
    echo "Performance Optimizer Initialization Test\n";
    echo "==========================================\n\n";

    // Get database connection
    $db = getDB();
    echo "✓ Database connection established\n";

    // Initialize performance optimizer
    $optimizer = PerformanceOptimizer::getInstance();
    $result = $optimizer->initialize($db->getConnection());

    if ($result) {
        echo "✓ Performance Optimizer initialized successfully\n";
    } else {
        echo "✗ Performance Optimizer initialization failed\n";
        exit(1);
    }

    // Verify tables exist
    $tables = [
        'cache_metadata',
        'performance_metrics',
        'query_cache_stats',
        'slow_queries',
        'image_optimization_stats',
    ];

    echo "\nVerifying database tables:\n";
    foreach ($tables as $table) {
        $result = $db->fetchOne("SELECT name FROM sqlite_master WHERE type='table' AND name=?", [$table]);
        if ($result) {
            echo "✓ Table '{$table}' exists\n";
        } else {
            echo "✗ Table '{$table}' not found\n";
            exit(1);
        }
    }

    // Verify directories exist
    echo "\nVerifying cache directories:\n";
    $directories = [
        'cache/pages',
        'cache/queries',
        'images/uploads/variants',
    ];

    foreach ($directories as $dir) {
        if (is_dir($dir)) {
            echo "✓ Directory '{$dir}' exists\n";
        } else {
            echo "✗ Directory '{$dir}' not found\n";
            exit(1);
        }
    }

    // Verify configuration
    echo "\nVerifying configuration:\n";
    $cacheEnabled = $optimizer->isFeatureEnabled('cache');
    $imageEnabled = $optimizer->isFeatureEnabled('image');
    $assetsEnabled = $optimizer->isFeatureEnabled('assets');
    $queryEnabled = $optimizer->isFeatureEnabled('query_cache');

    echo "✓ Cache feature: " . ($cacheEnabled ? 'enabled' : 'disabled') . "\n";
    echo "✓ Image feature: " . ($imageEnabled ? 'enabled' : 'disabled') . "\n";
    echo "✓ Assets feature: " . ($assetsEnabled ? 'enabled' : 'disabled') . "\n";
    echo "✓ Query Cache feature: " . ($queryEnabled ? 'enabled' : 'disabled') . "\n";

    echo "\n✓ All initialization tests passed!\n";

} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
