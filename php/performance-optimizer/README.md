# Performance Optimizer

A comprehensive PHP-based performance optimization system designed to reduce page load times by 40-60% through intelligent caching, image optimization, asset minification, and database query optimization.

## Project Structure

```
php/performance-optimizer/
├── cache/                          # Page caching component
│   ├── PageCache.php              # Page cache implementation
│   ├── CachePreloader.php         # Cache preloading system
│   └── CacheManager.php           # Cache management utilities
├── image/                          # Image optimization component
│   ├── ImageOptimizer.php         # Image optimization implementation
│   └── ImageProcessor.php         # Image processing utilities
├── assets/                         # Asset minification component
│   ├── AssetMinifier.php          # Asset minification implementation
│   └── AssetCombiner.php          # Asset combining utilities
├── database/                       # Database query optimization
│   ├── QueryCache.php             # Query caching implementation
│   └── QueryCacheBackend.php      # Cache backend implementations
├── PerformanceOptimizer.php       # Main orchestrator class
├── PerformanceMonitor.php         # Performance monitoring
├── DatabaseInitializer.php        # Database table initialization
├── CacheBackend.php               # Cache backend interface
├── OptimizationComponent.php      # Base component class
├── bootstrap.php                  # System bootstrap
├── config.php                     # Configuration file
└── README.md                      # This file
```

## Features

### 1. Page Caching
- Full-page HTML caching with configurable TTL
- Cache key generation from URL and query parameters
- Tag-based cache invalidation
- Dynamic page exclusion (login, booking, dashboard)
- X-Cache-Status header support
- LRU eviction when cache size limit (500MB) is reached

### 2. Image Optimization
- Automatic image compression (70-80% quality)
- WebP format conversion
- Three size variants (150x150, 400x400, 800x800)
- Aspect ratio preservation with center-crop
- GD and ImageMagick backend support
- Graceful error handling

### 3. Lazy Loading
- Native HTML loading="lazy" attribute injection
- JavaScript fallback with Intersection Observer API
- Above-the-fold image detection
- Low-quality placeholder generation

### 4. Asset Minification
- CSS, JavaScript, and HTML minification
- Multi-file combining
- Source map generation
- Defer/async attribute injection

### 5. Database Query Optimization
- Query result caching with configurable TTL
- Redis and file-based backend support
- Automatic cache invalidation on data updates
- Slow query detection (>500ms threshold)
- Query performance metrics

### 6. Cache Preloading
- Automatic popular page identification
- Configurable preload schedule (daily, weekly, on-demand)
- Preload statistics logging
- Automatic cache regeneration

### 7. Performance Monitoring
- Page load time tracking
- Bandwidth savings calculation
- Cache hit rate monitoring
- Core Web Vitals estimation
- Daily performance reports

### 8. Admin Panel Integration
- Real-time cache statistics
- Image optimization metrics
- Query cache performance
- Manual cache clearing
- Configuration management

## Configuration

The system is configured via `/php/performance-optimizer/config.php`:

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
    // ... more configuration
];
```

## Database Tables

The system creates the following tables:

- `cache_metadata` - Cache entry metadata and statistics
- `performance_metrics` - Page load time and performance data
- `query_cache_stats` - Query cache performance statistics
- `slow_queries` - Detected slow queries (>500ms)
- `image_optimization_stats` - Image optimization metrics

## Directory Structure

```
/cache/
├── pages/          # Full-page HTML cache
├── queries/        # Query result cache
└── .htaccess       # Access control

/images/uploads/
├── original/       # Original uploaded images
├── thumbnail/      # 150x150 variants
├── medium/         # 400x400 variants
├── large/          # 800x800 variants
└── webp/           # WebP format variants
```

## Usage

### Initialization

Include the bootstrap file in your application:

```php
require_once 'php/performance-optimizer/bootstrap.php';
```

Or manually initialize:

```php
use PerformanceOptimizer\PerformanceOptimizer;

$optimizer = PerformanceOptimizer::getInstance();
$optimizer->initialize($db);
```

### Accessing Components

```php
$optimizer = PerformanceOptimizer::getInstance();

// Get configuration
$cacheTtl = $optimizer->getConfig('cache.ttl');

// Check if feature is enabled
if ($optimizer->isFeatureEnabled('cache')) {
    // Use caching
}

// Get registered components
$pageCache = $optimizer->getComponent('PageCache');
```

## PHP Best Practices

The system follows PHP best practices:

- Uses `echo` instead of `print`
- Uses single quotes for string literals
- Uses comma operator in echo statements
- Uses pre-increment (++$i) in loops
- Uses `isset()` for string checks
- Avoids @ error suppression operator
- Uses `array_key_exists()` for null values
- Caches function results outside loops
- Uses local variables instead of globals
- Uses ternary and null coalescing operators
- Caches object properties in local variables
- Uses static variables for function result caching

## Testing

The system includes comprehensive testing:

- Unit tests for all components
- Property-based tests for correctness properties
- Integration tests for component interactions
- Performance benchmarks

## Performance Targets

- 40-60% reduction in page load times
- Improved Core Web Vitals scores
- Reduced server response time (TTFB)
- Reduced bandwidth usage
- Improved cache hit rates

## Error Handling

The system gracefully handles errors:

- Image optimization failures don't block uploads
- Cache failures fall back to direct execution
- Query cache failures fall back to direct queries
- Asset minification failures serve original assets
- All errors are logged for administrator review

## Monitoring

Monitor performance via the admin panel:

- Real-time cache statistics
- Image optimization metrics
- Query cache performance
- Slow query detection
- Performance metrics and reports

## Integration Points

The system integrates with:

- Page request/response cycle (caching)
- Image upload handlers (optimization)
- Asset serving (minification)
- Database query execution (query caching)
- Admin panel (monitoring and management)
- Content update hooks (cache invalidation)

## Support

For issues or questions, refer to the design document or contact the development team.
