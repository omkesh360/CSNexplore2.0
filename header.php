<?php
// header.php – shared header for all CSNExplore pages
// Usage: set $page_title and $current_page before including this file

// Initialize performance optimization (caching, etc.) - Skip for admin and API pages
$skip_cache = isset($current_page) && (
    strpos($current_page, 'admin') !== false || 
    strpos($_SERVER['REQUEST_URI'] ?? '', '/php/api/') !== false ||
    strpos($_SERVER['REQUEST_URI'] ?? '', 'listing.php') !== false
);

if (!$skip_cache && file_exists(__DIR__ . '/php/performance-optimizer/bootstrap.php')) {
    require_once __DIR__ . '/php/performance-optimizer/bootstrap.php';
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    <link href="mobile-responsive.css" rel="stylesheet"/>
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
        .glass { background:rgba(255,255,255,0.08); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,0.15); box-shadow:0 8px 32px rgba(0,0,0,0.1); }
        .glass-dark { background:rgba(10,7,5,0.75); backdrop-filter:blur(25px); -webkit-backdrop-filter:blur(25px); border:1px solid rgba(236,91,19,0.15); box-shadow:0 8px 32px rgba(0,0,0,0.3); }
        .glass-light { background:rgba(255,255,255,0.12); backdrop-filter:blur(18px); -webkit-backdrop-filter:blur(18px); border:1px solid rgba(255,255,255,0.2); }
        .glass-card { background:rgba(255,255,255,0.06); backdrop-filter:blur(16px); -webkit-backdrop-filter:blur(16px); border:1px solid rgba(255,255,255,0.1); box-shadow:0 4px 24px rgba(0,0,0,0.08); }
        .glass-button { background:rgba(236,91,19,0.85); backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.2); box-shadow:0 4px 16px rgba(236,91,19,0.3); }
        .glass-button:hover { background:rgba(236,91,19,0.95); box-shadow:0 6px 24px rgba(236,91,19,0.4); }
        .glass-input { background:rgba(255,255,255,0.08); backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.15); color:#fff; }
        .glass-input::placeholder { color:rgba(255,255,255,0.6); }
        .glass-input:focus { background:rgba(255,255,255,0.12); border-color:rgba(255,255,255,0.25); outline:none; }
        .glass-badge { background:rgba(236,91,19,0.8); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,0.15); }
        .glass-overlay { background:rgba(0,0,0,0.4); backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px); }
        .glass-nav { background:rgba(0,0,0,0.5); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border-bottom:1px solid rgba(255,255,255,0.1); }
        .glass-modal { background:rgba(255,255,255,0.95); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,0.3); box-shadow:0 20px 60px rgba(0,0,0,0.15); }
        .glass-section { background:linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%); backdrop-filter:blur(15px); -webkit-backdrop-filter:blur(15px); border:1px solid rgba(255,255,255,0.08); }
        .glass-glow { background:rgba(236,91,19,0.1); backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); border:1px solid rgba(236,91,19,0.2); box-shadow:0 0 30px rgba(236,91,19,0.15); }
        .header-solid { background:#000000 !important; backdrop-filter:none !important; -webkit-backdrop-filter:none !important; }
        
        /* Mobile-first header height optimization */
        #site-header {
            height: 56px; /* Mobile height */
        }
        
        #site-header nav {
            height: 56px; /* Mobile nav height */
            padding-left: var(--space-4);
            padding-right: var(--space-4);
        }
        
        /* Desktop header height */
        @media (min-width: 768px) {
            #site-header {
                height: 64px; /* Desktop height */
            }
            
            #site-header nav {
                height: 64px; /* Desktop nav height */
                padding-left: var(--space-6);
                padding-right: var(--space-6);
            }
        }
        /* Page transition */
        html { background:#fff; }
        body { opacity:0; will-change:opacity; backface-visibility:hidden; -webkit-backface-visibility:hidden; }
        body.page-ready { animation: pageFadeIn 0.15s ease-out forwards; }
        @keyframes pageFadeIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; font-family:'Material Symbols Outlined'; font-style:normal; display:inline-block; line-height:1; }

        /* ── Global Motion System ── */
        [data-reveal] {
            opacity: 0;
            transform: translateY(32px);
            transition: opacity 0.65s cubic-bezier(.22,1,.36,1), transform 0.65s cubic-bezier(.22,1,.36,1);
        }
        [data-reveal="left"]  { transform: translateX(-40px); }
        [data-reveal="right"] { transform: translateX(40px); }
        [data-reveal="scale"] { transform: scale(0.92) translateY(20px); }
        [data-reveal].revealed {
            opacity: 1 !important;
            transform: none !important;
        }
        [data-delay="1"] { transition-delay: 0.08s; }
        [data-delay="2"] { transition-delay: 0.16s; }
        [data-delay="3"] { transition-delay: 0.24s; }
        [data-delay="4"] { transition-delay: 0.32s; }
        [data-delay="5"] { transition-delay: 0.40s; }
        [data-delay="6"] { transition-delay: 0.48s; }

        /* Card hover glow */
        .card-glow { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card-glow:hover { transform: translateY(-6px) scale(1.01); box-shadow: 0 20px 50px rgba(236,91,19,0.18), 0 4px 16px rgba(0,0,0,0.12); }

        /* Shimmer on images */
        .img-shimmer { position:relative; overflow:hidden; }
        .img-shimmer::after { content:''; position:absolute; inset:0; background:linear-gradient(105deg,transparent 40%,rgba(255,255,255,0.08) 50%,transparent 60%); transform:translateX(-100%); transition:transform 0.6s ease; }
        .img-shimmer:hover::after { transform:translateX(100%); }

        /* Pulse ring on CTA buttons */
        .btn-pulse { position:relative; }
        .btn-pulse::before { content:''; position:absolute; inset:-3px; border-radius:inherit; border:2px solid rgba(236,91,19,0.5); opacity:0; animation:btnRing 2s ease infinite; }
        @keyframes btnRing { 0%,100%{opacity:0;transform:scale(1)} 50%{opacity:1;transform:scale(1.04)} }

        /* Floating orbs background */
        .orb { position:absolute; border-radius:50%; filter:blur(60px); pointer-events:none; animation:orbFloat 8s ease-in-out infinite; }
        @keyframes orbFloat { 0%,100%{transform:translateY(0) scale(1)} 50%{transform:translateY(-20px) scale(1.05)} }

        <?php if (!empty($extra_styles)) echo $extra_styles; ?>
    </style>
    <?php if (!empty($extra_head)) echo $extra_head; ?>
</head>
<body class="bg-white dark:bg-background-dark font-display text-slate-900 dark:text-slate-100">

<!-- Promo Bar - Always visible at top -->
<div class="bg-primary text-white py-1.5 overflow-hidden relative z-[70]">
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

<!-- Sticky Header that transforms to pill -->
<div id="hdr-wrap" class="sticky top-0 z-[60] pointer-events-none transition-all duration-300" style="padding:0">
    <header id="site-header" class="w-full pointer-events-auto transition-all duration-300" style="background:#000000;border-radius:0;border-bottom:1px solid rgba(255,255,255,0.08);box-shadow:none;border-left:none;border-right:none;border-top:none;backdrop-filter:none;-webkit-backdrop-filter:none;">
        <nav class="flex items-center justify-between">
            <!-- Logo -->
            <a href="index.php" class="flex items-center shrink-0">
                <img src="images/travelhub.png" alt="CSNExplore" class="h-8 sm:h-9 object-contain" loading="lazy"/>
            </a>

            <!-- Desktop / Tablet nav links (hidden on mobile) -->
            <div class="hidden md:flex items-center gap-0.5">
                <?php if ($is_listing_page): ?>
                    <?php foreach ($listing_nav as $link): $is_active = ($link['type'] === $active_listing_type); ?>
                    <a href="<?php echo $link['href']; ?>"
                       class="flex items-center gap-1.5 text-sm font-semibold px-3 py-1.5 rounded-full transition-colors <?php echo $is_active ? 'text-white bg-white/15' : 'text-white/60 hover:bg-white/10 hover:text-white'; ?>">
                        <span class="material-symbols-outlined text-base"><?php echo $link['icon']; ?></span>
                        <?php echo $link['label']; ?>
                    </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php foreach ($nav_links as $link): $is_active = ($link['href'] === $current_page); ?>
                    <a href="<?php echo $link['href']; ?>"
                       class="text-sm font-semibold px-4 py-1.5 rounded-full transition-colors <?php echo $is_active ? 'text-white bg-white/15' : 'text-white/60 hover:bg-white/10 hover:text-white'; ?>">
                        <?php echo $link['label']; ?>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Right: auth + hamburger -->
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
                <!-- Logged-out buttons -->
                <a href="login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI'] ?? ''); ?>" id="hdr-login-btn" class="text-white/80 hover:text-white text-xs sm:text-sm font-semibold px-3 sm:px-4 py-1.5 rounded-full hover:bg-white/10 transition-all">Login</a>
                <!-- Logged-in user menu -->
                <div id="hdr-user-menu" class="hidden relative">
                    <button id="hdr-user-btn" class="flex items-center gap-1.5 text-white text-xs sm:text-sm font-semibold px-3 py-1.5 hover:bg-white/10 rounded-full transition-all">
                        <span class="material-symbols-outlined text-base text-primary">account_circle</span>
                        <span id="hdr-user-name" class="max-w-[70px] sm:max-w-[100px] truncate"></span>
                        <span class="material-symbols-outlined text-sm">expand_more</span>
                    </button>
                    <div id="hdr-dropdown" class="hidden absolute right-0 top-full mt-2 w-44 bg-[#1a1208] border border-white/10 rounded-2xl shadow-2xl py-1.5 z-[200]">
                        <button id="hdr-logout-btn" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-400 hover:bg-white/5 hover:text-red-300 transition-colors rounded-xl mx-0">
                            <span class="material-symbols-outlined text-base">logout</span>Logout
                        </button>
                    </div>
                </div>
                <!-- Hamburger (mobile only) -->
                <button id="mob-btn" class="md:hidden p-1.5 rounded-full hover:bg-white/10 transition-colors">
                    <span class="material-symbols-outlined text-xl text-white">menu</span>
                </button>
            </div>
        </nav>
    </header>

    <!-- Mobile dropdown nav -->
    <div id="mob-menu" class="hidden md:hidden fixed inset-x-0 top-[64px] mx-4 rounded-3xl bg-[#0a0705]/95 backdrop-blur-2xl border border-white/10 shadow-[0_20px_50px_rgba(0,0,0,0.5)] px-3 py-4 flex flex-col gap-1.5 pointer-events-auto z-[100] animate-in fade-in slide-in-from-top-4 duration-300">
        <?php if ($is_listing_page): ?>
            <?php foreach ($listing_nav as $link): $is_active = ($link['type'] === $active_listing_type); ?>
            <a href="<?php echo $link['href']; ?>"
               class="flex items-center gap-3 text-sm font-bold px-5 py-3.5 rounded-2xl transition-all <?php echo $is_active ? 'text-primary bg-white/10' : 'text-white/70 hover:bg-white/5 active:scale-[0.98]'; ?>">
                <span class="material-symbols-outlined text-lg"><?php echo $link['icon']; ?></span>
                <?php echo $link['label']; ?>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <?php foreach ($nav_links as $link): $is_active = ($link['href'] === $current_page); ?>
            <a href="<?php echo $link['href']; ?>"
               class="text-sm font-bold px-5 py-4 rounded-2xl transition-all <?php echo $is_active ? 'text-primary bg-white/10' : 'text-white/70 hover:bg-white/5 active:scale-[0.98]'; ?>">
                <?php echo $link['label']; ?>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="h-px bg-white/10 my-1 mx-4"></div>
        <a href="tel:+918600968888" class="flex items-center gap-3 text-white/80 px-5 py-3.5 text-sm font-bold"><span class="material-symbols-outlined text-lg text-primary">call</span>Call Us</a>
        <a href="https://wa.me/918600968888" class="flex items-center gap-3 text-whatsapp px-5 py-3.5 text-sm font-bold"><span class="material-symbols-outlined text-lg">chat</span>WhatsApp</a>
    </div>
</div>
    <script>
        // Morph header: flat full-width at top → floating pill on scroll
        (function(){
            var wrap = document.getElementById('hdr-wrap');
            var hdr  = document.getElementById('site-header');
            var callBtn = document.getElementById('call-btn');
            var whatsappBtn = document.getElementById('whatsapp-btn');
            var callText = callBtn ? callBtn.querySelector('.call-text') : null;
            var whatsappText = whatsappBtn ? whatsappBtn.querySelector('.whatsapp-text') : null;
            
            function onScroll(){
                if(window.scrollY > 10){
                    wrap.style.padding = '12px 20px 4px';
                    hdr.style.borderRadius = '9999px';
                    hdr.style.background   = 'rgba(0,0,0,0.75)';
                    hdr.style.backdropFilter = 'blur(20px)';
                    hdr.style.webkitBackdropFilter = 'blur(20px)';
                    hdr.style.border = '1px solid rgba(255,255,255,0.12)';
                    hdr.style.boxShadow = '0 8px 32px rgba(0,0,0,0.6)';
                    
                    if(callText) callText.style.display = 'none';
                    if(whatsappText) whatsappText.style.display = 'none';
                    if(callBtn) {
                        callBtn.classList.remove('gap-1', 'px-2');
                        callBtn.classList.add('w-9', 'h-9', 'justify-center');
                    }
                    if(whatsappBtn) {
                        whatsappBtn.classList.remove('gap-1', 'px-2');
                        whatsappBtn.classList.add('w-9', 'h-9', 'justify-center');
                    }
                } else {
                    wrap.style.padding = '0';
                    hdr.style.borderRadius = '0';
                    hdr.style.background   = '#000000';
                    hdr.style.backdropFilter = 'none';
                    hdr.style.webkitBackdropFilter = 'none';
                    hdr.style.border = 'none';
                    hdr.style.borderBottom = '1px solid rgba(255,255,255,0.08)';
                    hdr.style.boxShadow = 'none';
                    
                    if(callText) callText.style.display = 'inline';
                    if(whatsappText) whatsappText.style.display = 'inline';
                    if(callBtn) {
                        callBtn.classList.add('gap-1', 'px-2');
                        callBtn.classList.remove('w-9', 'h-9', 'justify-center');
                    }
                    if(whatsappBtn) {
                        whatsappBtn.classList.add('gap-1', 'px-2');
                        whatsappBtn.classList.remove('w-9', 'h-9', 'justify-center');
                    }
                }
            }
            window.addEventListener('scroll', onScroll, {passive:true});
            onScroll();
        })();

        document.getElementById('mob-btn').addEventListener('click', function(){
            document.getElementById('mob-menu').classList.toggle('hidden');
        });

        (function(){
            var token = localStorage.getItem('csn_token');
            var user  = JSON.parse(localStorage.getItem('csn_user') || 'null');

            function clearAll() {
                localStorage.removeItem('csn_token');
                localStorage.removeItem('csn_user');
                localStorage.removeItem('csn_admin_token');
                localStorage.removeItem('csn_admin_user');
                token = null; user = null;
            }

            if (token) {
                try {
                    var parts = token.split('.');
                    if (parts.length === 3) {
                        var payload = JSON.parse(atob(parts[1].replace(/-/g,'+').replace(/_/g,'/')));
                        if (payload.exp && payload.exp < Math.floor(Date.now()/1000)) { clearAll(); }
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
                document.addEventListener('click', function(){ if(dropdown) dropdown.classList.add('hidden'); });
            }

            function doLogout() {
                clearAll();
                window.location.href = 'index.php';
            }
            var lb = document.getElementById('hdr-logout-btn');
            if (lb) lb.addEventListener('click', doLogout);
        })();

        function addPageReady(){document.body.classList.add('page-ready');}
        if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',addPageReady);}else{addPageReady();}
        document.addEventListener('click', function(e) {
            var a = e.target.closest('a');
            if (!a) return;
            var href = a.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript') || href.startsWith('whatsapp:') || href.startsWith('https://wa.me') || a.target === '_blank') return;
            e.preventDefault();
            document.body.style.transition = 'opacity 0.12s ease-out';
            document.body.style.opacity = '0';
            setTimeout(function(){ window.location.href = href; }, 130);
        });
    </script>
<script>
// ── Global Scroll Reveal ──────────────────────────────────────────────────
(function(){
    if (!window.IntersectionObserver) return;
    var io = new IntersectionObserver(function(entries){
        entries.forEach(function(e){
            if(e.isIntersecting){ e.target.classList.add('revealed'); io.unobserve(e.target); }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -30px 0px' });
    function observe(){
        document.querySelectorAll('[data-reveal]').forEach(function(el){ io.observe(el); });
    }
    if(document.readyState==='loading'){ document.addEventListener('DOMContentLoaded', observe); } else { observe(); }
})();
</script>
