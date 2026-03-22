<?php
// CSNExplore – Central config
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
$_logDir = __DIR__ . '/../logs';
if (!is_dir($_logDir)) @mkdir($_logDir, 0755, true);
ini_set('error_log', $_logDir . '/php_errors.log');

// Load .env file if exists
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

define('JWT_SECRET', getenv('JWT_SECRET') ?: 'csnexplore_secure_jwt_2025_!@#$%');
define('ADMIN_EMAIL', 'travelhubadmin@gmail.com');

// MailerLite Email Configuration
define('MAILERLITE_API_KEY', getenv('MAILERLITE_API_KEY') ?: '');
define('MAILERLITE_FROM_EMAIL', getenv('MAILERLITE_FROM_EMAIL') ?: 'noreply@csnexplore.com');
define('MAILERLITE_FROM_NAME', getenv('MAILERLITE_FROM_NAME') ?: 'CSN Explore');
define('ADMIN_NOTIFICATION_EMAIL', getenv('ADMIN_NOTIFICATION_EMAIL') ?: 'supportcsnexplore@gmail.com');

// SMTP Configuration for PHPMailer
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USERNAME', getenv('SMTP_USERNAME') ?: '');
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') ?: '');
define('SMTP_ENCRYPTION', getenv('SMTP_ENCRYPTION') ?: 'tls');

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
