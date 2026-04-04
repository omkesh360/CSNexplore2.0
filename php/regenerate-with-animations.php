<?php
/**
 * Regenerate all listing detail HTML pages with animations
 */
require_once __DIR__ . '/config.php';

$db = getDB();
$categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];

$total_generated = 0;
$results = [];

echo "Starting regeneration with animations...\n\n";

foreach ($categories as $category) {
    echo "Processing {$category}...\n";
    
    // Get all active listings
    $listings = $db->fetchAll("SELECT * FROM {$category} WHERE is_active = 1");
    $count = 0;
    
    foreach ($listings as $listing) {
        $html = generateListingDetailHTML($category, $listing, $db);
        
        // Generate slug
        $slug = generateSlug($category, $listing['id'], $listing['name']);
        $filename = __DIR__ . '/../listing-detail/' . $slug . '.html';
        
        // Ensure directory exists
        $dir = dirname($filename);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        // Write file
        file_put_contents($filename, $html);
        $count++;
        $total_generated++;
        
        // Show progress every 10 listings
        if ($count % 10 == 0) {
            echo "  ... {$count} pages generated\n";
        }
    }
    
    $results[$category] = $count;
    echo "  ✓ Generated {$count} {$category} pages\n";
}

echo "\n✅ Successfully generated {$total_generated} listing detail pages with animations!\n";
echo "\nBreakdown:\n";
foreach ($results as $cat => $count) {
    echo "  • {$cat}: {$count} pages\n";
}

/**
 * Generate HTML for a listing detail page with animations
 * Uses ORIGINAL template structure from GitHub + NEW animations
 */
function generateListingDetailHTML($category, $listing, $db) {
    $id = $listing['id'];
    $title = htmlspecialchars($listing['name'] ?? 'Listing');
    $description = htmlspecialchars(substr($listing['description'] ?? '', 0, 160));
    $full_description = htmlspecialchars($listing['description'] ?? '');
    $image = $listing['image'] ?? '';
    $map_embed = $listing['map_embed'] ?? '';
    $location = htmlspecialchars($listing['location'] ?? 'Location not specified');
    $rating = $listing['rating'] ?? 0;
    
    // Determine price column
    $price_columns = [
        'stays' => 'price_per_night',
        'cars' => 'price_per_day',
        'bikes' => 'price_per_day',
        'restaurants' => 'price_per_person',
        'attractions' => 'entry_fee',
        'buses' => 'price',
    ];
    $price_col = $price_columns[$category] ?? 'price';
    $price = $listing[$price_col] ?? 0;
    
    // Get similar listings
    $similar_listings = $db->fetchAll(
        "SELECT * FROM {$category} WHERE id != ? AND is_active = 1 ORDER BY RAND() LIMIT 4",
        [$id]
    );
    
    // Get gallery images (use multiple images if available, otherwise repeat main image)
    $gallery_images = [];
    if ($image) {
        $gallery_images[] = $image;
        // Add more images if available in database
        // For now, we'll use placeholder images
        $placeholder_images = [
            '../images/uploads/ellora_caves.png',
            '../images/uploads/ellora.png',
            '../images/uploads/bibi.png',
            '../images/uploads/daulatabad.png',
            '../images/uploads/grishneshwar.png'
        ];
        foreach ($placeholder_images as $idx => $placeholder) {
            if ($idx < 5) $gallery_images[] = $placeholder;
        }
    }
    
    // Generate slug for canonical URL
    $slug = generateSlug($category, $id, $listing['name']);
    
    ob_start();
    ?>
<!DOCTYPE html>
<html class="light" lang="en" style="scroll-behavior:smooth">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover"/>
<meta name="mobile-web-app-capable" content="yes"/>
<meta name="apple-mobile-web-app-capable" content="yes"/>
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
<meta name="format-detection" content="telephone=no"/>
<title><?php echo $title; ?> | CSNExplore</title>
<meta name="description" content="<?php echo $description; ?>">
<meta name="keywords" content="Chhatrapati Sambhajinagar, Aurangabad, tourism, <?php echo $category; ?>">
<link rel="canonical" href="https://csnexplore.com/listing-detail/<?php echo generateSlug($category, $id, $listing['name']); ?>.html" />

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:url" content="https://csnexplore.com/listing-detail/<?php echo generateSlug($category, $id, $listing['name']); ?>.html">
<meta property="og:title" content="<?php echo $title; ?> | CSNExplore">
<meta property="og:description" content="<?php echo $description; ?>">
<meta property="og:image" content="<?php echo $image; ?>">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="https://csnexplore.com/listing-detail/<?php echo generateSlug($category, $id, $listing['name']); ?>.html">
<meta name="twitter:title" content="<?php echo $title; ?> | CSNExplore">
<meta name="twitter:description" content="<?php echo $description; ?>">
<meta name="twitter:image" content="<?php echo $image; ?>">

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<link rel="stylesheet" href="../mobile-responsive.css"/>
<link rel="stylesheet" href="../animations.css"/>
<script>tailwind.config={{darkMode:"class",theme:{{extend:{{colors:{{"primary":"#ec5b13","whatsapp":"#25D366","background-dark":"#0a0705"}},fontFamily:{{display:["Inter","sans-serif"],serif:["Playfair Display","serif"]}}}}}}})</script>
<style>
body{opacity:0;will-change:opacity;}
body.page-ready{animation:pageFadeIn 0.2s ease forwards;}
@keyframes pageFadeIn{from{opacity:0;}to{opacity:1;}}
.material-symbols-outlined{font-variation-settings:"FILL" 0,"wght" 400,"GRAD" 0,"opsz" 24;font-family:"Material Symbols Outlined";font-style:normal;display:inline-block;line-height:1;}
.glass{background:rgba(255,255,255,0.08);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,0.15);box-shadow:0 8px 32px rgba(0,0,0,0.1);}
.glass-button{background:rgba(236,91,19,0.85);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.2);box-shadow:0 4px 16px rgba(236,91,19,0.3);}
.glass-button:hover{background:rgba(236,91,19,0.95);box-shadow:0 6px 24px rgba(236,91,19,0.4);}
.gallery-thumb{cursor:zoom-in;position:relative;overflow:hidden;border-radius:12px;aspect-ratio:4/3;background:#f1f5f9;border:1px solid #e2e8f0;}
.gallery-thumb img{width:100%;height:100%;object-fit:cover;transition:all 0.4s cubic-bezier(0.4, 0, 0.2, 1);}
.gallery-thumb:hover img{transform:scale(1.08);filter:brightness(1.05);}
.line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
</style>
</head>
<body class="bg-white dark:bg-background-dark font-display text-slate-900 dark:text-slate-100">

<!-- Simple Header for Static Pages -->
<header class="fixed top-0 left-0 right-0 z-50 bg-black border-b border-white/10">
    <nav class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="../" class="flex items-center">
            <img src="../images/travelhub.png" alt="CSNExplore" class="h-8 object-contain"/>
        </a>
        <a href="../" class="text-white text-sm font-bold hover:text-primary transition">← Back to Home</a>
    </nav>
</header>

<!-- Main Content -->
<div class="max-w-6xl mx-auto px-4 py-8 mt-20">
    <!-- Listing Header -->
    <div class="flex flex-col lg:grid lg:grid-cols-3 gap-8 mb-12 lg:items-start">
        <!-- Left: Image & Details -->
        <div class="lg:col-span-2 order-1 lg:order-1 w-full">
            <!-- Main Image -->
            <div class="mb-6 rounded-lg overflow-hidden bg-gray-200 h-96 img-zoom-container" data-animate="fade-in-up">
                <?php if ($image): ?>
                    <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo $title; ?>" class="w-full h-full object-cover img-zoom">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center bg-gray-300">
                        <span class="material-symbols-outlined text-6xl text-gray-400">image</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Title & Info -->
            <div data-animate="fade-in-up" data-animate-delay="1">
                <h1 class="text-4xl font-bold mb-2"><?php echo $title; ?></h1>
                <div class="flex items-center gap-4 mb-6 text-gray-600">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-lg">location_on</span>
                        <?php echo htmlspecialchars($listing['location'] ?? 'Location not specified'); ?>
                    </span>
                    <?php if ($listing['rating'] ?? 0): ?>
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-lg text-yellow-500">star</span>
                            <?php echo number_format($listing['rating'], 1); ?>/5
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Description -->
            <?php if ($listing['description']): ?>
                <div class="prose prose-sm max-w-none mb-8" data-animate="fade-in-up" data-animate-delay="2">
                    <p><?php echo nl2br(htmlspecialchars($listing['description'])); ?></p>
                </div>
            <?php endif; ?>

            <!-- Amenities/Features -->
            <?php if ($listing['amenities'] ?? $listing['features']): ?>
                <div class="mb-8" data-animate="fade-in-up" data-animate-delay="3">
                    <h3 class="text-xl font-bold mb-4">Amenities & Features</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <?php 
                        $amenities_raw = $listing['amenities'] ?? $listing['features'] ?? '';
                        // Handle JSON format
                        if (strpos($amenities_raw, '[') === 0) {
                            $amenities_array = json_decode($amenities_raw, true);
                            $items = is_array($amenities_array) ? $amenities_array : [];
                        } else {
                            // Handle comma-separated format
                            $items = array_filter(array_map('trim', explode(',', $amenities_raw)));
                        }
                        foreach ($items as $item): 
                        ?>
                            <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg hover-lift">
                                <span class="material-symbols-outlined text-primary text-lg">check_circle</span>
                                <span><?php echo htmlspecialchars($item); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right: Booking Card (order-2 on mobile) -->
        <div class="lg:col-span-1 order-2 lg:order-2 w-full">
            <div class="sticky top-24 bg-white border border-gray-200 rounded-lg p-6 shadow-lg" data-animate="fade-in-left">
                <!-- Price -->
                <div class="mb-4">
                    <div class="text-3xl font-bold text-primary mb-1">₹<?php echo number_format($price, 0); ?></div>
                    <div class="text-sm text-gray-600">
                        <?php 
                        if ($category === 'stays') echo 'per night';
                        elseif (in_array($category, ['cars', 'bikes'])) echo 'per day';
                        elseif ($category === 'restaurants') echo 'per person';
                        elseif ($category === 'attractions') echo 'entry fee';
                        elseif ($category === 'buses') echo 'per ticket';
                        ?>
                    </div>
                </div>

                <!-- Verified Badge -->
                <div class="mb-4 flex items-center gap-2 text-sm text-green-600">
                    <span class="material-symbols-outlined text-lg">verified</span>
                    <span class="font-medium">Verified</span>
                </div>

                <!-- Free Cancellation Notice -->
                <div class="mb-6 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-800 font-medium">Free cancellation · No hidden charges</p>
                </div>

                <!-- Room Type Selection (for stays) -->
                <?php if ($category === 'stays' && count($room_types) > 0): ?>
                    <div class="mb-6">
                        <label class="block text-sm font-bold mb-2">Select Room Type</label>
                        <select id="room-type-select" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                            <option value="">Choose a room type...</option>
                            <?php foreach ($room_types as $rt): ?>
                                <option value="<?php echo $rt['id']; ?>">
                                    <?php echo htmlspecialchars($rt['name']); ?> - ₹<?php echo number_format($rt['base_price'], 0); ?>/night
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <!-- Book Now Button -->
                <a href="../" class="block w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-orange-600 transition text-center mb-4">
                    Book Now
                </a>

                <!-- Contact Info -->
                <div class="pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-3">Need help?</p>
                    <div class="flex gap-2">
                        <a href="tel:+918600968888" class="flex-1 flex items-center justify-center gap-1 bg-blue-50 text-blue-600 py-2 rounded-lg hover:bg-blue-100 transition text-sm font-bold">
                            <span class="material-symbols-outlined text-lg">call</span> Call
                        </a>
                        <a href="https://wa.me/918600968888" target="_blank" class="flex-1 flex items-center justify-center gap-1 bg-green-50 text-green-600 py-2 rounded-lg hover:bg-green-100 transition text-sm font-bold">
                            <span class="material-symbols-outlined text-lg">chat</span> WhatsApp
                        </a>
                    </div>
                </div>
            </div>

            <!-- Location Map (Below Booking Card) -->
            <div class="mt-6 bg-white border border-gray-200 rounded-lg p-6 shadow-lg" data-animate="fade-in-left" data-animate-delay="1">
                <h3 class="text-xl font-bold mb-4">Location Map</h3>
                <div class="rounded-lg overflow-hidden border border-gray-200" style="height: 350px;">
                    <?php 
                    if ($map_embed) {
                        echo $map_embed;
                    } else {
                        // Default map if none provided
                        echo '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d16426.329596104577!2d75.30037121099188!3d19.851617685624074!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bdb98f39ca15447%3A0x96c2632e2aaa42c!2sKamalnayan%20Bajaj%20Hospital!5e0!3m2!1sen!2sin!4v1775290483319!5m2!1sen!2sin" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Similar Listings Section (BOTTOM - Full Width) -->
    <?php if (!empty($similar_listings)): ?>
        <div class="mb-8" data-animate="fade-in-up">
            <h3 class="text-2xl font-bold mb-6">Similar Listings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php foreach ($similar_listings as $idx => $item): 
                    $item_price = $item[$price_col] ?? 0;
                    $item_image = $item['image'] ?? '';
                    $item_name = htmlspecialchars($item['name'] ?? $item['operator'] ?? 'Listing');
                    $item_location = htmlspecialchars($item['location'] ?? '');
                    $item_rating = $item['rating'] ?? 0;
                    $item_slug = generateSlug($category, $item['id'], $item['name']);
                ?>
                    <a href="<?php echo $item_slug; ?>.html" 
                       class="group hover-lift" 
                       data-animate="fade-in-up" 
                       data-animate-delay="<?php echo min($idx + 1, 4); ?>">
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition">
                            <!-- Image -->
                            <div class="relative h-40 bg-gray-200 overflow-hidden img-zoom-container">
                                <?php if ($item_image): ?>
                                    <img src="<?php echo htmlspecialchars($item_image); ?>" alt="<?php echo $item_name; ?>" class="w-full h-full object-cover img-zoom">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center bg-gray-300">
                                        <span class="material-symbols-outlined text-4xl text-gray-400">image</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Content -->
                            <div class="p-3">
                                <h4 class="font-bold text-sm mb-1 line-clamp-2"><?php echo $item_name; ?></h4>
                                
                                <?php if ($item_location): ?>
                                    <p class="text-xs text-gray-600 mb-2 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">location_on</span>
                                        <?php echo $item_location; ?>
                                    </p>
                                <?php endif; ?>

                                <div class="flex items-center justify-between">
                                    <div class="text-primary font-bold text-sm">₹<?php echo number_format($item_price, 0); ?></div>
                                    <?php if ($item_rating): ?>
                                        <div class="flex items-center gap-1 text-xs">
                                            <span class="material-symbols-outlined text-xs text-yellow-500">star</span>
                                            <?php echo number_format($item_rating, 1); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Simple Footer -->
<footer class="bg-slate-900 text-white py-8">
    <div class="max-w-6xl mx-auto px-4 text-center">
        <p class="text-sm text-white/50">© <?php echo date('Y'); ?> CSNExplore. All rights reserved.</p>
        <div class="mt-4 flex justify-center gap-4 text-sm">
            <a href="../" class="hover:text-primary transition">Home</a>
            <a href="../about" class="hover:text-primary transition">About</a>
            <a href="../contact" class="hover:text-primary transition">Contact</a>
        </div>
    </div>
</footer>

<script src="../animations.js"></script>
<script>
document.body.classList.add('page-ready');
</script>
</body>
</html>
    <?php
    return ob_get_clean();
}
?>
