<!DOCTYPE html>
<html>
<head>
    <title>Regenerate Maps - CSNExplore</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔄 Regenerating Maps for All Listings</h1>
    <hr>
    
    <?php
    require_once 'php/config.php';
    
    echo "<p class='info'>Starting regeneration process...</p>";
    
    $db = getDB();
    $categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];
    
    // Default map embed for Aurangabad
    $default_map = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d16426.329596104577!2d75.30037121099188!3d19.851617685624074!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bdb98f39ca15447%3A0x96c2632e2aaa42c!2sKamalnayan%20Bajaj%20Hospital!5e0!3m2!1sen!2sin!4v1775290483319!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
    
    $total_updated = 0;
    $results = [];
    
    foreach ($categories as $category) {
        echo "<h3>Processing: " . ucfirst($category) . "</h3>";
        
        try {
            // Get all active listings without maps
            $listings = $db->fetchAll(
                "SELECT id, name FROM $category WHERE is_active = 1 AND (map_embed IS NULL OR map_embed = '')",
                []
            );
            
            echo "<p>Found " . count($listings) . " listings without maps</p>";
            
            $count = 0;
            foreach ($listings as $listing) {
                $id = $listing['id'];
                $name = $listing['name'] ?? $listing['operator'] ?? 'Listing';
                
                $db->update(
                    $category,
                    ['map_embed' => $default_map],
                    'id = ?',
                    [$id]
                );
                
                $count++;
                $total_updated++;
                echo "<p class='success'>✓ Updated: #$id - " . htmlspecialchars($name) . "</p>";
            }
            
            $results[$category] = $count;
            echo "<p><strong>Completed: $count listings updated</strong></p><hr>";
            
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p><hr>";
        }
    }
    
    echo "<h2 class='success'>✅ Regeneration Complete!</h2>";
    echo "<p><strong>Total listings updated: $total_updated</strong></p>";
    
    echo "<h3>Summary by Category:</h3>";
    echo "<pre>";
    foreach ($results as $cat => $count) {
        echo ucfirst($cat) . ": $count listings\n";
    }
    echo "</pre>";
    
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Visit any listing page to see the map and similar listings</li>";
    echo "<li>Maps will appear below the amenities section</li>";
    echo "<li>Similar listings will appear at the bottom of the page</li>";
    echo "<li>You can customize individual maps from Admin → Map Embeds</li>";
    echo "</ol>";
    
    echo "<p><a href='test-listing-detail.php' style='color: blue;'>→ Click here to get test URLs for each category</a></p>";
    ?>
</body>
</html>
