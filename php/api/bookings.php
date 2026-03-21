<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    $db = getDB();

    // GET – admin only
    if ($method === 'GET') {
        requireAdmin();
        $where  = ['1=1'];
        $params = [];

        if (!empty($_GET['status'])) {
            $where[] = 'status = ?';
            $params[] = $_GET['status'];
        }
        if (!empty($_GET['search'])) {
            $where[] = '(full_name LIKE ? OR email LIKE ? OR phone LIKE ?)';
            $s = '%' . $_GET['search'] . '%';
            $params = array_merge($params, [$s, $s, $s]);
        }

        $sql = "SELECT * FROM bookings WHERE " . implode(' AND ', $where) . " ORDER BY created_at DESC";
        sendJson($db->fetchAll($sql, $params));
    }

    // POST – public (create booking)
    elseif ($method === 'POST') {
        $data = getJsonInput();
        $name  = sanitize($data['full_name'] ?? '');
        $phone = sanitize($data['phone'] ?? '');
        $email = strtolower(trim($data['email'] ?? ''));

        if (!$name || !$phone) sendError('Name and phone are required', 400);
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) sendError('Invalid email', 400);

        $newId = $db->insert('bookings', [
            'full_name'        => $name,
            'phone'            => $phone,
            'email'            => $email,
            'booking_date'     => sanitize($data['booking_date'] ?? ''),
            'checkin_date'     => sanitize($data['checkin_date'] ?? ''),
            'checkout_date'    => sanitize($data['checkout_date'] ?? ''),
            'number_of_people' => max(1, (int)($data['number_of_people'] ?? 1)),
            'service_type'     => sanitize($data['service_type'] ?? ''),
            'listing_id'       => (int)($data['listing_id'] ?? 0) ?: null,
            'listing_name'     => sanitize($data['listing_name'] ?? ''),
            'notes'            => sanitize($data['notes'] ?? ''),
            'status'           => 'pending',
        ]);

        sendJson(['success' => true, 'id' => $newId], 201);
    }

    // PUT – admin only (update status/notes)
    elseif ($method === 'PUT' && $id) {
        requireAdmin();
        $data   = getJsonInput();
        $update = [];

        if (isset($data['status']) && in_array($data['status'], ['pending','completed','cancelled'])) {
            $update['status'] = $data['status'];
        }
        if (isset($data['notes'])) $update['notes'] = sanitize($data['notes']);
        $update['updated_at'] = date('Y-m-d H:i:s');

        $db->update('bookings', $update, 'id = :id', [':id' => $id]);
        sendJson(['success' => true]);
    }

    // DELETE – admin only
    elseif ($method === 'DELETE' && $id) {
        requireAdmin();
        $db->delete('bookings', 'id = ?', [$id]);
        sendJson(['success' => true]);
    }

    else {
        sendError('Not found', 404);
    }

} catch (Exception $e) {
    error_log('Bookings error: ' . $e->getMessage());
    sendError('Server error', 500);
}
