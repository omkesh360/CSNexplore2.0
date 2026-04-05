<?php
/**
 * Regenerate all listing detail HTML pages with ORIGINAL structure + NEW animations
 * This version uses the exact original template from GitHub commit 662eb1a
 * and adds new animation attributes on top
 */
require_once __DIR__ . '/config.php';

$db = getDB();
$categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];

$total_generated = 0;
$results = [];

echo "Starting regeneration with ORIGINAL structure + NEW animations...\n\n";

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
    }
    
    $results[$category] = $count;
    echo "  ✓ Generated {$count} {$category} pages\n";
}

echo "\n✅ Successfully generated {$total_generated} listing detail pages!\n";
echo "\nBreakdown:\n";
foreach ($results as $cat => $count) {
    echo "  • {$cat}: {$count} pages\n";
}

/**
 * Generate HTML using ORIGINAL template structure + NEW animations
 */
function generateListingDetailHTML($category, $listing, $db) {
    $id = $listing['id'];
    $title = htmlspecialchars($listing['name'] ?? 'Listing');
    $description = htmlspecialchars(substr($listing['description'] ?? '', 0, 160));
    $full_description = htmlspecialchars($listing['description'] ?? '');
    $image = $listing['image'] ?? '../images/travelhub.png';
    $map_embed = $listing['map_embed'] ?? '';
    $location = htmlspecialchars($listing['location'] ?? 'Location not specified');
    $rating = $listing['rating'] ?? 0;
    
    // Determine price column and label
    $price_info = [
        'stays' => ['col' => 'price_per_night', 'label' => 'per night', 'type' => 'Accommodation'],
        'cars' => ['col' => 'price_per_day', 'label' => 'per day', 'type' => 'Car Rental'],
        'bikes' => ['col' => 'price_per_day', 'label' => 'per day', 'type' => 'Bike Rental'],
        'restaurants' => ['col' => 'price_per_person', 'label' => 'per person', 'type' => 'Restaurant'],
        'attractions' => ['col' => 'entry_fee', 'label' => 'entry', 'type' => 'Attraction'],
        'buses' => ['col' => 'price', 'label' => 'per ticket', 'type' => 'Bus Service'],
    ];
    $price_col = $price_info[$category]['col'] ?? 'price';
    $price_label = $price_info[$category]['label'] ?? '';
    $price_type = $price_info[$category]['type'] ?? 'Service';
    $price = $listing[$price_col] ?? 0;
    
    // Get similar listings
    $similar_listings = $db->fetchAll(
        "SELECT * FROM {$category} WHERE id != ? AND is_active = 1 ORDER BY RAND() LIMIT 4",
        [$id]
    );
    
    // Get gallery images
    $gallery_images = [];
    if ($image && $image !== '../images/travelhub.png') {
        $gallery_images[] = $image;
    }
    // Add placeholder images to make 6 total
    $placeholders = [
        '../images/uploads/ellora_caves.png',
        '../images/uploads/ellora.png',
        '../images/uploads/bibi.png',
        '../images/uploads/daulatabad.png',
        '../images/uploads/grishneshwar.png'
    ];
    foreach ($placeholders as $p) {
        if (count($gallery_images) < 6) $gallery_images[] = $p;
    }
    
    // Generate slug
    $slug = generateSlug($category, $id, $listing['name']);
    
    // Amenities/Features
    $amenities = $listing['amenities'] ?? $listing['features'] ?? '';
    $amenity_items = array_filter(array_map('trim', explode(',', $amenities)));
    
    ob_start();
    ?><!DOCTYPE html>
<html class="light" lang="en" style="scroll-behavior:smooth">
<head>
<meta charset="utf-8"/>
<link rel="preconnect" href="https://cdn.tailwindcss.com">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="dns-prefetch" href="https://images.unsplash.com">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover"/>
<meta name="mobile-web-app-capable" content="yes"/>
<meta name="apple-mobile-web-app-capable" content="yes"/>
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
<meta name="format-detection" content="telephone=no"/>
<link rel="apple-touch-icon" sizes="57x57" href="../images/fevicon/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="../images/fevicon/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="../images/fevicon/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="../images/fevicon/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="../images/fevicon/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="../images/fevicon/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="../images/fevicon/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="../images/fevicon/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="../images/fevicon/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192" href="../images/fevicon/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="../images/fevicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="../images/fevicon/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="../images/fevicon/favicon-16x16.png">
<link rel="shortcut icon" href="../images/fevicon/favicon.ico" type="image/x-icon">
<meta name="msapplication-TileColor" content="#000000">
<meta name="msapplication-TileImage" content="../images/fevicon/ms-icon-144x144.png">
<meta name="theme-color" content="#000000">
<title><?php echo $title; ?> | CSNExplore</title>

<meta name="description" content="<?php echo $description; ?>">
<meta name="keywords" content="Chhatrapati Sambhajinagar, Aurangabad, tourism, hotels, bike rent, car rent, attractions">
<link rel="canonical" href="https://csnexplore.com/listing-detail/<?php echo $slug; ?>" />

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:url" content="https://csnexplore.com/listing-detail/<?php echo $slug; ?>">
<meta property="og:title" content="<?php echo $title; ?> | CSNExplore">
<meta property="og:description" content="<?php echo $description; ?>">
<meta property="og:image" content="<?php echo $image; ?>">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="https://csnexplore.com/listing-detail/<?php echo $slug; ?>">
<meta name="twitter:title" content="<?php echo $title; ?> | CSNExplore">
<meta name="twitter:description" content="<?php echo $description; ?>">
<meta name="twitter:image" content="<?php echo $image; ?>">
<script type="application/ld+json">{"@context":"https://schema.org","@type":"<?php echo $price_type; ?>","name":"<?php echo $title; ?>","image":"<?php echo $image; ?>","description":"<?php echo $description; ?>"}</script>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<link rel="stylesheet" href="../mobile-responsive.css"/>
<!-- NEW: Add animations.css -->
<link rel="stylesheet" href="../animations.css"/>
<script>tailwind.config={{darkMode:"class",theme:{{extend:{{colors:{{"primary":"#ec5b13","whatsapp":"#25D366","background-dark":"#0a0705"}},fontFamily:{{display:["Inter","sans-serif"],serif:["Playfair Display","serif"]}}}}}}}}</script>
<style>
body{opacity:0;will-change:opacity;}
body.page-ready{animation:pageFadeIn 0.2s ease forwards;}
@keyframes pageFadeIn{from{opacity:0;}to{opacity:1;}}
.material-symbols-outlined{font-variation-settings:"FILL" 0,"wght" 400,"GRAD" 0,"opsz" 24;font-family:"Material Symbols Outlined";font-style:normal;display:inline-block;line-height:1;}
@keyframes marquee{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
.prose h2{font-size:1.5rem;font-weight:800;margin:2rem 0 0.75rem;}
.prose h3{font-size:1.2rem;font-weight:700;margin:1.5rem 0 0.5rem;}
.prose p{margin-bottom:1.1rem;line-height:1.85;}
.prose ul{list-style:disc;padding-left:1.5rem;margin-bottom:1.1rem;}
.prose ul li{margin-bottom:0.4rem;line-height:1.7;}
.prose strong{font-weight:700;}
.line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}

/* ✨✨ Global Motion System ✨✨ */
[data-reveal]{opacity:0;transform:translateY(32px);transition:opacity 0.65s cubic-bezier(.22,1,.36,1),transform 0.65s cubic-bezier(.22,1,.36,1);}
[data-reveal="left"]{transform:translateX(-40px);}
[data-reveal="right"]{transform:translateX(40px);}
[data-reveal="scale"]{transform:scale(0.92) translateY(20px);}
[data-reveal].revealed{opacity:1!important;transform:none!important;}
[data-delay="1"]{transition-delay:0.08s;}[data-delay="2"]{transition-delay:0.16s;}
[data-delay="3"]{transition-delay:0.24s;}[data-delay="4"]{transition-delay:0.32s;}
[data-delay="5"]{transition-delay:0.40s;}[data-delay="6"]{transition-delay:0.48s;}
.card-glow{transition:transform 0.3s ease,box-shadow 0.3s ease;}
.card-glow:hover{transform:translateY(-6px) scale(1.01);box-shadow:0 20px 50px rgba(236,91,19,0.18),0 4px 16px rgba(0,0,0,0.12);}
.img-shimmer{position:relative;overflow:hidden;}
.img-shimmer::after{content:\"\";position:absolute;inset:0;background:linear-gradient(105deg,transparent 40%,rgba(255,255,255,0.08) 50%,transparent 60%);transform:translateX(-100%);transition:transform 0.6s ease;}
.img-shimmer:hover::after{transform:translateX(100%);}
/* ✨✨ Glassmorphism Effects ✨✨ */
.glass{background:rgba(255,255,255,0.08);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,0.15);box-shadow:0 8px 32px rgba(0,0,0,0.1);}
.glass-dark { background:rgba(10,7,5,0.7); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border:1px solid rgba(236,91,19,0.1); }
.header-solid { background:#000000 !important; backdrop-filter:none !important; -webkit-backdrop-filter:none !important; }
.glass-card{background:rgba(255,255,255,0.06);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,0.1);box-shadow:0 4px 24px rgba(0,0,0,0.08);}
.glass-button{background:rgba(236,91,19,0.85);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.2);box-shadow:0 4px 16px rgba(236,91,19,0.3);}
.glass-button:hover{background:rgba(236,91,19,0.95);box-shadow:0 6px 24px rgba(236,91,19,0.4);}
.glass-input{background:rgba(255,255,255,0.08);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.15);color:#fff;}
.glass-input::placeholder{color:rgba(255,255,255,0.6);}
.glass-input:focus{background:rgba(255,255,255,0.12);border-color:rgba(255,255,255,0.25);outline:none;}
.glass-badge{background:rgba(236,91,19,0.8);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.15);}
.glass-overlay{background:rgba(0,0,0,0.4);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);}
.glass-section{background:linear-gradient(135deg,rgba(255,255,255,0.05) 0%,rgba(255,255,255,0.02) 100%);backdrop-filter:blur(15px);-webkit-backdrop-filter:blur(15px);border:1px solid rgba(255,255,255,0.08);}
.glass-glow{background:rgba(236,91,19,0.1);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid rgba(236,91,19,0.2);box-shadow:0 0 30px rgba(236,91,19,0.15);}
/* ✨✨ Gallery Lightbox ✨✨ */
.gallery-thumb{cursor:zoom-in;position:relative;overflow:hidden;border-radius:12px;aspect-ratio:4/3;background:#f1f5f9;border:1px solid #e2e8f0;}
.gallery-thumb img{width:100%;height:100%;object-fit:cover;transition:all 0.4s cubic-bezier(0.4, 0, 0.2, 1);background:#f1f5f9;}
.gallery-thumb:hover img{transform:scale(1.08);filter:brightness(1.05);}
.gallery-thumb::after{content:"\e8ff";font-family:"Material Symbols Outlined";position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.3);color:#fff;font-size:26px;opacity:0;transition:opacity 0.25s;pointer-events:none;}
.gallery-thumb:hover::after{opacity:1;}
.gallery-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px;}
#csn-gallery-modal{position:fixed !important;top:0 !important;left:0 !important;width:100% !important;height:100% !important;background:rgba(0,0,0,0.85) !important;z-index:999999999 !important;display:none !important;align-items:center !important;justify-content:center !important;overflow:hidden !important;}
#csn-gallery-modal.active{display:flex !important;}
.csn-modal-content{position:fixed !important;top:50% !important;left:50% !important;transform:translate(-50%,-50%) !important;background:transparent !important;border-radius:0 !important;padding:0 !important;max-width:85vw !important;max-height:85vh !important;display:flex !important;flex-direction:column !important;align-items:center !important;justify-content:center !important;box-shadow:none !important;z-index:1000000000 !important;overflow:visible !important;border:none !important;}
.csn-modal-content img{width:85vw !important;height:85vh !important;max-width:85vw !important;max-height:85vh !important;object-fit:contain !important;border-radius:0 !important;transition:opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), transform 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;border:none !important;outline:none !important;box-shadow:none !important;opacity:1 !important;}
.csn-modal-close{position:fixed !important;top:30px !important;right:30px !important;background:#ec5b13 !important;border:none !important;color:#fff !important;width:40px !important;height:40px !important;border-radius:50% !important;cursor:pointer !important;font-size:24px !important;display:flex !important;align-items:center !important;justify-content:center !important;transition:all 0.3s !important;font-weight:bold !important;z-index:1000000001 !important;}
.csn-modal-close:hover{background:#d94a0a !important;transform:scale(1.1) !important;}
.csn-modal-nav{position:fixed !important;top:50% !important;transform:translateY(-50%) !important;background:#ec5b13 !important;border:none !important;color:#fff !important;width:45px !important;height:45px !important;border-radius:50% !important;cursor:pointer !important;font-size:20px !important;display:flex !important;align-items:center !important;justify-content:center !important;transition:all 0.3s !important;z-index:1000000001 !important;}
.csn-modal-nav:hover{background:#d94a0a !important;transform:translateY(-50%) scale(1.1) !important;}
.csn-modal-prev{left:20px !important;}
.csn-modal-next{right:20px !important;}
.csn-modal-counter{position:fixed !important;bottom:30px !important;left:50% !important;transform:translateX(-50%) !important;background:#ec5b13 !important;color:#fff !important;padding:8px 16px !important;border-radius:20px !important;font-size:13px !important;font-weight:bold !important;z-index:1000000001 !important;}
.csn-zoom-controls{position:fixed !important;bottom:30px !important;right:30px !important;display:flex !important;gap:8px !important;z-index:1000000001 !important;}
.csn-zoom-btn{background:#ec5b13 !important;border:none !important;color:#fff !important;width:36px !important;height:36px !important;border-radius:50% !important;cursor:pointer !important;font-size:18px !important;display:flex !important;align-items:center !important;justify-content:center !important;transition:all 0.3s !important;font-weight:bold !important;}
.csn-zoom-btn:hover{background:#d94a0a !important;transform:scale(1.1) !important;}
.csn-modal-content img.zoomed{max-width:none !important;max-height:none !important;width:auto !important;height:auto !important;}
@media(max-width:768px){
  .csn-modal-content{padding:0 !important;max-width:95vw !important;max-height:85vh !important;}
  .csn-modal-content img{width:95vw !important;height:85vh !important;max-width:95vw !important;max-height:85vh !important;}
  .csn-modal-close{top:15px !important;right:15px !important;width:36px !important;height:36px !important;font-size:20px !important;}
  .csn-modal-nav{width:40px !important;height:40px !important;font-size:18px !important;}
  .csn-modal-prev{left:10px !important;}
  .csn-modal-next{right:10px !important;}
  .csn-zoom-controls{bottom:15px !important;right:15px !important;gap:6px !important;}
  .csn-zoom-btn{width:32px !important;height:32px !important;font-size:16px !important;}
}
/* ✨✨ Gallery Grid ✨✨ */
.gallery-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px;}
@media(max-width:768px){.gallery-grid{grid-template-columns:repeat(2,1fr);gap:10px;}}
@media(max-width:480px){.gallery-grid{grid-template-columns:repeat(2,1fr);gap:8px;}}
</style>
<script>
function playBookingSound() {
  try {
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    function playTone(freq, start, duration) {
      const osc = audioCtx.createOscillator();
      const gain = audioCtx.createGain();
      osc.connect(gain); gain.connect(audioCtx.destination);
      osc.frequency.value = freq; osc.type = "sine";
      gain.gain.setValueAtTime(0.3, start);
      gain.gain.exponentialRampToValueAtTime(0.01, start + duration);
      osc.start(start); osc.stop(start + duration);
    }
    // "Tring Tring" 10 times
    for(let i=0; i<10; i++) {
        let t = audioCtx.currentTime + (i * 0.4);
        playTone(880, t, 0.1);
        playTone(880, t + 0.15, 0.1);
    }
  } catch(e) { console.error("Sound failed", e); }
}
</script>
</head>
<body class="bg-white dark:bg-background-dark font-display text-slate-900 dark:text-slate-100">
<?php
    // Continue in next part due to size...
    return ob_get_clean();
}
?>