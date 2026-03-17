<?php
// listing.php – Unified listing page for all categories
require_once 'php/config.php';

$type = $_GET['type'] ?? 'stays';
$allowed = ['stays','cars','bikes','attractions','restaurants','buses'];
if (!in_array($type, $allowed)) $type = 'stays';

// ── Fetch from DB ─────────────────────────────────────────────────────────────
$db = getDB();
$search     = trim($_GET['search'] ?? '');
$filterType = trim($_GET['filter_type'] ?? '');

$priceCol = ['stays'=>'price_per_night','cars'=>'price_per_day','bikes'=>'price_per_day',
             'restaurants'=>'price_per_person','attractions'=>'entry_fee','buses'=>'price'];
$pc = $priceCol[$type];

$where  = ['is_active = 1'];
$params = [];
if ($search) { $where[] = 'name LIKE ?'; $params[] = '%'.$search.'%'; }
if ($filterType) { $where[] = 'type = ?'; $params[] = $filterType; }

$sql = "SELECT * FROM $type WHERE " . implode(' AND ', $where) . " ORDER BY display_order ASC, id ASC";
$items = $db->fetchAll($sql, $params);

// Decode JSON fields
foreach ($items as &$item) {
    foreach (['amenities','features','gallery','menu_highlights'] as $f) {
        if (isset($item[$f]) && is_string($item[$f])) {
            $item[$f] = json_decode($item[$f], true) ?: [];
        }
    }
}
unset($item);

$config = [
  'stays' => [
    'title'    => 'Hotel Stays | CSNExplore',
    'heading'  => 'Hotels in Chhatrapati Sambhajinagar',
    'icon'     => 'bed',
    'label'    => 'Stays',
    'unit'     => '/ night',
    'cta'      => 'View Details',
    'hero_bg'  => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1600&q=80',
    'hero_h1'  => 'Find Your Perfect Stay',
    'hero_sub' => 'Hotels, resorts & homestays in Chhatrapati Sambhajinagar.',
    'filters'  => ['Luxury Hotels','Boutique Hotels','Budget Stays','Resorts','Homestays'],
    'filter_label' => 'Property Type',
    'search_fields'=> ['location','checkin','checkout'],
    'promo'    => 'First hotel booking with CSNExplore',
  ],
  'cars' => [
    'title'    => 'Car Rentals | CSNExplore',
    'heading'  => 'Cars in Chhatrapati Sambhajinagar',
    'icon'     => 'directions_car',
    'label'    => 'Car Rentals',
    'unit'     => '/ day',
    'cta'      => 'Book Now',
    'hero_bg'  => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=1600&q=80',
    'hero_h1'  => 'Rent a Car Your Way',
    'hero_sub' => 'Self-drive or with driver — explore Maharashtra at your pace.',
    'filters'  => ['Sedan','SUV','MPV','Hatchback','Luxury'],
    'filter_label' => 'Vehicle Type',
    'search_fields'=> ['pickup','dropoff','pickdate','dropdate'],
    'promo'    => 'First car rental with CSNExplore',
  ],
  'bikes' => [
    'title'    => 'Bike Rentals | CSNExplore',
    'heading'  => 'Bikes in Chhatrapati Sambhajinagar',
    'icon'     => 'motorcycle',
    'label'    => 'Bike Rentals',
    'unit'     => '/ day',
    'cta'      => 'Book Now',
    'hero_bg'  => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1600&q=80',
    'hero_h1'  => 'Ride the Open Road',
    'hero_sub' => 'Scooters, cruisers & sports bikes for every trail.',
    'filters'  => ['Scooter','Cruiser','Sports','Commuter','Adventure'],
    'filter_label' => 'Bike Type',
    'search_fields'=> ['location','pickdate','dropdate'],
    'promo'    => 'Free cancellation on bike rentals',
  ],
  'attractions' => [
    'title'    => 'Attractions | CSNExplore',
    'heading'  => 'Attractions near Chhatrapati Sambhajinagar',
    'icon'     => 'confirmation_number',
    'label'    => 'Attractions',
    'unit'     => ' entry',
    'cta'      => 'Explore',
    'hero_bg'  => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Kailash_Temple%2C_Ellora.jpg/1280px-Kailash_Temple%2C_Ellora.jpg',
    'hero_h1'  => 'Explore Ancient Marvels',
    'hero_sub' => 'Forts, caves, temples & UNESCO heritage sites await.',
    'filters'  => ['UNESCO Heritage','Fort','Temple','Museum','Garden'],
    'filter_label' => 'Category',
    'search_fields'=> ['location','date'],
    'promo'    => 'Verified guides for Ajanta & Ellora',
  ],
  'restaurants' => [
    'title'    => 'Restaurants | CSNExplore',
    'heading'  => 'Restaurants in Chhatrapati Sambhajinagar',
    'icon'     => 'restaurant',
    'label'    => 'Restaurants',
    'unit'     => ' for two',
    'cta'      => 'Reserve',
    'hero_bg'  => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1600&q=80',
    'hero_h1'  => 'Dine Out Tonight',
    'hero_sub' => 'Best restaurants, cafes & local eateries in the city.',
    'filters'  => ['Maharashtrian','North Indian','Biryani','Pure Veg','Café'],
    'filter_label' => 'Cuisine',
    'search_fields'=> ['location','date','time'],
    'promo'    => 'Best dining deals in the city',
  ],
  'buses' => [
    'title'    => 'Bus Routes | CSNExplore',
    'heading'  => 'Bus Routes from Chhatrapati Sambhajinagar',
    'icon'     => 'directions_bus',
    'label'    => 'Buses',
    'unit'     => '',
    'cta'      => 'Enquire',
    'hero_bg'  => 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=1600&q=80',
    'hero_h1'  => 'Travel by Bus',
    'hero_sub' => 'Intercity buses on request — call or WhatsApp to book.',
    'filters'  => ['MSRTC','Private','Luxury','Sleeper','Semi-Sleeper'],
    'filter_label' => 'Bus Type',
    'search_fields'=> ['from','to','date'],
    'promo'    => 'Group discounts available on buses',
  ],
];

$c = $config[$type];
$page_title   = $c['title'];
$current_page = 'listing.php';

// ── Per-category listing data ────────────────────────────────────────────────
$listings = [
  'stays' => [
    ['name'=>'Hotel Rama International',   'sub'=>'Station Road, Sambhajinagar',   'rating'=>'4.7','price'=>'₹2,800','badge'=>'Top Rated',  'badge_color'=>'bg-primary',   'img'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuCt5P-q6vBune1XdFy0xt2CR1TfTzrKo4WcAqKxf2MafD_Vt1UoGG4g15NJXHbU2WkGPXidKOPrbQuKM9fud0zPzSjeQ1OwmJbZjT85fpEcp1RaPct7o1yuZYGWKney-X0Z__TmzzWXHRWkHd92prhpMmpGdzAI2vlB_d6HVZLdOsti2HcQ5Sdptyj4AaoPqQUN5A215WXQhbYLs0pC-8V9kGznWEnzZHUYeJ4sTcjEOocgtHySJnnSmNOWbPKYJj1-fSyUNNvrr_YZ'],
    ['name'=>'Lemon Tree Hotel',           'sub'=>'Chikalthana, Sambhajinagar',    'rating'=>'4.5','price'=>'₹3,500','badge'=>'',           'badge_color'=>'',             'img'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuDOdNX-7setLVYiyyHaTUkvhWVA13YoHf2mAY63IE-7-l-g-OouyLyl5-ghRge3d2GM5CBY8H8SQKj05TqDm7N9_0ECHibz_eYxbVCgHGCMB_Tn5YvwDxAoNC_e-U6CK9axWX_yfLYMOv7nDZEy4JV_mc2uCPeovot-UFxt7_KtxF_Tahk_YOQnJNfqax65L-T1gHiHTJtVTnHXdjNzfVX9D7JbPWmVhbJQwarVGeynaWFxdJkV_w7hnz2XcPCPMfOAIns1A2PId9MV'],
    ['name'=>'Hotel Ajanta Ambassador',    'sub'=>'Jalna Road, Sambhajinagar',     'rating'=>'4.3','price'=>'₹1,900','badge'=>'Best Value', 'badge_color'=>'bg-green-500', 'img'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuBjN9FO0GiCWCxLzecVU1eAXKDXFnH9cFCrAhrZ1xcCRNgX01hdWxg9LRaYJwoo9rh4ywpGp8uy40Fsj99CYHdRRegnKwY4FlSmo6ZgHwR79vcC_kJybr494hb61Yq9-IEn4dN0xoGrB1BE1Arv7lNvozBjrfCg9rtiwNsQgXa6SeT9GXcWKswJFwUHzUnadl7YvK-tMx3SvKqEvdlz9tVqOcZ-nVdz3rXPzFpnSvHvWxCsNU0DoIQ90MbrSKJ1dRHa52KgftW_aEny'],
    ['name'=>'Vivanta Aurangabad',         'sub'=>'Chikalthana, Sambhajinagar',    'rating'=>'4.9','price'=>'₹6,200','badge'=>'Luxury',     'badge_color'=>'bg-amber-500', 'img'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuA-Z_8YC_jsWBlnalO05kqGlJcKUYVMf6TED1IHidwQ_z7rDHpxoIl-xuxp66RYnOPb8UlR4Sij3hAgo_P6QLi3-SHEf0c90c2E6x2q2QdVDFkGkwzoWT8liiaKH5sQ9NOhZXN3vvuELzBbncWPKL3ywhztIfl46Ay9YcH9vteQLk97g3WMIL5-48Roizso0wwNJTPMNPsEz6FHki4gHa89M2-yJzQs70RroxNueGxh31mSX4CnqzjST3SiJGkOCKKDXEgAd-zXAs-g'],
    ['name'=>'Hotel Nandanvan',            'sub'=>'Samarth Nagar, Sambhajinagar',  'rating'=>'4.2','price'=>'₹1,500','badge'=>'',           'badge_color'=>'',             'img'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuA_2sB-HUteR3nF9oYKYnE-HA3xjJA81VvT9zGiLT5DZakgCJVBIbJuw-x8lunFKtYh24i2zZZIkB1pcGC8XLV1Qf_uCRPJas1h0lvHpY-h93PXfdTyRXBJ6PtxmCJb-7rN5OX6C8WJ1ovwFHyHG5DmgdSPe4WQe8i0Hej2a0Hh637MtfcLsCtMBWQpSlgVfH9wEKlSBV6exAv_0JJXRZBu29xl2rZ3rZiMmf3GCwRIpXgKHdvvMWkw6Oj5j_YpyPPrmJKgfqLfJ2Sc'],
    ['name'=>'The Meadows Resort',         'sub'=>'Aurangabad Road, Sambhajinagar','rating'=>'4.6','price'=>'₹4,100','badge'=>'',           'badge_color'=>'',             'img'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuCccVYKQLnMUeeBf7TV7A_GVMPlzeySp5k3RbfOmAUAdm5rye_urvijxDijPY5AM9LGaIw-cH0FrnjoNHlOSuSuCifqZAnborMBCtc6Nwj95ZLH041nTRo0lFDpoqGiFK5M2DqdJGDBLzIp6uPAUdHgLR7m_WfPtMkNKi1fsaPAAmnvOMx7sxbx0AwwuAJzEbULXAE0tT7QhRq_F0x0W_K-5LawR-gPIDXBUeKiIvRNMQWA0em3Urlehr070-f6kt16nvcUEmOl0VFy'],
  ],
  'cars' => [
    ['name'=>'Maruti Swift Dzire',  'sub'=>'Sedan · 5 Seats · AC',       'rating'=>'4.6','price'=>'₹1,200','badge'=>'Popular',    'badge_color'=>'bg-primary',   'img'=>'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=600&q=80'],
    ['name'=>'Toyota Innova Crysta','sub'=>'MPV · 7 Seats · AC',         'rating'=>'4.8','price'=>'₹2,500','badge'=>'Top Rated',  'badge_color'=>'bg-amber-500', 'img'=>'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&q=80'],
    ['name'=>'Hyundai Creta',       'sub'=>'SUV · 5 Seats · AC',         'rating'=>'4.5','price'=>'₹1,800','badge'=>'',           'badge_color'=>'',             'img'=>'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=600&q=80'],
    ['name'=>'Mahindra Scorpio',    'sub'=>'SUV · 7 Seats · AC',         'rating'=>'4.4','price'=>'₹2,000','badge'=>'',           'badge_color'=>'',             'img'=>'https://images.unsplash.com/photo-1502877338535-766e1452684a?w=600&q=80'],
    ['name'=>'Maruti Ertiga',       'sub'=>'MPV · 7 Seats · AC',         'rating'=>'4.3','price'=>'₹1,500','badge'=>'Best Value', 'badge_color'=>'bg-green-500', 'img'=>'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=600&q=80'],
    ['name'=>'Toyota Fortuner',     'sub'=>'SUV · 7 Seats · AC · 4WD',   'rating'=>'4.9','price'=>'₹3,800','badge'=>'Luxury',     'badge_color'=>'bg-amber-500', 'img'=>'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?w=600&q=80'],
  ],
  'bikes' => [
    ['name'=>'Honda Activa 6G',     'sub'=>'Scooter · Automatic',        'rating'=>'4.5','price'=>'₹350', 'badge'=>'Popular',    'badge_color'=>'bg-primary',   'img'=>'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80'],
    ['name'=>'Royal Enfield Classic 350','sub'=>'Cruiser · Manual',      'rating'=>'4.8','price'=>'₹800', 'badge'=>'Top Rated',  'badge_color'=>'bg-amber-500', 'img'=>'https://images.unsplash.com/photo-1609630875171-b1321377ee65?w=600&q=80'],
    ['name'=>'TVS Jupiter',         'sub'=>'Scooter · Automatic',        'rating'=>'4.3','price'=>'₹300', 'badge'=>'Best Value', 'badge_color'=>'bg-green-500', 'img'=>'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?w=600&q=80'],
    ['name'=>'Bajaj Pulsar NS200',  'sub'=>'Sports · Manual',            'rating'=>'4.6','price'=>'₹650', 'badge'=>'',           'badge_color'=>'',             'img'=>'https://images.unsplash.com/photo-1449426468159-d96dbf08f19f?w=600&q=80'],
    ['name'=>'Hero Splendor Plus',  'sub'=>'Commuter · Manual',          'rating'=>'4.2','price'=>'₹250', 'badge'=>'',           'badge_color'=>'',             'img'=>'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=600&q=80'],
    ['name'=>'KTM Duke 390',        'sub'=>'Sports · Manual',            'rating'=>'4.7','price'=>'₹1,100','badge'=>'',          'badge_color'=>'',             'img'=>'https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=600&q=80'],
  ],
  'attractions' => [
    ['name'=>'Ajanta Caves',        'sub'=>'UNESCO World Heritage Site',  'rating'=>'4.9','price'=>'₹40',  'badge'=>'UNESCO',     'badge_color'=>'bg-primary',   'img'=>'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Kailash_Temple%2C_Ellora.jpg/640px-Kailash_Temple%2C_Ellora.jpg'],
    ['name'=>'Ellora Caves',        'sub'=>'UNESCO World Heritage Site',  'rating'=>'4.9','price'=>'₹40',  'badge'=>'UNESCO',     'badge_color'=>'bg-primary',   'img'=>'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=600&q=80'],
    ['name'=>'Bibi Ka Maqbara',     'sub'=>'Mughal Monument, City Centre','rating'=>'4.6','price'=>'₹25',  'badge'=>'Heritage',   'badge_color'=>'bg-amber-500', 'img'=>'https://images.unsplash.com/photo-1548013146-72479768bada?w=600&q=80'],
    ['name'=>'Daulatabad Fort',     'sub'=>'Medieval Fort, 15 km away',   'rating'=>'4.5','price'=>'₹25',  'badge'=>'',           'badge_color'=>'',             'img'=>'https://images.unsplash.com/photo-1587474260584-136574528ed5?w=600&q=80'],
    ['name'=>'Panchakki',           'sub'=>'Water Mill & Garden',         'rating'=>'4.3','price'=>'₹15',  'badge'=>'',           'badge_color'=>'',             'img'=>'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=600&q=80'],
    ['name'=>'Siddharth Garden & Zoo','sub'=>'City Garden & Zoo',         'rating'=>'4.1','price'=>'₹20',  'badge'=>'Family',     'badge_color'=>'bg-green-500', 'img'=>'https://images.unsplash.com/photo-1534430480872-3498386e7856?w=600&q=80'],
  ],
  'restaurants' => [
    ['name'=>'Bhoj Restaurant',     'sub'=>'Maharashtrian · City Centre', 'rating'=>'4.7','price'=>'₹400', 'badge'=>'Top Rated',  'badge_color'=>'bg-primary',   'img'=>'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&q=80'],
    ['name'=>'Tandoor Restaurant',  'sub'=>'North Indian · Station Road', 'rating'=>'4.5','price'=>'₹600', 'badge'=>'',           'badge_color'=>'',             'img'=>'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600&q=80'],
    ['name'=>'Swad Pure Veg',       'sub'=>'Pure Veg · Samarth Nagar',    'rating'=>'4.4','price'=>'₹300', 'badge'=>'Pure Veg',   'badge_color'=>'bg-green-500', 'img'=>'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=600&q=80'],
    ['name'=>'Biryani House',       'sub'=>'Biryani · Osmanpura',         'rating'=>'4.6','price'=>'₹450', 'badge'=>'Must Try',   'badge_color'=>'bg-amber-500', 'img'=>'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=600&q=80'],
    ['name'=>'Café Nirvana',        'sub'=>'Café · Prozone Mall Area',    'rating'=>'4.3','price'=>'₹350', 'badge'=>'',           'badge_color'=>'',             'img'=>'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=600&q=80'],
    ['name'=>'Hotel Foodwala',      'sub'=>'Multi-Cuisine · Jalna Road',  'rating'=>'4.2','price'=>'₹500', 'badge'=>'',           'badge_color'=>'',             'img'=>'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=600&q=80'],
  ],
  'buses' => [
    ['name'=>'Sambhajinagar → Pune',    'sub'=>'MSRTC Shivneri · 5 hrs',     'rating'=>'4.5','price'=>'₹350', 'badge'=>'Daily',      'badge_color'=>'bg-primary',   'img'=>'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=600&q=80'],
    ['name'=>'Sambhajinagar → Mumbai',  'sub'=>'Luxury Sleeper · 7 hrs',     'rating'=>'4.7','price'=>'₹600', 'badge'=>'Popular',    'badge_color'=>'bg-amber-500', 'img'=>'https://images.unsplash.com/photo-1570125909232-eb263c188f7e?w=600&q=80'],
    ['name'=>'Sambhajinagar → Nashik',  'sub'=>'Semi-Sleeper · 4 hrs',       'rating'=>'4.3','price'=>'₹280', 'badge'=>'',           'badge_color'=>'',             'img'=>'https://images.unsplash.com/photo-1464219789935-c2d9d9aba644?w=600&q=80'],
    ['name'=>'Sambhajinagar → Nagpur',  'sub'=>'MSRTC · 6 hrs',              'rating'=>'4.2','price'=>'₹420', 'badge'=>'',           'badge_color'=>'',             'img'=>'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80'],
    ['name'=>'Sambhajinagar → Shirdi',  'sub'=>'Private · 3 hrs',            'rating'=>'4.6','price'=>'₹250', 'badge'=>'On Request', 'badge_color'=>'bg-green-500', 'img'=>'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=600&q=80'],
    ['name'=>'Sambhajinagar → Hyderabad','sub'=>'Overnight Sleeper · 9 hrs', 'rating'=>'4.4','price'=>'₹750', 'badge'=>'',           'badge_color'=>'',             'img'=>'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?w=600&q=80'],
  ],
];

$items = $listings[$type];

$extra_styles = "
  .glassy { background:rgba(255,255,255,0.7); backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.3); }
  .hide-scrollbar::-webkit-scrollbar{display:none} .hide-scrollbar{-ms-overflow-style:none;scrollbar-width:none}
  body { background-color:#f8f6f6; }
";
require 'header.php';

$category_nav = [
  ['type'=>'stays',       'icon'=>'bed',                'label'=>'Stays'],
  ['type'=>'cars',        'icon'=>'directions_car',     'label'=>'Car Rentals'],
  ['type'=>'bikes',       'icon'=>'motorcycle',         'label'=>'Bike Rentals'],
  ['type'=>'attractions', 'icon'=>'confirmation_number','label'=>'Attractions'],
  ['type'=>'restaurants', 'icon'=>'restaurant',         'label'=>'Restaurants'],
  ['type'=>'buses',       'icon'=>'directions_bus',     'label'=>'Buses'],
];
?>
<!-- Category Sub-Nav -->
<nav class="bg-white border-b border-slate-100 sticky top-16 z-40 shadow-sm">
  <div class="max-w-7xl mx-auto px-4 overflow-x-auto hide-scrollbar">
    <div class="flex items-center gap-1 h-12">
      <?php foreach ($category_nav as $cat): $active = ($cat['type'] === $type); ?>
      <a href="listing.php?type=<?php echo $cat['type']; ?>"
         class="flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all
                <?php echo $active ? 'bg-primary text-white shadow shadow-primary/30' : 'text-slate-500 hover:text-primary hover:bg-primary/10'; ?>">
        <span class="material-symbols-outlined text-[15px]"><?php echo $cat['icon']; ?></span>
        <?php echo $cat['label']; ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</nav>

<!-- Breadcrumb -->
<div class="bg-white border-b border-slate-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5 flex items-center gap-2 text-xs text-slate-400">
    <a href="index.php" class="hover:text-primary transition-colors">Home</a>
    <span class="material-symbols-outlined text-[12px]">chevron_right</span>
    <span class="text-slate-600 font-medium"><?php echo htmlspecialchars($c['label']); ?></span>
  </div>
</div>

<main class="min-h-screen" style="background:#f8f6f6">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <div class="flex flex-col lg:flex-row gap-8">

    <!-- Sidebar -->
    <aside class="w-full lg:w-72 shrink-0 space-y-6">
      <div class="glassy p-6 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-bold">Filters</h3>
          <button class="text-sm text-primary font-medium">Reset</button>
        </div>
        <!-- Category filters -->
        <div class="mb-8">
          <p class="text-xs font-bold text-slate-500 mb-4 flex items-center gap-2 uppercase tracking-wider">
            <span class="material-symbols-outlined text-lg">category</span><?php echo htmlspecialchars($c['filter_label']); ?>
          </p>
          <div class="space-y-3">
            <?php foreach ($c['filters'] as $f): ?>
            <label class="flex items-center gap-3 cursor-pointer group">
              <input type="checkbox" class="rounded text-primary focus:ring-primary size-4 border-slate-300"/>
              <span class="text-sm font-medium group-hover:text-primary transition-colors"><?php echo htmlspecialchars($f); ?></span>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
        <!-- Price Range -->
        <div class="mb-8">
          <p class="text-xs font-bold text-slate-500 mb-4 flex items-center gap-2 uppercase tracking-wider">
            <span class="material-symbols-outlined text-lg">payments</span>Price Range
          </p>
          <div class="relative h-1.5 w-full bg-slate-200 rounded-full mb-4">
            <div class="absolute left-[10%] right-[20%] h-full bg-primary rounded-full"></div>
            <div class="absolute left-[10%] top-1/2 -translate-y-1/2 size-4 rounded-full bg-primary border-2 border-white shadow-md cursor-pointer"></div>
            <div class="absolute right-[20%] top-1/2 -translate-y-1/2 size-4 rounded-full bg-primary border-2 border-white shadow-md cursor-pointer"></div>
          </div>
          <div class="flex justify-between text-xs font-bold text-slate-600"><span>₹100</span><span>₹10,000</span></div>
        </div>
        <!-- Rating -->
        <div>
          <p class="text-xs font-bold text-slate-500 mb-4 flex items-center gap-2 uppercase tracking-wider">
            <span class="material-symbols-outlined text-lg">star</span>Rating
          </p>
          <div class="space-y-3">
            <?php foreach (['4.5+ Exceptional','4.0+ Very Good','3.5+ Good'] as $r): ?>
            <label class="flex items-center gap-3 cursor-pointer">
              <input type="radio" name="rating" class="text-primary focus:ring-primary size-4 border-slate-300"/>
              <span class="text-sm font-medium flex items-center gap-1">
                <?php echo $r; ?> <span class="material-symbols-outlined text-sm text-amber-400">star</span>
              </span>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <!-- Promo card -->
      <div class="rounded-2xl bg-primary overflow-hidden relative p-6 text-white h-44 flex flex-col justify-end">
        <div class="absolute top-0 right-0 p-4 opacity-20">
          <span class="material-symbols-outlined text-8xl">local_offer</span>
        </div>
        <h4 class="text-xl font-bold relative z-10">Get 20% Off</h4>
        <p class="text-sm opacity-90 relative z-10 mb-4"><?php echo htmlspecialchars($c['promo']); ?></p>
        <button class="w-fit px-4 py-2 bg-white text-primary rounded-lg text-sm font-bold relative z-10">Claim Now</button>
      </div>
    </aside>

    <!-- Main content -->
    <div class="flex-1">
      <div class="flex flex-col sm:flex-row items-center justify-between mb-8 gap-4">
        <div>
          <h1 class="text-3xl font-extrabold tracking-tight"><?php echo htmlspecialchars($c['heading']); ?></h1>
          <p class="text-slate-500"><?php echo count($items); ?> results found</p>
        </div>
        <div class="flex items-center gap-2 bg-white p-1 rounded-xl shadow-sm border border-slate-100">
          <button class="px-4 py-2 bg-primary text-white shadow-sm rounded-lg text-sm font-bold">Recommended</button>
          <button class="px-4 py-2 text-slate-500 text-sm font-bold">Price: Low to High</button>
        </div>
      </div>

      <!-- Cards grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php foreach ($items as $item): ?>
        <div class="group glassy rounded-2xl overflow-hidden flex flex-col hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
          <div class="relative h-56 overflow-hidden">
            <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                 src="<?php echo $item['img']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>"/>
            <button class="absolute top-4 right-4 size-10 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white hover:bg-white/40 transition-colors">
              <span class="material-symbols-outlined">favorite</span>
            </button>
            <?php if ($item['badge']): ?>
            <div class="absolute bottom-4 left-4">
              <span class="px-3 py-1 <?php echo $item['badge_color']; ?> text-white text-xs font-bold rounded-full">
                <?php echo htmlspecialchars($item['badge']); ?>
              </span>
            </div>
            <?php endif; ?>
          </div>
          <div class="p-5 flex flex-col flex-1">
            <div class="flex justify-between items-start mb-2">
              <h3 class="text-base font-bold leading-tight group-hover:text-primary transition-colors">
                <?php echo htmlspecialchars($item['name']); ?>
              </h3>
              <div class="flex items-center gap-1 text-sm font-bold shrink-0 ml-2">
                <span class="material-symbols-outlined text-amber-400 text-lg">star</span>
                <span><?php echo $item['rating']; ?></span>
              </div>
            </div>
            <div class="flex items-center gap-1 text-slate-500 text-sm mb-4">
              <span class="material-symbols-outlined text-sm">location_on</span>
              <span><?php echo htmlspecialchars($item['sub']); ?></span>
            </div>
            <div class="mt-auto flex items-center justify-between border-t border-slate-100 pt-4">
              <div>
                <span class="text-2xl font-black text-primary"><?php echo $item['price']; ?></span>
                <?php if ($c['unit']): ?>
                <span class="text-xs text-slate-400 font-medium"><?php echo htmlspecialchars($c['unit']); ?></span>
                <?php endif; ?>
              </div>
              <?php if ($type === 'buses'): ?>
              <a href="tel:+918600968888" class="px-4 py-2 bg-primary/10 text-primary hover:bg-primary hover:text-white rounded-xl text-sm font-bold transition-all">
                <?php echo htmlspecialchars($c['cta']); ?>
              </a>
              <?php else: ?>
              <button class="px-4 py-2 bg-primary/10 text-primary hover:bg-primary hover:text-white rounded-xl text-sm font-bold transition-all">
                <?php echo htmlspecialchars($c['cta']); ?>
              </button>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Pagination -->
      <div class="mt-12 flex justify-center">
        <div class="flex items-center gap-2 p-1 glassy rounded-xl">
          <button class="size-10 flex items-center justify-center rounded-lg hover:bg-slate-100">
            <span class="material-symbols-outlined">chevron_left</span>
          </button>
          <button class="size-10 flex items-center justify-center rounded-lg bg-primary text-white font-bold">1</button>
          <button class="size-10 flex items-center justify-center rounded-lg hover:bg-slate-100 font-bold">2</button>
          <button class="size-10 flex items-center justify-center rounded-lg hover:bg-slate-100 font-bold">3</button>
          <button class="size-10 flex items-center justify-center rounded-lg hover:bg-slate-100">
            <span class="material-symbols-outlined">chevron_right</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
</main>

<?php require 'footer.php'; ?>
