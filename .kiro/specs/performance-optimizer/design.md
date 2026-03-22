# Performance Optimizer Design Document

## Overview

The Performance Optimizer is a modular PHP system designed to reduce page load times by 40-60% through intelligent caching, image optimization, asset minification, and database query optimization. The system operates transparently, integrating with existing application logic while providing comprehensive admin controls and monitoring capabilities.

### Key Design Principles

- **Modularity**: Each optimization component (caching, images, assets, queries) operates independently
- **Transparency**: Optimization happens automatically without requiring application code changes
- **Fallback Support**: Graceful degradation when dependencies (Redis, ImageMagick) are unavailable
- **Performance First**: All optimization code follows PHP best practices for minimal overhead
- **Observability**: Comprehensive metrics and logging for monitoring effectiveness

## Architecture

### System Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                     HTTP Request                                │
└────────────────────────┬────────────────────────────────────────┘
                         │
                    ┌────▼─────┐
                    │ PageCache │ ◄─── Check cache hit
                    └────┬─────┘
                         │ (miss)
        ┌────────────────┼────────────────┐
        │                │                │
    ┌───▼────┐    ┌──────▼──────┐   ┌────▼────┐
    │ Query  │    │ Application │   │ Asset   │
    │ Cache  │    │   Logic     │   │Minifier │
    └────┬───┘    └──────┬──────┘   └────┬────┘
         │                │               │
         └────────────────┼───────────────┘
                          │
                    ┌─────▼──────┐
                    │Image Optim. │
                    └─────┬──────┘
                          │
                    ┌─────▼──────┐
                    │ PageCache  │ ◄─── Store result
                    │   Store    │
                    └────────────┘
```

### Component Interaction Flow

1. **Request Entry**: HTTP request arrives at application
2. **Cache Check**: PageCache checks for valid cached response
3. **Cache Hit**: Return cached HTML with X-Cache-Status header
4. **Cache Miss**: Execute application logic
5. **Query Optimization**: QueryCache intercepts database queries
6. **Asset Processing**: AssetMinifier processes CSS/JS/HTML
7. **Image Handling**: ImageOptimizer processes uploaded images
8. **Response Caching**: PageCache stores response for future requests
9. **Metrics**: PerformanceMonitor tracks all metrics

## Components and Interfaces

### 1. PageCache Class

**Location**: `/php/performance-optimizer/cache/PageCache.php`

**Responsibilities**:
- Generate cache keys from URL and query parameters
- Store/retrieve full-page HTML cache
- Manage cache expiration and TTL
- Handle cache invalidation by tags
- Add X-Cache-Status headers

**Key Methods**:
```php
public function get($url, $queryParams = []): ?string
public function set($url, $queryParams, $content, $ttl = 3600): bool
public function invalidate($url, $queryParams = []): bool
public function invalidateByTag($tag): int
public function clear(): bool
public function getStats(): array
```

**Cache Key Generation**:
- Format: `page_{hash(url + sorted_query_params)}`
- Example: `page_a1b2c3d4e5f6g7h8`

**Storage**:
- Location: `/cache/pages/`
- File format: `{cache_key}.html`
- Metadata: `{cache_key}.meta` (TTL, tags, created_at)

### 2. ImageOptimizer Class

**Location**: `/php/performance-optimizer/image/ImageOptimizer.php`

**Responsibilities**:
- Compress images to 70-80% quality
- Convert to WebP format
- Generate size variants (thumbnail, medium, large)
- Maintain aspect ratio with center-crop
- Support GD and ImageMagick backends
- Log optimization metrics

**Key Methods**:
```php
public function optimize($sourcePath, $destinationDir): array
public function generateVariants($imagePath, $destinationDir): array
public function convertToWebP($imagePath, $quality = 75): string
public function getOptimizationStats(): array
```

**Image Variants**:
- Thumbnail: 150x150px (square, center-crop)
- Medium: 400x400px (square, center-crop)
- Large: 800x800px (square, center-crop)
- WebP: All variants in WebP format

**Storage Structure**:
```
/images/uploads/
├── original/
│   └── {filename}.{ext}
├── thumbnail/
│   ├── {filename}.jpg
│   └── {filename}.webp
├── medium/
│   ├── {filename}.jpg
│   └── {filename}.webp
├── large/
│   ├── {filename}.jpg
│   └── {filename}.webp
└── webp/
    └── {filename}.webp
```

### 3. AssetMinifier Class

**Location**: `/php/performance-optimizer/assets/AssetMinifier.php`

**Responsibilities**:
- Minify CSS, JavaScript, and HTML
- Combine multiple CSS files
- Combine multiple JavaScript files
- Generate source maps for debugging
- Add defer/async attributes appropriately
- Cache minified output

**Key Methods**:
```php
public function minifyCSS($css): string
public function minifyJS($js): string
public function minifyHTML($html): string
public function combineCSS($cssFiles): string
public function combineJS($jsFiles): string
public function getMinificationStats(): array
```

**Minification Rules**:
- Remove comments, whitespace, unnecessary characters
- Preserve string literals and regex patterns
- Maintain CSS specificity and cascade
- Preserve JavaScript functionality

### 4. QueryCache Class

**Location**: `/php/performance-optimizer/database/QueryCache.php`

**Responsibilities**:
- Cache database query results
- Support Redis and file-based backends
- Automatic invalidation on data updates
- Track query performance metrics
- Detect and log slow queries (>500ms)

**Key Methods**:
```php
public function get($query, $params = []): ?array
public function set($query, $params, $result, $ttl = 3600): bool
public function invalidate($table): int
public function getStats(): array
public function getSlowQueries($hours = 24): array
```

**Cache Key Format**:
- Format: `query_{hash(query + params)}`
- TTL: Configurable per query type (default 3600s)

**Invalidation Triggers**:
- INSERT/UPDATE/DELETE on tracked tables
- Automatic tag-based invalidation
- Manual invalidation via admin panel

### 5. CachePreloader Class

**Location**: `/php/performance-optimizer/cache/CachePreloader.php`

**Responsibilities**:
- Identify popular pages from configuration
- Generate cache for high-traffic pages
- Run on configurable schedule
- Log preload statistics
- Regenerate expired preloaded cache

**Key Methods**:
```php
public function preload($pages = []): array
public function getPopularPages(): array
public function schedulePreload($schedule = 'daily'): bool
public function getPreloadStats(): array
```

**Popular Pages Configuration**:
- Homepage
- Top 10 destinations
- Category listing pages
- Blog listing page
- Top 20 blog posts

### 6. PerformanceMonitor Class

**Location**: `/php/performance-optimizer/PerformanceMonitor.php`

**Responsibilities**:
- Track page load times
- Calculate bandwidth savings
- Monitor cache hit rates
- Estimate Core Web Vitals
- Generate performance reports
- Log all metrics to database

**Key Methods**:
```php
public function startTimer(): void
public function endTimer($page): void
public function recordCacheHit($page, $size): void
public function recordImageOptimization($original, $optimized): void
public function getMetrics($period = '24h'): array
public function generateReport(): array
```

**Metrics Tracked**:
- Page load time (before/after optimization)
- Cache hit rate and effectiveness
- Image optimization savings (bytes, percentage)
- Query cache performance
- Server response time (TTFB)
- Estimated LCP, FID, CLS

### 7. AdminPanel Integration

**Location**: `/admin/performance-optimizer.php`

**Dashboard Sections**:
1. **Cache Statistics**: Size, hit rate, miss rate, effectiveness
2. **Image Optimization**: Total optimized, space saved, format breakdown
3. **Query Cache**: Hits, misses, average query time
4. **Performance Metrics**: Page load time, TTFB, Core Web Vitals
5. **Slow Queries**: List of queries >500ms in last 24 hours
6. **Controls**: Clear cache, trigger preload, configure settings

**Configuration Options**:
- Cache TTL (seconds)
- Image compression quality (1-100)
- Preload schedule (daily, weekly, on-demand)
- CDN URLs
- Enable/disable features

## Data Models

### Cache Metadata Table

```sql
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
);
```

### Performance Metrics Table

```sql
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
);
```

### Query Cache Table

```sql
CREATE TABLE IF NOT EXISTS query_cache_stats (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    query_hash TEXT UNIQUE NOT NULL,
    query_text TEXT,
    execution_time_ms REAL,
    cache_hits INTEGER DEFAULT 0,
    cache_misses INTEGER DEFAULT 0,
    last_executed DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### Slow Queries Table

```sql
CREATE TABLE IF NOT EXISTS slow_queries (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    query_text TEXT NOT NULL,
    execution_time_ms REAL,
    table_name TEXT,
    detected_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### Image Optimization Stats Table

```sql
CREATE TABLE IF NOT EXISTS image_optimization_stats (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    original_filename TEXT NOT NULL,
    original_size_bytes INTEGER,
    optimized_size_bytes INTEGER,
    webp_size_bytes INTEGER,
    compression_ratio REAL,
    processing_time_ms REAL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

## Configuration

**Location**: `/php/performance-optimizer/config.php`

```php
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
        'backend' => 'file', // 'redis' or 'file'
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
```

## Error Handling

### Image Optimization Failures

- Log error with filename and reason
- Return original image without blocking upload
- Notify admin of optimization failures
- Retry optimization on next preload cycle

### Cache Failures

- Graceful fallback to direct execution
- Log cache operation failures
- Continue serving content without cache
- Alert admin if cache directory is full

### Query Cache Failures

- Fall back to direct database query
- Log cache operation failures
- Continue serving data without cache
- Detect and handle Redis connection failures

### Asset Minification Failures

- Serve original unminified assets
- Log minification errors
- Continue serving content
- Alert admin of minification issues

## Testing Strategy

### Unit Testing

**Cache Operations**:
- Cache key generation from URLs with various query parameters
- Cache storage and retrieval
- Cache expiration and TTL handling
- Cache invalidation by tag
- Cache size limit enforcement

**Image Optimization**:
- Image compression quality verification
- WebP conversion success
- Variant generation with correct dimensions
- Aspect ratio preservation
- Error handling for corrupted images

**Asset Minification**:
- CSS minification preserves functionality
- JavaScript minification preserves logic
- HTML minification preserves structure
- File combining produces valid output
- Source map generation

**Query Caching**:
- Query result caching and retrieval
- Cache invalidation on data updates
- Slow query detection
- Redis and file backend fallback

**Admin Panel**:
- Dashboard displays correct statistics
- Configuration changes apply immediately
- Manual cache clearing works
- Preload triggering works
- Metrics display correctly

### Property-Based Testing

Property-based tests will verify universal correctness properties across randomized inputs using a PHP property-based testing library (e.g., Eris or QuickCheck-style generators).

## Integration Points

### Content Update Hooks

**Blog Updates**:
- Invalidate blog detail page cache
- Invalidate blog listing page cache
- Invalidate homepage cache (if featured blogs shown)

**Listing Updates** (stays, cars, bikes, restaurants, attractions, buses):
- Invalidate listing detail page cache
- Invalidate category listing page cache
- Invalidate homepage cache (if featured listings shown)

**Destination Updates**:
- Invalidate destination page cache
- Invalidate related category pages
- Invalidate homepage cache

**Booking Creation**:
- Invalidate user dashboard cache
- Invalidate booking confirmation page cache

### Admin Panel Integration

- New "Performance Optimizer" section in admin dashboard
- Performance metrics widget on main dashboard
- Cache management controls
- Configuration panel
- Monitoring and reporting section

### Existing Application Integration

- Minimal changes to existing code
- Transparent caching via middleware
- Automatic image optimization on upload
- Query cache integration via database wrapper
- Asset minification via output buffering

## PHP Best Practices Implementation

### Code Style Guidelines

1. **Output**: Use `echo` instead of `print`
   ```php
   echo 'Cache hit';  // ✓
   print 'Cache hit'; // ✗
   ```

2. **String Literals**: Use single quotes when no interpolation
   ```php
   $key = 'cache_' . $id;  // ✓
   $key = "cache_$id";     // ✗ (unless interpolation needed)
   ```

3. **Echo Operator**: Use comma operator instead of concatenation
   ```php
   echo 'Size: ', $size, ' bytes';  // ✓
   echo 'Size: ' . $size . ' bytes'; // ✗
   ```

4. **Loop Increment**: Use pre-increment in loops
   ```php
   for (++$i; $i < 10; ++$i) { }  // ✓
   for ($i++; $i < 10; $i++) { }  // ✗
   ```

5. **Empty Checks**: Use `isset()` for strings
   ```php
   if (isset($str) && $str !== '') { }  // ✓
   if (strlen($str) > 0) { }            // ✗
   ```

6. **Error Suppression**: Avoid @ operator
   ```php
   $result = @file_get_contents($file);  // ✗
   $result = file_get_contents($file);   // ✓ (with proper error handling)
   ```

7. **Array Key Checks**: Use `array_key_exists()` for null values
   ```php
   if (array_key_exists('key', $arr)) { }  // ✓ (when value might be null)
   if (isset($arr['key'])) { }             // ✓ (when value won't be null)
   ```

8. **Loop Optimization**: Cache function results outside loops
   ```php
   $count = count($items);
   for (++$i; $i < $count; ++$i) { }  // ✓
   for (++$i; $i < count($items); ++$i) { }  // ✗
   ```

9. **Variable Scope**: Use local variables instead of globals
   ```php
   $cache = $this->getCache();  // ✓
   global $cache;               // ✗
   ```

10. **Conditional Assignment**: Use ternary and null coalescing
    ```php
    $value = $input ?? 'default';           // ✓
    $value = isset($input) ? $input : 'default';  // ✓
    $value = $input ?: 'default';           // ✓
    ```

11. **Property Caching**: Cache object properties in local variables
    ```php
    $size = $this->cache->size;
    for (++$i; $i < $size; ++$i) { }  // ✓
    for (++$i; $i < $this->cache->size; ++$i) { }  // ✗
    ```

12. **Static Caching**: Use static variables for function result caching
    ```php
    static $config = null;
    if ($config === null) {
        $config = loadConfig();
    }
    return $config;  // ✓
    ```

## Directory Structure

```
/php/performance-optimizer/
├── cache/
│   ├── PageCache.php
│   ├── CachePreloader.php
│   └── CacheManager.php
├── image/
│   ├── ImageOptimizer.php
│   └── ImageProcessor.php
├── assets/
│   ├── AssetMinifier.php
│   └── AssetCombiner.php
├── database/
│   ├── QueryCache.php
│   └── QueryCacheBackend.php
├── PerformanceMonitor.php
├── PerformanceOptimizer.php
└── config.php

/admin/
└── performance-optimizer.php

/cache/
├── pages/
├── queries/
└── .htaccess

/images/uploads/
├── original/
├── thumbnail/
├── medium/
├── large/
└── webp/
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Cache Key Generation Consistency

*For any* URL and query parameter combination, generating a cache key twice should produce identical keys, ensuring consistent cache lookups.

**Validates: Requirements 1.1**

### Property 2: Cached Content Retrieval

*For any* cached page, requesting it again should return the exact same HTML content without re-executing application logic, verified by comparing response content and execution time.

**Validates: Requirements 1.2**

### Property 3: Cache Expiration Enforcement

*For any* cache entry with a TTL, after the TTL expires, the cache should be invalid and a fresh request should regenerate the cache.

**Validates: Requirements 1.3**

### Property 4: Tag-Based Cache Invalidation

*For any* set of cache entries tagged with a specific tag, invalidating by that tag should remove all tagged entries while leaving untagged entries intact.

**Validates: Requirements 1.4, 11.5**

### Property 5: Dynamic Pages Exclusion

*For any* page marked as dynamic (login, booking, dashboard, user profile), the cache system should not store or serve cached versions of that page.

**Validates: Requirements 1.5**

### Property 6: Cache Status Header Presence

*For any* HTTP response, the X-Cache-Status header should be present and correctly indicate whether the response was a cache hit or miss.

**Validates: Requirements 1.6**

### Property 7: Cache Directory Size Limit

*For any* cache directory, the total size of all cache files should not exceed the configured maximum (500MB), with oldest entries evicted when limit is approached.

**Validates: Requirements 1.7**

### Property 8: Image Compression Quality

*For any* JPEG or PNG image, after optimization, the file size should be reduced to 70-80% of the original while maintaining visual quality.

**Validates: Requirements 2.1**

### Property 9: WebP Format Generation

*For any* optimized image, a WebP version should be generated alongside the original format, with WebP file size smaller than the original.

**Validates: Requirements 2.2**

### Property 10: Image Variant Generation

*For any* image, optimization should generate exactly three size variants (150x150, 400x400, 800x800) with correct dimensions.

**Validates: Requirements 2.3**

### Property 11: Aspect Ratio Preservation

*For any* image with any aspect ratio, generated square variants should maintain the center portion of the image through center-crop.

**Validates: Requirements 2.4**

### Property 12: Image Optimization Error Handling

*For any* corrupted or invalid image file, optimization should fail gracefully, log the error, and return the original image without blocking the upload process.

**Validates: Requirements 2.7**

### Property 13: Image Directory Structure

*For any* optimized image, files should be stored in the correct subdirectories (/original, /thumbnail, /medium, /large, /webp) with appropriate naming.

**Validates: Requirements 2.8**

### Property 14: Lazy Loading Attribute Injection

*For any* HTML content with img tags, the lazy loading system should add the loading="lazy" attribute to all img tags in travel listings and blog content.

**Validates: Requirements 3.1**

### Property 15: Above-the-Fold Image Exclusion

*For any* HTML content, images identified as above-the-fold should not have the lazy loading attribute applied.

**Validates: Requirements 3.4**

### Property 16: Lazy Loading Placeholder Generation

*For any* lazy-loaded image, a placeholder or low-quality preview should be generated and served initially.

**Validates: Requirements 3.5**

### Property 17: CSS Minification

*For any* CSS file, minification should remove all whitespace and comments while preserving CSS functionality and specificity.

**Validates: Requirements 4.1**

### Property 18: JavaScript Minification

*For any* JavaScript file, minification should remove all whitespace and comments while preserving JavaScript functionality.

**Validates: Requirements 4.2**

### Property 19: HTML Minification

*For any* HTML content, minification should remove whitespace and comments while preserving HTML structure and functionality.

**Validates: Requirements 4.3**

### Property 20: CSS File Combining

*For any* set of multiple CSS files, combining should produce a single minified CSS file that includes all styles from the original files.

**Validates: Requirements 4.4**

### Property 21: JavaScript File Combining

*For any* set of multiple JavaScript files, combining should produce a single minified JavaScript file that includes all code from the original files.

**Validates: Requirements 4.5**

### Property 22: JavaScript Defer Attribute

*For any* combined JavaScript file, the resulting script tag should have the defer attribute to ensure proper execution order.

**Validates: Requirements 4.6**

### Property 23: Query Result Caching

*For any* database query on Travel_Data, executing it twice should return identical results, with the second execution using cached results.

**Validates: Requirements 5.1, 5.2**

### Property 24: Query Cache Invalidation on Update

*For any* cached query result, updating the underlying data should invalidate the cache, causing the next query to fetch fresh data.

**Validates: Requirements 5.3**

### Property 25: Slow Query Detection

*For any* database query with execution time exceeding 500ms, the query should be logged for administrator review.

**Validates: Requirements 5.8**

### Property 26: Cache Preloader Page Identification

*For any* preload run, the system should identify and preload cache for configured popular pages (homepage, top destinations, category pages).

**Validates: Requirements 6.1, 6.2, 6.3**

### Property 27: Cache Preload Statistics Logging

*For any* completed cache preload operation, the number of pages preloaded and total cache size generated should be logged.

**Validates: Requirements 6.5**

### Property 28: Preloaded Cache Regeneration

*For any* preloaded cache entry that expires, the cache preloader should automatically regenerate it on the next preload cycle.

**Validates: Requirements 6.6**

### Property 29: CDN URL Rewriting

*For any* configured CDN URL, asset URLs in HTML should be rewritten to point to the CDN domain instead of the origin server.

**Validates: Requirements 7.2**

### Property 30: Cache-Busting Query Parameters

*For any* asset URL, a cache-busting query parameter (version hash) should be appended to enable long-term caching with version control.

**Validates: Requirements 7.3**

### Property 31: Cache-Control Headers

*For any* served asset, appropriate Cache-Control headers should be present to enable long-term caching (max-age=31536000 or similar).

**Validates: Requirements 7.4**

### Property 32: Server Response Time Measurement

*For any* HTTP request, the server response time (TTFB) should be measured from request start to first byte sent and logged.

**Validates: Requirements 9.1**

### Property 33: Slow Request Detection

*For any* request with response time exceeding 200ms, the request should be logged for analysis.

**Validates: Requirements 9.2**

### Property 34: Blog Cache Invalidation

*For any* blog post creation or update, cache for the blog detail page and blog listing pages should be invalidated.

**Validates: Requirements 11.1**

### Property 35: Listing Cache Invalidation

*For any* listing (hotel, attraction, bike, bus) creation or update, cache for the listing detail page and category listing pages should be invalidated.

**Validates: Requirements 11.2**

### Property 36: Destination Cache Invalidation

*For any* destination update, cache for destination pages and related category pages should be invalidated.

**Validates: Requirements 11.3**

### Property 37: Booking Cache Invalidation

*For any* booking creation, cache for user dashboard and booking confirmation pages should be invalidated.

**Validates: Requirements 11.4**

### Property 38: Page Load Time Tracking

*For any* page request, the page load time should be measured and logged for performance analysis.

**Validates: Requirements 12.1**

### Property 39: Bandwidth Savings Calculation

*For any* optimized image, the bandwidth savings should be calculated as the difference between original and optimized file sizes.

**Validates: Requirements 12.2**

### Property 40: Cache Hit Rate Calculation

*For any* cache operation, the hit rate should be calculated as the ratio of cache hits to total cache requests.

**Validates: Requirements 12.3**

### Property 41: Performance Report Generation

*For any* daily performance report, it should include optimization impact metrics (cache hits, image savings, query performance).

**Validates: Requirements 12.4**

### Property 42: Performance Metrics JSON Output

*For any* metrics request, the response should be valid JSON containing performance data for integration with analytics tools.

**Validates: Requirements 12.5**

### Property 43: Core Web Vitals Estimation

*For any* set of optimization metrics, Core Web Vitals estimates (LCP, FID, CLS) should be calculated based on measured performance data.

**Validates: Requirements 12.6**

## Error Handling

### Image Optimization Failures

- Log error with filename and reason
- Return original image without blocking upload
- Notify admin of optimization failures
- Retry optimization on next preload cycle

### Cache Failures

- Graceful fallback to direct execution
- Log cache operation failures
- Continue serving content without cache
- Alert admin if cache directory is full

### Query Cache Failures

- Fall back to direct database query
- Log cache operation failures
- Continue serving data without cache
- Detect and handle Redis connection failures

### Asset Minification Failures

- Serve original unminified assets
- Log minification errors
- Continue serving content
- Alert admin of minification issues

## Testing Strategy

### Unit Testing Approach

**Cache Operations**:
- Cache key generation from URLs with various query parameters
- Cache storage and retrieval with different content types
- Cache expiration and TTL handling with edge cases
- Cache invalidation by tag with multiple tags
- Cache size limit enforcement and eviction

**Image Optimization**:
- Image compression quality verification (70-80% range)
- WebP conversion success and file size comparison
- Variant generation with exact dimension verification
- Aspect ratio preservation with various input ratios
- Error handling for corrupted and invalid images
- Directory structure creation and file organization

**Asset Minification**:
- CSS minification preserves functionality and specificity
- JavaScript minification preserves logic and variable scope
- HTML minification preserves structure and attributes
- File combining produces valid and functional output
- Source map generation in development mode
- Defer/async attribute injection correctness

**Query Caching**:
- Query result caching and retrieval accuracy
- Cache invalidation on data updates
- Slow query detection (>500ms threshold)
- Redis and file backend fallback behavior
- Query metrics logging and statistics

**Admin Panel**:
- Dashboard displays correct statistics and metrics
- Configuration changes apply immediately
- Manual cache clearing functionality
- Preload triggering and execution
- Metrics display accuracy and formatting

### Property-Based Testing Approach

Property-based tests will verify universal correctness properties across randomized inputs. Each property test will:

1. Generate random inputs (URLs, images, CSS/JS files, queries)
2. Execute the operation being tested
3. Verify the property holds for all generated inputs
4. Run minimum 100 iterations per property test

**Property Test Configuration**:
- Minimum 100 iterations per property test
- Each test references its design document property
- Tag format: **Feature: performance-optimizer, Property {number}: {property_text}**
- Tests cover both success and failure paths

**Example Property Tests**:

- **Cache Key Consistency**: Generate random URLs and query parameters, verify cache keys are identical across multiple generations
- **Image Compression**: Generate random images, verify compressed size is 70-80% of original
- **CSS Minification**: Generate random CSS, verify minified version is smaller and functionally equivalent
- **Query Caching**: Generate random queries, verify cached results match fresh queries
- **Cache Invalidation**: Generate random cache entries with tags, verify tag-based invalidation works correctly

### Integration Testing

- End-to-end page caching workflow
- Image optimization with upload and retrieval
- Asset minification in page generation
- Query caching with database updates
- Cache preloading and regeneration
- Admin panel functionality and controls

### Performance Testing

- Measure page load time improvements
- Verify cache hit rates under load
- Monitor memory usage with large caches
- Test cache directory size limit enforcement
- Verify query cache performance gains

