<?php
// Dynamic Listing Detail Page - Handles all listing types (stays, cars, bikes, restaurants, attractions, buses)
require_once 'php/config.php';
require_once 'php/jwt.php';

// Get category and ID from URL
$category = sanitize($_GET['category'] ?? '');
$id = (int)($_GET['id'] ?? 0);

// Validate inputs
$valid_categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];
if (!in_array($category, $valid_categories) || !$id) {
    http_response_code(404);
    die('Listing not found');
}

// Fetch listing from database
$db = getDB();
$listing = $db->fetchOne("SELECT * FROM $category WHERE id = ? AND is_active = 1", [$id]);

if (!$listing) {
    http_response_code(404);
    die('Listing not found');
}

// Get room types if it's a stay listing
$room_types = [];
if ($category === 'stays') {
    $room_types = $db->fetchAll(
        "SELECT rt.*, (SELECT COUNT(*) FROM rooms WHERE room_type_id = rt.id) as rooms_count 
         FROM room_types rt 
         WHERE rt.stay_id = ? OR (rt.vendor_id = (SELECT vendor_id FROM stays WHERE id = ?) AND rt.stay_id IS NULL)
         ORDER BY rt.created_at DESC",
        [$id, $id]
    );
}

// Get user info if logged in
$user = null;
$token = getAuthToken();
if ($token) {
    $payload = verifyJWT($token, JWT_SECRET);
    if ($payload) {
        $user = $db->fetchOne("SELECT id, email, name FROM users WHERE id = ?", [$payload['user_id'] ?? 0]);
    }
}

// Determine price column name
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

// Format title
$title = htmlspecialchars($listing['name'] ?? 'Listing');
$description = htmlspecialchars(substr($listing['description'] ?? '', 0, 160));
$image = $listing['image'] ?? '';
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
<link rel="canonical" href="https://csnexplore.com/listing-detail.php?category=<?php echo $category; ?>&id=<?php echo $id; ?>" />

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:url" content="https://csnexplore.com/listing-detail.php?category=<?php echo $category; ?>&id=<?php echo $id; ?>">
<meta property="og:title" content="<?php echo $title; ?> | CSNExplore">
<meta property="og:description" content="<?php echo $description; ?>">
<meta property="og:image" content="<?php echo $image; ?>">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="https://csnexplore.com/listing-detail.php?category=<?php echo $category; ?>&id=<?php echo $id; ?>">
<meta name="twitter:title" content="<?php echo $title; ?> | CSNExplore">
<meta name="twitter:description" content="<?php echo $description; ?>">
<meta name="twitter:image" content="<?php echo $image; ?>">

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<link rel="stylesheet" href="mobile-responsive.css"/>
<script>tailwind.config={{darkMode:"class",theme:{{extend:{{colors:{{"primary":"#ec5b13","whatsapp":"#25D366","background-dark":"#0a0705"}},fontFamily:{{display:["Inter","sans-serif"],serif:["Playfair Display","serif"]}}}}}}}}</script>
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
</style>
</head>
<body class="bg-white dark:bg-background-dark font-display text-slate-900 dark:text-slate-100">
