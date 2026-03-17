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
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; font-family:'Material Symbols Outlined'; font-style:normal; display:inline-block; line-height:1; }
        <?php if (!empty($extra_styles)) echo $extra_styles; ?>
    </style>
    <?php if (!empty($extra_head)) echo $extra_head; ?>
</head>
<body class="bg-white dark:bg-background-dark font-display text-slate-900 dark:text-slate-100">

<!-- Marquee Bar -->
<div class="bg-primary text-white py-1.5 overflow-hidden relative z-[60]">
    <div class="max-w-7xl mx-auto px-4 flex justify-between items-center gap-4 text-[11px] font-semibold uppercase tracking-widest">
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
        <div class="hidden md:flex items-center gap-4 shrink-0">
            <a href="tel:+918600968888" class="flex items-center gap-1.5 hover:text-white/80 transition-colors">
                <span class="material-symbols-outlined text-[14px]">call</span>+91 86009 68888
            </a>
            <a href="https://wa.me/918600968888" class="flex items-center gap-1.5 bg-white/15 px-3 py-0.5 rounded-full hover:bg-white/25 transition-all">
                <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.825c1.63.975 3.41 1.487 5.23 1.488 5.439 0 9.861-4.422 9.863-9.861.001-2.636-1.024-5.115-2.884-6.977-1.862-1.864-4.341-2.887-6.979-2.888-5.439 0-9.861 4.422-9.863 9.862 0 1.842.511 3.641 1.478 5.187l-.995 3.637 3.73-.978zm11.367-7.643c-.31-.155-1.837-.906-2.12-.108-.285.103-.55.515-.674.654-.124.14-.248.155-.558.001-.31-.155-1.31-.483-2.498-1.543-.924-.824-1.548-1.841-1.73-2.15-.181-.31-.019-.477.135-.631.14-.139.31-.36.465-.541.155-.181.206-.31.31-.515.103-.206.052-.386-.026-.541-.077-.155-.674-1.626-.924-2.228-.243-.585-.491-.504-.674-.513-.175-.008-.375-.01-.575-.01s-.525.075-.8.375c-.275.3-1.05 1.026-1.05 2.5s1.075 2.9 1.225 3.1c.15.2 2.11 3.221 5.113 4.513.714.307 1.272.49 1.706.629.718.227 1.37.195 1.886.118.575-.085 1.837-.75 2.096-1.475.258-.725.258-1.346.181-1.475-.077-.129-.283-.206-.593-.361z"/></svg>
                WhatsApp
            </a>
        </div>
    </div>
</div>

<!-- Header – always dark/black with blur -->
<header id="site-header" class="sticky top-0 w-full z-50 transition-all duration-300 glass-dark border-b border-white/5">
    <nav class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
        <a href="index.php" class="flex items-center shrink-0">
            <img src="images/travelhub.png" alt="CSNExplore" class="h-14 object-contain"/>
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
        <div class="flex items-center gap-2">
            <a href="login.php" class="text-white text-sm font-semibold px-4 py-1.5 hover:bg-white/10 rounded-full transition-all">Login</a>
            <a href="register.php" class="bg-primary text-white text-sm font-bold px-5 py-1.5 rounded-full hover:bg-orange-600 transition-all shadow-lg shadow-primary/20">Register</a>
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
        <div class="flex gap-2 pt-2 border-t border-white/10 mt-1">
            <a href="login.php" class="flex-1 text-center text-white hover:bg-white/10 text-sm font-semibold py-2 rounded-xl transition-all">Login</a>
            <a href="register.php" class="flex-1 text-center bg-primary text-white text-sm font-bold py-2 rounded-xl hover:bg-orange-600 transition-all">Register</a>
        </div>
    </div>
    <script>
        document.getElementById('mob-btn').addEventListener('click', function(){
            document.getElementById('mob-menu').classList.toggle('hidden');
        });
    </script>
</header>
