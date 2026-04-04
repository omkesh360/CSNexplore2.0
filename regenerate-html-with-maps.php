<?php
// Regenerate all HTML listing files with maps and similar listings
require_once 'php/config.php';

$db = getDB();

// Default map iframe
$default_map = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d16426.329596104577!2d75.30037121099188!3d19.851617685624074!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bdb98f39ca15447%3A0x96c2632e2aaa42c!2sKamalnayan%20Bajaj%20Hospital!5e0!3m2!1sen!2sin!4v1775290483319!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';

// Map section HTML to insert
$map_section_html = '
    <!-- Location Map Section -->
    <div class="border-t border-slate-200 bg-slate-50 py-14">
      <div class="max-w-6xl mx-auto px-4">
        <h3 class="text-2xl font-serif font-black text-slate-900 mb-8 flex items-center gap-3">
          <span class="w-8 h-1 bg-[#ec5b13] rounded-full inline-block"></span>
          Location Map
        </h3>
        <div class="rounded-2xl overflow-hidden border border-slate-200 shadow-lg" style="height: 450px;">
          ' . $default_map . '
        </div>
      </div>
    </div>
';

$categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];
$total_updated = 0;

echo "<!DOCTYPE html><html><head><title>Regenerating HTML Files</title><style>body{font-family:Arial;padding:20px;max-width:800px;margin:0 auto}.success{color:green}.error{color:red}.info{color:blue}</style></head><body>";
echo "<h1>Regenerating HTML Files with Maps</h1><hr>";

foreach ($categories as $category) {
    echo "<h2 class='info'>Processing: " . ucfirst($category) . "</h2>";
    
    // Get all listings from database
    $listings = $db->fetchAll("SELECT id, name FROM $category WHERE is_active = 1", []);
    
    foreach ($listings as $listing) {
        $id = $listing['id'];
        $name = $listing['name'] ?? $listing['operator'] ?? 'Listing';
        
        // Generate slug
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        $slug = substr($slug, 0, 60);
        
        $filename = "listing-detail/{$category}-{$id}-{$slug}.html";
        
        if (file_exists($filename)) {
            // Read the file
            $content = file_get_contents($filename);
            
            // Check if map section already exists
            if (strpos($content, 'Location Map') === false) {
                // Find the position to insert (before the related listings section)
                $insert_before = '<div id="related-listings-section"';
                $pos = strpos($content, $insert_before);
                
                if ($pos !== false) {
                    // Insert the map section
                    $new_content = substr($content, 0, $pos) . $map_section_html . substr($content, $pos);
                    
                    // Write back to file
                    file_put_contents($filename, $new_content);
                    
                    echo "<p class='success'>✓ Updated: $filename</p>";
                    $total_updated++;
                } else {
                    echo "<p class='error'>✗ Could not find insertion point in: $filename</p>";
                }
            } else {
                echo "<p>• Already has map: $filename</p>";
            }
        } else {
            echo "<p class='error'>✗ File not found: $filename</p>";
        }
    }
    
    echo "<hr>";
}

echo "<h2 class='success'>✅ Complete!</h2>";
echo "<p><strong>Total files updated: $total_updated</strong></p>";
echo "<p><a href='listing-detail/stays-7-city-center-business-hotel.html'>→ Test: View a listing page</a></p>";
echo "</body></html>";
?>
