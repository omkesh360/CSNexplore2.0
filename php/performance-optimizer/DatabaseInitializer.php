<?php

namespace PerformanceOptimizer;

/**
 * DatabaseInitializer Class
 * 
 * Handles creation and initialization of all performance optimizer database tables.
 * Creates tables for cache metadata, performance metrics, query cache stats, 
 * slow queries, and image optimization statistics.
 */
class DatabaseInitializer
{
    /**
     * Database connection
     * 
     * @var \PDO
     */
    private $db;

    /**
     * Constructor
     * 
     * @param \PDO $db Database connection
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Initialize all performance optimizer tables
     * 
     * @return bool True if successful
     */
    public function initialize()
    {
        try {
            $this->createCacheMetadataTable();
            $this->createPerformanceMetricsTable();
            $this->createQueryCacheStatsTable();
            $this->createSlowQueriesTable();
            $this->createImageOptimizationStatsTable();
            return true;
        } catch (\Exception $e) {
            error_log('Performance Optimizer: Database initialization failed - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create cache_metadata table
     * 
     * @return void
     */
    private function createCacheMetadataTable()
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS cache_metadata (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            cache_key TEXT UNIQUE NOT NULL,
            url TEXT NOT NULL,
            query_params TEXT,
            tags TEXT,
            ttl INTEGER,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME,
            hit_count INTEGER DEFAULT 0,
            last_accessed DATETIME
        )
        ";
        $this->db->exec($sql);
    }

    /**
     * Create performance_metrics table
     * 
     * @return void
     */
    private function createPerformanceMetricsTable()
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS performance_metrics (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            page_url TEXT NOT NULL,
            page_load_time_ms REAL,
            ttfb_ms REAL,
            cache_hit INTEGER DEFAULT 0,
            cache_size_bytes INTEGER,
            query_count INTEGER,
            query_time_ms REAL,
            image_count INTEGER,
            image_size_bytes INTEGER,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
        ";
        $this->db->exec($sql);
    }

    /**
     * Create query_cache_stats table
     * 
     * @return void
     */
    private function createQueryCacheStatsTable()
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS query_cache_stats (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            query_hash TEXT UNIQUE NOT NULL,
            query_text TEXT,
            execution_time_ms REAL,
            cache_hits INTEGER DEFAULT 0,
            cache_misses INTEGER DEFAULT 0,
            last_executed DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
        ";
        $this->db->exec($sql);
    }

    /**
     * Create slow_queries table
     * 
     * @return void
     */
    private function createSlowQueriesTable()
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS slow_queries (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            query_text TEXT NOT NULL,
            execution_time_ms REAL,
            table_name TEXT,
            detected_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
        ";
        $this->db->exec($sql);
    }

    /**
     * Create image_optimization_stats table
     * 
     * @return void
     */
    private function createImageOptimizationStatsTable()
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS image_optimization_stats (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            original_filename TEXT NOT NULL,
            original_size_bytes INTEGER,
            optimized_size_bytes INTEGER,
            webp_size_bytes INTEGER,
            compression_ratio REAL,
            processing_time_ms REAL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
        ";
        $this->db->exec($sql);
    }
}
