<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$method = $_SERVER['REQUEST_METHOD'];
$path   = trim($_GET['action'] ?? '', '/');

try {
    $db = getDB();

    /**
     * Simple file-based rate limiter
     */
    function rateLimit($key, $limit = 5, $period = 60) {
        $dir = __DIR__ . '/../../logs/rate_limit';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        
        $file = $dir . '/' . md5($key) . '.json';
        $now = time();
        $data = ['count' => 0, 'first_attempt' => $now];
        
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            if ($now - $data['first_attempt'] > $period) {
                // Period expired, reset
                $data = ['count' => 1, 'first_attempt' => $now];
            } else {
                $data['count']++;
            }
        } else {
            $data['count'] = 1;
        }
        
        file_put_contents($file, json_encode($data));
        
        if ($data['count'] > $limit) {
            return false; // Rate limit exceeded
        }
        return true;
    }

    /**
     * Helper to verify Cloudflare Turnstile token
     */
    function verifyTurnstile($token) {
        if (empty($token)) return false;
        
        $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
        $data = [
            'secret'   => TURNSTILE_SECRET_KEY,
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) return false;

        $response = json_decode($result, true);
        return !empty($response['success']);
    }

    // POST /api/auth.php?action=login
    if ($method === 'POST' && $path === 'login') {
        $input = getJsonInput();
        $email = strtolower(trim($input['email'] ?? ''));
        $pass  = $input['password'] ?? '';
        $turnstile = $input['turnstileResponse'] ?? '';

        if (!rateLimit('login_' . $_SERVER['REMOTE_ADDR'], 10, 60)) sendError('Too many attempts. Please try again in a minute.', 429);

        if (!$email || !$pass) sendError('Email and password required', 400);
        // if (!verifyTurnstile($turnstile)) sendError('Security check failed (CAPTCHA)', 400);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) sendError('Invalid email', 400);

        $user = $db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
        if (!$user || !password_verify($pass, $user['password_hash'])) {
            sendError('Invalid credentials', 401);
        }

        $token = createJWT([
            'id'    => $user['id'],
            'email' => $user['email'],
            'name'  => $user['name'],
            'role'  => $user['role'],
        ], JWT_SECRET);

        sendJson([
            'token' => $token,
            'user'  => [
                'id'    => $user['id'],
                'email' => $user['email'],
                'name'  => $user['name'],
                'phone' => $user['phone'],
                'role'  => $user['role'],
            ]
        ]);
    }

    // POST /api/auth.php?action=register
    elseif ($method === 'POST' && $path === 'register') {
        $input = getJsonInput();
        $email = strtolower(trim($input['email'] ?? ''));
        $pass  = $input['password'] ?? '';
        $name  = sanitize($input['name'] ?? '');
        $phone = sanitize($input['phone'] ?? '');
        $turnstile = $input['turnstileResponse'] ?? '';

        if (!rateLimit('register_' . $_SERVER['REMOTE_ADDR'], 5, 3600)) sendError('Too many registration attempts. Please try again later.', 429);

        if (!$email || !$pass || !$name) sendError('Name, email and password required', 400);
        // if (!verifyTurnstile($turnstile)) sendError('Security check failed (CAPTCHA)', 400);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) sendError('Invalid email', 400);
        if (strlen($pass) < 8 || !preg_match('/[0-9]/', $pass)) {
            sendError('Password must be at least 8 characters and contain at least one number', 400);
        }

        $exists = $db->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
        if ($exists) sendError('Email already registered', 409);

        $id = $db->insert('users', [
            'email'         => $email,
            'password_hash' => password_hash($pass, PASSWORD_DEFAULT),
            'name'          => $name,
            'phone'         => $phone,
            'role'          => 'user',
        ]);

        $token = createJWT(['id'=>$id,'email'=>$email,'name'=>$name,'role'=>'user'], JWT_SECRET);
        sendJson(['token'=>$token,'user'=>['id'=>$id,'email'=>$email,'name'=>$name,'phone'=>$phone,'role'=>'user']], 201);
    }

    // GET /api/auth.php?action=verify
    elseif ($method === 'GET' && $path === 'verify') {
        $payload = verifyToken();
        sendJson(['valid' => true, 'user' => $payload]);
    }

    // POST /api/auth.php?action=change_password  (admin only)
    elseif ($method === 'POST' && $path === 'change_password') {
        requireAdmin();
        $input   = getJsonInput();
        $userId  = (int)($input['user_id'] ?? 0);
        $newPass = $input['new_password'] ?? '';

        if (!$userId || strlen($newPass) < 8 || !preg_match('/[0-9]/', $newPass)) {
            sendError('user_id required, and password must be min 8 chars with a number', 400);
        }

        $exists = $db->fetchOne("SELECT id FROM users WHERE id = ?", [$userId]);
        if (!$exists) sendError('User not found', 404);

        $db->update('users', ['password_hash' => password_hash($newPass, PASSWORD_DEFAULT)], 'id = :id', [':id' => $userId]);
        sendJson(['success' => true]);
    }

    else {
        sendError('Not found', 404);
    }

} catch (Exception $e) {
    error_log('Auth error: ' . $e->getMessage());
    sendError('Server error', 500);
}
