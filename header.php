<?php
// header.php – shared header for all CSNExplore pages
// Usage: set $page_title and $current_page before including this file
$current_page = $current_page ?? '';
$page_title   = $page_title   ?? 'CSNExplore – Chhatrapati Sambhajinagar';
$nav_links = [
    ['href' => 'index.php',   'label' => 'Home'],
    ['href' => 'about.php',   'label' => 'About Us'],
    ['href' => 'contact.php', 'label' => 'Contact Us'],
    ['href' => 'blogs.php',   'label' => 'Blogs'],
];

$listing_nav = [
    ['href' => 'listing.php?type=stays',       'icon' => 'bed',                'label' => 'Stays',       'type' => 'stays'],
    ['href' => 'listing.php?type=cars',        'icon' => 'directions_car',     'label' => 'Car Rentals', 'type' => 'cars'],
    ['href' => 'listing.php?type=bikes',       'icon' => 'motorcycle',         'label' => 'Bike Rentals','type' => 'bikes'],
    ['href' => 'listing.php?type=attractions', 'icon' => 'confirmation_number','label' => 'Attractions', 'type' => 'attractions'],
    ['href' => 'listing.php?type=restaurants', 'icon' => 'restaurant',         'label' => 'Restaurants', 'type' => 'restaurants'],
    ['href' => 'listing.php?type=buses',       'icon' => 'directions_bus',     'label' => 'Buses',       'type' => 'buses'],
];

$is_listing_page = ($current_page === 'listing.php');
$active_listing_type = $listing_type ?? '';
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
    <style>
        .glass { background:rgba(255,255,255,0.07); backdrop-filter:blur(16px); -webkit-backdrop-filter:blur(16px); border:1px solid rgba(255,255,255,0.12); }
        .glass-dark { background:rgba(10,7,5,0.7); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border:1px solid rgba(236,91,19,0.1); }
        .header-solid { background:#000000 !important; backdrop-filter:none !important; -webkit-backdrop-filter:none !important; }
        /* Page transition */
        html { background:#fff; }
        body { opacity:0; will-change:opacity; backface-visibility:hidden; -webkit-backface-visibility:hidden; }
        body.page-ready { animation: pageFadeIn 0.2s ease forwards; }
        @keyframes pageFadeIn { from { opacity:0; } to { opacity:1; } }
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; font-family:'Material Symbols Outlined'; font-style:normal; display:inline-block; line-height:1; }
        <?php if (!empty($extra_styles)) echo $extra_styles; ?>
    </style>
    <?php if (!empty($extra_head)) echo $extra_head; ?>
</head>
<body class="bg-white dark:bg-background-dark font-display text-slate-900 dark:text-slate-100">

<!-- Marquee Bar -->
<div class="bg-primary text-white py-1.5 overflow-hidden relative z-[60]">
    <div class="max-w-7xl mx-auto px-6 flex justify-between items-center gap-4 text-[11px] font-semibold uppercase tracking-widest">
        <div class="flex-1 overflow-hidden">
            <div class="flex whitespace-nowrap" style="animation:marquee 30s linear infinite">
                <style>@keyframes marquee{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}</style>
                <span class="px-6">★ 20% OFF on first heritage tour booking</span>
                <span class="px-6">★ Verified guides for Ajanta &amp; Ellora</span>
                <span class="px-6">★ Free cancellation on bike rentals</span>
                <span class="px-6">★ 24/7 tourist support available</span>
                <span class="px-6">★ 20% OFF on first heritage tour booking</span>
                <span class="px-6">★ Verified guides for Ajanta &amp; Ellora</span>
                <span class="px-6">★ Free cancellation on bike rentals</span>
                <span class="px-6">★ 24/7 tourist support available</span>
            </div>
        </div>

    </div>
</div>

<!-- Header – always dark/black with blur -->
<header id="site-header" class="sticky top-0 w-full z-50 transition-all duration-300 glass-dark border-b border-white/5">
    <nav class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        <a href="index.php" class="flex items-center shrink-0">
            <img src="images/travelhub.png" alt="CSNExplore" class="h-9 object-contain"/>
        </a>
        <div class="hidden md:flex items-center gap-1">
            <?php if ($is_listing_page): ?>
                <?php foreach ($listing_nav as $link): $is_active = ($link['type'] === $active_listing_type); ?>
                <a href="<?php echo $link['href']; ?>"
                   class="flex items-center gap-1.5 text-sm font-semibold px-3 py-2 rounded-full transition-colors <?php echo $is_active ? 'text-white bg-white/15' : 'text-white/60 hover:bg-white/10 hover:text-white'; ?>">
                    <span class="material-symbols-outlined text-base"><?php echo $link['icon']; ?></span>
                    <?php echo $link['label']; ?>
                </a>
                <?php endforeach; ?>
            <?php else: ?>
                <?php foreach ($nav_links as $link): $is_active = ($link['href'] === $current_page); ?>
                <a href="<?php echo $link['href']; ?>"
                   class="nav-link text-sm font-semibold px-4 py-2 rounded-full transition-colors <?php echo $is_active ? 'text-white bg-white/10' : 'text-white/70 hover:bg-white/10 hover:text-white'; ?>">
                    <?php echo $link['label']; ?>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="flex items-center gap-2" id="header-auth-area">
            <!-- Auth buttons — swapped by JS based on localStorage -->
            <a href="login.php" id="hdr-login-btn" class="text-white text-sm font-semibold px-4 py-1.5 hover:bg-white/10 rounded-full transition-all">Login</a>
            <a href="register.php" id="hdr-register-btn" class="bg-primary text-white text-sm font-bold px-5 py-1.5 rounded-full hover:bg-orange-600 transition-all shadow-lg shadow-primary/20">Register</a>
            <!-- User menu (hidden by default, shown when logged in) -->
            <div id="hdr-user-menu" class="hidden relative">
                <button id="hdr-user-btn" class="flex items-center gap-2 text-white text-sm font-semibold px-3 py-1.5 hover:bg-white/10 rounded-full transition-all">
                    <span class="material-symbols-outlined text-base text-primary">account_circle</span>
                    <span id="hdr-user-name" class="max-w-[100px] truncate"></span>
                    <span class="material-symbols-outlined text-sm">expand_more</span>
                </button>
                <div id="hdr-dropdown" class="hidden absolute right-0 top-full mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-1 z-[200]">
                    <a href="my-booking.php" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-base">book_online</span>My Bookings
                    </a>
                    <hr class="my-1 border-slate-100"/>
                    <button id="hdr-logout-btn" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors">
                        <span class="material-symbols-outlined text-base">logout</span>Logout
                    </button>
                </div>
            </div>
            <button id="mob-btn" class="md:hidden p-2 rounded-lg transition-colors ml-1">
                <span class="material-symbols-outlined text-2xl text-white">menu</span>
            </button>
        </div>
    </nav>
    <div id="mob-menu" class="hidden md:hidden border-t border-white/10 px-4 py-3 flex flex-col gap-1">
        <?php if ($is_listing_page): ?>
            <?php foreach ($listing_nav as $link): $is_active = ($link['type'] === $active_listing_type); ?>
            <a href="<?php echo $link['href']; ?>"
               class="flex items-center gap-2 text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors <?php echo $is_active ? 'text-primary bg-white/5' : 'text-white/70 hover:bg-white/10 hover:text-white'; ?>">
                <span class="material-symbols-outlined text-base"><?php echo $link['icon']; ?></span>
                <?php echo $link['label']; ?>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <?php foreach ($nav_links as $link): $is_active = ($link['href'] === $current_page); ?>
            <a href="<?php echo $link['href']; ?>"
               class="text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors <?php echo $is_active ? 'text-primary' : 'text-white/70 hover:bg-white/10 hover:text-white'; ?>">
                <?php echo $link['label']; ?>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="flex gap-2 pt-2 border-t border-white/10 mt-1" id="mob-auth-area">
            <a href="login.php" id="mob-login-btn" class="flex-1 text-center text-white hover:bg-white/10 text-sm font-semibold py-2 rounded-xl transition-all">Login</a>
            <a href="register.php" id="mob-register-btn" class="flex-1 text-center bg-primary text-white text-sm font-bold py-2 rounded-xl hover:bg-orange-600 transition-all">Register</a>
            <button id="mob-logout-btn" class="hidden flex-1 text-center bg-red-500 text-white text-sm font-bold py-2 rounded-xl hover:bg-red-600 transition-all">Logout</button>
        </div>
    </div>
    <script>
        document.getElementById('mob-btn').addEventListener('click', function(){
            document.getElementById('mob-menu').classList.toggle('hidden');
        });
        (function(){
            var h = document.getElementById('site-header');
            function updateHeader() {
                if (window.scrollY === 0) { h.classList.add('header-solid'); }
                else { h.classList.remove('header-solid'); }
            }
            updateHeader();
            window.addEventListener('scroll', updateHeader, {passive:true});
        })();

        // ── Auth state ──────────────────────────────────────────────────────
        (function(){
            var token = localStorage.getItem('csn_token');
            var user  = JSON.parse(localStorage.getItem('csn_user') || 'null');

            // Basic JWT expiry check (decode payload, check exp)
            if (token) {
                try {
                    var parts = token.split('.');
                    if (parts.length === 3) {
                        var payload = JSON.parse(atob(parts[1].replace(/-/g,'+').replace(/_/g,'/')));
                        if (payload.exp && payload.exp < Math.floor(Date.now()/1000)) {
                            localStorage.removeItem('csn_token');
                            localStorage.removeItem('csn_user');
                            token = null; user = null;
                        }
                    }
                } catch(e) {
                    localStorage.removeItem('csn_token');
                    localStorage.removeItem('csn_user');
                    token = null; user = null;
                }
            }

            if (token && user) {
                // Desktop: hide login/register, show user menu
                document.getElementById('hdr-login-btn').style.display    = 'none';
                document.getElementById('hdr-register-btn').style.display = 'none';
                var um = document.getElementById('hdr-user-menu');
                um.classList.remove('hidden');
                document.getElementById('hdr-user-name').textContent = user.name ? user.name.split(' ')[0] : 'Account';

                // Mobile: hide login/register, show logout
                document.getElementById('mob-login-btn').style.display    = 'none';
                document.getElementById('mob-register-btn').style.display = 'none';
                document.getElementById('mob-logout-btn').classList.remove('hidden');
            }

            // Desktop dropdown toggle
            var userBtn = document.getElementById('hdr-user-btn');
            var dropdown = document.getElementById('hdr-dropdown');
            if (userBtn) {
                userBtn.addEventListener('click', function(e){
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                });
                document.addEventListener('click', function(){ dropdown.classList.add('hidden'); });
            }

            // Logout handlers
            function doLogout() {
                localStorage.removeItem('csn_token');
                localStorage.removeItem('csn_user');
                window.location.href = 'index.php';
            }
            var logoutBtn = document.getElementById('hdr-logout-btn');
            if (logoutBtn) logoutBtn.addEventListener('click', doLogout);
            var mobLogout = document.getElementById('mob-logout-btn');
            if (mobLogout) mobLogout.addEventListener('click', doLogout);
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
