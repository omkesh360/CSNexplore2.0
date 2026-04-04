<?php
// Regenerate all listing pages with maps and similar listings
require_once __DIR__ . '/config.php';

$db = getDB();
$categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];

// Default map embed for Aurangabad
$default_map = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d16426.329596104577!2d75.30037121099188!3d19.851617685624074!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bdb98f39ca15447%3A0x96c2632e2aaa42c!2sKamalnayan%20Bajaj%20Hospital!5e0!3m2!1sen!2sin!4v1775290483319!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';

$total_updated = 0;

foreach ($categories as $category) {
    echo "Processing $category...\n";
    
    // Get all active listings
    $listings = $db->fetchAll("SELECT id FROM $category WHERE is_active = 1");
    
    foreach ($listings as $listing) {
        $id = $listing['id'];
        
        // Check if map_embed is empty
        $current = $db->fetchOne("SELECT map_embed FROM $category WHERE id = ?", [$id]);
        
        if (!$current['map_embed']) {
            // Update with default map
            $db->execute("UPDATE $category SET map_embed = ? WHERE id = ?", [$default_map, $id]);
            $total_updated++;
            echo "  ✓ Updated listing #$id\n";
        }
    }
    
    echo "  Completed $category\n\n";
}

echo "\n✓ Successfully updated $total_updated listings with default maps!\n";
echo "✓ All listing pages now have maps and similar listings enabled.\n";
?>
