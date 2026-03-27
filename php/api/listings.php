<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$method   = $_SERVER['REQUEST_METHOD'];
$category = sanitize($_GET['category'] ?? '');
$id       = isset($_GET['id']) ? (int)$_GET['id'] : null;
$action   = $_GET['action'] ?? '';

$valid = ['stays','cars','bikes','restaurants','attractions','buses'];
if (!in_array($category, $valid)) sendError('Invalid category', 400);

// Price column per category
$priceCol = [
    'stays'       => 'price_per_night',
    'cars'        => 'price_per_day',
    'bikes'       => 'price_per_day',
    'restaurants' => 'price_per_person',
    'attractions' => 'entry_fee',
    'buses'       => 'price',
];
$pc = $priceCol[$category];

try {
    $db = getDB();

    // Check if admin
    $isAdmin = false;
    $tok = getAuthToken();
    if ($tok) {
        $p = verifyJWT($tok, JWT_SECRET);
        if ($p && strtolower($p['role'] ?? '') === 'admin') $isAdmin = true;
    }

    // GET list
    if ($method === 'GET' && !$id) {
        $where  = $isAdmin ? [] : ['is_active = 1'];
        $params = [];

        if (!empty($_GET['search'])) {
            $where[] = 'name LIKE ?';
            $params[] = '%' . $_GET['search'] . '%';
        }
        if (!empty($_GET['type'])) {
            $where[] = 'type = ?';
            $params[] = $_GET['type'];
        }

        $sql = "SELECT * FROM $category";
        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY display_order ASC, id DESC';

        $items = $db->fetchAll($sql, $params);
        foreach ($items as &$item) decodeJson($item);
        sendJson($items);
    }

    // GET single
    elseif ($method === 'GET' && $id) {
        $item = $db->fetchOne("SELECT * FROM $category WHERE id = ?", [$id]);
        if (!$item) sendError('Not found', 404);
        decodeJson($item);
        sendJson($item);
    }

    // POST create (admin)
    elseif ($method === 'POST' && $action !== 'reorder') {
        requireAdmin();
        $data = getJsonInput();
        $row  = buildRow($data, $category, $pc);
        $newId = $db->insert($category, $row);
        $item  = $db->fetchOne("SELECT * FROM $category WHERE id = ?", [$newId]);
        decodeJson($item);
        sendJson($item, 201);
    }

    // POST reorder (admin)
    elseif ($method === 'POST' && $action === 'reorder') {
        requireAdmin();
        $data = getJsonInput();
        $updates = $data['updates'] ?? [];
        foreach ($updates as $update) {
            $rid = (int)($update['id'] ?? 0);
            $order = (int)($update['display_order'] ?? 0);
            if ($rid > 0) {
                $db->update($category, ['display_order' => $order], 'id = :id', [':id' => $rid]);
            }
        }
        sendJson(['success' => true]);
    }

    // PUT update (admin)
    elseif ($method === 'PUT' && $id) {
        requireAdmin();
        $data = getJsonInput();
        $row  = buildRow($data, $category, $pc, true);
        $row['updated_at'] = date('Y-m-d H:i:s');
        $db->update($category, $row, 'id = :id', [':id' => $id]);
        $item = $db->fetchOne("SELECT * FROM $category WHERE id = ?", [$id]);
        decodeJson($item);
        sendJson($item);
    }

    // DELETE (admin) – soft delete
    elseif ($method === 'DELETE' && $id) {
        requireAdmin();
        $db->update($category, ['is_active' => 0, 'updated_at' => date('Y-m-d H:i:s')], 'id = :id', [':id' => $id]);
        sendJson(['success' => true]);
    }

    else {
        sendError('Not found', 404);
    }

} catch (Exception $e) {
    error_log('Listings error: ' . $e->getMessage());
    sendError('Server error', 500);
}

function buildRow($data, $category, $pc, $partial = false) {
    $row = [];
    $fields = [
        'name','type','location','description','badge','image','gallery',
        'rating','reviews','is_active','display_order',
    ];
    // Category-specific
    if ($category === 'stays')       $fields = array_merge($fields, ['amenities','room_type','max_guests']);
    if ($category === 'cars')        $fields = array_merge($fields, ['features','fuel_type','transmission','seats']);
    if ($category === 'bikes')       $fields = array_merge($fields, ['features','fuel_type','cc']);
    if ($category === 'restaurants') $fields = array_merge($fields, ['cuisine','menu_highlights']);
    if ($category === 'attractions') $fields = array_merge($fields, ['opening_hours','best_time']);
    if ($category === 'buses')       $fields = array_merge($fields, ['operator','bus_type','from_location','to_location','departure_time','arrival_time','duration','amenities','seats_available']);

    $fields[] = $pc;

    foreach ($fields as $f) {
        if (!array_key_exists($f, $data)) { if ($partial) continue; else $row[$f] = null; continue; }
        $v = $data[$f];
        if (in_array($f, ['gallery','amenities','features','menu_highlights'])) {
            $row[$f] = is_array($v) ? json_encode($v) : (string)$v;
        } elseif (in_array($f, ['rating','reviews','is_active','display_order','max_guests','seats','seats_available'])) {
            $row[$f] = is_numeric($v) ? (float)$v : 0;
        } elseif ($f === $pc) {
            $row[$f] = is_numeric($v) ? (float)$v : 0;
        } else {
            $row[$f] = sanitize((string)$v);
        }
    }
    return $row;
}

function decodeJson(&$item) {
    foreach (['gallery','amenities','features','menu_highlights'] as $f) {
        if (isset($item[$f]) && is_string($item[$f])) {
            $item[$f] = json_decode($item[$f], true) ?: [];
        }
    }
}
