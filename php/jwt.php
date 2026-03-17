<?php
function b64ue($d) { return rtrim(strtr(base64_encode($d), '+/', '-_'), '='); }
function b64ud($d) { return base64_decode(strtr($d, '-_', '+/')); }

function createJWT($payload, $secret, $exp = 28800) {
    $header  = b64ue(json_encode(['typ'=>'JWT','alg'=>'HS256']));
    $payload['iat'] = time();
    $payload['exp'] = time() + $exp;
    $body    = b64ue(json_encode($payload));
    $sig     = b64ue(hash_hmac('sha256', "$header.$body", $secret, true));
    return "$header.$body.$sig";
}

function verifyJWT($jwt, $secret) {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) return false;
    [$h, $b, $s] = $parts;
    $expected = b64ue(hash_hmac('sha256', "$h.$b", $secret, true));
    if (!hash_equals($expected, $s)) return false;
    $payload = json_decode(b64ud($b), true);
    if (!$payload || $payload['exp'] < time()) return false;
    return $payload;
}

function getAuthToken() {
    $headers = getallheaders();
    $auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    return str_replace('Bearer ', '', $auth) ?: null;
}

function verifyToken() {
    $token = getAuthToken();
    if (!$token) sendError('Unauthorized', 401);
    $payload = verifyJWT($token, JWT_SECRET);
    if (!$payload) sendError('Invalid or expired token', 401);
    return $payload;
}

function isAdmin($user) {
    return isset($user['role']) && strtolower($user['role']) === 'admin';
}

function requireAdmin() {
    $user = verifyToken();
    if (!isAdmin($user)) sendError('Admin access required', 403);
    return $user;
}
