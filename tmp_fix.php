<?php

// Fix generate_html.php
$gen = file_get_contents('php/api/generate_html.php');
$gen = preg_replace(
    '/function sharedHeader\(\$base\) \{.*?\n\}/s',
    "function sharedHeader(\$base) {\n    return file_get_contents(dirname(__DIR__, 2) . '/header-html.html');\n}",
    $gen
);

// Add basic SEO tags to htmlHead
$seo = '
<meta name="description" content="Discover the best hotels, bikes, cars & attractions in Chhatrapati Sambhajinagar with CSNExplore.">
<meta name="keywords" content="Chhatrapati Sambhajinagar, Aurangabad, tourism, hotels, bike rent, car rent, attractions">
<meta property="og:title" content="\' . htmlspecialchars($title) . \'">
<meta property="og:description" content="Explore Chhatrapati Sambhajinagar (Aurangabad) with the best travel packages, guides, and rentals.">
';
$gen = preg_replace('/<title>.*?<\/title>/s', "<title>' . htmlspecialchars(\$title) . '</title>\n$seo", $gen);
file_put_contents('php/api/generate_html.php', $gen);


// Fix index.php
$idx = file_get_contents('index.php');

$extra_head_in_idx = <<<EOD
<?php
\$page_desc = "Your premium gateway to the wonders of Chhatrapati Sambhajinagar, Maharashtra. Book hotels, cars, bikes, and explore attractions easily.";
\$extra_head = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css"/><script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>';
\$extra_styles = '
        .hide-scrollbar::-webkit-scrollbar { display:none; }
        .hide-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }
        .card-hover:hover { box-shadow:0 0 30px rgba(236,91,19,0.15); }
        :root { --card-w-attractions: 80vw; --card-w-bikes: 80vw; --card-w-restaurants: 80vw; --card-w-buses: 80vw; --card-w-blogs: 80vw; }
        @media (min-width: 640px) { :root { --card-w-attractions: calc(50% - 14px); --card-w-bikes: calc(50% - 14px); --card-w-restaurants: calc(50% - 14px); --card-w-buses: calc(50% - 14px); --card-w-blogs: calc(50% - 14px); } }
        @media (min-width: 1024px) { :root { --card-w-attractions: calc(25% - 15px); --card-w-bikes: calc(25% - 15px); --card-w-restaurants: calc(20% - 16px); --card-w-buses: calc(50% - 10px); --card-w-blogs: calc(33.333% - 14px); } }
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
          .search-box { padding:14px; }
          .search-row { flex-wrap:wrap; gap:8px; }
          .search-field { flex:1 1 100%; height:46px; }
          .date-field { flex:1 1 calc(50% - 4px); height:46px; min-width:0; }
          .search-btn { width:100%; justify-content:center; height:46px; font-size:14px; padding:0 16px; }
          .tab-btn { padding:6px 10px; font-size:12px; }
          .tab-btn .material-symbols-outlined { font-size:15px; }
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
EOD;

$idx = preg_replace('/<\!DOCTYPE html>.*?<\/header>/s', $extra_head_in_idx, $idx);
file_put_contents('index.php', $idx);

echo "Done editing templates.";
