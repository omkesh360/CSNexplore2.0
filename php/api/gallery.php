<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

requireAdmin();

$uploadDir = __DIR__ . '/../../images/uploads/';
$baseUrl   = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
           . '://' . $_SERVER['HTTP_HOST'] . '/images/uploads/';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!is_dir($uploadDir)) { sendJson([]); }
    $files = [];
    foreach (glob($uploadDir . '*.{jpg,jpeg,png,webp,gif}', GLOB_BRACE) as $f) {
        $name = basename($f);
        $files[] = [
            'filename' => $name,
            'url'      => $baseUrl . $name,
            'size'     => filesize($f),
            'modified' => filemtime($f),
        ];
    }
    // Sort newest first
    usort($files, fn($a,$b) => $b['modified'] - $a['modified']);
    sendJson($files);
}

elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = getJsonInput();
    $filename = basename($data['filename'] ?? '');
    if (!$filename) sendError('No filename', 400);
    $path = $uploadDir . $filename;
    if (!file_exists($path)) sendError('File not found', 404);
    unlink($path);
    sendJson(['success' => true]);
}

else {
    sendError('Method not allowed', 405);
}
