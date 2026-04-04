<?php
// Test listing detail page
require_once 'php/config.php';

$db = getDB();

echo "=== Testing Listing Detail Page ===\n\n";

// Get first active listing from each category
$categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];

foreach ($categories as $category) {
    $listing = $db->fetchOne(
        "SELECT id, name, map_embed FROM $category WHERE is_active = 1 LIMIT 1",
        []
    );
    
    if ($listing) {
        echo "Category: $category\n";
        echo "  ID: " . $listing['id'] . "\n";
        echo "  Name: " . $listing['name'] . "\n";
        echo "  Has map_embed: " . (!empty($listing['map_embed']) ? 'YES' : 'NO') . "\n";
        echo "  Test URL: listing-detail.php?category=$category&id=" . $listing['id'] . "\n";
        echo "\n";
    } else {
        echo "Category: $category - NO ACTIVE LISTINGS\n\n";
    }
}

echo "=== Instructions ===\n";
echo "1. Click on any test URL above to view the listing\n";
echo "2. Scroll down to check if maps and similar listings appear\n";
echo "3. If not showing, check the browser console for errors\n";
?>
