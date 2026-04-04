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

// Get similar listings (4 items)
$similar_listings = $db->fetchAll(
    "SELECT * FROM $category WHERE id != ? AND is_active = 1 ORDER BY RAND() LIMIT 4",
    [$id]
);

// Format title
$title = htmlspecialchars($listing['name'] ?? 'Listing');
$description = htmlspecialchars(substr($listing['description'] ?? '', 0, 160));
$image = $listing['image'] ?? '';
$map_embed = $listing['map_embed'] ?? '';
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

<?php include 'header.php'; ?>

<!-- Main Content -->
<div class="max-w-6xl mx-auto px-4 py-8 mt-20">
    <!-- Listing Header -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <!-- Left: Image & Details -->
        <div class="lg:col-span-2">
            <!-- Main Image -->
            <div class="mb-6 rounded-lg overflow-hidden bg-gray-200 h-96">
                <?php if ($image): ?>
                    <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo $title; ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center bg-gray-300">
                        <span class="material-symbols-outlined text-6xl text-gray-400">image</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Title & Info -->
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

            <!-- Description -->
            <?php if ($listing['description']): ?>
                <div class="prose prose-sm max-w-none mb-8">
                    <p><?php echo nl2br(htmlspecialchars($listing['description'])); ?></p>
                </div>
            <?php endif; ?>

            <!-- Amenities/Features -->
            <?php if ($listing['amenities'] ?? $listing['features']): ?>
                <div class="mb-8">
                    <h3 class="text-xl font-bold mb-4">Amenities & Features</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <?php 
                        $amenities = $listing['amenities'] ?? $listing['features'] ?? '';
                        $items = array_filter(array_map('trim', explode(',', $amenities)));
                        foreach ($items as $item): 
                        ?>
                            <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-lg">check_circle</span>
                                <span><?php echo htmlspecialchars($item); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Embedded Map -->
            <?php if ($map_embed): ?>
                <div class="mb-8">
                    <h3 class="text-xl font-bold mb-4">Location Map</h3>
                    <div class="rounded-lg overflow-hidden border border-gray-200 h-96">
                        <?php echo $map_embed; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right: Booking Card -->
        <div class="lg:col-span-1">
            <div class="sticky top-24 bg-white border border-gray-200 rounded-lg p-6 shadow-lg">
                <!-- Price -->
                <div class="mb-6">
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

                <!-- Booking Form -->
                <?php if ($user): ?>
                    <!-- User is logged in - show booking form -->
                    <form id="booking-form" class="space-y-4">
                        <input type="hidden" name="service_type" value="<?php echo $category; ?>">
                        <input type="hidden" name="listing_id" value="<?php echo $id; ?>">
                        <input type="hidden" name="listing_name" value="<?php echo htmlspecialchars($listing['name']); ?>">
                        
                        <div>
                            <label class="block text-sm font-bold mb-1">Full Name</label>
                            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-1">Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-1">Phone</label>
                            <input type="tel" name="phone" required class="w-full border border-gray-300 rounded-lg p-2 text-sm" placeholder="+91 XXXXX XXXXX">
                        </div>

                        <?php if ($category === 'stays'): ?>
                            <div>
                                <label class="block text-sm font-bold mb-1">Check-in Date</label>
                                <input type="date" name="checkin_date" required class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-1">Check-out Date</label>
                                <input type="date" name="checkout_date" required class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                            </div>
                        <?php else: ?>
                            <div>
                                <label class="block text-sm font-bold mb-1">Booking Date</label>
                                <input type="date" name="booking_date" required class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                            </div>
                        <?php endif; ?>

                        <div>
                            <label class="block text-sm font-bold mb-1">Number of People</label>
                            <input type="number" name="number_of_people" value="1" min="1" required class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-1">Notes (Optional)</label>
                            <textarea name="notes" class="w-full border border-gray-300 rounded-lg p-2 text-sm" rows="3" placeholder="Any special requests?"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-orange-600 transition">
                            Book Now
                        </button>
                    </form>
                <?php else: ?>
                    <!-- User not logged in - show login prompt -->
                    <div class="text-center">
                        <p class="text-gray-600 mb-4">Sign in to make a booking</p>
                        <a href="login" class="block w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-orange-600 transition mb-2">
                            Sign In
                        </a>
                        <a href="register" class="block w-full bg-gray-200 text-gray-800 font-bold py-3 rounded-lg hover:bg-gray-300 transition">
                            Create Account
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Contact Info -->
                <div class="mt-6 pt-6 border-t border-gray-200">
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
        </div>
    </div>

    <!-- Similar Listings Section -->
    <?php if (count($similar_listings) > 0): ?>
    <div class="mt-16 pt-12 border-t border-gray-200">
        <h2 class="text-3xl font-bold mb-8">Similar <?php echo ucfirst($category); ?></h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($similar_listings as $item): 
                $item_price = $item[$price_col] ?? 0;
                $item_image = $item['image'] ?? '';
                $item_name = htmlspecialchars($item['name'] ?? $item['operator'] ?? 'Listing');
                $item_location = htmlspecialchars($item['location'] ?? '');
                $item_rating = $item['rating'] ?? 0;
            ?>
                <a href="listing-detail.php?category=<?php echo $category; ?>&id=<?php echo $item['id']; ?>" class="group">
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition">
                        <!-- Image -->
                        <div class="relative h-48 bg-gray-200 overflow-hidden">
                            <?php if ($item_image): ?>
                                <img src="<?php echo htmlspecialchars($item_image); ?>" alt="<?php echo $item_name; ?>" class="w-full h-full object-cover group-hover:scale-105 transition">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center bg-gray-300">
                                    <span class="material-symbols-outlined text-4xl text-gray-400">image</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Content -->
                        <div class="p-4">
                            <h3 class="font-bold text-lg mb-1 line-clamp-2"><?php echo $item_name; ?></h3>
                            
                            <?php if ($item_location): ?>
                                <p class="text-sm text-gray-600 mb-2 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">location_on</span>
                                    <?php echo $item_location; ?>
                                </p>
                            <?php endif; ?>

                            <div class="flex items-center justify-between">
                                <div class="text-primary font-bold">₹<?php echo number_format($item_price, 0); ?></div>
                                <?php if ($item_rating): ?>
                                    <div class="flex items-center gap-1 text-sm">
                                        <span class="material-symbols-outlined text-sm text-yellow-500">star</span>
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

<?php include 'footer.php'; ?>

<script>
// Booking form submission
document.getElementById('booking-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('php/api/bookings.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + (localStorage.getItem('csn_user_token') || '')
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success || response.ok) {
            alert('Booking request submitted successfully! We will contact you soon.');
            e.target.reset();
        } else {
            alert('Error: ' + (result.error || 'Failed to submit booking'));
        }
    } catch (error) {
        console.error('Booking error:', error);
        alert('Error submitting booking. Please try again.');
    }
});

// Mark page as ready
document.body.classList.add('page-ready');
</script>
</body>
</html>