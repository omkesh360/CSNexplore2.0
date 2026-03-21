<?php
// get_related.php - Fetch related listings
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config.php';

try {
    $db = getDB();
    
    $type = $_GET['type'] ?? '';
    $exclude = intval($_GET['exclude'] ?? 0);
    $limit = intval($_GET['limit'] ?? 4);
    
    // Validate type
    $valid_types = ['stays', 'cars', 'bikes', 'attractions', 'restaurants', 'buses'];
    if (!in_array($type, $valid_types)) {
        throw new Exception('Invalid type');
    }
    
    // Build query
    $sql = "SELECT id, name, image_url, rating, price 
            FROM {$type} 
            WHERE is_active = 1 AND id != :exclude 
            ORDER BY rating DESC, display_order ASC 
            LIMIT :limit";
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':exclude', $exclude, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'listings' => $listings
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
