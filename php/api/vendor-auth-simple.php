<?php
// Ultra-simple vendor auth - uses existing database connection
header('Content-Type: application/json');

// Disable all error display
error_reporting(0);
ini_set('display_errors', 0);

// Start fresh
ob_clean();

// Load config and database
require_once __DIR__ . '/../config.php';

// Get input
$input = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $_GET['action'] ?? '';

// Simple response
if ($action === 'login') {
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';
    
    if (!$username || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'Username and password required']);
        exit;
    }
    
    // Try to connect to database
    try {
        $pdo = getDB()->getConnection();
        
        // Get vendor
        $stmt = $pdo->prepare("SELECT * FROM vendors WHERE username = ?");
        $stmt->execute([$username]);
        $vendor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$vendor) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid username or password']);
            exit;
        }
        
        if ($vendor['status'] !== 'active') {
            http_response_code(403);
            echo json_encode(['error' => 'Account inactive']);
            exit;
        }
        
        if (!password_verify($password, $vendor['password_hash'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid username or password']);
            exit;
        }
        
        // Generate simple token (not JWT, just base64)
        $token = base64_encode(json_encode([
            'vendor_id' => $vendor['id'],
            'username' => $vendor['username'],
            'type' => 'vendor',
            'exp' => time() + (7 * 24 * 60 * 60)
        ]));
        
        unset($vendor['password_hash']);
        
        echo json_encode([
            'success' => true,
            'token' => $token,
            'vendor' => $vendor
        ]);
        exit;
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
        exit;
    }
}

if ($action === 'verify') {
    $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $token = str_replace('Bearer ', '', $token);
    
    if (!$token) {
        http_response_code(401);
        echo json_encode(['error' => 'No token']);
        exit;
    }
    
    try {
        $payload = json_decode(base64_decode($token), true);
        if (!$payload || $payload['exp'] < time()) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid token']);
            exit;
        }
        
        echo json_encode(['success' => true, 'vendor' => $payload]);
        exit;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Token error']);
        exit;
    }
}

http_response_code(400);
echo json_encode(['error' => 'Invalid action']);
