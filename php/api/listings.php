<?php
// Get listings by category
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$category = sanitize($_GET['category'] ?? 'stays');
$valid_categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];

if (!in_array($category, $valid_categories)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid category']);
    exit;
}

try {
    $db = getDB();
    $listings = $db->fetchAll(
        "SELECT id, name, operator, location, image, rating, map_embed, price_per_night, price_per_day, price_per_person, entry_fee, price FROM $category ORDER BY id DESC"
    );
    
    echo json_encode([
        'success' => true,
        'listings' => $listings
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
