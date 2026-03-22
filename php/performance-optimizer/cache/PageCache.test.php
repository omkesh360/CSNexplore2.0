<?php

namespace PerformanceOptimizer\Cache\Tests;

use PerformanceOptimizer\Cache\PageCache;

/**
 * PageCache Unit Tests
 * 
 * Tests for cache key generation, storage, retrieval, expiration,
 * tag-based invalidation, dynamic page exclusion, and size limits.
 */
class PageCacheTest
{
    private $testCacheDir;
    private $pageCache;

    public function setUp()
    {
        // Create temporary cache directory
        $this->testCacheDir = sys_get_temp_dir() . '/page_cache_test_' . uniqid();
        mkdir($this->testCacheDir, 0755, true);

        // Initialize PageCache with test directory
        $this->pageCache = new PageCache($this->testCacheDir, 500, 3600);
    }

    public function tearDown()
    {
        // Clean up test directory
        $this->removeDirectory($this->testCacheDir);
    }

    private function removeDirectory($dir)
    {
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $path = $dir . '/' . $file;
                    if (is_dir($path)) {
                        $this->removeDirectory($path);
                    } else {
                        unlink($path);
                    }
                }
            }
            rmdir($dir);
        }
    }

    // ============ Cache Key Generation Tests ============

    public function testCacheKeyGenerationConsistency()
    {
        $url = '/blogs/test-post';
        $queryParams = ['id' => 1, 'sort' => 'date'];

        $key1 = $this->pageCache->generateCacheKey($url, $queryParams);
        $key2 = $this->pageCache->generateCacheKey($url, $queryParams);

        assert($key1 === $key2, 'Cache keys should be identical for same URL and params');
        assert(strpos($key1, 'page_') === 0, 'Cache key should start with page_');
    }

    public function testCacheKeyGenerationWithDifferentParams()
    {
        $url = '/blogs/test-post';
        $params1 = ['id' => 1];
        $params2 = ['id' => 2];

        $key1 = $this->pageCache->generateCacheKey($url, $params1);
        $key2 = $this->pageCache->generateCacheKey($url, $params2);

        assert($key1 !== $key2, 'Different params should generate different keys');
    }

    public function testCacheKeyGenerationWithReorderedParams()
    {
        $url = '/blogs/test-post';
        $params1 = ['id' => 1, 'sort' => 'date'];
        $params2 = ['sort' => 'date', 'id' => 1];

        $key1 = $this->pageCache->generateCacheKey($url, $params1);
        $key2 = $this->pageCache->generateCacheKey($url, $params2);

        assert($key1 === $key2, 'Reordered params should generate same key');
    }

    public function testCacheKeyGenerationWithTrailingSlash()
    {
        $url1 = '/blogs/test-post';
        $url2 = '/blogs/test-post/';

        $key1 = $this->pageCache->generateCacheKey($url1);
        $key2 = $this->pageCache->generateCacheKey($url2);

        assert($key1 === $key2, 'URLs with/without trailing slash should generate same key');
    }

    // ============ Cache Storage and Retrieval Tests ============

    public function testCacheStorageAndRetrieval()
    {
        $url = '/blogs/test-post';
        $queryParams = ['id' => 1];
        $content = '<html><body>Test Content</body></html>';

        $stored = $this->pageCache->setByUrl($url, $queryParams, $content);
        assert($stored === true, 'Cache storage should succeed');

        $retrieved = $this->pageCache->getByUrl($url, $queryParams);
        assert($retrieved === $content, 'Retrieved content should match stored content');
    }

    public function testCacheRetrievalMiss()
    {
        $url = '/blogs/nonexistent';
        $queryParams = [];

        $retrieved = $this->pageCache->getByUrl($url, $queryParams);
        assert($retrieved === null, 'Non-existent cache should return null');
    }

    public function testCacheRetrievalWithDifferentParams()
    {
        $url = '/blogs/test-post';
        $content = '<html><body>Test Content</body></html>';

        $this->pageCache->setByUrl($url, ['id' => 1], $content);

        $retrieved = $this->pageCache->getByUrl($url, ['id' => 2]);
        assert($retrieved === null, 'Different params should not retrieve cached content');
    }

    public function testCacheExists()
    {
        $url = '/blogs/test-post';
        $queryParams = ['id' => 1];
        $content = '<html><body>Test Content</body></html>';

        $this->pageCache->setByUrl($url, $queryParams, $content);

        $exists = $this->pageCache->existsByUrl($url, $queryParams);
        assert($exists === true, 'Cache should exist after storage');

        $notExists = $this->pageCache->existsByUrl($url, ['id' => 2]);
        assert($notExists === false, 'Cache should not exist for different params');
    }

    // ============ Cache Expiration Tests ============

    public function testCacheExpiration()
    {
        $url = '/blogs/test-post';
        $queryParams = [];
        $content = '<html><body>Test Content</body></html>';

        // Store with 1 second TTL
        $this->pageCache->setByUrl($url, $queryParams, $content, 1);

        // Should be available immediately
        $retrieved = $this->pageCache->getByUrl($url, $queryParams);
        assert($retrieved === $content, 'Cache should be available immediately');

        // Wait for expiration
        sleep(2);

        // Should be expired now
        $retrieved = $this->pageCache->getByUrl($url, $queryParams);
        assert($retrieved === null, 'Cache should be expired after TTL');
    }

    public function testCacheExpirationWithDefaultTtl()
    {
        $url = '/blogs/test-post';
        $queryParams = [];
        $content = '<html><body>Test Content</body></html>';

        // Store without specifying TTL (should use default 3600)
        $this->pageCache->setByUrl($url, $queryParams, $content);

        $retrieved = $this->pageCache->getByUrl($url, $queryParams);
        assert($retrieved === $content, 'Cache should use default TTL');
    }

    // ============ Tag-Based Invalidation Tests ============

    public function testTagBasedInvalidation()
    {
        $url1 = '/blogs/post-1';
        $url2 = '/blogs/post-2';
        $content = '<html><body>Test Content</body></html>';

        // Store two pages with same tag
        $this->pageCache->setByUrl($url1, [], $content, 3600, ['blog']);
        $this->pageCache->setByUrl($url2, [], $content, 3600, ['blog']);

        // Both should exist
        assert($this->pageCache->existsByUrl($url1) === true, 'First cache should exist');
        assert($this->pageCache->existsByUrl($url2) === true, 'Second cache should exist');

        // Invalidate by tag
        $count = $this->pageCache->invalidateByTag('blog');
        assert($count === 2, 'Should invalidate 2 entries');

        // Both should be gone
        assert($this->pageCache->existsByUrl($url1) === false, 'First cache should be invalidated');
        assert($this->pageCache->existsByUrl($url2) === false, 'Second cache should be invalidated');
    }

    public function testTagBasedInvalidationWithMultipleTags()
    {
        $url1 = '/blogs/post-1';
        $url2 = '/blogs/post-2';
        $url3 = '/attractions/place-1';
        $content = '<html><body>Test Content</body></html>';

        // Store pages with different tags
        $this->pageCache->setByUrl($url1, [], $content, 3600, ['blog', 'featured']);
        $this->pageCache->setByUrl($url2, [], $content, 3600, ['blog']);
        $this->pageCache->setByUrl($url3, [], $content, 3600, ['attractions']);

        // Invalidate only 'blog' tag
        $count = $this->pageCache->invalidateByTag('blog');
        assert($count === 2, 'Should invalidate 2 blog entries');

        // Blog entries should be gone, attractions should remain
        assert($this->pageCache->existsByUrl($url1) === false, 'First blog cache should be invalidated');
        assert($this->pageCache->existsByUrl($url2) === false, 'Second blog cache should be invalidated');
        assert($this->pageCache->existsByUrl($url3) === true, 'Attractions cache should remain');
    }

    public function testTagBasedInvalidationNoMatches()
    {
        $url = '/blogs/post-1';
        $content = '<html><body>Test Content</body></html>';

        $this->pageCache->setByUrl($url, [], $content, 3600, ['blog']);

        $count = $this->pageCache->invalidateByTag('nonexistent');
        assert($count === 0, 'Should invalidate 0 entries for nonexistent tag');

        assert($this->pageCache->existsByUrl($url) === true, 'Cache should still exist');
    }

    // ============ Dynamic Page Exclusion Tests ============

    public function testDynamicPageExclusion()
    {
        $dynamicUrls = [
            '/login',
            '/register',
            '/dashboard',
            '/user-profile',
            '/booking',
            '/checkout',
            '/cart',
        ];

        foreach ($dynamicUrls as $url) {
            $isDynamic = $this->pageCache->isDynamicPage($url);
            assert($isDynamic === true, "URL $url should be detected as dynamic");
        }
    }

    public function testDynamicPageNotCached()
    {
        $url = '/login';
        $content = '<html><body>Login Page</body></html>';

        $stored = $this->pageCache->setByUrl($url, [], $content);
        assert($stored === false, 'Dynamic page should not be cached');

        $retrieved = $this->pageCache->getByUrl($url);
        assert($retrieved === null, 'Dynamic page should not be retrievable');
    }

    public function testNonDynamicPageCached()
    {
        $url = '/blogs/test-post';
        $content = '<html><body>Blog Post</body></html>';

        $stored = $this->pageCache->setByUrl($url, [], $content);
        assert($stored === true, 'Non-dynamic page should be cached');

        $retrieved = $this->pageCache->getByUrl($url);
        assert($retrieved === $content, 'Non-dynamic page should be retrievable');
    }

    // ============ Cache Status Header Tests ============

    public function testCacheStatusHeaderOnHit()
    {
        $url = '/blogs/test-post';
        $content = '<html><body>Test Content</body></html>';

        $this->pageCache->setByUrl($url, [], $content);
        $this->pageCache->getByUrl($url);

        $status = $this->pageCache->getCacheStatus();
        assert($status === 'HIT', 'Cache status should be HIT');
    }

    public function testCacheStatusHeaderOnMiss()
    {
        $url = '/blogs/nonexistent';

        $this->pageCache->getByUrl($url);

        $status = $this->pageCache->getCacheStatus();
        assert($status === 'MISS', 'Cache status should be MISS');
    }

    public function testCacheStatusHeaderOnBypass()
    {
        $url = '/login';

        $this->pageCache->getByUrl($url);

        $status = $this->pageCache->getCacheStatus();
        assert($status === 'BYPASS', 'Cache status should be BYPASS for dynamic pages');
    }

    // ============ Cache Size Limit Tests ============

    public function testCacheSizeLimitEnforcement()
    {
        // Create a small cache with 1MB limit
        $smallCacheDir = sys_get_temp_dir() . '/small_cache_test_' . uniqid();
        mkdir($smallCacheDir, 0755, true);
        $smallCache = new PageCache($smallCacheDir, 1, 3600);

        // Create content that's about 600KB
        $largeContent = str_repeat('x', 600 * 1024);

        // Store first entry (should succeed)
        $stored1 = $smallCache->setByUrl('/page1', [], $largeContent);
        assert($stored1 === true, 'First entry should be stored');

        // Store second entry (should trigger LRU eviction)
        $stored2 = $smallCache->setByUrl('/page2', [], $largeContent);
        assert($stored2 === true, 'Second entry should be stored with LRU eviction');

        // First entry should be evicted
        $retrieved1 = $smallCache->getByUrl('/page1');
        assert($retrieved1 === null, 'First entry should be evicted due to LRU');

        // Second entry should still exist
        $retrieved2 = $smallCache->getByUrl('/page2');
        assert($retrieved2 === $largeContent, 'Second entry should still exist');

        // Clean up
        $this->removeDirectory($smallCacheDir);
    }

    public function testCacheSizeStats()
    {
        $url = '/blogs/test-post';
        $content = '<html><body>Test Content</body></html>';

        $this->pageCache->setByUrl($url, [], $content);

        $stats = $this->pageCache->getStats();

        assert($stats['total_entries'] === 1, 'Should have 1 cache entry');
        assert($stats['total_size_bytes'] > 0, 'Total size should be greater than 0');
        assert($stats['total_size_mb'] >= 0, 'Total size in MB should be >= 0');
        assert($stats['max_size_mb'] === 500, 'Max size should be 500MB');
    }

    // ============ Cache Clear Tests ============

    public function testCacheClear()
    {
        $url1 = '/blogs/post-1';
        $url2 = '/blogs/post-2';
        $content = '<html><body>Test Content</body></html>';

        $this->pageCache->setByUrl($url1, [], $content);
        $this->pageCache->setByUrl($url2, [], $content);

        assert($this->pageCache->existsByUrl($url1) === true, 'First cache should exist');
        assert($this->pageCache->existsByUrl($url2) === true, 'Second cache should exist');

        $cleared = $this->pageCache->clear();
        assert($cleared === true, 'Clear should succeed');

        assert($this->pageCache->existsByUrl($url1) === false, 'First cache should be cleared');
        assert($this->pageCache->existsByUrl($url2) === false, 'Second cache should be cleared');
    }

    // ============ Cache Hit Tracking Tests ============

    public function testCacheHitTracking()
    {
        $url = '/blogs/test-post';
        $content = '<html><body>Test Content</body></html>';

        $this->pageCache->setByUrl($url, [], $content);

        // Access cache multiple times
        $this->pageCache->getByUrl($url);
        $this->pageCache->getByUrl($url);
        $this->pageCache->getByUrl($url);

        $stats = $this->pageCache->getStats();
        assert($stats['total_hits'] === 3, 'Should track 3 cache hits');
    }

    // ============ Run All Tests ============

    public function runAllTests()
    {
        $tests = [
            'testCacheKeyGenerationConsistency',
            'testCacheKeyGenerationWithDifferentParams',
            'testCacheKeyGenerationWithReorderedParams',
            'testCacheKeyGenerationWithTrailingSlash',
            'testCacheStorageAndRetrieval',
            'testCacheRetrievalMiss',
            'testCacheRetrievalWithDifferentParams',
            'testCacheExists',
            'testCacheExpiration',
            'testCacheExpirationWithDefaultTtl',
            'testTagBasedInvalidation',
            'testTagBasedInvalidationWithMultipleTags',
            'testTagBasedInvalidationNoMatches',
            'testDynamicPageExclusion',
            'testDynamicPageNotCached',
            'testNonDynamicPageCached',
            'testCacheStatusHeaderOnHit',
            'testCacheStatusHeaderOnMiss',
            'testCacheStatusHeaderOnBypass',
            'testCacheSizeLimitEnforcement',
            'testCacheSizeStats',
            'testCacheClear',
            'testCacheHitTracking',
        ];

        $passed = 0;
        $failed = 0;

        foreach ($tests as $test) {
            $this->setUp();

            try {
                $this->$test();
                echo "✓ $test\n";
                ++$passed;
            } catch (AssertionError $e) {
                echo "✗ $test: " . $e->getMessage() . "\n";
                ++$failed;
            } catch (Exception $e) {
                echo "✗ $test: " . $e->getMessage() . "\n";
                ++$failed;
            }

            $this->tearDown();
        }

        echo "\n" . str_repeat('=', 50) . "\n";
        echo "Tests Passed: $passed\n";
        echo "Tests Failed: $failed\n";
        echo "Total Tests: " . ($passed + $failed) . "\n";

        return $failed === 0;
    }
}

// Run tests if executed directly
if (php_sapi_name() === 'cli' && basename($argv[0] ?? '') === basename(__FILE__)) {
    require_once __DIR__ . '/../CacheBackend.php';
    require_once __DIR__ . '/PageCache.php';

    $test = new PageCacheTest();
    $success = $test->runAllTests();
    exit($success ? 0 : 1);
}
