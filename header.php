<?php
// header.php – shared header for all CSNExplore pages
// Usage: set $page_title and $current_page before including this file
$current_page = $current_page ?? '';
$page_title   = $page_title   ?? 'CSNExplore – Chhatrapati Sambhajinagar';
$nav_links = [
    ['href' => BASE_PATH . '/index',   'label' => 'Home'],
    ['href' => BASE_PATH . '/about',   'label' => 'About Us'],
    ['href' => BASE_PATH . '/contact', 'label' => 'Contact Us'],
    ['href' => BASE_PATH . '/blogs',   'label' => 'Our Blogs'],
];

$listing_nav = [
    ['href' => BASE_PATH . '/listing/stays',       'icon' => 'bed',                'label' => 'Stays',       'type' => 'stays'],
    ['href' => BASE_PATH . '/listing/cars',        'icon' => 'directions_car',     'label' => 'Cars',        'type' => 'cars'],
    ['href' => BASE_PATH . '/listing/bikes',       'icon' => 'motorcycle',         'label' => 'Bikes',       'type' => 'bikes'],
    ['href' => BASE_PATH . '/listing/attractions', 'icon' => 'confirmation_number','label' => 'Attractions', 'type' => 'attractions'],
    ['href' => BASE_PATH . '/listing/restaurants', 'icon' => 'restaurant',         'label' => 'Dine',        'type' => 'restaurants'],
    ['href' => BASE_PATH . '/listing/buses',       'icon' => 'directions_bus',     'label' => 'Buses',       'type' => 'buses'],
];

// Show listing nav on listing pages AND listing-detail pages
$is_listing_page = ($current_page === 'listing' || $current_page === 'listing-detail' || isset($listing_type));
$active_listing_type = $listing_type ?? '';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_desc ?? 'Discover the best hotels, bikes, cars & attractions in Chhatrapati Sambhajinagar with CSNExplore.'); ?>">
    <meta name="keywords" content="Chhatrapati Sambhajinagar, Aurangabad, tourism, hotels, bike rent, car rent, attractions">
    <?php
        $canonical_url = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'csnexplore.com') . strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
        // Clean trailing slash or index.php if needed
        $canonical_url = rtrim($canonical_url, '/');
        if (str_ends_with($canonical_url, '/index.php') || str_ends_with($canonical_url, '/index')) {
            $canonical_url = str_replace(['/index.php', '/index'], '', $canonical_url);
        }
    ?>
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>" />
    
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_desc ?? 'Explore Chhatrapati Sambhajinagar (Aurangabad) with the best travel packages, guides, and rentals.'); ?>">
    <meta property="og:image" content="https://csnexplore.com/images/og-image.jpg">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($page_desc ?? 'Explore Chhatrapati Sambhajinagar (Aurangabad) with the best travel packages, guides, and rentals.'); ?>">
    <meta name="twitter:image" content="https://csnexplore.com/images/og-image.jpg">
    
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
                <?php
                $default_marquee = [
                    "★ 20% OFF on first heritage tour booking",
                    "★ Verified guides for Ajanta &amp; Ellora",
                    "★ Free cancellation on bike rentals",
                    "★ 24/7 tourist support available",
                ];
                global $hp_settings;
                if (!empty($hp_settings['marquee'])) {
                    $marquee_items = array_filter(array_map('trim', explode("\n", $hp_settings['marquee'])));
                    if (empty($marquee_items)) $marquee_items = $default_marquee;
                } else {
                    $marquee_items = $default_marquee;
                }
                $all_items = array_merge($marquee_items, $marquee_items);
                foreach ($all_items as $item): ?>
                    <span class="px-6"><?php echo htmlspecialchars($item); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Header – always dark/black with blur -->
<header id="site-header" class="sticky top-0 w-full z-50 transition-all duration-300 glass-dark border-b border-white/5">
    <nav class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        <a href="<?php echo BASE_PATH; ?>/index" class="flex items-center shrink-0">
            <img src="<?php echo BASE_PATH; ?>/images/travelhub.png" alt="CSNExplore" class="h-9 object-contain"/>
        </a>
        <div class="hidden md:flex items-center gap-0.5">
            <?php if ($is_listing_page): ?>
                <?php foreach ($listing_nav as $link): $is_active = ($link['type'] === $active_listing_type); ?>
                <a href="<?php echo $link['href']; ?>"
                   class="text-sm font-semibold px-3 py-1.5 rounded-full transition-colors <?php echo $is_active ? 'text-white bg-white/15' : 'text-white/60 hover:bg-white/10 hover:text-white'; ?>">
                    <?php echo $link['label']; ?>
                </a>
                <?php endforeach; ?>
            <?php else: ?>
                <?php foreach ($nav_links as $link): 
                    $is_active = (trim($link['href'], '/') === trim($current_page, '/') || ($current_page === 'home' && $link['href'] === '/index')); 
                ?>
                <a href="<?php echo $link['href']; ?>"
                   class="text-sm font-semibold px-4 py-2 rounded-full transition-colors <?php echo $is_active ? 'text-white bg-white/10' : 'text-white/70 hover:bg-white/10 hover:text-white'; ?>">
                    <?php echo $link['label']; ?>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo BASE_PATH; ?>/login?redirect=<?php echo urlencode($_SERVER['REQUEST_URI'] ?? '/'); ?>" id="hdr-login-btn" class="text-white text-sm font-semibold px-4 py-1.5 hover:bg-white/10 rounded-full transition-all">Login</a>
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
            <a href="tel:+918600968888" class="hidden lg:flex items-center gap-1.5 bg-slate-800 text-white text-sm font-bold px-4 py-1.5 rounded-full hover:bg-slate-700 transition-all border border-slate-700">
                <span class="material-symbols-outlined text-base text-primary">call</span> Call Now
            </a>
            <a href="https://wa.me/918600968888" target="_blank" class="hidden sm:flex items-center gap-1.5 bg-[#25D366] text-white text-sm font-bold px-4 py-1.5 rounded-full hover:bg-[#128C7E] transition-all shadow-lg shadow-[#25D366]/20">
                <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.825c1.63.975 3.41 1.487 5.23 1.488 5.439 0 9.861-4.422 9.863-9.861.001-2.636-1.024-5.115-2.884-6.977-1.862-1.864-4.341-2.887-6.979-2.888-5.439 0-9.861 4.422-9.863 9.862 0 1.842.511 3.641 1.478 5.187l-.995 3.637 3.73-.978zm11.367-7.643c-.31-.155-1.837-.906-2.12-.108-.285.103-.55.515-.674.654-.124.14-.248.155-.558.001-.31-.155-1.31-.483-2.498-1.543-.924-.824-1.548-1.841-1.73-2.15-.181-.31-.019-.477.135-.631.14-.139.31-.36.465-.541.155-.181.206-.31.31-.515.103-.206.052-.386-.026-.541-.077-.155-.674-1.626-.924-2.228-.243-.585-.491-.504-.674-.513-.175-.008-.375-.01-.575-.01s-.525.075-.8.375c-.275.3-1.05 1.026-1.05 2.5s1.075 2.9 1.225 3.1c.15.2 2.11 3.221 5.113 4.513.714.307 1.272.49 1.706.629.718.227 1.37.195 1.886.118.575-.085 1.837-.75 2.096-1.475.258-.725.258-1.346.181-1.475-.077-.129-.283-.206-.593-.361z"/></svg>
                WhatsApp
            </a>
            <button id="mob-btn" class="md:hidden p-2 rounded-lg transition-colors ml-1">
                <span class="material-symbols-outlined text-2xl text-white">menu</span>
            </button>
        </div>
    </nav>
    <div id="mob-menu" class="hidden md:hidden border-t border-white/10 px-4 py-3 flex flex-col gap-1">
        <?php if ($is_listing_page): ?>
            <?php foreach ($listing_nav as $link): $is_active = ($link['type'] === $active_listing_type); ?>
            <a href="<?php echo $link['href']; ?>"
               class="text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors <?php echo $is_active ? 'text-primary bg-white/5' : 'text-white/70 hover:bg-white/10 hover:text-white'; ?>">
                <?php echo $link['label']; ?>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <?php foreach ($nav_links as $link): 
                $is_active = (trim($link['href'], '/') === trim($current_page, '/') || ($current_page === 'home' && $link['href'] === '/index')); 
            ?>
            <a href="<?php echo $link['href']; ?>"
               class="text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors <?php echo $is_active ? 'text-primary bg-white/5' : 'text-white/70 hover:bg-white/10 hover:text-white'; ?>">
                <?php echo $link['label']; ?>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="flex gap-2 pt-2 border-t border-white/10 mt-1">
            <a href="<?php echo BASE_PATH; ?>/login?redirect=<?php echo urlencode($_SERVER['REQUEST_URI'] ?? '/'); ?>" id="mob-login-btn" class="flex-1 text-center text-white hover:bg-white/10 text-sm font-semibold py-2 rounded-xl transition-all">Login</a>
            <a href="tel:+918600968888" class="flex-1 text-center bg-slate-800 text-white text-sm font-bold py-2 rounded-xl hover:bg-slate-700 transition-all border border-slate-700">Call Now</a>
        </div>
        <a href="https://wa.me/918600968888" target="_blank" class="w-full mt-2 text-center bg-[#25D366] text-white text-sm font-bold py-2.5 rounded-xl hover:bg-[#128C7E] transition-all flex items-center justify-center gap-2">
            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.825c1.63.975 3.41 1.487 5.23 1.488 5.439 0 9.861-4.422 9.863-9.861.001-2.636-1.024-5.115-2.884-6.977-1.862-1.864-4.341-2.887-6.979-2.888-5.439 0-9.861 4.422-9.863 9.862 0 1.842.511 3.641 1.478 5.187l-.995 3.637 3.73-.978zm11.367-7.643c-.31-.155-1.837-.906-2.12-.108-.285.103-.55.515-.674.654-.124.14-.248.155-.558.001-.31-.155-1.31-.483-2.498-1.543-.924-.824-1.548-1.841-1.73-2.15-.181-.31-.019-.477.135-.631.14-.139.31-.36.465-.541.155-.181.206-.31.31-.515.103-.206.052-.386-.026-.541-.077-.155-.674-1.626-.924-2.228-.243-.585-.491-.504-.674-.513-.175-.008-.375-.01-.575-.01s-.525.075-.8.375c-.275.3-1.05 1.026-1.05 2.5s1.075 2.9 1.225 3.1c.15.2 2.11 3.221 5.113 4.513.714.307 1.272.49 1.706.629.718.227 1.37.195 1.886.118.575-.085 1.837-.75 2.096-1.475.258-.725.258-1.346.181-1.475-.077-.129-.283-.206-.593-.361z"/></svg> WhatsApp
        </a>
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

        (function(){
            var token=localStorage.getItem("csn_token");
            var user=JSON.parse(localStorage.getItem("csn_user")||"null");
            function clearAll(){
              localStorage.removeItem("csn_token"); localStorage.removeItem("csn_user");
              localStorage.removeItem("csn_admin_token"); localStorage.removeItem("csn_admin_user");
            }
            if(token){try{var parts=token.split(".");if(parts.length===3){var p=JSON.parse(atob(parts[1].replace(/-/g,"+").replace(/_/g,"/")));if(p.exp&&p.exp<Math.floor(Date.now()/1000)){clearAll();token=null;user=null;}}}catch(e){clearAll();token=null;user=null;}}
            if(token&&user){
              var pl=document.getElementById("hdr-login-btn");
              var ml=document.getElementById("mob-login-btn");
              var pu=document.getElementById("hdr-user-menu");
              var pn=document.getElementById("hdr-user-name");
              if(pl)pl.style.display="none";
              if(ml)ml.style.display="none";
              if(pu)pu.classList.remove("hidden");
              if(pn)pn.textContent=user.name?user.name.split(" ")[0]:"Account";
            }
            var userBtn=document.getElementById("hdr-user-btn");
            var dropdown=document.getElementById("hdr-dropdown");
            if(userBtn){
              userBtn.addEventListener("click",function(e){e.stopPropagation();dropdown.classList.toggle("hidden");});
              document.addEventListener("click",function(){if(dropdown)dropdown.classList.add("hidden");});
            }
            function doLogout(){clearAll();window.location.reload();}
            var lb=document.getElementById("hdr-logout-btn");
            if(lb)lb.addEventListener("click",doLogout);
        })();
    </script>
</header>
