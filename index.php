<?php
// index.php – CSNExplore Home Page
$page_title = "CSNExplore – Hotels, Bikes, Cars & Attractions in Chhatrapati Sambhajinagar";
$current_page = "home";
require_once 'php/config.php';
$db = getDB();

// Fetch homepage settings
$hp_settings_row = $db->fetchOne("SELECT content FROM about_contact WHERE section = 'homepage'");
$hp_settings = [];
if ($hp_settings_row && !empty($hp_settings_row['content'])) {
    $decoded = json_decode($hp_settings_row['content'], true);
    if (is_array($decoded)) $hp_settings = $decoded;
}
// Defaults
$hp_defaults = [
    'title_attractions' => 'Ancient Marvels',
    'title_bikes'       => 'Quick Bike Rentals',
    'title_restaurants' => 'Taste the City',
    'title_buses'       => 'Travel Your Way',
    'title_blogs'       => 'Travel Insights',
    'title_cars'        => 'Self Drive Cars',
    'title_stays'       => 'Premium Stays',
    'show_attractions'  => true,
    'show_bikes'        => true,
    'show_restaurants'  => true,
    'show_buses'        => true,
    'show_blogs'        => true,
    'show_cars'         => true,
    'show_stays'        => true,
    'hero_subtext'      => 'Stays, cars, bikes, restaurants, attractions and buses — all in one place.',
    'city_intro'        => '',
    'stat1_label'       => '500+ Hotels',
    'stat2_label'       => '50+ Attractions',
    'stat3_label'       => '200+ Restaurants',
    'stat4_label'       => '10K+ Happy Travelers',
    'count_attractions' => 4,
    'count_bikes'       => 4,
    'count_restaurants' => 4,
    'count_buses'       => 2,
    'count_blogs'       => 3,
    'count_cars'        => 4,
    'count_stays'       => 4,
    'layout_attractions'=> '4-col',
    'layout_bikes'      => '4-col',
    'layout_restaurants'=> '3-col',
    'layout_buses'      => '2-col',
    'layout_blogs'      => '3-col',
    'layout_cars'       => '4-col',
    'layout_stays'      => '4-col',
    'section_order'     => ['cars','bikes','attractions','stays','restaurants','buses','blogs'],
    'picks_attractions' => [],
    'picks_bikes'       => [],
    'picks_restaurants' => [],
    'picks_buses'       => [],
    'picks_blogs'       => [],
    'picks_cars'        => [],
    'picks_stays'       => [],
];
foreach ($hp_defaults as $k => $v) {
    if (!isset($hp_settings[$k]) || $hp_settings[$k] === '') {
        $hp_settings[$k] = $v;
    }
}
// Ensure section_order is always a valid array
if (!is_array($hp_settings['section_order']) || count($hp_settings['section_order']) < 5) {
    $hp_settings['section_order'] = ['cars','bikes','attractions','stays','restaurants','buses','blogs'];
}

// Helper: layout string → Tailwind grid/flex class
function hp_grid_class($layout) {
    $map = [
        '3-col' => 'grid grid-cols-1 md:grid-cols-3 gap-5',
        '4-col' => 'grid grid-cols-2 md:grid-cols-4 gap-5',
        '2-col' => 'grid grid-cols-1 md:grid-cols-2 gap-4',
        'list'  => 'flex flex-col gap-3',
        'scroll'=> 'flex gap-5 overflow-x-auto hide-scrollbar pb-3 snap-x snap-mandatory',
    ];
    return $map[$layout] ?? $map['3-col'];
}
// Card wrapper class for scroll layout
function hp_card_wrap($layout) {
    return $layout === 'scroll' ? 'flex-shrink-0 w-64' : '';
}

// Fetch real data from DB — use picks if set, otherwise use saved counts
function hp_fetch_picks($db, $table, $picks, $where_active, $fallback_sql) {
    if (!empty($picks) && is_array($picks)) {
        $ids = implode(',', array_map('intval', $picks));
        $rows = $db->fetchAll("SELECT * FROM {$table} WHERE id IN ({$ids}) AND {$where_active}");
        // Preserve pick order
        $indexed = [];
        foreach ($rows as $r) $indexed[$r['id']] = $r;
        $ordered = [];
        foreach ($picks as $pid) { if (isset($indexed[$pid])) $ordered[] = $indexed[$pid]; }
        return $ordered;
    }
    return $db->fetchAll($fallback_sql);
}

$hp_attractions = hp_fetch_picks($db, 'attractions', $hp_settings['picks_attractions'], 'is_active=1',
    "SELECT * FROM attractions WHERE is_active=1 ORDER BY display_order ASC, rating DESC LIMIT " . (int)$hp_settings['count_attractions']);
$hp_bikes = hp_fetch_picks($db, 'bikes', $hp_settings['picks_bikes'], 'is_active=1',
    "SELECT * FROM bikes WHERE is_active=1 ORDER BY display_order ASC, rating DESC LIMIT " . (int)$hp_settings['count_bikes']);
$hp_restaurants = hp_fetch_picks($db, 'restaurants', $hp_settings['picks_restaurants'], 'is_active=1',
    "SELECT * FROM restaurants WHERE is_active=1 ORDER BY display_order ASC, rating DESC LIMIT " . (int)$hp_settings['count_restaurants']);
$hp_buses = hp_fetch_picks($db, 'buses', $hp_settings['picks_buses'] ?? [], 'is_active=1',
    "SELECT * FROM buses WHERE is_active=1 ORDER BY display_order ASC LIMIT " . (int)$hp_settings['count_buses']);
$hp_blogs = hp_fetch_picks($db, 'blogs', $hp_settings['picks_blogs'] ?? [], "status='published'",
    "SELECT * FROM blogs WHERE status='published' ORDER BY created_at DESC LIMIT " . (int)$hp_settings['count_blogs']);
$hp_cars = hp_fetch_picks($db, 'cars', $hp_settings['picks_cars'] ?? [], 'is_active=1',
    "SELECT * FROM cars WHERE is_active=1 ORDER BY display_order ASC, rating DESC LIMIT " . (int)$hp_settings['count_cars']);
$hp_stays = hp_fetch_picks($db, 'stays', $hp_settings['picks_stays'] ?? [], 'is_active=1',
    "SELECT * FROM stays WHERE is_active=1 ORDER BY display_order ASC, rating DESC LIMIT " . (int)$hp_settings['count_stays']);
?>
<?php
$page_desc = "Your premium gateway to the wonders of Chhatrapati Sambhajinagar, Maharashtra. Book hotels, cars, bikes, and explore attractions easily.";
$extra_head = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css"/><script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>';
$extra_styles = '
        .hide-scrollbar::-webkit-scrollbar { display:none; }
        .hide-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }
        .card-hover:hover { box-shadow:0 0 30px rgba(236,91,19,0.15); }
        :root { --card-w-attractions: 80vw; --card-w-bikes: 80vw; --card-w-restaurants: 80vw; --card-w-buses: 80vw; --card-w-blogs: 80vw; --card-w-cars: 80vw; --card-w-stays: 80vw; }
        @media (min-width: 640px) { :root { --card-w-attractions: calc(50% - 14px); --card-w-bikes: calc(50% - 14px); --card-w-restaurants: calc(50% - 14px); --card-w-buses: calc(50% - 14px); --card-w-blogs: calc(50% - 14px); --card-w-cars: calc(50% - 14px); --card-w-stays: calc(50% - 14px); } }
        @media (min-width: 1024px) { :root { --card-w-attractions: calc(25% - 15px); --card-w-bikes: calc(25% - 15px); --card-w-restaurants: calc(20% - 16px); --card-w-buses: calc(50% - 10px); --card-w-blogs: calc(33.333% - 14px); --card-w-cars: calc(25% - 15px); --card-w-stays: calc(25% - 15px); } }
        #hero-label, #hero-pre, #hero-highlight, #hero-post, #hero-desc { transition: opacity 0.25s ease; }
        .search-box { background:rgba(255,255,255,0.08); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,0.15); border-radius:16px; padding:20px 22px; }
        .tab-btn { display:flex; align-items:center; gap:6px; padding:8px 16px; border-radius:50px; font-size:13px; font-weight:700; color:rgba(255,255,255,0.55); cursor:pointer; transition:all .2s; border:none; background:transparent; white-space:nowrap; }
        .tab-btn:hover { color:#fff; background:rgba(255,255,255,0.08); }
        .tab-btn.active { color:#fff; background:#ec5b13; box-shadow:0 3px 10px rgba(236,91,19,0.35); }
        .tab-btn .material-symbols-outlined { font-size:17px; }
        .search-field { display:flex; align-items:center; gap:9px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.12); border-radius:12px; padding:0 16px; flex:1; min-width:0; height:54px; transition:border-color .2s; }
        .search-field:focus-within { border-color:#ec5b13; }
        .search-field .material-symbols-outlined { color:rgba(255,255,255,0.4); font-size:20px; flex-shrink:0; }
        .search-field input { background:transparent; border:none; outline:none; color:#fff; font-size:15px; font-weight:500; width:100%; min-width:0; box-shadow:none; -webkit-appearance:none; }
        .search-field input::placeholder { color:rgba(255,255,255,0.4); }
        .date-field { display:flex; align-items:center; gap:9px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.12); border-radius:12px; padding:0 16px; cursor:pointer; transition:border-color .2s; flex:1; min-width:0; height:54px; }
        .date-field:focus-within { border-color:#ec5b13; }
        .date-field .material-symbols-outlined { color:rgba(255,255,255,0.4); font-size:20px; flex-shrink:0; }
        .date-field input { background:transparent; border:none; outline:none; color:#fff; font-size:15px; font-weight:500; width:100%; cursor:pointer; min-width:0; box-shadow:none; -webkit-appearance:none; }
        .date-field input::placeholder { color:rgba(255,255,255,0.4); }
        .search-row { display:flex; gap:10px; align-items:center; flex-wrap:wrap; width:100%; }
        .search-btn { background:#ec5b13; color:#fff; font-weight:800; font-size:15px; padding:0 28px; border-radius:12px; border:none; cursor:pointer; display:flex; align-items:center; gap:6px; transition:background .2s; white-space:nowrap; flex-shrink:0; height:54px; }
        .search-btn:hover { background:#d44e0e; }
        .search-btn .material-symbols-outlined { font-size:18px; }
        @media(max-width:768px){
          .search-box { padding:12px; border-radius:12px; }
          .search-row { flex-wrap:wrap; gap:6px; }
          .search-field { flex:1 1 100%; height:44px; padding:0 12px; }
          .search-field .material-symbols-outlined { font-size:18px; }
          .search-field input { font-size:14px; }
          .date-field { flex:1 1 calc(50% - 3px); height:44px; min-width:0; padding:0 12px; }
          .date-field .material-symbols-outlined { font-size:18px; }
          .date-field input { font-size:14px; }
          .search-btn { width:100%; justify-content:center; height:44px; font-size:14px; padding:0 16px; border-radius:12px; }
          .tab-btn { padding:7px 14px; font-size:12px; flex:0 0 auto; justify-content:center; }
          .tab-btn .material-symbols-outlined { font-size:15px; }
          #search-tabs-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; display: flex; flex-wrap: nowrap; gap: 6px; padding-bottom: 4px; justify-content: flex-start; scroll-snap-type: x mandatory; margin-bottom: 10px; }
          .tab-btn { scroll-snap-align: start; }
          #search-tabs-scroll::-webkit-scrollbar { display: none; }
        }
        @media(max-width:400px){ .date-field { flex:1 1 100%; } }
        .search-panel { display:none; }
        .search-panel.active { display:flex; flex-direction:column; gap:8px; }
        .flatpickr-calendar { background:#1c1410 !important; border:1px solid rgba(236,91,19,0.3) !important; border-radius:16px !important; box-shadow:0 20px 60px rgba(0,0,0,0.6) !important; }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange { background:#ec5b13 !important; border-color:#ec5b13 !important; }
        .flatpickr-day.inRange { background:rgba(236,91,19,0.2) !important; border-color:transparent !important; }
        .flatpickr-day:hover { background:rgba(236,91,19,0.3) !important; }
        .flatpickr-months .flatpickr-month, .flatpickr-current-month { color:#fff !important; }
        .flatpickr-weekday { color:rgba(255,255,255,0.5) !important; }
        .flatpickr-day { color:#fff !important; }
        .flatpickr-day.flatpickr-disabled { color:rgba(255,255,255,0.2) !important; }
        .flatpickr-prev-month svg, .flatpickr-next-month svg { fill:#fff !important; }
        #hero-bg { will-change: transform; }
        .particle { position:absolute; border-radius:50%; pointer-events:none; animation:particleDrift linear infinite; }
        @keyframes particleDrift { 0% { transform:translateY(0) translateX(0) scale(1); opacity:0; } 10% { opacity:1; } 90% { opacity:0.6; } 100% { transform:translateY(-120vh) translateX(30px) scale(0.5); opacity:0; } }
        .stat-num { display:inline-block; }
        .wave-divider { line-height:0; overflow:hidden; }
        .gradient-text { background:linear-gradient(135deg,#ec5b13,#ff8c42); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        .glow-badge { box-shadow:0 0 20px rgba(236,91,19,0.4); }
';
require 'header.php';
?>

<main>
<!-- Hero -->
<section class="relative min-h-[100svh] md:min-h-[85vh] flex flex-col items-center justify-center overflow-hidden pt-20 md:pt-24 pb-8 md:pb-12 w-full">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-b from-black/75 via-black/50 to-[#0a0705] z-10"></div>
        <div id="hero-bg" class="w-full h-full bg-cover bg-center transition-all duration-1000"
             style="background-image:url('https://lh3.googleusercontent.com/aida-public/AB6AXuDfTDZo8LglfsdX1vCy-PfHltcZor3jl-l4xxrXMYSU-zLgoKXxY-ouUImyR0WZq69V0y63PE1wDL2_EfqYwWhgQOHPVDJVHhyGGB7H8kZNyboNAXVxWDvlFW_Z_QRXuTKMBuuk7a9HgI3Gde3PidzWIcOhtgs4QAHX2DHA2V6QUaFo6mYDZzEhvq1Y7FwjBSsjTNmfwco23Zfvdb8laeVoTMZHDGMoMrH3yPn4aQDHZ9AJE-WXiuWGVG-c0BegSoJwB1zEXVVWIUie')">
        </div>
        <!-- Floating orbs -->
        <div class="orb w-96 h-96 bg-primary/20 top-1/4 -left-24 z-[5]" style="animation-delay:0s"></div>
        <div class="orb w-64 h-64 bg-orange-400/10 bottom-1/3 right-10 z-[5]" style="animation-delay:3s"></div>
        <!-- Particles container -->
        <div id="particles" class="absolute inset-0 z-[6] overflow-hidden"></div>    </div>
    <div class="relative z-20 text-center px-4 w-full max-w-[1140px] mx-auto pt-4 md:pt-8 pb-4 md:pb-6">
        <p id="hero-label" class="text-primary font-bold text-[10px] md:text-xs uppercase tracking-widest mb-2 md:mb-3">Chhatrapati Sambhajinagar</p>
        <h1 class="font-serif text-2xl sm:text-3xl md:text-5xl lg:text-6xl text-white mb-3 md:mb-4 leading-tight font-black px-2">
            <span id="hero-pre">Explore </span><span class="text-primary" id="hero-highlight">Your City</span><span id="hero-post"> Your Way</span>
        </h1>
        <p id="hero-desc" class="text-white/70 text-xs sm:text-sm md:text-lg mb-6 md:mb-8 lg:mb-10 max-w-2xl mx-auto px-2"><?php echo htmlspecialchars($hp_settings['hero_subtext']); ?></p>

        <!-- Search Box -->
        <div class="search-box max-w-4xl mx-auto w-full">
            <div id="search-tabs-scroll" class="flex sm:flex-wrap md:flex-wrap gap-1.5 mb-4 pb-3 border-b border-white/10 sm:justify-center overflow-x-auto hide-scrollbar w-full">
                <?php
                $tabs = [
                    ['id' => 'stays',       'icon' => 'bed',                  'label' => 'Stays'],
                    ['id' => 'cars',        'icon' => 'directions_car',        'label' => 'Cars'],
                    ['id' => 'bikes',       'icon' => 'motorcycle',            'label' => 'Bikes'],
                    ['id' => 'attractions', 'icon' => 'confirmation_number',   'label' => 'Attractions'],
                    ['id' => 'dine',        'icon' => 'restaurant',            'label' => 'Dine'],
                    ['id' => 'buses',       'icon' => 'directions_bus',        'label' => 'Buses'],
                ];
                foreach ($tabs as $i => $tab): ?>
                    <button class="tab-btn <?php echo $i === 0 ? 'active' : ''; ?>" data-tab="<?php echo $tab['id']; ?>" onclick="switchTab('<?php echo $tab['id']; ?>')">
                        <span class="material-symbols-outlined"><?php echo $tab['icon']; ?></span><?php echo $tab['label']; ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- CARS panel -->
            <div id="panel-cars" class="search-panel active">
                <div class="search-row" style="gap: 16px;">
                    <div class="search-field"><span class="material-symbols-outlined">trip_origin</span><input id="cars-pickup" type="text" placeholder="Chhatrapati Sambhajinagar" value="Chhatrapati Sambhajinagar"/></div>
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="cars-drop" type="text" placeholder="Drop location"/></div>
                    <div class="date-field" onclick="document.getElementById('cars-date').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="cars-date" type="text" placeholder="Select date" readonly/></div>
                    <button class="search-btn" onclick="doSearch('cars')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
            <!-- STAYS panel -->
            <div id="panel-stays" class="search-panel">
                <div class="search-row" style="gap: 16px;">
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="stays-location" type="text" placeholder="Chhatrapati Sambhajinagar" value="Chhatrapati Sambhajinagar"/></div>
                    <div class="date-field" onclick="document.getElementById('stays-checkin').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="stays-checkin" type="text" placeholder="Check-in" readonly/></div>
                    <div class="date-field" onclick="document.getElementById('stays-checkout').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="stays-checkout" type="text" placeholder="Check-out" readonly/></div>
                    <button class="search-btn" onclick="doSearch('stays')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
            <!-- BIKES panel -->
            <div id="panel-bikes" class="search-panel">
                <div class="search-row" style="gap: 16px;">
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="bikes-location" type="text" placeholder="Chhatrapati Sambhajinagar" value="Chhatrapati Sambhajinagar"/></div>
                    <div class="date-field" onclick="document.getElementById('bikes-date').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="bikes-date" type="text" placeholder="From date" readonly/></div>
                    <div class="date-field" onclick="document.getElementById('bikes-return').focus()"><span class="material-symbols-outlined">event_available</span><input id="bikes-return" type="text" placeholder="Return date" readonly/></div>
                    <button class="search-btn" onclick="doSearch('bikes')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
            <!-- ATTRACTIONS panel -->
            <div id="panel-attractions" class="search-panel">
                <div class="search-row" style="gap: 16px;">
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="attractions-location" type="text" placeholder="Chhatrapati Sambhajinagar" value="Chhatrapati Sambhajinagar"/></div>
                    <div class="date-field" onclick="document.getElementById('attractions-date').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="attractions-date" type="text" placeholder="Select date" readonly/></div>
                    <button class="search-btn" onclick="doSearch('attractions')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
            <!-- DINE panel -->
            <div id="panel-dine" class="search-panel">
                <div class="search-row" style="gap: 16px;">
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="dine-location" type="text" placeholder="Chhatrapati Sambhajinagar" value="Chhatrapati Sambhajinagar"/></div>
                    <div class="date-field" onclick="document.getElementById('dine-date').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="dine-date" type="text" placeholder="Select date" readonly/></div>
                    <button class="search-btn" onclick="doSearch('dine')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
            <!-- BUSES panel -->
            <div id="panel-buses" class="search-panel">
                <div class="search-row" style="gap: 16px;">
                    <div class="search-field"><span class="material-symbols-outlined">trip_origin</span><input id="buses-from" type="text" placeholder="Chhatrapati Sambhajinagar" value="Chhatrapati Sambhajinagar"/></div>
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="buses-to" type="text" placeholder="Destination city"/></div>
                    <div class="date-field" onclick="document.getElementById('buses-date').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="buses-date" type="text" placeholder="Select date" readonly/></div>
                    <button class="search-btn" onclick="doSearch('buses')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function switchTab(tab, fromAuto) {
    document.querySelectorAll('.tab-btn').forEach(function(b){ b.classList.remove('active'); });
    document.querySelector('[data-tab="'+tab+'"]').classList.add('active');
    document.querySelectorAll('.search-panel').forEach(function(p){ p.classList.remove('active'); });
    document.getElementById('panel-'+tab).classList.add('active');
    var heroData = {
        cars:        { img: 'images/car-rental-hero-section%20(3).webp', label:'Rent a Car',            pre:'Drive in ',   highlight:'Premium Style',     post:' Today',       desc:'Luxury sedans, SUVs and hatchbacks with professional chauffeurs at your service.' },
        bikes:       { img: 'images/bike%20rentals-hero-section%20(6).webp', label:'Rent a Bike',           pre:'Ride ',       highlight:'The Open Road',     post:' Your Way',    desc:'Scooters, cruisers and sports bikes — ride the city your way, anytime.' },
        attractions: { img: 'images/attractions-hero-section%20(7).webp', label:'Discover Attractions',  pre:'Explore ',    highlight:'Ancient Marvels',   post:' Around You',  desc:'Ellora, Ajanta, Bibi Ka Maqbara and more — heritage wonders await you.' },
        stays:       { img: 'images/hotel-hero-section%20(4).webp', label:'Find Your Stay',       pre:'Discover ',   highlight:'Perfect Hotels',    post:' Near You',    desc:'The best hotels, guesthouses and homestays in Chhatrapati Sambhajinagar.' },
        dine:        { img: 'images/dine-hero-section%20(1).webp', label:'Taste the City',        pre:'Savour ',     highlight:'Local Flavours',    post:' Tonight',     desc:'From Mughlai feasts to street food — find the best restaurants near you.' },
        buses:       { img: 'images/bus-hero-section%20(2).webp', label:'Book a Bus',            pre:'Travel ',     highlight:'Your Way',          post:' Comfortably', desc:'AC sleepers, Volvo coaches and MSRTC buses to and from Sambhajinagar.' }
    };
    var d = heroData[tab];
    
    // Update background image
    if (d.img) document.getElementById('hero-bg').style.backgroundImage = "url('" + d.img + "')";

    ['hero-label','hero-pre','hero-highlight','hero-post','hero-desc'].forEach(function(id){ document.getElementById(id).style.opacity='0'; });
    setTimeout(function(){
        document.getElementById('hero-label').textContent = d.label;
        document.getElementById('hero-pre').textContent = d.pre;
        document.getElementById('hero-highlight').textContent = d.highlight;
        document.getElementById('hero-post').textContent = d.post;
        document.getElementById('hero-desc').textContent = d.desc;
        ['hero-label','hero-pre','hero-highlight','hero-post','hero-desc'].forEach(function(id){ document.getElementById(id).style.opacity='1'; });
    }, 250);
    
    // Stop auto-rotation when user manually selects a tab
    if (!fromAuto) {
        if (window.heroInterval) {
            clearInterval(window.heroInterval);
            window.heroInterval = null;
        }
    }
}

// Auto-rotation functionality
var tabsList = ['cars', 'bikes', 'attractions', 'stays', 'dine', 'buses'];
var currentTabIndex = 0;
function autoSwitch() {
    currentTabIndex = (currentTabIndex + 1) % tabsList.length;
    switchTab(tabsList[currentTabIndex], true);
}
window.heroInterval = setInterval(autoSwitch, 3000);

// Initialize with the first background (cars)
document.getElementById('hero-bg').style.backgroundImage = "url('images/car-rental-hero-section%20(3).webp')";

var searchUrls = { stays:'<?php echo BASE_PATH; ?>/listing/stays', cars:'<?php echo BASE_PATH; ?>/listing/cars', bikes:'<?php echo BASE_PATH; ?>/listing/bikes', attractions:'<?php echo BASE_PATH; ?>/listing/attractions', dine:'<?php echo BASE_PATH; ?>/listing/restaurants', buses:'<?php echo BASE_PATH; ?>/listing/buses' };
function doSearch(tab) {
    window.location.href = searchUrls[tab];
}
document.addEventListener('DOMContentLoaded', function() {    var today = new Date(), tomorrow = new Date(today);
    tomorrow.setDate(today.getDate()+1);
    var opts = { dateFormat:'d M Y', minDate:'today', disableMobile:false };
    var fpCI = flatpickr('#stays-checkin', Object.assign({},opts,{ defaultDate:today, onChange:function(s){ if(s[0]){ var n=new Date(s[0]); n.setDate(n.getDate()+1); fpCO.set('minDate',n); if(!fpCO.selectedDates[0]||fpCO.selectedDates[0]<=s[0]) fpCO.setDate(n); } } }));
    var fpCO = flatpickr('#stays-checkout', Object.assign({},opts,{ defaultDate:tomorrow }));
    flatpickr('#cars-date', Object.assign({},opts,{ defaultDate:today }));
    var fpBD = flatpickr('#bikes-date', Object.assign({},opts,{ defaultDate:today, onChange:function(s){ if(s[0]){ var n=new Date(s[0]); n.setDate(n.getDate()+1); fpBR.set('minDate',n); if(!fpBR.selectedDates[0]||fpBR.selectedDates[0]<=s[0]) fpBR.setDate(n); } } }));
    var fpBR = flatpickr('#bikes-return', Object.assign({},opts,{ defaultDate:tomorrow }));
    flatpickr('#attractions-date', Object.assign({},opts,{ defaultDate:today }));
    flatpickr('#dine-date', Object.assign({},opts,{ defaultDate:today }));
    flatpickr('#buses-date', Object.assign({},opts,{ defaultDate:today }));
});

// Banner Text Auto-Rotation
document.addEventListener('DOMContentLoaded', function() {
    var bannerData = [
        {
            tracking: "Explore Your Way",
            heading: "Experience the essence of <span class='bg-gradient-to-r from-primary to-orange-400 bg-clip-text text-transparent italic px-1'>Maharashtra.</span>",
            quote: "\"Chhatrapati Sambhajinagar is more than a city; it's a living museum of ancient artistry.\""
        },
        {
            tracking: "Uncover Hidden Gems",
            heading: "Journey through <span class='bg-gradient-to-r from-primary to-orange-400 bg-clip-text text-transparent italic px-1'>Time.</span>",
            quote: "\"From majestic forts to silent caves, every stone here tells a forgotten tale.\""
        },
        {
            tracking: "Adventure Awaits",
            heading: "Feel the pulse of <span class='bg-gradient-to-r from-primary to-orange-400 bg-clip-text text-transparent italic px-1'>The Deccan.</span>",
            quote: "\"Taste the vibrant culture and escape into the ultimate local adventure.\""
        },
        {
            tracking: "Your Premium Guide",
            heading: "Travel seamlessly <span class='bg-gradient-to-r from-primary to-orange-400 bg-clip-text text-transparent italic px-1'>Everywhere.</span>",
            quote: "\"Premium stays, fast rides, and flawless itineraries, all curated just for you.\""
        }
    ];
    var bannerIndex = 0;
    var container = document.getElementById('banner-text-container');
    
    if (container) {
        setInterval(function() {
            bannerIndex = (bannerIndex + 1) % bannerData.length;
            
            // Fade out
            container.style.opacity = '0';
            container.style.transform = 'translateY(10px)';
            
            setTimeout(function() {
                var d = bannerData[bannerIndex];
                document.getElementById('banner-tracking').innerHTML = d.tracking;
                document.getElementById('banner-heading').innerHTML = d.heading;
                document.getElementById('banner-quote').innerHTML = d.quote;
                
                // Fade in
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 500); // Matches transition duration
        }, 3000); // changes every 3 seconds
    }
});
</script>


<!-- Banner Section -->
<section class="py-16 bg-white overflow-hidden">
    <div class="max-w-[1140px] mx-auto px-5">
        <div class="flex flex-col lg:flex-row items-center gap-12 mb-16">
            <div class="flex-1 transition-all duration-500" id="banner-text-container" style="opacity: 1; transform: translateY(0);" data-reveal data-reveal="left">
                <p id="banner-tracking" class="text-primary font-bold text-xs uppercase tracking-widest mb-2">Explore Your Way</p>
                <h2 id="banner-heading" class="font-serif text-3xl md:text-5xl text-slate-900 leading-tight mb-6">Experience the essence of <span class="bg-gradient-to-r from-primary to-orange-400 bg-clip-text text-transparent italic px-1">Maharashtra.</span></h2>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-primary mt-2"></div>
                        <p id="banner-quote" class="text-slate-600 text-sm italic">"Chhatrapati Sambhajinagar is more than a city; it's a living museum of ancient artistry."</p>
                    </div>
                </div>
            </div>
            <div class="flex-1 hidden sm:grid grid-cols-2 gap-4">
                <!-- Car Rentals Card -->
                <div data-reveal data-reveal="right" class="group relative overflow-hidden rounded-2xl h-40 sm:h-64 shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
                    <img alt="Car Rentals" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=800&q=80"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-3 sm:p-5">
                        <h4 class="text-white text-sm sm:text-lg font-bold mb-1">Car Rentals</h4>
                        <a href="<?php echo BASE_PATH; ?>/listing/cars" class="text-white/80 text-xs hover:text-white transition-colors flex items-center gap-1">Browse Cars <span class="material-symbols-outlined text-[10px]">arrow_forward</span></a>
                    </div>
                </div>
                <!-- Bike Rentals Card -->
                <div data-reveal data-reveal="right" class="group relative overflow-hidden rounded-2xl h-40 sm:h-64 shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
                    <img alt="Bike Rentals" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?w=800&q=80"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-3 sm:p-5">
                        <h4 class="text-white text-sm sm:text-lg font-bold mb-1">Bike Rentals</h4>
                        <a href="<?php echo BASE_PATH; ?>/listing/bikes" class="text-white/80 text-xs hover:text-white transition-colors flex items-center gap-1">Browse Bikes <span class="material-symbols-outlined text-[10px]">arrow_forward</span></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Highlight Cards (Inside Banner Section) -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <?php 
            $highlights = [
                ['icon'=>'hotel', 'label'=>'500+ Hotels', 'sub' => 'Premium Stays'],
                ['icon'=>'directions_car', 'label'=>'50+ Rentals', 'sub' => 'Cars & Bikes'],
                ['icon'=>'restaurant', 'label'=>'200+ Eateries', 'sub' => 'Best Dining'],
                ['icon'=>'map', 'label'=>'50+ Wonders', 'sub' => 'Heritage Sites'],
                ['icon'=>'groups', 'label'=>'10K+ Travelers', 'sub' => 'Happy Guests']
            ];
            foreach($highlights as $h): ?>
            <div data-reveal class="bg-white p-6 rounded-2xl shadow-xl shadow-black/5 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 border border-slate-100 group flex flex-col items-center text-center">
                <div class="size-11 rounded-xl bg-orange-50 flex items-center justify-center mb-4 group-hover:bg-primary transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-primary group-hover:text-white transition-colors text-2xl"><?php echo $h['icon']; ?></span>
                </div>
                <h4 class="text-slate-900 font-bold text-sm leading-tight italic"><?php echo $h['label']; ?></h4>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mt-1.5"><?php echo $h['sub']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (!empty($hp_settings['city_intro'])): ?>
<!-- City Intro -->
<div class="bg-white py-10">
    <div class="max-w-[1140px] mx-auto px-5">
        <p class="text-slate-600 text-base md:text-lg leading-relaxed text-center max-w-3xl mx-auto">
            <?php echo nl2br(htmlspecialchars($hp_settings['city_intro'])); ?>
        </p>
    </div>
</div>
<?php endif; ?>


<?php
// Base counts — if more items than this are shown, switch to horizontal scroll
$_sec_base_counts = [
    'attractions' => 4,
    'bikes'       => 4,
    'restaurants' => 6,
    'buses'       => 2,
    'blogs'       => 3,
    'cars'        => 4,
    'stays'       => 4,
];

// ── Render sections in saved order ───────────────────────────────────────────
$_sec_bg_toggle = false;
foreach ($hp_settings['section_order'] as $_sec_key):
    if (empty($hp_settings['show_' . $_sec_key])) continue;
    $_layout = $hp_settings['layout_' . $_sec_key];
    // If more items than base count → force horizontal scroll
    $_sec_items_map = ['attractions'=>$hp_attractions,'bikes'=>$hp_bikes,'restaurants'=>$hp_restaurants,'buses'=>$hp_buses,'blogs'=>$hp_blogs,'cars'=>$hp_cars,'stays'=>$hp_stays];
    $_sec_item_count = count($_sec_items_map[$_sec_key] ?? []);
    $_base = $_sec_base_counts[$_sec_key] ?? 4;
    if ($_sec_item_count > $_base) {
        $_layout = 'scroll';
    }
    $_grid   = hp_grid_class($_layout);
    $_bg     = $_sec_bg_toggle ? 'bg-white' : 'bg-slate-50';
    $_sec_bg_toggle = !$_sec_bg_toggle;
?>
<section class="py-12 <?php echo $_bg; ?>">
    <div class="max-w-[1140px] mx-auto px-5">
        <div class="flex items-end justify-between mb-6" data-reveal>
            <?php if ($_sec_key === 'blogs'): ?>
            <div>
                <p class="text-primary font-bold text-xs uppercase tracking-widest mb-1">Our Travel Journals</p>
                <h2 class="font-serif text-2xl md:text-3xl text-slate-900"><?php echo htmlspecialchars($hp_settings['title_blogs']); ?></h2>
            </div>
            <a href="<?php echo BASE_PATH; ?>/blogs" class="text-sm font-bold text-primary hover:underline">Read more &rarr;</a>
            <?php else: ?>
            <div>
                <p class="text-primary font-bold text-xs uppercase tracking-widest mb-1"><?php
                    $sec_subtitles = ['attractions'=>'Heritage & Culture','bikes'=>'Two-Wheeler Rentals','restaurants'=>'Food & Dining','buses'=>'Intercity Travel','cars'=>'Self-Drive & Taxis','stays'=>'Hotels & Resorts'];
                    echo $sec_subtitles[$_sec_key] ?? 'Explore';
                ?></p>
                <h2 class="font-serif text-2xl md:text-3xl text-slate-900"><?php echo htmlspecialchars($hp_settings['title_' . $_sec_key]); ?></h2>
            </div>
            <a href="<?php echo BASE_PATH; ?>/listing/<?php echo $_sec_key; ?>" class="text-sm font-bold text-primary hover:underline">See all &rarr;</a>
            <?php endif; ?>
        </div>
        <?php
        // ── Visible-cards-per-section config ─────────────────────────────────
        $_vis = ['attractions'=>4,'bikes'=>4,'restaurants'=>4,'buses'=>2,'blogs'=>3];
        $_vis_count = $_vis[$_sec_key] ?? 4;
        // Mobile: fixed 80vw so ~1.1 cards visible. Desktop: percentage of container.
        $_card_w = 'var(--card-w-' . $_sec_key . ')';

        if ($_sec_key === 'attractions'):
            $render_fn = function($a) {
                $slug = BASE_PATH . '/listing-detail/' . generateSlug('attractions', $a['id'], $a['name']) . '.html';
                $img = strpos($a['image'] ?? '', 'http') === 0 ? htmlspecialchars($a['image']) : BASE_PATH . '/' . ltrim(htmlspecialchars($a['image'] ?? ''), '/');
                $name=htmlspecialchars($a['name']);
                $tag=htmlspecialchars($a['type']??'Attraction');
                $price=$a['entry_fee']>0 ? '&#8377;'.number_format($a['entry_fee']) : 'Free';
                $rating=number_format((float)($a['rating']??0),1);
                return '<a href="'.$slug.'" class="group relative overflow-hidden rounded-2xl bg-white border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex-shrink-0" style="width:VAR_W">'
                    .'<div class="h-44 overflow-hidden relative"><img alt="'.$name.'" loading="lazy" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="'.$img.'"/>'
                    .'<div class="absolute top-2.5 right-2.5 flex items-center gap-1 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2 py-0.5 rounded-full z-20"><span style="font-family:Material Symbols Outlined;font-size:12px;color:#fbbf24">star</span>'.$rating.'</div></div>'
                    .'<div class="p-4"><span class="text-primary text-[10px] font-bold uppercase tracking-widest relative z-20">'.$tag.'</span>'
                    .'<h5 class="font-serif text-base text-slate-900 mt-1 mb-3 line-clamp-1 relative z-20">'.$name.'</h5>'
                    .'<div class="flex items-center justify-between relative z-20">'
                    .'<p class="font-black text-slate-900 text-sm">'.$price.' <span class="text-xs text-slate-400 font-normal">entry</span></p>'
                    .'<span class="bg-primary text-white px-3 py-1.5 rounded-full font-bold text-xs group-hover:bg-orange-600 transition-all">Check Details</span>'
                    .'</div></div></a>';
            };
            $items = $hp_attractions;
        elseif ($_sec_key === 'bikes'):
            $render_fn = function($b) {
                $slug = BASE_PATH . '/listing-detail/' . generateSlug('bikes', $b['id'], $b['name']) . '.html';
                $img = strpos($b['image'] ?? '', 'http') === 0 ? htmlspecialchars($b['image']) : BASE_PATH . '/' . ltrim(htmlspecialchars($b['image'] ?? ''), '/');
                $name=htmlspecialchars($b['name']);
                $type=htmlspecialchars($b['type']); $price=number_format($b['price_per_day']);
                $rating=number_format((float)($b['rating']??0),1);
                return '<a href="'.$slug.'" class="group overflow-hidden rounded-2xl bg-white border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex-shrink-0" style="width:VAR_W">'
                    .'<div class="h-44 overflow-hidden relative"><img alt="'.$name.'" loading="lazy" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="'.$img.'"/>'
                    .'<div class="absolute top-2.5 right-2.5 flex items-center gap-1 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2 py-0.5 rounded-full"><span style="font-family:Material Symbols Outlined;font-size:12px;color:#fbbf24">star</span>'.$rating.'</div></div>'
                    .'<div class="p-4"><span class="text-primary text-[10px] font-bold uppercase tracking-widest">'.$type.'</span>'
                    .'<h5 class="font-serif text-base text-slate-900 mt-1 mb-3 line-clamp-1">'.$name.'</h5>'
                    .'<div class="flex items-center justify-between">'
                    .'<p class="font-black text-slate-900 text-sm">&#8377;'.$price.' <span class="text-xs text-slate-400 font-normal">/day</span></p>'
                    .'<span class="bg-primary text-white px-3 py-1.5 rounded-full font-bold text-xs group-hover:bg-orange-600 transition-all">Check Availability</span>'
                    .'</div></div></a>';
            };
            $items = $hp_bikes;
        elseif ($_sec_key === 'restaurants'):
            $render_fn = function($r) {
                $slug = BASE_PATH . '/listing-detail/' . generateSlug('restaurants', $r['id'], $r['name']) . '.html';
                $img = strpos($r['image'] ?? '', 'http') === 0 ? htmlspecialchars($r['image']) : BASE_PATH . '/' . ltrim(htmlspecialchars($r['image'] ?? ''), '/');
                $name=htmlspecialchars($r['name']);
                $cuisine=htmlspecialchars($r['cuisine']??$r['type']); $price=number_format($r['price_per_person']??0);
                $rating=number_format((float)($r['rating']??0),1);
                return '<a href="'.$slug.'" class="group relative overflow-hidden rounded-2xl bg-white border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex-shrink-0" style="width:VAR_W">'
                    .'<div class="h-44 overflow-hidden relative"><img alt="'.$name.'" loading="lazy" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="'.$img.'"/>'
                    .'<div class="absolute top-2.5 right-2.5 flex items-center gap-1 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2 py-0.5 rounded-full z-20"><span style="font-family:Material Symbols Outlined;font-size:12px;color:#fbbf24">star</span>'.$rating.'</div></div>'
                    .'<div class="p-4"><span class="text-primary text-[10px] font-bold uppercase tracking-widest relative z-20">'.$cuisine.'</span>'
                    .'<h5 class="font-serif text-base text-slate-900 mt-1 mb-2 line-clamp-1 relative z-20">'.$name.'</h5>'
                    .'<div class="flex items-center gap-1 text-slate-500 text-xs mb-3 relative z-20"><span style="font-family:Material Symbols Outlined;font-size:14px">location_on</span><span class="line-clamp-1">'.htmlspecialchars($r['location']??'').'</span></div>'
                    .'<div class="space-y-1.5 text-xs text-slate-600 mb-3 relative z-20">'
                    .'<div class="flex items-center gap-2"><span style="font-family:Material Symbols Outlined;font-size:14px;color:#ec5b13">verified</span><span class="font-semibold">Verified</span></div>'
                    .'<div class="flex items-center gap-2"><span style="font-family:Material Symbols Outlined;font-size:14px;color:#ec5b13">cancel</span><span>Free cancellation</span></div>'
                    .'<div class="flex items-center gap-2"><span style="font-family:Material Symbols Outlined;font-size:14px;color:#ec5b13">info</span><span>No hidden charges</span></div>'
                    .'</div>'
                    .'<div class="flex items-center justify-between gap-3 relative z-20 border-t border-slate-100 pt-3">'
                    .'<p class="font-black text-slate-900 text-sm">&#8377;'.$price.' <span class="text-xs text-slate-400 font-normal">for two</span></p>'
                    .'<span class="bg-primary text-white px-4 py-1.5 rounded-full font-bold text-xs group-hover:bg-orange-600 transition-all whitespace-nowrap">Check Details</span>'
                    .'</div></div></a>';
            };
            $items = $hp_restaurants;
        elseif ($_sec_key === 'cars'):
            $render_fn = function($c) {
                $slug = BASE_PATH . '/listing-detail/' . generateSlug('cars', $c['id'], $c['name']) . '.html';
                $img = strpos($c['image'] ?? '', 'http') === 0 ? htmlspecialchars($c['image']) : BASE_PATH . '/' . ltrim(htmlspecialchars($c['image'] ?? ''), '/');
                $name=htmlspecialchars($c['name']);
                $type=htmlspecialchars($c['type']??'Sedan'); $price=number_format($c['price_per_day']??0);
                $rating=number_format((float)($c['rating']??0),1);
                return '<a href="'.$slug.'" class="group overflow-hidden rounded-2xl bg-white border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex-shrink-0" style="width:VAR_W">'
                    .'<div class="h-44 overflow-hidden relative"><img alt="'.$name.'" loading="lazy" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="'.$img.'"/>'
                    .'<div class="absolute top-2.5 right-2.5 flex items-center gap-1 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2 py-0.5 rounded-full"><span style="font-family:Material Symbols Outlined;font-size:12px;color:#fbbf24">star</span>'.$rating.'</div></div>'
                    .'<div class="p-4"><span class="text-primary text-[10px] font-bold uppercase tracking-widest">'.$type.'</span>'
                    .'<h5 class="font-serif text-base text-slate-900 mt-1 mb-3 line-clamp-1">'.$name.'</h5>'
                    .'<div class="flex items-center justify-between">'
                    .'<p class="font-black text-slate-900 text-sm">&#8377;'.$price.' <span class="text-xs text-slate-400 font-normal">/day</span></p>'
                    .'<span class="bg-primary text-white px-3 py-1.5 rounded-full font-bold text-xs group-hover:bg-orange-600 transition-all">Check Availability</span>'
                    .'</div></div></a>';
            };
            $items = $hp_cars;
        elseif ($_sec_key === 'stays'):
            $render_fn = function($s) {
                $slug = BASE_PATH . '/listing-detail/' . generateSlug('stays', $s['id'], $s['name']) . '.html';
                $img = strpos($s['image'] ?? '', 'http') === 0 ? htmlspecialchars($s['image']) : BASE_PATH . '/' . ltrim(htmlspecialchars($s['image'] ?? ''), '/');
                $name=htmlspecialchars($s['name']);
                $type=htmlspecialchars($s['type']??'Hotel'); $price=number_format($s['price_per_night']??0);
                $rating=number_format((float)($s['rating']??0),1);
                return '<a href="'.$slug.'" class="group relative overflow-hidden rounded-2xl bg-white border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex-shrink-0" style="width:VAR_W">'
                    .'<div class="h-44 overflow-hidden relative"><img alt="'.$name.'" loading="lazy" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="'.$img.'"/>'
                    .'<div class="absolute top-2.5 right-2.5 flex items-center gap-1 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2 py-0.5 rounded-full z-20"><span style="font-family:Material Symbols Outlined;font-size:12px;color:#fbbf24">star</span>'.$rating.'</div></div>'
                    .'<div class="p-4"><span class="text-primary text-[10px] font-bold uppercase tracking-widest relative z-20">'.$type.'</span>'
                    .'<h5 class="font-serif text-base text-slate-900 mt-1 mb-2 line-clamp-1 relative z-20">'.$name.'</h5>'
                    .'<div class="flex items-center gap-1 text-slate-500 text-xs mb-3 relative z-20"><span style="font-family:Material Symbols Outlined;font-size:14px">location_on</span><span class="line-clamp-1">'.htmlspecialchars($s['location']??'').'</span></div>'
                    .'<div class="flex items-center justify-between gap-3 relative z-20 border-t border-slate-100 pt-3">'
                    .'<p class="font-black text-slate-900 text-sm">&#8377;'.$price.' <span class="text-xs text-slate-400 font-normal">/night</span></p>'
                    .'<span class="bg-primary text-white px-4 py-1.5 rounded-full font-bold text-xs group-hover:bg-orange-600 transition-all whitespace-nowrap">Check Details</span>'
                    .'</div></div></a>';
            };
            $items = $hp_stays;
        elseif ($_sec_key === 'buses'):
            $render_fn = function($bus) {
                $op=htmlspecialchars($bus['operator']); $bt=htmlspecialchars($bus['bus_type']);
                $route=htmlspecialchars($bus['from_location']).' → '.htmlspecialchars($bus['to_location']);
                $price=number_format($bus['price']);
                $slug = BASE_PATH . '/listing-detail/' . generateSlug('buses', $bus['id'], $bus['operator']) . '.html';
                return '<a href="'.$slug.'" class="glass-dark p-5 rounded-2xl flex items-center justify-between gap-4 card-hover flex-shrink-0 group relative overflow-hidden" style="width:VAR_W">'
                    .'<div class="flex items-center gap-4 min-w-0 relative z-20">'
                    .'<div class="w-12 h-12 bg-primary/15 rounded-xl flex items-center justify-center shrink-0">'
                    .'<span class="material-symbols-outlined text-primary text-2xl">directions_bus</span></div>'
                    .'<div class="min-w-0"><p class="text-white font-bold text-sm truncate">'.$op.' <span class="text-[10px] font-normal text-white/50 bg-white/10 px-2 py-0.5 rounded ml-1">'.$bt.'</span></p>'
                    .'<p class="text-white/50 text-xs mt-0.5 truncate">'.$route.'</p></div></div>'
                    .'<div class="flex items-center gap-3 shrink-0 relative z-20">'
                    .'<p class="text-primary font-black text-lg">&#8377;'.$price.'</p>'
                    .'<span class="bg-primary text-white px-4 py-2 rounded-xl font-bold text-xs group-hover:bg-orange-600 transition-all">Check Details</span>'
                    .'</div></a>';
            };
            $items = $hp_buses;
        else:
            $render_fn = function($blog) {
                $rt=max(3,intval(strlen(strip_tags($blog['content']??''))/1000));
                $t=strtolower(trim($blog['title']));
                $t=preg_replace('/[^a-z0-9\s-]/','',$t);
                $t=preg_replace('/[\s-]+/','-',$t);
                $slug = BASE_PATH . '/blogs/'.$blog['id'].'-'.substr(trim($t,'-'),0,60) . '.html';
                $img = strpos($blog['image'] ?? '', 'http') === 0 ? htmlspecialchars($blog['image'] ?? '') : BASE_PATH . '/' . ltrim(htmlspecialchars($blog['image'] ?? ''), '/');
                $title=htmlspecialchars($blog['title']);
                $cat=htmlspecialchars($blog['category']??'Travel');
                return '<a href="'.$slug.'" class="group cursor-pointer flex-shrink-0 hover:-translate-y-1 transition-all duration-300" style="width:VAR_W">'
                    .'<div class="rounded-2xl overflow-hidden aspect-[16/10] mb-3 shadow-md relative">'
                    .'<img alt="'.$title.'" loading="lazy" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="'.$img.'"/>'
                    .'<div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-4">'
                    .'<span class="bg-white text-black px-4 py-1.5 rounded-full font-bold text-xs">READ POST</span></div></div>'
                    .'<div class="flex items-center gap-3 mb-2">'
                    .'<span class="bg-primary/10 text-primary px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase">'.$cat.'</span>'
                    .'<span class="text-slate-400 text-xs flex items-center gap-1"><span class="material-symbols-outlined text-sm">schedule</span>'.$rt.' min</span>'
                    .'</div><h4 class="font-serif text-base text-slate-900 group-hover:text-primary transition-colors line-clamp-2">'.$title.'</h4></a>';
            };
            $items = $hp_blogs;
        endif;
        ?>
        <div class="relative overflow-hidden" id="carousel-wrap-<?php echo $_sec_key; ?>">
            <div id="carousel-track-<?php echo $_sec_key; ?>" class="flex gap-5" style="will-change:transform;">
                <?php
                if (!empty($items)) {
                    // Render 3× for seamless infinite loop
                    for ($__r = 0; $__r < 3; $__r++) {
                        foreach ($items as $__item) {
                            echo str_replace('VAR_W', $_card_w, $render_fn($__item));
                        }
                    }
                } else {
                    echo '<p class="text-slate-400 py-8">No items yet.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</section>
<?php endforeach; ?>
</main>

<?php require 'footer.php'; ?>

<!-- Infinite carousel auto-scroll for all sections -->
<script>
(function(){
    ['attractions','bikes','restaurants','buses','blogs'].forEach(function(key) {
        var wrap  = document.getElementById('carousel-wrap-' + key);
        var track = document.getElementById('carousel-track-' + key);
        if (!wrap || !track) return;
        var paused = false, pos = 0, speed = 0.7;
        var oneSetWidth = 0;
        var isDragging = false, dragStartX = 0, dragStartPos = 0;

        function calcWidth() {
            oneSetWidth = track.scrollWidth / 3;
        }

        requestAnimationFrame(function(){
            calcWidth();
            if (oneSetWidth <= 0) return;
            window.addEventListener('resize', function(){ calcWidth(); if (pos >= oneSetWidth) pos = pos % oneSetWidth; });

            function step() {
                if (!paused && !isDragging) {
                    pos += speed;
                    if (pos >= oneSetWidth) pos -= oneSetWidth;
                    track.style.transform = 'translateX(-' + pos + 'px)';
                }
                requestAnimationFrame(step);
            }

            // Pause on hover
            wrap.addEventListener('mouseenter', function(){ paused = true; });
            wrap.addEventListener('mouseleave', function(){ if(!isDragging) paused = false; });

            // Mouse drag — use document for move/up so it works outside the clipped wrap
            wrap.addEventListener('mousedown', function(e){
                isDragging = true; paused = true;
                dragStartX = e.clientX; dragStartPos = pos;
                wrap.style.cursor = 'grabbing';
                e.preventDefault();
            });
            document.addEventListener('mousemove', function(e){
                if (!isDragging) return;
                var dx = dragStartX - e.clientX;
                pos = dragStartPos + dx;
                if (pos < 0) pos += oneSetWidth;
                if (pos >= oneSetWidth) pos -= oneSetWidth;
                track.style.transform = 'translateX(-' + pos + 'px)';
            });
            document.addEventListener('mouseup', function(){
                if (!isDragging) return;
                isDragging = false;
                wrap.style.cursor = '';
                setTimeout(function(){ paused = false; }, 300);
            });

            // Touch drag
            var touchStartX = 0, touchStartPos = 0;
            wrap.addEventListener('touchstart', function(e){ paused=true; touchStartX=e.touches[0].clientX; touchStartPos=pos; }, {passive:true});
            wrap.addEventListener('touchmove', function(e){
                var dx = touchStartX - e.touches[0].clientX;
                pos = touchStartPos + dx;
                if (pos < 0) pos += oneSetWidth;
                if (pos >= oneSetWidth) pos -= oneSetWidth;
                track.style.transform = 'translateX(-' + pos + 'px)';
            }, {passive:true});
            wrap.addEventListener('touchend', function(){ setTimeout(function(){ paused=false; }, 1500); }, {passive:true});

            requestAnimationFrame(step);
        });
    });
})();
</script>

<!-- Go to Top Button -->
<button id="go-top-btn" onclick="window.scrollTo({top:0,behavior:'smooth'})"
    style="position:fixed;bottom:28px;right:24px;z-index:9999;width:48px;height:48px;border-radius:50%;background:#ec5b13;color:#fff;border:none;cursor:pointer;box-shadow:0 4px 20px rgba(236,91,19,0.5);display:flex;align-items:center;justify-content:center;opacity:0;visibility:hidden;transform:translateY(12px);transition:opacity .25s ease,visibility .25s ease,transform .25s ease;"
    aria-label="Go to top">
    <span class="material-symbols-outlined" style="font-size:22px;line-height:1;pointer-events:none;">arrow_upward</span>
</button>
<script>
(function(){
    var btn = document.getElementById('go-top-btn');
    function updateBtn() {
        if (window.scrollY > 200) {
            btn.style.opacity = '1';
            btn.style.visibility = 'visible';
            btn.style.transform = 'translateY(0)';
        } else {
            btn.style.opacity = '0';
            btn.style.visibility = 'hidden';
            btn.style.transform = 'translateY(12px)';
        }
    }
    updateBtn();
    window.addEventListener('scroll', updateBtn, {passive:true});
})();
</script>
</body>
</html>
