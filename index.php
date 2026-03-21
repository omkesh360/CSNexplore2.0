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
    'show_attractions'  => true,
    'show_bikes'        => true,
    'show_restaurants'  => true,
    'show_buses'        => true,
    'show_blogs'        => true,
    'hero_subtext'      => 'Stays, cars, bikes, restaurants, attractions and buses — all in one place.',
    'city_intro'        => '',
    'stat1_label'       => '500+ Hotels',
    'stat2_label'       => '50+ Attractions',
    'stat3_label'       => '200+ Restaurants',
    'stat4_label'       => '10K+ Happy Travelers',
    'count_attractions' => 4,
    'count_bikes'       => 4,
    'count_restaurants' => 6,
    'count_buses'       => 2,
    'count_blogs'       => 3,
    'layout_attractions'=> '4-col',
    'layout_bikes'      => '4-col',
    'layout_restaurants'=> '3-col',
    'layout_buses'      => '2-col',
    'layout_blogs'      => '3-col',
    'section_order'     => ['attractions','bikes','restaurants','buses','blogs'],
    'picks_attractions' => [],
    'picks_bikes'       => [],
    'picks_restaurants' => [],
    'picks_buses'       => [],
    'picks_blogs'       => [],
];
foreach ($hp_defaults as $k => $v) {
    if (!isset($hp_settings[$k]) || $hp_settings[$k] === '') {
        $hp_settings[$k] = $v;
    }
}
// Ensure section_order is always a valid array
if (!is_array($hp_settings['section_order']) || count($hp_settings['section_order']) < 5) {
    $hp_settings['section_order'] = ['attractions','bikes','restaurants','buses','blogs'];
}

// Helper: layout string → Tailwind grid/flex class
function hp_grid_class($layout) {
    $map = [
        '3-col' => 'grid grid-cols-1 md:grid-cols-3 gap-5',
        '4-col' => 'grid grid-cols-2 md:grid-cols-4 gap-5',
        '2-col' => 'grid grid-cols-1 md:grid-cols-2 gap-4',
        'list'  => 'flex flex-col gap-3',
        'scroll'=> 'flex gap-5 overflow-x-auto hide-scrollbar pb-3',
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
$hp_buses = hp_fetch_picks($db, 'buses', $hp_settings['picks_buses'], 'is_active=1',
    "SELECT * FROM buses WHERE is_active=1 ORDER BY display_order ASC LIMIT " . (int)$hp_settings['count_buses']);
$hp_blogs = hp_fetch_picks($db, 'blogs', $hp_settings['picks_blogs'], "status='published'",
    "SELECT * FROM blogs WHERE status='published' ORDER BY created_at DESC LIMIT " . (int)$hp_settings['count_blogs']);
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#ec5b13",
                        "whatsapp": "#25D366",
                        "background-dark": "#0a0705",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"],
                        "serif": ["Playfair Display", "serif"]
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css"/>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        @keyframes marquee { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }
        .marquee { animation: marquee 30s linear infinite; }
        .glass { background:rgba(255,255,255,0.07); backdrop-filter:blur(16px); -webkit-backdrop-filter:blur(16px); border:1px solid rgba(255,255,255,0.12); }
        .glass-dark { background:rgba(10,7,5,0.7); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border:1px solid rgba(236,91,19,0.1); }
        .header-solid { background:#000000 !important; backdrop-filter:none !important; -webkit-backdrop-filter:none !important; }
        /* Page transition */
        html { background:#0a0705; }
        body { opacity:0; will-change:opacity; backface-visibility:hidden; -webkit-backface-visibility:hidden; }
        body.page-ready { animation: pageFadeIn 0.2s ease forwards; }
        @keyframes pageFadeIn { from { opacity:0; } to { opacity:1; } }
        .hide-scrollbar::-webkit-scrollbar { display:none; }
        .hide-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; font-family:'Material Symbols Outlined'; font-style:normal; display:inline-block; line-height:1; }
        .card-hover:hover { box-shadow:0 0 30px rgba(236,91,19,0.15); }
        /* Carousel card widths — mobile: 80vw (shows ~1.1 cards), desktop: percentage */
        :root {
            --card-w-attractions: 80vw;
            --card-w-bikes:       80vw;
            --card-w-restaurants: 80vw;
            --card-w-buses:       80vw;
            --card-w-blogs:       80vw;
        }
        @media (min-width: 640px) {
            :root {
                --card-w-attractions: calc(50% - 14px);
                --card-w-bikes:       calc(50% - 14px);
                --card-w-restaurants: calc(50% - 14px);
                --card-w-buses:       calc(50% - 14px);
                --card-w-blogs:       calc(50% - 14px);
            }
        }
        @media (min-width: 1024px) {
            :root {
                --card-w-attractions: calc(25% - 15px);
                --card-w-bikes:       calc(25% - 15px);
                --card-w-restaurants: calc(20% - 16px);
                --card-w-buses:       calc(50% - 10px);
                --card-w-blogs:       calc(33.333% - 14px);
            }
        }
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
        /* Mobile responsive search */
        @media(max-width:768px){
          .search-box { padding:14px; }
          .search-row { flex-wrap:wrap; gap:8px; }
          .search-field { flex:1 1 100%; height:46px; }
          .date-field { flex:1 1 calc(50% - 4px); height:46px; min-width:0; }
          .search-btn { width:100%; justify-content:center; height:46px; font-size:14px; padding:0 16px; }
          .tab-btn { padding:6px 10px; font-size:12px; }
          .tab-btn .material-symbols-outlined { font-size:15px; }
        }
        @media(max-width:400px){
          .date-field { flex:1 1 100%; }
        }
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

        /* ── Index-specific motion enhancements ── */
        /* Hero parallax */
        #hero-bg { will-change: transform; }
        /* Floating particles */
        .particle { position:absolute; border-radius:50%; pointer-events:none; animation:particleDrift linear infinite; }
        @keyframes particleDrift {
            0%   { transform:translateY(0) translateX(0) scale(1); opacity:0; }
            10%  { opacity:1; }
            90%  { opacity:0.6; }
            100% { transform:translateY(-120vh) translateX(30px) scale(0.5); opacity:0; }
        }
        /* Stats counter */
        .stat-num { display:inline-block; }
        /* Section divider wave */
        .wave-divider { line-height:0; overflow:hidden; }
        /* Gradient text */
        .gradient-text { background:linear-gradient(135deg,#ec5b13,#ff8c42); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        /* Glow badge */
        .glow-badge { box-shadow:0 0 20px rgba(236,91,19,0.4); }
    </style>
</head>
<body class="bg-white dark:bg-background-dark font-display text-slate-900 dark:text-slate-100">

<!-- Marquee Bar -->
<div class="bg-primary text-white py-1.5 overflow-hidden relative z-[60]">
    <div class="max-w-7xl mx-auto px-6 flex justify-between items-center gap-4 text-[11px] font-semibold uppercase tracking-widest">
        <div class="flex-1 overflow-hidden">
            <div class="flex whitespace-nowrap marquee">
                <?php
                $default_marquee = [
                    "★ 20% OFF on first heritage tour booking",
                    "★ Verified guides for Ajanta &amp; Ellora",
                    "★ Free cancellation on bike rentals",
                    "★ 24/7 tourist support available",
                ];
                if (!empty($hp_settings['marquee'])) {
                    $marquee_items = array_filter(array_map('trim', explode("\n", $hp_settings['marquee'])));
                    if (empty($marquee_items)) $marquee_items = $default_marquee;
                } else {
                    $marquee_items = $default_marquee;
                }
                // Duplicate for seamless loop
                $all_items = array_merge($marquee_items, $marquee_items);
                foreach ($all_items as $item): ?>
                    <span class="px-6"><?php echo htmlspecialchars($item); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        
    </div>
</div>

<!-- Header -->
<?php
$nav_links_home = [
    ['href' => 'index.php',   'label' => 'Home'],
    ['href' => 'about.php',   'label' => 'About Us'],
    ['href' => 'contact.php', 'label' => 'Contact Us'],
    ['href' => 'blogs.php',   'label' => 'Blogs'],
];
?>
<!-- Morphing Header: full-width at top → pill on scroll -->
<div id="hdr-wrap" class="sticky top-0 z-50 pointer-events-none transition-all duration-300" style="padding:0">
    <header id="site-header" class="w-full pointer-events-auto transition-all duration-300" style="background:#000000;border-radius:0;border-bottom:1px solid rgba(255,255,255,0.08);box-shadow:none;backdrop-filter:none;-webkit-backdrop-filter:none;">
        <nav class="px-4 sm:px-6 flex items-center justify-between" style="height:56px">
            <a href="index.php" class="flex items-center shrink-0">
                <img src="images/travelhub.png" alt="CSNExplore" class="h-8 sm:h-9 object-contain"/>
            </a>
            <div class="hidden md:flex items-center gap-0.5">
                <?php foreach ($nav_links_home as $link): $active = ($link['href'] === 'index.php'); ?>
                    <a href="<?php echo $link['href']; ?>"
                       class="text-sm font-semibold px-4 py-1.5 rounded-full transition-colors <?php echo $active ? 'text-white bg-white/15' : 'text-white/60 hover:bg-white/10 hover:text-white'; ?>">
                        <?php echo $link['label']; ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="flex items-center gap-1.5">
                <!-- Call & WhatsApp -->
                <a href="tel:+918600968888" id="call-btn" class="hidden sm:flex items-center gap-1 text-white/70 hover:text-white text-xs font-semibold px-2 py-1.5 rounded-full hover:bg-white/10 transition-all" title="Call Us">
                    <span class="material-symbols-outlined text-base">call</span>
                    <span class="call-text">+91 86009 68888</span>
                </a>
                <a href="https://wa.me/918600968888" id="whatsapp-btn" target="_blank" rel="noopener" class="hidden sm:flex items-center gap-1 text-[#25D366] hover:text-white text-xs font-semibold px-2 py-1.5 rounded-full hover:bg-white/10 transition-all" title="WhatsApp Us">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    <span class="whatsapp-text">WhatsApp</span>
                </a>
                <a href="login.php" id="hdr-login-btn" class="text-white/80 hover:text-white text-xs sm:text-sm font-semibold px-3 sm:px-4 py-1.5 rounded-full hover:bg-white/10 transition-all">Login</a>
                <div id="hdr-user-menu" class="hidden relative">
                    <button id="hdr-user-btn" class="flex items-center gap-1.5 text-white text-xs sm:text-sm font-semibold px-3 py-1.5 hover:bg-white/10 rounded-full transition-all">
                        <span class="material-symbols-outlined text-base text-primary">account_circle</span>
                        <span id="hdr-user-name" class="max-w-[70px] sm:max-w-[100px] truncate"></span>
                        <span class="material-symbols-outlined text-sm">expand_more</span>
                    </button>
                    <div id="hdr-dropdown" class="hidden absolute right-0 top-full mt-2 w-44 bg-[#1a1208] border border-white/10 rounded-2xl shadow-2xl py-1.5 z-[200]">
                        <button id="hdr-logout-btn" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-400 hover:bg-white/5 hover:text-red-300 transition-colors rounded-xl">
                            <span class="material-symbols-outlined text-base">logout</span>Logout
                        </button>
                    </div>
                </div>
                <button id="mob-btn" class="md:hidden p-1.5 rounded-full hover:bg-white/10 transition-colors">
                    <span class="material-symbols-outlined text-xl text-white">menu</span>
                </button>
            </div>
        </nav>
    </header>
    <div id="mob-menu" class="hidden md:hidden mt-2 mx-3 rounded-2xl bg-black border border-white/10 shadow-2xl px-2 py-2 flex flex-col gap-0.5 pointer-events-auto">
        <?php foreach ($nav_links_home as $link): $active = ($link['href'] === 'index.php'); ?>
            <a href="<?php echo $link['href']; ?>"
               class="text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors <?php echo $active ? 'text-primary bg-white/10' : 'text-white/70 hover:bg-white/10 hover:text-white'; ?>">
                <?php echo $link['label']; ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>
    <script>
        // Morph header: flat full-width at top → floating pill on scroll
        (function(){
            var wrap = document.getElementById('hdr-wrap');
            var hdr  = document.getElementById('site-header');
            function onScroll(){
                if(window.scrollY > 10){
                    wrap.style.padding = '12px 20px 4px';
                    hdr.style.borderRadius = '9999px';
                    hdr.style.background   = 'rgba(0,0,0,0.75)';
                    hdr.style.backdropFilter = 'blur(20px)';
                    hdr.style.webkitBackdropFilter = 'blur(20px)';
                    hdr.style.border = '1px solid rgba(255,255,255,0.12)';
                    hdr.style.boxShadow = '0 8px 32px rgba(0,0,0,0.6)';
                } else {
                    wrap.style.padding = '0';
                    hdr.style.borderRadius = '0';
                    hdr.style.background   = '#000000';
                    hdr.style.backdropFilter = 'none';
                    hdr.style.webkitBackdropFilter = 'none';
                    hdr.style.border = 'none';
                    hdr.style.borderBottom = '1px solid rgba(255,255,255,0.08)';
                    hdr.style.boxShadow = 'none';
                }
            }
            window.addEventListener('scroll', onScroll, {passive:true});
            onScroll();
        })();

        document.getElementById('mob-btn').addEventListener('click', function(){
            document.getElementById('mob-menu').classList.toggle('hidden');
        });
        // ── Auth state ──────────────────────────────────────────────────────
        (function(){
            var token = localStorage.getItem('csn_token');
            var user  = JSON.parse(localStorage.getItem('csn_user') || 'null');

            function clearAll() {
                localStorage.removeItem('csn_token'); localStorage.removeItem('csn_user');
                localStorage.removeItem('csn_admin_token'); localStorage.removeItem('csn_admin_user');
                token = null; user = null;
            }

            if (token) {
                try {
                    var parts = token.split('.');
                    if (parts.length === 3) {
                        var p = JSON.parse(atob(parts[1].replace(/-/g,'+').replace(/_/g,'/')));
                        if (p.exp && p.exp < Math.floor(Date.now()/1000)) { clearAll(); }
                    } else { clearAll(); }
                } catch(e) { clearAll(); }
            }
            if (token && user) {
                var pl = document.getElementById('hdr-login-btn');
                var pu = document.getElementById('hdr-user-menu');
                var pn = document.getElementById('hdr-user-name');
                if (pl) pl.style.display = 'none';
                if (pu) pu.classList.remove('hidden');
                if (pn) pn.textContent = user.name ? user.name.split(' ')[0] : 'Account';
            }
            var userBtn  = document.getElementById('hdr-user-btn');
            var dropdown = document.getElementById('hdr-dropdown');
            if (userBtn) {
                userBtn.addEventListener('click', function(e){ e.stopPropagation(); dropdown.classList.toggle('hidden'); });
                document.addEventListener('click', function(){ dropdown.classList.add('hidden'); });
            }
            function doLogout() {
                clearAll();
                window.location.href = 'index.php';
            }
            var lb = document.getElementById('hdr-logout-btn');
            if (lb) lb.addEventListener('click', doLogout);
        })();

        // Fade in on load
        function addPageReady(){document.body.classList.add('page-ready');}
        if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',addPageReady);}else{addPageReady();}
        // Fade out on navigation
        document.addEventListener('click', function(e) {
            var a = e.target.closest('a');
            if (!a) return;
            var href = a.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript') || a.target === '_blank') return;
            e.preventDefault();
            document.body.style.transition = 'opacity 0.18s ease';
            document.body.style.opacity = '0';
            setTimeout(function(){ window.location.href = href; }, 190);
        });
    </script>
</header>

<main>
<!-- Hero -->
<section class="relative min-h-[100svh] md:min-h-[80vh] flex flex-col items-center justify-center overflow-hidden">
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
    <div class="relative z-20 text-center px-4 w-full max-w-5xl mx-auto pt-8 pb-6">
        <p id="hero-label" class="text-primary font-bold text-xs uppercase tracking-widest mb-3">Chhatrapati Sambhajinagar</p>
        <h1 class="font-serif text-3xl sm:text-4xl md:text-6xl text-white mb-4 leading-tight font-black">
            <span id="hero-pre">Explore </span><span class="text-primary" id="hero-highlight">Your City</span><span id="hero-post"> Your Way</span>
        </h1>
        <p id="hero-desc" class="text-white/70 text-sm md:text-lg mb-6 md:mb-10 max-w-2xl mx-auto px-2"><?php echo htmlspecialchars($hp_settings['hero_subtext']); ?></p>

        <!-- Search Box -->
        <div class="search-box max-w-4xl mx-auto w-full">
            <div class="flex flex-wrap gap-1.5 mb-4 pb-3 border-b border-white/10 justify-center">
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

            <!-- STAYS panel -->
            <div id="panel-stays" class="search-panel active">
                <div class="search-row">
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="stays-location" type="text" placeholder="Chhatrapati Sambhajinagar" value="Chhatrapati Sambhajinagar"/></div>
                    <div class="date-field" onclick="document.getElementById('stays-checkin').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="stays-checkin" type="text" placeholder="Check-in" readonly/></div>
                    <div class="date-field" onclick="document.getElementById('stays-checkout').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="stays-checkout" type="text" placeholder="Check-out" readonly/></div>
                    <button class="search-btn" onclick="doSearch('stays')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
            <!-- CARS panel -->
            <div id="panel-cars" class="search-panel">
                <div class="search-row">
                    <div class="search-field"><span class="material-symbols-outlined">trip_origin</span><input id="cars-pickup" type="text" placeholder="Chhatrapati Sambhajinagar" value="Chhatrapati Sambhajinagar"/></div>
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="cars-drop" type="text" placeholder="Drop location"/></div>
                    <div class="date-field" onclick="document.getElementById('cars-date').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="cars-date" type="text" placeholder="Select date" readonly/></div>
                    <button class="search-btn" onclick="doSearch('cars')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
            <!-- BIKES panel -->
            <div id="panel-bikes" class="search-panel">
                <div class="search-row">
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="bikes-location" type="text" placeholder="Chhatrapati Sambhajinagar" value="Chhatrapati Sambhajinagar"/></div>
                    <div class="date-field" onclick="document.getElementById('bikes-date').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="bikes-date" type="text" placeholder="From date" readonly/></div>
                    <div class="date-field" onclick="document.getElementById('bikes-return').focus()"><span class="material-symbols-outlined">event_available</span><input id="bikes-return" type="text" placeholder="Return date" readonly/></div>
                    <button class="search-btn" onclick="doSearch('bikes')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
            <!-- ATTRACTIONS panel -->
            <div id="panel-attractions" class="search-panel">
                <div class="search-row">
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="attractions-location" type="text" placeholder="Chhatrapati Sambhajinagar" value="Chhatrapati Sambhajinagar"/></div>
                    <div class="date-field" onclick="document.getElementById('attractions-date').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="attractions-date" type="text" placeholder="Select date" readonly/></div>
                    <button class="search-btn" onclick="doSearch('attractions')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
            <!-- DINE panel -->
            <div id="panel-dine" class="search-panel">
                <div class="search-row">
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="dine-location" type="text" placeholder="Chhatrapati Sambhajinagar" value="Chhatrapati Sambhajinagar"/></div>
                    <div class="date-field" onclick="document.getElementById('dine-date').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="dine-date" type="text" placeholder="Select date" readonly/></div>
                    <button class="search-btn" onclick="doSearch('dine')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
            <!-- BUSES panel -->
            <div id="panel-buses" class="search-panel">
                <div class="search-row">
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
        stays:       { label:'Find Your Stay',       pre:'Discover ',   highlight:'Perfect Hotels',    post:' Near You',    desc:'The best hotels, guesthouses and homestays in Chhatrapati Sambhajinagar.' },
        cars:        { label:'Rent a Car',            pre:'Drive in ',   highlight:'Premium Style',     post:' Today',       desc:'Luxury sedans, SUVs and hatchbacks with professional chauffeurs at your service.' },
        bikes:       { label:'Rent a Bike',           pre:'Ride ',       highlight:'The Open Road',     post:' Your Way',    desc:'Scooters, cruisers and sports bikes — ride the city your way, anytime.' },
        attractions: { label:'Discover Attractions',  pre:'Explore ',    highlight:'Ancient Marvels',   post:' Around You',  desc:'Ellora, Ajanta, Bibi Ka Maqbara and more — heritage wonders await you.' },
        dine:        { label:'Taste the City',        pre:'Savour ',     highlight:'Local Flavours',    post:' Tonight',     desc:'From Mughlai feasts to street food — find the best restaurants near you.' },
        buses:       { label:'Book a Bus',            pre:'Travel ',     highlight:'Your Way',          post:' Comfortably', desc:'AC sleepers, Volvo coaches and MSRTC buses to and from Sambhajinagar.' }
    };
    var d = heroData[tab];
    ['hero-label','hero-pre','hero-highlight','hero-post','hero-desc'].forEach(function(id){ document.getElementById(id).style.opacity='0'; });
    setTimeout(function(){
        document.getElementById('hero-label').textContent = d.label;
        document.getElementById('hero-pre').textContent = d.pre;
        document.getElementById('hero-highlight').textContent = d.highlight;
        document.getElementById('hero-post').textContent = d.post;
        document.getElementById('hero-desc').textContent = d.desc;
        ['hero-label','hero-pre','hero-highlight','hero-post','hero-desc'].forEach(function(id){ document.getElementById(id).style.opacity='1'; });
    }, 250);
    if (!fromAuto) { clearInterval(heroTimer); heroTimer = setInterval(autoRotate, 5000); }
}
var heroTabs = ['stays','cars','bikes','attractions','dine','buses'], heroIndex = 0;
function autoRotate() { heroIndex = (heroIndex + 1) % heroTabs.length; switchTab(heroTabs[heroIndex], true); }
var heroTimer = setInterval(autoRotate, 5000);
// Pause auto-rotate when user is typing in any search input
var _searchFocused = false;
document.addEventListener('focusin', function(e){ if(e.target && (e.target.tagName==='INPUT'||e.target.tagName==='TEXTAREA')){ _searchFocused=true; clearInterval(heroTimer); } });
document.addEventListener('focusout', function(e){ if(e.target && (e.target.tagName==='INPUT'||e.target.tagName==='TEXTAREA')){ _searchFocused=false; heroTimer=setInterval(autoRotate,5000); } });
var searchUrls = { stays:'listing.php?type=stays', cars:'listing.php?type=cars', bikes:'listing.php?type=bikes', attractions:'listing.php?type=attractions', dine:'listing.php?type=restaurants', buses:'listing.php?type=buses' };
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
</script>

<!-- Stats Bar -->
<div class="bg-[#0a0705] border-t border-white/5 py-8">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <?php
            $stats = [
                ['icon' => 'hotel',                    'label' => $hp_settings['stat1_label'], 'color' => 'text-primary'],
                ['icon' => 'confirmation_number',      'label' => $hp_settings['stat2_label'], 'color' => 'text-amber-400'],
                ['icon' => 'restaurant',               'label' => $hp_settings['stat3_label'], 'color' => 'text-green-400'],
                ['icon' => 'sentiment_very_satisfied', 'label' => $hp_settings['stat4_label'], 'color' => 'text-blue-400'],
            ];
            foreach ($stats as $i => $stat): ?>
            <div class="flex flex-col items-center gap-2 group" data-reveal data-delay="<?php echo $i+1; ?>">
                <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center group-hover:bg-white/10 transition-colors group-hover:scale-110 transition-transform duration-300">
                    <span class="material-symbols-outlined <?php echo $stat['color']; ?> text-2xl"><?php echo $stat['icon']; ?></span>
                </div>
                <p class="font-black text-white text-lg leading-tight"><?php echo htmlspecialchars($stat['label']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php if (!empty($hp_settings['city_intro'])): ?>
<!-- City Intro -->
<div class="bg-white py-10">
    <div class="max-w-7xl mx-auto px-6">
        <p class="text-slate-600 text-base md:text-lg leading-relaxed text-center max-w-3xl mx-auto">
            <?php echo nl2br(htmlspecialchars($hp_settings['city_intro'])); ?>
        </p>
    </div>
</div>
<?php endif; ?>

<!-- Ride & Explore -->
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-end justify-between mb-6" data-reveal>
            <div>
                <p class="text-primary font-bold text-xs uppercase tracking-widest mb-1">Self-Drive & Chauffeur</p>
                <h2 class="font-serif text-2xl md:text-3xl text-slate-900">Ride &amp; Explore</h2>
            </div>
            <div class="flex gap-3 text-sm font-bold text-primary">
                <a href="listing.php?type=cars" class="hover:underline">Cars →</a>
                <a href="listing.php?type=bikes" class="hover:underline">Bikes →</a>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div data-reveal data-reveal="left" class="group relative overflow-hidden rounded-2xl h-64 shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 img-shimmer">
                <img alt="Luxury Car" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDlDMSSDq5u4EWTtyLrL2T01rG1QVLx79iWAxTc-Q5v-4DB7Qaf3se4mMQ0OXya60SgJNz-esA3YItuP3cAQgCOUMELZ93GiboDiWUtyGlo3vcROCcNprMWU9HsV96e-umpDcBQWbOJp3OcHtPHXGe0NfG1iYfBR6dtozOW1-x0kzci9SbakuCN5LahXPRRgoI5AgrCPrXLIv8hlg56V8HPrYua2wCw58U5qNgwuVnf4hEy-HOTzh45fEkiS4W70yyelAJTlwjmXCUK"/>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-5">
                    <span class="text-primary text-[10px] font-bold uppercase tracking-widest mb-1 block">Self-Drive & Chauffeur</span>
                    <h4 class="text-white text-xl font-serif mb-1">Premium Car Rentals</h4>
                    <p class="text-white/70 text-xs mb-3">Luxury sedans, SUVs & hatchbacks at your service.</p>
                    <a href="listing.php?type=cars" class="inline-block bg-white text-black px-5 py-2 rounded-full font-bold text-xs hover:bg-primary hover:text-white transition-all btn-pulse">Explore Cars</a>
                </div>
            </div>
            <div data-reveal data-reveal="right" class="group relative overflow-hidden rounded-2xl h-64 shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 img-shimmer">
                <img alt="Adventure Bike" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBFYIfhiyxiKGSvW26EEi6qWok9NLO6cRlbBw0oCLlVCiV9F1e_mtQoiJK-2Dnb5uwU3K6b01miWgbmBQaNlDcPazf_LXbqwv3zx4f_F6Jsl627xYGPA3B5kQg_01L4gEPJseizInfQycEdL6o-IO9u7fAjGuMEnr_iPgYZShZ5e9VLbqTAdlhGFW8Tnss81gBfiFHSmzorGkalt_cF3Hi8ycEbYCGC_a4e9UyOZAQ8J4m9XHF2EcZwdaPo2OpFbnwwGvVRYdxLSxsT"/>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-5">
                    <span class="text-primary text-[10px] font-bold uppercase tracking-widest mb-1 block">Scooters to Royal Enfields</span>
                    <h4 class="text-white text-xl font-serif mb-1">Bike Rentals</h4>
                    <p class="text-white/70 text-xs mb-3">Ride the city your way — any road, any time.</p>
                    <a href="listing.php?type=bikes" class="inline-block bg-white text-black px-5 py-2 rounded-full font-bold text-xs hover:bg-primary hover:text-white transition-all btn-pulse">Explore Bikes</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Base counts — if more items than this are shown, switch to horizontal scroll
$_sec_base_counts = [
    'attractions' => 4,
    'bikes'       => 4,
    'restaurants' => 6,
    'buses'       => 2,
    'blogs'       => 3,
];

// ── Render sections in saved order ───────────────────────────────────────────
$_sec_bg_toggle = false;
foreach ($hp_settings['section_order'] as $_sec_key):
    if (empty($hp_settings['show_' . $_sec_key])) continue;
    $_layout = $hp_settings['layout_' . $_sec_key];
    // If more items than base count → force horizontal scroll
    $_sec_items_map = ['attractions'=>$hp_attractions,'bikes'=>$hp_bikes,'restaurants'=>$hp_restaurants,'buses'=>$hp_buses,'blogs'=>$hp_blogs];
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
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-end justify-between mb-6" data-reveal>
            <?php if ($_sec_key === 'blogs'): ?>
            <div>
                <p class="text-primary font-bold text-xs uppercase tracking-widest mb-1">Our Travel Journals</p>
                <h2 class="font-serif text-2xl md:text-3xl text-slate-900"><?php echo htmlspecialchars($hp_settings['title_blogs']); ?></h2>
            </div>
            <a href="blogs.php" class="text-sm font-bold text-primary hover:underline">Read more &rarr;</a>
            <?php else: ?>
            <div>
                <p class="text-primary font-bold text-xs uppercase tracking-widest mb-1"><?php
                    $sec_subtitles = ['attractions'=>'Heritage & Culture','bikes'=>'Two-Wheeler Rentals','restaurants'=>'Food & Dining','buses'=>'Intercity Travel'];
                    echo $sec_subtitles[$_sec_key] ?? 'Explore';
                ?></p>
                <h2 class="font-serif text-2xl md:text-3xl text-slate-900"><?php echo htmlspecialchars($hp_settings['title_' . $_sec_key]); ?></h2>
            </div>
            <a href="listing.php?type=<?php echo $_sec_key; ?>" class="text-sm font-bold text-primary hover:underline">See all &rarr;</a>
            <?php endif; ?>
        </div>
        <?php
        // ── Visible-cards-per-section config ─────────────────────────────────
        $_vis = ['attractions'=>4,'bikes'=>4,'restaurants'=>6,'buses'=>2,'blogs'=>3];
        $_vis_count = $_vis[$_sec_key] ?? 4;
        // Mobile: fixed 80vw so ~1.1 cards visible. Desktop: percentage of container.
        $_card_w = 'var(--card-w-' . $_sec_key . ')';

        if ($_sec_key === 'attractions'):
            $render_fn = function($a) {
                $slug = 'listing-detail/attractions-'.$a['id'].'-'.substr(trim(preg_replace('/[\s-]+/','-',preg_replace('/[^a-z0-9\s-]/', '', strtolower($a['name']))),'-'),0,60).'.html';
                $img=htmlspecialchars($a['image']); $name=htmlspecialchars($a['name']);
                $tag=htmlspecialchars($a['type']??'Attraction');
                $price=$a['entry_fee']>0 ? '&#8377;'.number_format($a['entry_fee']) : 'Free';
                $rating=number_format((float)($a['rating']??0),1);
                return '<a href="'.$slug.'" class="group overflow-hidden rounded-2xl bg-white border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex-shrink-0" style="width:VAR_W">'
                    .'<div class="h-44 overflow-hidden relative"><img alt="'.$name.'" loading="lazy" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="'.$img.'"/>'
                    .'<div class="absolute top-2.5 right-2.5 flex items-center gap-1 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2 py-0.5 rounded-full"><span style="font-family:Material Symbols Outlined;font-size:12px;color:#fbbf24">star</span>'.$rating.'</div></div>'
                    .'<div class="p-4"><span class="text-primary text-[10px] font-bold uppercase tracking-widest">'.$tag.'</span>'
                    .'<h5 class="font-serif text-base text-slate-900 mt-1 mb-3 line-clamp-1">'.$name.'</h5>'
                    .'<div class="flex items-center justify-between">'
                    .'<p class="font-black text-slate-900 text-sm">'.$price.' <span class="text-xs text-slate-400 font-normal">entry</span></p>'
                    .'<span class="bg-primary text-white px-3 py-1.5 rounded-full font-bold text-xs group-hover:bg-orange-600 transition-all">Check Availability</span>'
                    .'</div></div></a>';
            };
            $items = $hp_attractions;
        elseif ($_sec_key === 'bikes'):
            $render_fn = function($b) {
                $slug = 'listing-detail/bikes-'.$b['id'].'-'.substr(trim(preg_replace('/[\s-]+/','-',preg_replace('/[^a-z0-9\s-]/', '', strtolower($b['name']))),'-'),0,60).'.html';
                $img=htmlspecialchars($b['image']); $name=htmlspecialchars($b['name']);
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
                $slug = 'listing-detail/restaurants-'.$r['id'].'-'.substr(trim(preg_replace('/[\s-]+/','-',preg_replace('/[^a-z0-9\s-]/', '', strtolower($r['name']))),'-'),0,60).'.html';
                $img=htmlspecialchars($r['image']); $name=htmlspecialchars($r['name']);
                $cuisine=htmlspecialchars($r['cuisine']??$r['type']); $price=number_format($r['price_per_person']??0);
                $rating=number_format((float)($r['rating']??0),1);
                return '<a href="'.$slug.'" class="group overflow-hidden rounded-2xl bg-white border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex-shrink-0" style="width:VAR_W">'
                    .'<div class="h-44 overflow-hidden relative"><img alt="'.$name.'" loading="lazy" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="'.$img.'"/>'
                    .'<div class="absolute top-2.5 right-2.5 flex items-center gap-1 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2 py-0.5 rounded-full"><span style="font-family:Material Symbols Outlined;font-size:12px;color:#fbbf24">star</span>'.$rating.'</div></div>'
                    .'<div class="p-4"><span class="text-primary text-[10px] font-bold uppercase tracking-widest">'.$cuisine.'</span>'
                    .'<h5 class="font-serif text-base text-slate-900 mt-1 mb-3 line-clamp-1">'.$name.'</h5>'
                    .'<div class="flex items-center justify-between">'
                    .'<p class="font-black text-slate-900 text-sm">&#8377;'.$price.' <span class="text-xs text-slate-400 font-normal">for two</span></p>'
                    .'<span class="bg-primary text-white px-3 py-1.5 rounded-full font-bold text-xs group-hover:bg-orange-600 transition-all">Check Availability</span>'
                    .'</div></div></a>';
            };
            $items = $hp_restaurants;
        elseif ($_sec_key === 'buses'):
            $render_fn = function($bus) {
                $op=htmlspecialchars($bus['operator']); $bt=htmlspecialchars($bus['bus_type']);
                $route=htmlspecialchars($bus['from_location']).' → '.htmlspecialchars($bus['to_location']);
                $price=number_format($bus['price']);
                return '<div class="glass-dark p-5 rounded-2xl flex items-center justify-between gap-4 card-hover flex-shrink-0" style="width:VAR_W">'
                    .'<div class="flex items-center gap-4 min-w-0">'
                    .'<div class="w-12 h-12 bg-primary/15 rounded-xl flex items-center justify-center shrink-0">'
                    .'<span class="material-symbols-outlined text-primary text-2xl">directions_bus</span></div>'
                    .'<div class="min-w-0"><p class="text-white font-bold text-sm truncate">'.$op.' <span class="text-[10px] font-normal text-white/50 bg-white/10 px-2 py-0.5 rounded ml-1">'.$bt.'</span></p>'
                    .'<p class="text-white/50 text-xs mt-0.5 truncate">'.$route.'</p></div></div>'
                    .'<div class="flex items-center gap-3 shrink-0">'
                    .'<p class="text-primary font-black text-lg">&#8377;'.$price.'</p>'
                    .'<a href="bus.php" class="bg-primary text-white px-4 py-2 rounded-xl font-bold text-xs hover:bg-orange-600 transition-all">Book</a>'
                    .'</div></div>';
            };
            $items = $hp_buses;
        else:
            $render_fn = function($blog) {
                $rt=max(3,intval(strlen(strip_tags($blog['content']??''))/1000));
                $t=strtolower(trim($blog['title']));
                $t=preg_replace('/[^a-z0-9\s-]/','',$t);
                $t=preg_replace('/[\s-]+/','-',$t);
                $slug='blogs/'.$blog['id'].'-'.substr(trim($t,'-'),0,60).'.html';
                $img=htmlspecialchars($blog['image']??''); $title=htmlspecialchars($blog['title']);
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
