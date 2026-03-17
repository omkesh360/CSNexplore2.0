<?php
// CSNExplore – Central config
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

define('JWT_SECRET', getenv('JWT_SECRET') ?: 'csnexplore_secure_jwt_2025_!@#$%');
define('ADMIN_EMAIL', 'travelhubadmin@gmail.com');

require_once __DIR__ . '/database.php';

// ── Helpers ──────────────────────────────────────────────────────────────────
function sendJson($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function sendError($msg, $code = 400) {
    sendJson(['error' => $msg], $code);
}

function getJsonInput() {
    $raw = file_get_contents('php://input');
    return json_decode($raw, true) ?? [];
}

function getDB() {
    return Database::getInstance();
}

function sanitize($val) {
    return htmlspecialchars(strip_tags(trim((string)$val)), ENT_QUOTES, 'UTF-8');
}
