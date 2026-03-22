<?php

namespace PerformanceOptimizer\Cache;

use PerformanceOptimizer\CacheBackend;

/**
 * PageCache Class
 * 
 * Manages full-page HTML caching with support for:
 * - Cache key generation from URL and query parameters
 * - Cache storage and retrieval
 * - TTL-based expiration
 * - Tag-based invalidation
 * - Dynamic page exclusion
 * - X-Cache-Status header support
 * - Cache size limit enforcement with LRU eviction
 */
class PageCache implements CacheBackend
{
    /**
     * Cache directory path
     * @var string
     */
    private $cacheDir;

    /**
     * Metadata directory path
     * @var string
     */
    private $metadataDir;

    /**
     * Maximum cache size in bytes (500MB)
     * @var int
     */
    private $maxSizeBytes;

    /**
     * Default TTL in seconds
     * @var int
     */
    private $defaultTtl;

    /**
     * Dynamic pages that should not be cached
     * @var array
     */
    private $dynamicPages = [
        'login',
        'booking',
        'dashboard',
        'user-profile',
        'register',
        'checkout',
        'cart',
    ];

    /**
     * Cache status for current request
     * @var string
     */
    private $cacheStatus = 'MISS';

    /**
     * Constructor
     * 
     * @param string $cacheDir Cache directory path
     * @param int $maxSizeMb Maximum cache size in MB
     * @param int $defaultTtl Default TTL in seconds
     */
    public function __construct($cacheDir, $maxSizeMb = 500, $defaultTtl = 3600)
    {
        $this->cacheDir = $cacheDir . '/pages';
        $this->metadataDir = $cacheDir . '/metadata';
        $this->maxSizeBytes = $maxSizeMb * 1024 * 1024;
        $this->defaultTtl = $defaultTtl;

        $this->ensureDirectoriesExist();
    }

    /**
     * Ensure cache directories exist
     * 
     * @return void
     */
    private function ensureDirectoriesExist()
    {
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
        if (!is_dir($this->metadataDir)) {
            mkdir($this->metadataDir, 0755, true);
        }
    }

    /**
     * Generate cache key from URL and query parameters
     * 
     * @param string $url The page URL
     * @param array $queryParams Query parameters
     * @return string The cache key
     */
    public function generateCacheKey($url, $queryParams = [])
    {
        // Normalize URL
        $url = parse_url($url, PHP_URL_PATH) ?? $url;
        $url = rtrim($url, '/') ?: '/';

        // Sort query parameters for consistency
        ksort($queryParams);

        // Create hash from URL and query params
        $keyData = $url . '|' . json_encode($queryParams);
        $hash = hash('sha256', $keyData);

        return 'page_' . substr($hash, 0, 16);
    }

    /**
     * Check if a page is dynamic and should not be cached
     * 
     * @param string $url The page URL
     * @return bool True if page is dynamic
     */
    public function isDynamicPage($url)
    {
        $path = parse_url($url, PHP_URL_PATH) ?? $url;
        $path = strtolower($path);

        foreach ($this->dynamicPages as $dynamicPage) {
            if (strpos($path, $dynamicPage) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get a cached page (implements CacheBackend interface)
     * 
     * @param string $key The cache key (can be URL or cache key)
     * @return string|null The cached HTML, or null if not found or expired
     */
    public function get($key)
    {
        // If key looks like a URL, treat it as such
        if (strpos($key, '/') === 0 || strpos($key, 'http') === 0) {
            return $this->getByUrl($key);
        }

        // Otherwise treat as cache key
        return $this->getByKey($key);
    }

    /**
     * Get a cached page by URL
     * 
     * @param string $url The page URL
     * @param array $queryParams Query parameters
     * @return string|null The cached HTML, or null if not found or expired
     */
    public function getByUrl($url, $queryParams = [])
    {
        // Check if page is dynamic
        if ($this->isDynamicPage($url)) {
            $this->cacheStatus = 'BYPASS';
            return null;
        }

        $cacheKey = $this->generateCacheKey($url, $queryParams);
        return $this->getByKey($cacheKey);
    }

    /**
     * Get a cached page by cache key
     * 
     * @param string $cacheKey The cache key
     * @return string|null The cached HTML, or null if not found or expired
     */
    private function getByKey($cacheKey)
    {
        $cacheFile = $this->cacheDir . '/' . $cacheKey . '.html';
        $metadataFile = $this->metadataDir . '/' . $cacheKey . '.meta';

        // Check if cache file exists
        if (!file_exists($cacheFile) || !file_exists($metadataFile)) {
            $this->cacheStatus = 'MISS';
            return null;
        }

        // Check if cache has expired
        $metadata = json_decode(file_get_contents($metadataFile), true);
        if ($metadata === null) {
            $this->cacheStatus = 'MISS';
            return null;
        }

        if (isset($metadata['expires_at']) && time() > $metadata['expires_at']) {
            // Cache expired, delete it
            if (file_exists($cacheFile)) {
                unlink($cacheFile);
            }
            if (file_exists($metadataFile)) {
                unlink($metadataFile);
            }
            $this->cacheStatus = 'MISS';
            return null;
        }

        // Update last accessed time
        $metadata['last_accessed'] = time();
        $metadata['hit_count'] = ($metadata['hit_count'] ?? 0) + 1;
        file_put_contents($metadataFile, json_encode($metadata));

        // Read and return cached content
        $content = file_get_contents($cacheFile);
        $this->cacheStatus = 'HIT';

        return $content;
    }

    /**
     * Store a page in cache (implements CacheBackend interface)
     * 
     * @param string $key The cache key
     * @param mixed $value The value to cache (HTML content)
     * @param int $ttl Time-to-live in seconds (0 = use default)
     * @return bool True if successful
     */
    public function set($key, $value, $ttl = 0)
    {
        // For interface compatibility, treat key as cache key
        // Use the extended setByUrl method for URL-based caching
        return $this->setByKey($key, $value, $ttl);
    }

    /**
     * Store a page in cache by URL
     * 
     * @param string $url The page URL
     * @param array $queryParams Query parameters
     * @param string $content The HTML content to cache
     * @param int $ttl Time-to-live in seconds (0 = use default)
     * @param array $tags Tags for cache invalidation
     * @return bool True if successful
     */
    public function setByUrl($url, $queryParams, $content, $ttl = 0, $tags = [])
    {
        // Don't cache dynamic pages
        if ($this->isDynamicPage($url)) {
            return false;
        }

        $cacheKey = $this->generateCacheKey($url, $queryParams);
        return $this->setByKey($cacheKey, $content, $ttl, $url, $queryParams, $tags);
    }

    /**
     * Store a page in cache by cache key
     * 
     * @param string $cacheKey The cache key
     * @param string $content The HTML content to cache
     * @param int $ttl Time-to-live in seconds (0 = use default)
     * @param string $url The page URL (optional)
     * @param array $queryParams Query parameters (optional)
     * @param array $tags Tags for cache invalidation (optional)
     * @return bool True if successful
     */
    private function setByKey($cacheKey, $content, $ttl = 0, $url = '', $queryParams = [], $tags = [])
    {
        $cacheFile = $this->cacheDir . '/' . $cacheKey . '.html';
        $metadataFile = $this->metadataDir . '/' . $cacheKey . '.meta';

        // Use default TTL if not specified
        if ($ttl === 0) {
            $ttl = $this->defaultTtl;
        }

        // Check cache size before storing
        $this->enforceMaxSize(strlen($content));

        // Create metadata
        $metadata = [
            'cache_key' => $cacheKey,
            'url' => $url,
            'query_params' => $queryParams,
            'tags' => $tags,
            'ttl' => $ttl,
            'created_at' => time(),
            'expires_at' => time() + $ttl,
            'hit_count' => 0,
            'last_accessed' => time(),
            'size_bytes' => strlen($content),
        ];

        // Store cache file
        if (file_put_contents($cacheFile, $content) === false) {
            return false;
        }

        // Store metadata
        if (file_put_contents($metadataFile, json_encode($metadata)) === false) {
            unlink($cacheFile);
            return false;
        }

        return true;
    }

    /**
     * Delete a cached page (implements CacheBackend interface)
     * 
     * @param string $key The cache key
     * @return bool True if successful
     */
    public function delete($key)
    {
        $cacheFile = $this->cacheDir . '/' . $key . '.html';
        $metadataFile = $this->metadataDir . '/' . $key . '.meta';

        $success = true;

        if (file_exists($cacheFile)) {
            $success = unlink($cacheFile) && $success;
        }

        if (file_exists($metadataFile)) {
            $success = unlink($metadataFile) && $success;
        }

        return $success;
    }

    /**
     * Delete a cached page by URL
     * 
     * @param string $url The page URL
     * @param array $queryParams Query parameters
     * @return bool True if successful
     */
    public function deleteByUrl($url, $queryParams = [])
    {
        $cacheKey = $this->generateCacheKey($url, $queryParams);
        return $this->delete($cacheKey);
    }

    /**
     * Check if a cache entry exists and is valid (implements CacheBackend interface)
     * 
     * @param string $key The cache key
     * @return bool True if cache exists and is valid
     */
    public function exists($key)
    {
        return $this->get($key) !== null;
    }

    /**
     * Check if a cache entry exists by URL
     * 
     * @param string $url The page URL
     * @param array $queryParams Query parameters
     * @return bool True if cache exists and is valid
     */
    public function existsByUrl($url, $queryParams = [])
    {
        return $this->getByUrl($url, $queryParams) !== null;
    }

    /**
     * Invalidate cache by tag
     * 
     * @param string $tag The tag to invalidate
     * @return int Number of cache entries invalidated
     */
    public function invalidateByTag($tag)
    {
        $count = 0;
        $metadataFiles = glob($this->metadataDir . '/*.meta');

        foreach ($metadataFiles as $metadataFile) {
            $metadata = json_decode(file_get_contents($metadataFile), true);

            if ($metadata === null) {
                continue;
            }

            if (isset($metadata['tags']) && in_array($tag, $metadata['tags'], true)) {
                $cacheKey = $metadata['cache_key'];
                $cacheFile = $this->cacheDir . '/' . $cacheKey . '.html';

                if (file_exists($cacheFile)) {
                    unlink($cacheFile);
                }

                unlink($metadataFile);
                ++$count;
            }
        }

        return $count;
    }

    /**
     * Clear all cache entries
     * 
     * @return bool True if successful
     */
    public function clear()
    {
        $success = true;

        // Clear cache files
        $cacheFiles = glob($this->cacheDir . '/*.html');
        foreach ($cacheFiles as $file) {
            if (!unlink($file)) {
                $success = false;
            }
        }

        // Clear metadata files
        $metadataFiles = glob($this->metadataDir . '/*.meta');
        foreach ($metadataFiles as $file) {
            if (!unlink($file)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Get cache statistics
     * 
     * @return array Cache statistics
     */
    public function getStats()
    {
        $metadataFiles = glob($this->metadataDir . '/*.meta');
        $totalSize = 0;
        $totalHits = 0;
        $totalMisses = 0;
        $entryCount = 0;

        foreach ($metadataFiles as $metadataFile) {
            $metadata = json_decode(file_get_contents($metadataFile), true);

            if ($metadata === null) {
                continue;
            }

            ++$entryCount;
            $totalSize += $metadata['size_bytes'] ?? 0;
            $totalHits += $metadata['hit_count'] ?? 0;
        }

        $hitRate = $entryCount > 0 ? ($totalHits / ($totalHits + $totalMisses + 1)) * 100 : 0;

        return [
            'total_entries' => $entryCount,
            'total_size_bytes' => $totalSize,
            'total_size_mb' => round($totalSize / (1024 * 1024), 2),
            'max_size_mb' => $this->maxSizeBytes / (1024 * 1024),
            'total_hits' => $totalHits,
            'hit_rate_percent' => round($hitRate, 2),
            'cache_status' => $this->cacheStatus,
        ];
    }

    /**
     * Enforce maximum cache size with LRU eviction
     * 
     * @param int $requiredBytes Bytes needed for new entry
     * @return void
     */
    private function enforceMaxSize($requiredBytes)
    {
        $currentSize = $this->getCurrentCacheSize();

        // If adding new content would exceed limit, evict LRU entries
        if ($currentSize + $requiredBytes > $this->maxSizeBytes) {
            $this->evictLRU($currentSize + $requiredBytes - $this->maxSizeBytes);
        }
    }

    /**
     * Get current cache directory size
     * 
     * @return int Total size in bytes
     */
    private function getCurrentCacheSize()
    {
        $size = 0;
        $cacheFiles = glob($this->cacheDir . '/*.html');

        foreach ($cacheFiles as $file) {
            $size += filesize($file);
        }

        return $size;
    }

    /**
     * Evict least recently used cache entries
     * 
     * @param int $bytesToFree Bytes to free
     * @return void
     */
    private function evictLRU($bytesToFree)
    {
        $metadataFiles = glob($this->metadataDir . '/*.meta');
        $entries = [];

        // Collect all entries with their last accessed time
        foreach ($metadataFiles as $metadataFile) {
            $metadata = json_decode(file_get_contents($metadataFile), true);

            if ($metadata === null) {
                continue;
            }

            $entries[] = [
                'metadata_file' => $metadataFile,
                'cache_key' => $metadata['cache_key'],
                'last_accessed' => $metadata['last_accessed'] ?? 0,
                'size_bytes' => $metadata['size_bytes'] ?? 0,
            ];
        }

        // Sort by last accessed time (oldest first)
        usort($entries, function ($a, $b) {
            return $a['last_accessed'] <=> $b['last_accessed'];
        });

        // Evict entries until we've freed enough space
        $freedBytes = 0;
        foreach ($entries as $entry) {
            if ($freedBytes >= $bytesToFree) {
                break;
            }

            $cacheFile = $this->cacheDir . '/' . $entry['cache_key'] . '.html';

            if (file_exists($cacheFile)) {
                $freedBytes += $entry['size_bytes'];
                unlink($cacheFile);
            }

            unlink($entry['metadata_file']);
        }
    }

    /**
     * Get the current cache status (HIT, MISS, or BYPASS)
     * 
     * @return string The cache status
     */
    public function getCacheStatus()
    {
        return $this->cacheStatus;
    }

    /**
     * Set X-Cache-Status header
     * 
     * @return void
     */
    public function setStatusHeader()
    {
        header('X-Cache-Status: ' . $this->cacheStatus);
    }
}
