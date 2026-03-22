<?php

namespace PerformanceOptimizer;

/**
 * CacheBackend Interface
 * 
 * Defines the contract for cache backend implementations.
 * All cache backends must implement these methods to ensure
 * consistent behavior across different storage mechanisms.
 */
interface CacheBackend
{
    /**
     * Retrieve a value from the cache
     * 
     * @param string $key The cache key
     * @return mixed The cached value, or null if not found or expired
     */
    public function get($key);

    /**
     * Store a value in the cache
     * 
     * @param string $key The cache key
     * @param mixed $value The value to cache
     * @param int $ttl Time-to-live in seconds (0 = no expiration)
     * @return bool True if successful, false otherwise
     */
    public function set($key, $value, $ttl = 0);

    /**
     * Remove a value from the cache
     * 
     * @param string $key The cache key
     * @return bool True if successful, false otherwise
     */
    public function delete($key);

    /**
     * Check if a key exists in the cache
     * 
     * @param string $key The cache key
     * @return bool True if the key exists and is not expired
     */
    public function exists($key);

    /**
     * Clear all cache entries
     * 
     * @return bool True if successful, false otherwise
     */
    public function clear();

    /**
     * Get cache statistics
     * 
     * @return array Array containing cache statistics
     */
    public function getStats();
}
