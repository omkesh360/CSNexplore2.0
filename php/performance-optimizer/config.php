<?php

/**
 * Performance Optimizer Configuration
 * 
 * Central configuration file for all performance optimization features
 * including caching, image optimization, asset minification, and query caching.
 */

return array (
  'cache' => 
  array (
    'enabled' => true,
    'ttl' => 3600,
    'max_size_mb' => 2000,
    'directory' => '/Users/omkesh360/Documents/GitHub/CSNexplore2.0/php/performance-optimizer/../../cache',
  ),
  'image' => 
  array (
    'enabled' => true,
    'quality' => 75,
    'enable_webp' => true,
    'variants' => 
    array (
      'thumbnail' => 
      array (
        'width' => 150,
        'height' => 150,
      ),
      'medium' => 
      array (
        'width' => 400,
        'height' => 400,
      ),
      'large' => 
      array (
        'width' => 800,
        'height' => 800,
      ),
    ),
  ),
  'assets' => 
  array (
    'enabled' => true,
    'minify_css' => true,
    'minify_js' => true,
    'minify_html' => true,
    'combine_css' => true,
    'combine_js' => true,
  ),
  'query_cache' => 
  array (
    'enabled' => true,
    'ttl' => 3600,
    'backend' => 'file',
    'redis_host' => 'localhost',
    'redis_port' => 6379,
  ),
  'preloader' => 
  array (
    'enabled' => true,
    'schedule' => 'daily',
    'pages' => 
    array (
      0 => '/',
      1 => '/blogs',
      2 => '/attractions',
      3 => '/stays',
      4 => '/cars',
      5 => '/bikes',
    ),
  ),
  'cdn' => 
  array (
    'enabled' => false,
    'url' => '',
    'cache_control' => 'public, max-age=31536000',
  ),
  'monitoring' => 
  array (
    'enabled' => true,
    'track_metrics' => true,
    'slow_query_threshold_ms' => 500,
  ),
);
