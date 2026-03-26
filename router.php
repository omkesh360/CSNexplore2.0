<?php
// router.php - Handles clean URLs for PHP built-in web server
// Usage: php -S localhost:8000 router.php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = ltrim($path, '/');

// If requesting root, load index.php
if ($path === '' || $path === 'index') {
    include 'index.php';
    return;
}

// Check if the exact PHP file exists for the clean URL
if (file_exists($path . '.php')) {
    include $path . '.php';
    return;
}

// Support for clean /listing/type URLs
if (preg_match('#^listing/([a-zA-Z-]+)$#', $path, $matches)) {
    $_GET['type'] = $matches[1];
    include 'listing.php';
    return;
}

// ── Static HTML pages: /blogs/1-my-slug → blogs/1-my-slug.html ──────────
if (file_exists($path . '.html')) {
    // Serve HTML file directly with correct content type
    header('Content-Type: text/html');
    readfile($path . '.html');
    return;
}

// If it's a real file or directory (and it's not handled above), let the built-in server handle it (like images, css, js)
if (file_exists($path)) {
    return false;
}

// Custom manual fallback if something didn't match (for 404 handling like Apache ErrorDocument)
http_response_code(404);
include 'index.php';
return;
