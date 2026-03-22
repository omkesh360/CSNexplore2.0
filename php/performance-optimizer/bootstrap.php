<?php

/**
 * Performance Optimizer Bootstrap
 * 
 * Initializes the performance optimizer system and integrates it with the application.
 * This file should be included early in the application bootstrap process.
 */

namespace PerformanceOptimizer;

// Ensure the namespace is available
require_once __DIR__ . '/PerformanceOptimizer.php';
require_once __DIR__ . '/DatabaseInitializer.php';
require_once __DIR__ . '/CacheBackend.php';
require_once __DIR__ . '/OptimizationComponent.php';

/**
 * Initialize the performance optimizer
 * 
 * @param \PDO $db Database connection
 * @return PerformanceOptimizer
 */
function initializePerformanceOptimizer($db)
{
    $optimizer = PerformanceOptimizer::getInstance();
    $optimizer->initialize($db);
    return $optimizer;
}

// Auto-initialize if database is available
if (function_exists('getDB')) {
    try {
        $db = getDB();
        if ($db instanceof \PDO) {
            initializePerformanceOptimizer($db->getConnection());
        }
    } catch (\Exception $e) {
        error_log('Performance Optimizer: Failed to auto-initialize - ' . $e->getMessage());
    }
}
