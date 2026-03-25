<?php
// php/api/generate_html.php
// Generates static HTML files for all blogs and all listing items
// Visit: /php/api/generate_html.php?secret=csnexplore_seed
require_once __DIR__ . '/../config.php';

$secret = $_GET['secret'] ?? $argv[1] ?? '';
if (php_sapi_name() !== 'cli' && $secret !== 'csnexplore_seed') {
    http_response_code(403); die('Forbidden');
}

$db   = getDB();
$root = dirname(__DIR__, 2); // workspace root

// ── Shared HTML helpers ───────────────────────────────────────────────────────
function htmlHead($title, $depth = 0) {
    $base = str_repeat('../', $depth);
    return '<!DOCTYPE html>
<html lang="en" style="scroll-behavior:smooth">
<head>
<meta charset="utf-8"/>
<link rel="preconnect" href="https://cdn.tailwindcss.com">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="dns-prefetch" href="https://images.unsplash.com">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>' . htmlspecialchars($title) . '</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<script>tailwind.config={{theme:{{extend:{{colors:{{"primary":"#ec5b13"}},fontFamily:{{display:["Inter","sans-serif"],serif:["Playfair Display","serif"]}}}}}}}}</script>
<style>
body{opacity:0;will-change:opacity;}
body.page-ready{animation:pageFadeIn 0.15s ease-out forwards;}
@keyframes pageFadeIn{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:translateY(0);}}
.material-symbols-outlined{font-variation-settings:"FILL" 0,"wght" 400,"GRAD" 0,"opsz" 24;font-family:"Material Symbols Outlined";font-style:normal;display:inline-block;line-height:1;}
@keyframes marquee{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
.prose h2{font-size:1.5rem;font-weight:800;margin:2rem 0 0.75rem;}
.prose h3{font-size:1.2rem;font-weight:700;margin:1.5rem 0 0.5rem;}
.prose p{margin-bottom:1.1rem;line-height:1.85;}
.prose ul{list-style:disc;padding-left:1.5rem;margin-bottom:1.1rem;}
.prose ul li{margin-bottom:0.4rem;line-height:1.7;}
.prose strong{font-weight:700;}
.line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}

/* ── Global Motion System ── */
[data-reveal]{opacity:0;transform:translateY(32px);transition:opacity 0.65s cubic-bezier(.22,1,.36,1),transform 0.65s cubic-bezier(.22,1,.36,1);}
[data-reveal="left"]{transform:translateX(-40px);}
[data-reveal="right"]{transform:translateX(40px);}
[data-reveal="scale"]{transform:scale(0.92) translateY(20px);}
[data-reveal].revealed{opacity:1!important;transform:none!important;}
[data-delay="1"]{transition-delay:0.08s;}[data-delay="2"]{transition-delay:0.16s;}
[data-delay="3"]{transition-delay:0.24s;}[data-delay="4"]{transition-delay:0.32s;}
[data-delay="5"]{transition-delay:0.40s;}[data-delay="6"]{transition-delay:0.48s;}
.card-glow{transition:transform 0.3s ease,box-shadow 0.3s ease;}
.card-glow:hover{transform:translateY(-6px) scale(1.01);box-shadow:0 20px 50px rgba(236,91,19,0.18),0 4px 16px rgba(0,0,0,0.12);}
.img-shimmer{position:relative;overflow:hidden;}
.img-shimmer::after{content:\"\";position:absolute;inset:0;background:linear-gradient(105deg,transparent 40%,rgba(255,255,255,0.08) 50%,transparent 60%);transform:translateX(-100%);transition:transform 0.6s ease;}
.img-shimmer:hover::after{transform:translateX(100%);}
/* ── Glassmorphism Effects ── */
.glass{background:rgba(255,255,255,0.08);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,0.15);box-shadow:0 8px 32px rgba(0,0,0,0.1);}
.glass-card{background:rgba(255,255,255,0.06);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,0.1);box-shadow:0 4px 24px rgba(0,0,0,0.08);}
.glass-button{background:rgba(236,91,19,0.85);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.2);box-shadow:0 4px 16px rgba(236,91,19,0.3);}
.glass-button:hover{background:rgba(236,91,19,0.95);box-shadow:0 6px 24px rgba(236,91,19,0.4);}
.glass-input{background:rgba(255,255,255,0.08);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.15);color:#fff;}
.glass-input::placeholder{color:rgba(255,255,255,0.6);}
.glass-input:focus{background:rgba(255,255,255,0.12);border-color:rgba(255,255,255,0.25);outline:none;}
.glass-badge{background:rgba(236,91,19,0.8);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.15);}
.glass-overlay{background:rgba(0,0,0,0.4);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);}
.glass-section{background:linear-gradient(135deg,rgba(255,255,255,0.05) 0%,rgba(255,255,255,0.02) 100%);backdrop-filter:blur(15px);-webkit-backdrop-filter:blur(15px);border:1px solid rgba(255,255,255,0.08);}
.glass-glow{background:rgba(236,91,19,0.1);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid rgba(236,91,19,0.2);box-shadow:0 0 30px rgba(236,91,19,0.15);}
/* ── Gallery Lightbox ── */
.gallery-thumb{cursor:zoom-in;position:relative;overflow:hidden;border-radius:12px;aspect-ratio:4/3;background:#f1f5f9;border:1px solid #e2e8f0;}
.gallery-thumb img{width:100%;height:100%;object-fit:cover;transition:all 0.4s cubic-bezier(0.4, 0, 0.2, 1);background:#f1f5f9;}
.gallery-thumb:hover img{transform:scale(1.08);filter:brightness(1.05);}
.gallery-thumb::after{content:"\e8ff";font-family:"Material Symbols Outlined";position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.3);color:#fff;font-size:26px;opacity:0;transition:opacity 0.25s;pointer-events:none;}
.gallery-thumb:hover::after{opacity:1;}
.gallery-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px;}
#csn-gallery-modal{position:fixed !important;top:0 !important;left:0 !important;width:100% !important;height:100% !important;background:rgba(0,0,0,0.85) !important;z-index:999999999 !important;display:none !important;align-items:center !important;justify-content:center !important;overflow:hidden !important;}
#csn-gallery-modal.active{display:flex !important;}
.csn-modal-content{position:fixed !important;top:50% !important;left:50% !important;transform:translate(-50%,-50%) !important;background:transparent !important;border-radius:0 !important;padding:0 !important;max-width:85vw !important;max-height:85vh !important;display:flex !important;flex-direction:column !important;align-items:center !important;justify-content:center !important;box-shadow:none !important;z-index:1000000000 !important;overflow:visible !important;border:none !important;}
.csn-modal-content img{width:85vw !important;height:85vh !important;max-width:85vw !important;max-height:85vh !important;object-fit:contain !important;border-radius:0 !important;transition:transform 0.3s !important;border:none !important;outline:none !important;box-shadow:none !important;}
.csn-modal-close{position:fixed !important;top:30px !important;right:30px !important;background:#ec5b13 !important;border:none !important;color:#fff !important;width:40px !important;height:40px !important;border-radius:50% !important;cursor:pointer !important;font-size:24px !important;display:flex !important;align-items:center !important;justify-content:center !important;transition:all 0.3s !important;font-weight:bold !important;z-index:1000000001 !important;}
.csn-modal-close:hover{background:#d94a0a !important;transform:scale(1.1) !important;}
.csn-modal-nav{position:fixed !important;top:50% !important;transform:translateY(-50%) !important;background:#ec5b13 !important;border:none !important;color:#fff !important;width:45px !important;height:45px !important;border-radius:50% !important;cursor:pointer !important;font-size:20px !important;display:flex !important;align-items:center !important;justify-content:center !important;transition:all 0.3s !important;z-index:1000000001 !important;}
.csn-modal-nav:hover{background:#d94a0a !important;transform:translateY(-50%) scale(1.1) !important;}
.csn-modal-prev{left:20px !important;}
.csn-modal-next{right:20px !important;}
.csn-modal-counter{position:fixed !important;bottom:30px !important;left:50% !important;transform:translateX(-50%) !important;background:#ec5b13 !important;color:#fff !important;padding:8px 16px !important;border-radius:20px !important;font-size:13px !important;font-weight:bold !important;z-index:1000000001 !important;}
.csn-zoom-controls{position:fixed !important;bottom:30px !important;right:30px !important;display:flex !important;gap:8px !important;z-index:1000000001 !important;}
.csn-zoom-btn{background:#ec5b13 !important;border:none !important;color:#fff !important;width:36px !important;height:36px !important;border-radius:50% !important;cursor:pointer !important;font-size:18px !important;display:flex !important;align-items:center !important;justify-content:center !important;transition:all 0.3s !important;font-weight:bold !important;}
.csn-zoom-btn:hover{background:#d94a0a !important;transform:scale(1.1) !important;}
.csn-modal-content img.zoomed{max-width:none !important;max-height:none !important;width:auto !important;height:auto !important;}
@media(max-width:768px){
  .csn-modal-content{padding:0 !important;max-width:95vw !important;max-height:85vh !important;}
  .csn-modal-content img{width:95vw !important;height:85vh !important;max-width:95vw !important;max-height:85vh !important;}
  .csn-modal-close{top:15px !important;right:15px !important;width:36px !important;height:36px !important;font-size:20px !important;}
  .csn-modal-nav{width:40px !important;height:40px !important;font-size:18px !important;}
  .csn-modal-prev{left:10px !important;}
  .csn-modal-next{right:10px !important;}
  .csn-zoom-controls{bottom:15px !important;right:15px !important;gap:6px !important;}
  .csn-zoom-btn{width:32px !important;height:32px !important;font-size:16px !important;}
}
/* ── Gallery Grid ── */
.gallery-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px;}
@media(max-width:768px){.gallery-grid{grid-template-columns:repeat(2,1fr);gap:10px;}}
@media(max-width:480px){.gallery-grid{grid-template-columns:repeat(2,1fr);gap:8px;}}
</style>
</head>
<body class="bg-slate-50 font-display text-slate-900">
' . sharedHeader($base);
}

function sharedHeader($base) {
    $nav = [
        ['href'=>'index.php','label'=>'Home'],
        ['href'=>'about.php','label'=>'About Us'],
        ['href'=>'contact.php','label'=>'Contact Us'],
        ['href'=>'blogs.php','label'=>'Blogs'],
    ];
    $links = '';
    foreach ($nav as $n) {
        $links .= '<a href="'.$base.$n['href'].'" class="text-sm font-semibold px-4 py-1.5 rounded-full text-white/60 hover:bg-white/10 hover:text-white transition-colors">'.$n['label'].'</a>';
    }
    $moblinks = '';
    foreach ($nav as $n) {
        $moblinks .= '<a href="'.$base.$n['href'].'" class="text-sm font-semibold px-4 py-2.5 rounded-xl text-white/70 hover:bg-white/10 hover:text-white transition-colors">'.$n['label'].'</a>';
    }
    return '
<div class="sticky top-0 bg-[#ec5b13] text-white py-1.5 overflow-hidden z-[70]">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 text-[11px] font-semibold uppercase tracking-widest overflow-hidden">
    <div class="flex whitespace-nowrap" style="animation:marquee 30s linear infinite">
      <span class="px-6">★ Verified guides for Ajanta &amp; Ellora</span>
      <span class="px-6">★ Free cancellation on bike rentals</span>
      <span class="px-6">★ 24/7 tourist support available</span>
      <span class="px-6">★ 20% OFF on first heritage tour booking</span>
      <span class="px-6">★ Verified guides for Ajanta &amp; Ellora</span>
      <span class="px-6">★ Free cancellation on bike rentals</span>
      <span class="px-6">★ 24/7 tourist support available</span>
      <span class="px-6">★ 20% OFF on first heritage tour booking</span>
    </div>
  </div>
</div>
<div id="hdr-wrap" class="sticky top-[32px] z-[60] transition-all duration-300" style="padding:0">
  <header id="site-header" class="w-full transition-all duration-300" style="background:#000000;border-bottom:1px solid rgba(255,255,255,0.08);border-radius:0;">
    <nav class="px-4 sm:px-6 flex items-center justify-between" style="height:56px">
    <a href="'.$base.'index.php" class="flex items-center shrink-0">
      <img src="'.$base.'images/travelhub.png" alt="CSNExplore" class="h-8 sm:h-9 object-contain" loading="lazy"/>
    </a>
    <div class="hidden md:flex items-center gap-0.5">'.$links.'</div>
    <div class="flex items-center gap-1.5">
      <a href="tel:+918600968888" id="call-btn" class="hidden sm:flex items-center gap-1 text-white/70 hover:text-white text-xs font-semibold px-2 py-1.5 rounded-full hover:bg-white/10 transition-all" title="Call Us">
        <span class="material-symbols-outlined text-base">call</span>
        <span class="call-text">+91 86009 68888</span>
      </a>
      <a href="https://wa.me/918600968888" id="whatsapp-btn" target="_blank" rel="noopener" class="hidden sm:flex items-center gap-1 text-[#25D366] hover:text-white text-xs font-semibold px-2 py-1.5 rounded-full hover:bg-white/10 transition-all" title="WhatsApp Us">
        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        <span class="whatsapp-text">WhatsApp</span>
      </a>
      <a href="'.$base.'login.php" id="hdr-login-btn" class="text-white/80 hover:text-white text-xs sm:text-sm font-semibold px-3 sm:px-4 py-1.5 rounded-full hover:bg-white/10 transition-all">Login</a>
      <div id="hdr-user-menu" class="hidden relative">
        <button id="hdr-user-btn" class="flex items-center gap-1.5 text-white text-xs sm:text-sm font-semibold px-3 py-1.5 hover:bg-white/10 rounded-full transition-all">
          <span class="material-symbols-outlined text-base text-[#ec5b13]">account_circle</span>
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
</div>
<div id="mob-menu" class="hidden md:hidden sticky top-[88px] mx-3 rounded-2xl bg-black border border-white/10 shadow-2xl px-2 py-2 flex flex-col gap-0.5 z-[50]">
  '.$moblinks.'
</div>
<script>
  // Pill transformation on scroll
  (function(){
    var wrap = document.getElementById("hdr-wrap");
    var hdr = document.getElementById("site-header");
    
    function onScroll(){
      if(window.scrollY > 50){
        wrap.style.padding = "12px 20px";
        hdr.style.borderRadius = "9999px";
        hdr.style.background = "rgba(0,0,0,0.8)";
        hdr.style.backdropFilter = "blur(20px)";
        hdr.style.webkitBackdropFilter = "blur(20px)";
        hdr.style.border = "1px solid rgba(255,255,255,0.15)";
        hdr.style.boxShadow = "0 8px 32px rgba(0,0,0,0.6)";
      } else {
        wrap.style.padding = "0";
        hdr.style.borderRadius = "0";
        hdr.style.background = "#000000";
        hdr.style.backdropFilter = "none";
        hdr.style.webkitBackdropFilter = "none";
        hdr.style.border = "1px solid rgba(255,255,255,0.08)";
        hdr.style.boxShadow = "none";
      }
    }
    window.addEventListener("scroll", onScroll, {passive:true});
    onScroll();
  })();

  document.getElementById("mob-btn").addEventListener("click",function(){
    document.getElementById("mob-menu").classList.toggle("hidden");
  });

  (function(){
    var token=localStorage.getItem("csn_token");
    var user=JSON.parse(localStorage.getItem("csn_user")||"null");
    function clearAll(){
      localStorage.removeItem("csn_token");localStorage.removeItem("csn_user");
      localStorage.removeItem("csn_admin_token");localStorage.removeItem("csn_admin_user");
      token=null;user=null;
    }
    if(token){
      try{
        var parts=token.split(".");
        if(parts.length===3){
          var p=JSON.parse(atob(parts[1].replace(/-/g,"+").replace(/_/g,"/")));
          if(p.exp&&p.exp<Math.floor(Date.now()/1000)){clearAll();}
        }else{clearAll();}
      }catch(e){clearAll();}
    }
    if(token&&user){
      var pl=document.getElementById("hdr-login-btn");
      var pu=document.getElementById("hdr-user-menu");
      var pn=document.getElementById("hdr-user-name");
      if(pl)pl.style.display="none";
      if(pu)pu.classList.remove("hidden");
      if(pn)pn.textContent=user.name?user.name.split(" ")[0]:"Account";
    }
    var userBtn=document.getElementById("hdr-user-btn");
    var dropdown=document.getElementById("hdr-dropdown");
    if(userBtn){
      userBtn.addEventListener("click",function(e){e.stopPropagation();dropdown.classList.toggle("hidden");});
      document.addEventListener("click",function(){if(dropdown)dropdown.classList.add("hidden");});
    }
    function doLogout(){clearAll();window.location.href="'.$base.'index.php";}
    var lb=document.getElementById("hdr-logout-btn");
    if(lb)lb.addEventListener("click",doLogout);
  })();

  function addPageReady(){document.body.classList.add("page-ready");}
  if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",addPageReady);}else{addPageReady();}

  document.addEventListener("click",function(e){
    var a=e.target.closest("a");
    if(!a)return;
    var href=a.getAttribute("href");
    if(!href||href.startsWith("#")||href.startsWith("mailto:")||href.startsWith("tel:")||href.startsWith("javascript")||href.startsWith("whatsapp:")||href.startsWith("https://wa.me")||a.target==="_blank")return;
    e.preventDefault();
    document.body.style.transition="opacity 0.12s ease-out";
    document.body.style.opacity="0";
    setTimeout(function(){window.location.href=href;},130);
  });
</script>
';
}

function sharedFooter($base) {
    return '
<footer class="bg-[#0a0705] text-white pt-14 pb-8 mt-16">
  <div class="max-w-7xl mx-auto px-4">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
      <div>
        <img src="'.$base.'images/travelhub.png" alt="CSNExplore" class="h-9 object-contain mb-4"/>
        <p class="text-white/50 text-sm leading-relaxed">Your premium gateway to the wonders of Chhatrapati Sambhajinagar, Maharashtra.</p>
      </div>
      <div>
        <h5 class="font-bold text-sm mb-4">Quick Links</h5>
        <ul class="flex flex-col gap-2.5 text-white/50 text-sm">
          <li><a href="'.$base.'listing.php?type=stays" class="hover:text-[#ec5b13] transition-colors">Hotel Bookings</a></li>
          <li><a href="'.$base.'listing.php?type=cars" class="hover:text-[#ec5b13] transition-colors">Car Rentals</a></li>
          <li><a href="'.$base.'listing.php?type=bikes" class="hover:text-[#ec5b13] transition-colors">Bike Rentals</a></li>
          <li><a href="'.$base.'listing.php?type=attractions" class="hover:text-[#ec5b13] transition-colors">Heritage Sites</a></li>
          <li><a href="'.$base.'blogs.php" class="hover:text-[#ec5b13] transition-colors">Travel Guide</a></li>
        </ul>
      </div>
      <div>
        <h5 class="font-bold text-sm mb-4">Contact Info</h5>
        <ul class="flex flex-col gap-3 text-white/50 text-sm">
          <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[#ec5b13] text-base mt-0.5 shrink-0">location_on</span>Behind State Bank Of India, Plot No. 273 Samarth Nagar, Central Bus Stand, Chhatrapati Sambhajinagar, Maharashtra 431001</li>
          <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[#ec5b13] text-base">call</span><a href="tel:+918600968888" class="hover:text-[#ec5b13]">+91 86009 68888</a></li>
          <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[#ec5b13] text-base">mail</span><a href="mailto:supportcsnexplore@gmail.com" class="hover:text-[#ec5b13]">supportcsnexplore@gmail.com</a></li>
        </ul>
      </div>
      <div>
        <h5 class="font-bold text-sm mb-4">Stay Updated</h5>
        <p class="text-white/50 text-sm mb-4">Get travel tips and exclusive deals in your inbox.</p>
        <a href="'.$base.'blogs.php" class="inline-block bg-[#ec5b13] text-white font-bold py-2.5 px-6 rounded-xl text-sm hover:bg-orange-600 transition-colors">Read Blogs</a>
      </div>
    </div>
    <div class="border-t border-white/10 pt-6 flex flex-col md:flex-row items-center justify-between gap-3 text-white/30 text-xs">
      <p>© ' . date('Y') . ' CSNExplore. All rights reserved.</p>
    </div>
  </div>
</footer>
<button id="go-top-btn" onclick="window.scrollTo({top:0,behavior:\'smooth\'})"
    style="position:fixed;bottom:28px;right:24px;z-index:9999;width:48px;height:48px;border-radius:50%;background:#ec5b13;color:#fff;border:none;cursor:pointer;box-shadow:0 4px 20px rgba(236,91,19,0.5);display:flex;align-items:center;justify-content:center;opacity:0;visibility:hidden;transform:translateY(12px);transition:opacity .25s ease,visibility .25s ease,transform .25s ease;"
    aria-label="Go to top">
    <span class="material-symbols-outlined" style="font-size:22px;line-height:1;pointer-events:none;">arrow_upward</span>
</button>
<script>
(function(){
    var btn=document.getElementById("go-top-btn");
    function updateBtn(){
        if(window.scrollY>200){btn.style.opacity="1";btn.style.visibility="visible";btn.style.transform="translateY(0)";}
        else{btn.style.opacity="0";btn.style.visibility="hidden";btn.style.transform="translateY(12px)";}
    }
    updateBtn();
    window.addEventListener("scroll",updateBtn,{passive:true});
})();
</script>

<script>
(function(){
  if(!window.IntersectionObserver)return;
  var io=new IntersectionObserver(function(entries){
    entries.forEach(function(e){if(e.isIntersecting){e.target.classList.add(\'revealed\');io.unobserve(e.target);}});
  },{threshold:0.1,rootMargin:\'0px 0px -30px 0px\'});
  function obs(){document.querySelectorAll(\'[data-reveal]\').forEach(function(el){io.observe(el);});}
  if(document.readyState===\'loading\'){document.addEventListener(\'DOMContentLoaded\',obs);}else{obs();}
})();
</script>

<!-- Gallery Modal Popup -->
<div id="csn-gallery-modal" style="display:none !important;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.85);z-index:999999999 !important;align-items:center;justify-content:center;" onclick="if(event.target.id==\'csn-gallery-modal\')csnCloseGallery()">
  <div class="csn-modal-content" onclick="event.stopPropagation()">
    <button class="csn-modal-close" onclick="csnCloseGallery()">✕</button>
    <button class="csn-modal-nav csn-modal-prev" onclick="csnPrevImage()">❮</button>
    <img id="csn-modal-image" src="" alt="Gallery image">
    <button class="csn-modal-nav csn-modal-next" onclick="csnNextImage()">❯</button>
    <div class="csn-modal-counter"><span id="csn-image-counter">1</span> / <span id="csn-total-images">1</span></div>
    <div class="csn-zoom-controls">
      <button class="csn-zoom-btn" onclick="csnZoomIn()" title="Zoom In">+</button>
      <button class="csn-zoom-btn" onclick="csnZoomOut()" title="Zoom Out">−</button>
    </div>
  </div>
</div>
</body></html>';
}

function slugify($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

$log = [];

// ── 1. Generate Blog HTML files ───────────────────────────────────────────────
$blogsDir = $root . '/blogs';
if (!is_dir($blogsDir)) mkdir($blogsDir, 0755, true);

$blogs = $db->fetchAll("SELECT * FROM blogs WHERE status='published' ORDER BY id ASC");
foreach ($blogs as &$b) $b['tags'] = json_decode($b['tags'] ?? '[]', true) ?: [];
unset($b);

// Related: build category index
$byCategory = [];
foreach ($blogs as $b) $byCategory[$b['category']][] = $b;

$blogCount = 0;
foreach ($blogs as $blog) {
    $slug = $blog['id'] . '-' . slugify($blog['title']);
    $slug = substr($slug, 0, 80);
    $file = $blogsDir . '/' . $slug . '.html';

    // Related: same category, up to 3, exclude self
    $related = array_filter($byCategory[$blog['category']] ?? [], fn($r) => $r['id'] !== $blog['id']);
    $related = array_slice(array_values($related), 0, 3);

    $relatedHtml = '';
    foreach ($related as $r) {
        $rSlug = $r['id'] . '-' . substr(slugify($r['title']), 0, 60);
        $relatedHtml .= '
        <a href="'.$rSlug.'.html" class="group flex flex-col bg-white rounded-2xl overflow-hidden border border-slate-100 hover:shadow-lg transition-shadow">
          <div class="aspect-video overflow-hidden">
            <img src="'.htmlspecialchars($r['image'] ?? '').'" alt="'.htmlspecialchars($r['title']).'" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" onerror="this.src=\'../images/travelhub.png\'"/>
          </div>
          <div class="p-4">
            <span class="text-xs font-bold text-[#ec5b13] uppercase mb-2 block">'.htmlspecialchars($r['category']).'</span>
            <h4 class="text-sm font-bold line-clamp-2 group-hover:text-[#ec5b13] transition-colors">'.htmlspecialchars($r['title']).'</h4>
            <p class="text-xs text-slate-400 mt-2">'.date('M d, Y', strtotime($r['created_at'])).'</p>
          </div>
        </a>';
    }

    $tagsHtml = '';
    foreach ($blog['tags'] as $tag) {
        $tagsHtml .= '<a href="../blogs.php?search='.urlencode($tag).'" class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-semibold hover:bg-[#ec5b13] hover:text-white transition-colors">'.htmlspecialchars($tag).'</a>';
    }

        // Resolve main image path
        $mainImg = trim($blog['image'] ?? '');
        if (empty($mainImg)) {
            $mainImg = '../images/travelhub.png';
        } elseif (strpos($mainImg, 'http') !== 0 && strpos($mainImg, '../') !== 0 && strpos($mainImg, '/') !== 0) {
            $mainImg = '../' . $mainImg;
        }

    $html = htmlHead(htmlspecialchars($blog['title']) . ' | CSNExplore', 1);
    $html .= '
<main class="bg-white min-h-screen">
  <!-- Hero: shared image with breadcrumb at top, blog title at bottom -->
  <div class="w-full h-[420px] md:h-[500px] relative overflow-hidden">
    <img src="'.htmlspecialchars($mainImg).'" alt="Blog Hero" class="w-full h-full object-cover" loading="lazy"/>
    <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-[#0a0705]"></div>
    <!-- Breadcrumb at very top -->
    <div class="absolute top-0 left-0 right-0 pt-5">
      <div class="max-w-4xl mx-auto px-4 flex items-center gap-2 text-sm text-white/60 flex-wrap">
        <a href="../index.php" class="hover:text-white transition-colors flex items-center gap-1"><span class="material-symbols-outlined text-base">home</span>Home</a>
        <span class="material-symbols-outlined text-base">chevron_right</span>
        <a href="../blogs.php" class="hover:text-white transition-colors">Blogs</a>
        <span class="material-symbols-outlined text-base">chevron_right</span>
        <a href="../blogs.php?category='.urlencode($blog['category']).'" class="hover:text-white transition-colors">'.htmlspecialchars($blog['category']).'</a>
        <span class="material-symbols-outlined text-base">chevron_right</span>
        <span class="text-white/80 font-semibold truncate max-w-xs">'.htmlspecialchars($blog['title']).'</span>
      </div>
    </div>
    <div class="absolute bottom-0 left-0 right-0 max-w-4xl mx-auto px-4 pb-10">
      <span class="inline-block bg-[#ec5b13] text-white text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-4">'.htmlspecialchars($blog['category']).'</span>
      <h1 class="text-white text-3xl md:text-4xl lg:text-5xl font-serif font-black leading-tight">'.htmlspecialchars($blog['title']).'</h1>
    </div>
  </div>
  <div class="max-w-4xl mx-auto px-4 py-10">
    <div class="flex flex-wrap items-center gap-5 mb-10 pb-8 border-b border-slate-100">
      <div class="flex items-center gap-2 text-slate-500 text-sm"><span class="material-symbols-outlined text-base">person</span><span class="font-semibold">'.htmlspecialchars($blog['author']).'</span></div>
      <div class="flex items-center gap-2 text-slate-500 text-sm"><span class="material-symbols-outlined text-base">calendar_today</span><span>'.date('F d, Y', strtotime($blog['created_at'])).'</span></div>
      '.($blog['read_time'] ? '<div class="flex items-center gap-2 text-slate-500 text-sm"><span class="material-symbols-outlined text-base">schedule</span><span>'.htmlspecialchars($blog['read_time']).'</span></div>' : '').'
      '.($blog['meta_description'] ? '<p class="w-full text-slate-500 text-base italic mt-1">'.htmlspecialchars($blog['meta_description']).'</p>' : '').'
    </div>
    <article class="prose text-slate-800 max-w-none text-base leading-relaxed">'.$blog['content'].'</article>
    '.($tagsHtml ? '<div class="mt-10 pt-8 border-t border-slate-100 flex flex-wrap gap-2 items-center"><span class="text-xs font-bold text-slate-400 uppercase tracking-widest mr-2">Tags:</span>'.$tagsHtml.'</div>' : '').'
    <div class="mt-8 flex items-center gap-4 flex-wrap">
      <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Share:</span>
      <a href="https://twitter.com/intent/tweet?text='.urlencode($blog['title']).'&url='.urlencode('https://csnexplore.com/blogs/'.$slug.'.html').'" target="_blank" rel="noopener" class="flex items-center gap-1.5 px-4 py-2 bg-[#1DA1F2] text-white rounded-xl text-xs font-bold hover:opacity-90">
        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
        X / Twitter
      </a>
      <button onclick="navigator.clipboard.writeText(window.location.href).then(()=>alert(\'Link copied!\'))" class="flex items-center gap-1.5 px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-200 transition-colors">
        <span class="material-symbols-outlined text-base">link</span>Copy Link
      </button>
    </div>
    <div class="mt-10"><a href="../blogs.php" class="inline-flex items-center gap-2 text-[#ec5b13] font-bold hover:underline text-sm"><span class="material-symbols-outlined text-base">arrow_back</span>Back to all blogs</a></div>
  </div>
  '.($relatedHtml ? '
  <div class="border-t border-slate-100 bg-slate-50 py-14">
    <div class="max-w-4xl mx-auto px-4">
      <h3 class="text-2xl font-serif font-black mb-8 flex items-center gap-3"><span class="w-8 h-1 bg-[#ec5b13] rounded-full inline-block"></span>Related Stories</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">'.$relatedHtml.'</div>
    </div>
  </div>' : '').'
</main>';
    $html .= sharedFooter('../');

    file_put_contents($file, $html);
    $blogCount++;
}
$log[] = "Blogs: $blogCount HTML files → /blogs/";

// ── 2. Generate Listing Detail HTML files ─────────────────────────────────────
$listingsDir = $root . '/listing-detail';
if (!is_dir($listingsDir)) mkdir($listingsDir, 0755, true);

$types = ['stays','cars','bikes','attractions','restaurants','buses'];
$typeLabels = [
    'stays'       => ['label'=>'Stays',       'icon'=>'bed',                'unit'=>'/ night',   'price_col'=>'price_per_night'],
    'cars'        => ['label'=>'Car Rentals',  'icon'=>'directions_car',     'unit'=>'/ day',     'price_col'=>'price_per_day'],
    'bikes'       => ['label'=>'Bike Rentals', 'icon'=>'motorcycle',         'unit'=>'/ day',     'price_col'=>'price_per_day'],
    'attractions' => ['label'=>'Attractions',  'icon'=>'confirmation_number','unit'=>' entry',    'price_col'=>'entry_fee'],
    'restaurants' => ['label'=>'Restaurants',  'icon'=>'restaurant',         'unit'=>' for two',  'price_col'=>'price_per_person'],
    'buses'       => ['label'=>'Buses',        'icon'=>'directions_bus',     'unit'=>'',          'price_col'=>'price'],
];

$listingCount = 0;
foreach ($types as $type) {
    $meta  = $typeLabels[$type];
    $items = $db->fetchAll("SELECT * FROM $type WHERE is_active=1 ORDER BY display_order ASC, id ASC");

    foreach ($items as $item) {
        // Normalize name field (buses use 'operator')
        if ($type === 'buses' && !isset($item['name'])) {
            $item['name'] = $item['operator'] ?? 'bus';
        }

        // Decode JSON fields
        foreach (['amenities','features','gallery','menu_highlights'] as $f) {
            if (isset($item[$f]) && is_string($item[$f])) $item[$f] = json_decode($item[$f], true) ?: [];
        }

        $slug = $type . '-' . $item['id'] . '-' . substr(slugify($item['name'] ?? 'item'), 0, 60);
        $file = $listingsDir . '/' . $slug . '.html';

        $price_val = $item[$meta['price_col']] ?? 0;
        $price_fmt = $price_val > 0 ? '₹' . number_format($price_val) : 'Free';

        if ($type === 'buses') {
            $location = htmlspecialchars(($item['from_location'] ?? '') . ' → ' . ($item['to_location'] ?? ''));
        } else {
            $location = htmlspecialchars($item['location'] ?? '');
        }

        // Features/amenities list
        $featuresList = '';
        $feats = $item['amenities'] ?? $item['features'] ?? $item['menu_highlights'] ?? [];
        foreach ($feats as $feat) {
            $featuresList .= '<li class="flex items-center gap-2 text-sm text-slate-600"><span class="material-symbols-outlined text-[#ec5b13] text-base">check_circle</span>'.htmlspecialchars($feat).'</li>';
        }

        // Gallery
        $galleryImages = is_array($item['gallery']) ? $item['gallery'] : json_decode($item['gallery'] ?? '[]', true) ?? [];

        $galleryHtml = '';
        $resolvedGalleryImages = [];
        foreach ($galleryImages as $idx => $img) {
            // Fix path for uploaded images
            $resolvedImg = $img;
            if (strpos($resolvedImg, 'http') !== 0 && strpos($resolvedImg, '../') !== 0 && strpos($resolvedImg, '/') !== 0) {
                $resolvedImg = '../' . $resolvedImg;
            }
            $resolvedGalleryImages[] = $resolvedImg;
            
            $galleryHtml .= '<div class="gallery-thumb" onclick="openLightbox('.$idx.')" title="Click to zoom">'.
                '<img src="'.htmlspecialchars($resolvedImg).'" loading="lazy" alt="'.htmlspecialchars($item['name']).' photo '.($idx+1).'" onerror="this.src=\'../images/travelhub.png\'"/>'.
                '</div>';
        }
        // Build JS array of gallery images for lightbox
        $galleryJsArr = json_encode(array_values($resolvedGalleryImages));

        // Extra fields by type
        $extraMeta = '';
        if ($type === 'stays') {
            if (!empty($item['room_type'])) $extraMeta .= '<div class="flex items-center gap-2 text-sm text-slate-600"><span class="material-symbols-outlined text-[#ec5b13] text-base">bed</span>'.htmlspecialchars($item['room_type']).'</div>';
            if (!empty($item['max_guests'])) $extraMeta .= '<div class="flex items-center gap-2 text-sm text-slate-600"><span class="material-symbols-outlined text-[#ec5b13] text-base">group</span>Up to '.(int)$item['max_guests'].' guests</div>';
        } elseif ($type === 'cars') {
            if (!empty($item['fuel_type'])) $extraMeta .= '<div class="flex items-center gap-2 text-sm text-slate-600"><span class="material-symbols-outlined text-[#ec5b13] text-base">local_gas_station</span>'.htmlspecialchars($item['fuel_type']).'</div>';
            if (!empty($item['transmission'])) $extraMeta .= '<div class="flex items-center gap-2 text-sm text-slate-600"><span class="material-symbols-outlined text-[#ec5b13] text-base">settings</span>'.htmlspecialchars($item['transmission']).'</div>';
            if (!empty($item['seats'])) $extraMeta .= '<div class="flex items-center gap-2 text-sm text-slate-600"><span class="material-symbols-outlined text-[#ec5b13] text-base">airline_seat_recline_normal</span>'.(int)$item['seats'].' seats</div>';
        } elseif ($type === 'bikes') {
            if (!empty($item['fuel_type'])) $extraMeta .= '<div class="flex items-center gap-2 text-sm text-slate-600"><span class="material-symbols-outlined text-[#ec5b13] text-base">local_gas_station</span>'.htmlspecialchars($item['fuel_type']).'</div>';
            if (!empty($item['cc'])) $extraMeta .= '<div class="flex items-center gap-2 text-sm text-slate-600"><span class="material-symbols-outlined text-[#ec5b13] text-base">speed</span>'.htmlspecialchars($item['cc']).' CC</div>';
        } elseif ($type === 'attractions') {
            if (!empty($item['opening_hours'])) $extraMeta .= '<div class="flex items-center gap-2 text-sm text-slate-600"><span class="material-symbols-outlined text-[#ec5b13] text-base">schedule</span>'.htmlspecialchars($item['opening_hours']).'</div>';
            if (!empty($item['best_time'])) $extraMeta .= '<div class="flex items-center gap-2 text-sm text-slate-600"><span class="material-symbols-outlined text-[#ec5b13] text-base">wb_sunny</span>Best time: '.htmlspecialchars($item['best_time']).'</div>';
        } elseif ($type === 'restaurants') {
            if (!empty($item['cuisine'])) $extraMeta .= '<div class="flex items-center gap-2 text-sm text-slate-600"><span class="material-symbols-outlined text-[#ec5b13] text-base">restaurant_menu</span>'.htmlspecialchars($item['cuisine']).'</div>';
        } elseif ($type === 'buses') {
            if (!empty($item['departure_time'])) $extraMeta .= '<div class="flex items-center gap-2 text-sm text-slate-600"><span class="material-symbols-outlined text-[#ec5b13] text-base">departure_board</span>Departs: '.htmlspecialchars($item['departure_time']).'</div>';
            if (!empty($item['arrival_time'])) $extraMeta .= '<div class="flex items-center gap-2 text-sm text-slate-600"><span class="material-symbols-outlined text-[#ec5b13] text-base">flag</span>Arrives: '.htmlspecialchars($item['arrival_time']).'</div>';
            if (!empty($item['duration'])) $extraMeta .= '<div class="flex items-center gap-2 text-sm text-slate-600"><span class="material-symbols-outlined text-[#ec5b13] text-base">timer</span>Duration: '.htmlspecialchars($item['duration']).'</div>';
        }

        // Resolve main image path
        $mainImg = trim($item['image'] ?? '');
        if (empty($mainImg)) {
            $mainImg = '../images/travelhub.png';
        } elseif (strpos($mainImg, 'http') !== 0 && strpos($mainImg, '../') !== 0 && strpos($mainImg, '/') !== 0) {
            $mainImg = '../' . $mainImg;
        }

        $html = htmlHead(htmlspecialchars($item['name']) . ' | CSNExplore', 1);
        $html .= '
<main class="bg-slate-50 min-h-screen">
  <!-- Hero: shared image with breadcrumb at top, item name at bottom -->
  <div class="relative h-64 md:h-80 overflow-hidden">
    <img src="'.htmlspecialchars($mainImg).'" alt="'.htmlspecialchars($item['name']).'" class="w-full h-full object-cover" loading="lazy"/>
    <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-[#0a0705]"></div>
    <!-- Breadcrumb at very top -->
    <div class="absolute top-0 left-0 right-0 pt-5">
      <div class="max-w-6xl mx-auto px-4 flex items-center gap-2 text-sm text-white/60 flex-wrap">
        <a href="../index.php" class="hover:text-white transition-colors flex items-center gap-1"><span class="material-symbols-outlined text-base">home</span>Home</a>
        <span class="material-symbols-outlined text-base">chevron_right</span>
        <a href="../listing.php?type='.$type.'" class="hover:text-white transition-colors">'.htmlspecialchars($meta['label']).'</a>
        <span class="material-symbols-outlined text-base">chevron_right</span>
        <span class="text-white/80 font-semibold">'.htmlspecialchars($item['name']).'</span>
      </div>
    </div>
    <!-- Title at bottom -->
    <div class="absolute bottom-0 left-0 right-0 pb-6">
      <div class="max-w-6xl mx-auto px-4">
        <span class="text-xs font-bold text-[#ec5b13] uppercase tracking-widest mb-2 block">'.htmlspecialchars($meta['label']).'</span>
        <h1 class="text-white text-2xl md:text-3xl font-serif font-black leading-tight">'.htmlspecialchars($item['name']).'</h1>
      </div>
    </div>
  </div>
  <div class="max-w-6xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Left: details -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Title & rating card -->
        <div class="bg-white rounded-2xl p-6 shadow-sm">
          <div class="flex items-start justify-between gap-4 mb-3">
            <div>
              <div class="flex items-center gap-1 bg-amber-50 px-3 py-1.5 rounded-xl">
                <span class="material-symbols-outlined text-amber-400 text-lg">star</span>
                <span class="font-bold text-lg">'.number_format((float)($item['rating'] ?? 0), 1).'</span>
                <span class="text-slate-400 text-xs">('.((int)($item['reviews'] ?? 0)).' reviews)</span>
              </div>
            </div>
          </div>
          <div class="flex items-center gap-2 text-slate-500 text-sm mb-4">
            <span class="material-symbols-outlined text-base">location_on</span>
            <span>'.$location.'</span>
          </div>
          '.($extraMeta ? '<div class="flex flex-wrap gap-4 mb-4">'.$extraMeta.'</div>' : '').'
          '.(!empty($item['description']) ? '<p class="text-slate-600 leading-relaxed">'.htmlspecialchars($item['description']).'</p>' : '').'
        </div>
        '.($featuresList ? '
        <div class="bg-white rounded-2xl p-6 shadow-sm">
          <h3 class="text-lg font-bold mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-[#ec5b13]">checklist</span>Features &amp; Amenities</h3>
          <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3">'.$featuresList.'</ul>
        </div>' : '').'
        '.($galleryHtml ? '
        <div class="bg-white rounded-2xl p-6 shadow-sm">
          <h3 class="text-lg font-bold mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-[#ec5b13]">photo_library</span>Photo Gallery <span class="text-xs font-normal text-slate-400 ml-1">(click to zoom)</span></h3>
          <div class="gallery-grid">'.$galleryHtml.'</div>
        </div>' : '').'
      </div>
      <!-- Right: booking card -->
      <div class="space-y-4">
        <div class="bg-white rounded-2xl p-6 shadow-sm relative top-24">
          <!-- Need help? -->
          <div class="flex flex-col gap-2 mb-4 pb-4 border-b border-slate-100">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Need help?</p>
            <a href="tel:+918600968888" class="flex items-center gap-2 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold py-2.5 px-3 rounded-xl text-sm transition-colors">
              <span class="material-symbols-outlined text-base text-[#ec5b13]">call</span>+91 86009 68888
            </a>
            <a href="https://wa.me/918600968888" class="flex items-center gap-2 bg-green-50 hover:bg-green-100 text-green-700 font-bold py-2.5 px-3 rounded-xl text-sm transition-colors">
              <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.825c1.63.975 3.41 1.487 5.23 1.488 5.439 0 9.861-4.422 9.863-9.861.001-2.636-1.024-5.115-2.884-6.977-1.862-1.864-4.341-2.887-6.979-2.888-5.439 0-9.861 4.422-9.863 9.862 0 1.842.511 3.641 1.478 5.187l-.995 3.637 3.73-.978zm11.367-7.643c-.31-.155-1.837-.906-2.12-.108-.285.103-.55.515-.674.654-.124.14-.248.155-.558.001-.31-.155-1.31-.483-2.498-1.543-.924-.824-1.548-1.841-1.73-2.15-.181-.31-.019-.477.135-.631.14-.139.31-.36.465-.541.155-.181.206-.31.31-.515.103-.206.052-.386-.026-.541-.077-.155-.674-1.626-.924-2.228-.243-.585-.491-.504-.674-.513-.175-.008-.375-.01-.575-.01s-.525.075-.8.375c-.275.3-1.05 1.026-1.05 2.5s1.075 2.9 1.225 3.1c.15.2 2.11 3.221 5.113 4.513.714.307 1.272.49 1.706.629.718.227 1.37.195 1.886.118.575-.085 1.837-.75 2.096-1.475.258-.725.258-1.346.181-1.475-.077-.129-.283-.206-.593-.361z"/></svg>
              WhatsApp
            </a>
          </div>
          <div class="mb-4">
            <span class="text-3xl font-black text-[#ec5b13]">'.$price_fmt.'</span>
            '.($meta['unit'] && $price_val > 0 ? '<span class="text-slate-400 text-sm font-medium">'.htmlspecialchars($meta['unit']).'</span>' : '').'
          </div>
          '.(!empty($item['badge']) ? '<span class="inline-block bg-[#ec5b13] text-white text-xs font-bold px-3 py-1 rounded-full mb-4">'.htmlspecialchars($item['badge']).'</span>' : '').'
          <!-- Login required gate -->
          <div id="booking-login-gate" class="hidden">
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 text-center">
              <span class="material-symbols-outlined text-amber-500 text-3xl mb-2 block">lock</span>
              <p class="font-bold text-slate-800 mb-1">Sign in to book</p>
              <p class="text-sm text-slate-500 mb-4">Please log in to make a booking request.</p>
              <a href="../login.php?redirect='.$slug.'.html" class="inline-block w-full bg-[#ec5b13] text-white font-black py-3 rounded-2xl hover:bg-orange-600 transition-all text-center">Sign In</a>
              <a href="../register.php" class="inline-block w-full mt-2 border border-[#ec5b13] text-[#ec5b13] font-bold py-3 rounded-2xl hover:bg-orange-50 transition-all text-center text-sm">Create Account</a>
            </div>
          </div>
          <form id="booking-form" class="space-y-3 hidden">
            <div>
              <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Full Name *</label>
              <input type="text" id="b-name" required placeholder="Your name" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#ec5b13]/30"/>
            </div>
            <div>
              <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Phone *</label>
              <input type="tel" id="b-phone" required placeholder="+91 XXXXX XXXXX" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#ec5b13]/30"/>
            </div>
            '.($type === 'stays' ? '
            <div>
              <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Check-in Date *</label>
              <input type="date" id="b-checkin" required class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#ec5b13]/30"/>
            </div>
            <div>
              <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Check-out Date *</label>
              <input type="date" id="b-checkout" required class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#ec5b13]/30"/>
            </div>' : '
            <div>
              <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Date</label>
              <input type="date" id="b-date" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#ec5b13]/30"/>
            </div>').'
            <div>
              <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Guests</label>
              <input type="number" id="b-guests" min="1" max="20" value="1" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#ec5b13]/30"/>
            </div>
            <div id="booking-success" class="hidden bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
              <div class="flex items-start gap-2 mb-2">
                <span class="material-symbols-outlined text-base shrink-0 mt-0.5">check_circle</span>
                <div>
                  <p class="font-bold">Booking request sent!</p>
                </div>
              </div>
              <p class="text-xs text-green-700 font-medium bg-green-100 rounded-xl px-3 py-2 mt-1">⏱ We will process your request in the next <strong>4 hours</strong>. Our team will contact you to confirm.</p>
            </div>
            <div id="booking-error" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-start gap-2">
              <span class="material-symbols-outlined text-base shrink-0 mt-0.5">error</span>
              <div id="booking-error-text">Something went wrong. Please try again.</div>
            </div>
            <button type="submit" class="w-full bg-[#ec5b13] text-white font-black py-4 rounded-2xl hover:bg-orange-600 transition-all shadow-lg">Book Now</button>
          </form>
          <p class="text-center text-xs text-slate-400 mt-3">Free cancellation · No hidden charges</p>
        </div>
      </div>
    </div>
    <!-- Back button + Need help — always visible below the grid -->
    <div class="mt-8 flex flex-col sm:flex-row items-start sm:items-center gap-4 pt-6 border-t border-slate-200">
      <a href="../listing.php?type='.$type.'" class="flex items-center gap-2 text-[#ec5b13] font-bold text-sm hover:underline">
        <span class="material-symbols-outlined text-base">arrow_back</span>Back to '.htmlspecialchars($meta['label']).'
      </a>
      <div class="flex items-center gap-3 ml-auto">
        <span class="text-sm text-slate-500">Need help?</span>
        <a href="tel:+918600968888" class="flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2 px-4 rounded-xl text-sm transition-colors">
          <span class="material-symbols-outlined text-base text-[#ec5b13]">call</span>+91 86009 68888
        </a>
        <a href="https://wa.me/918600968888" class="flex items-center gap-2 bg-green-50 hover:bg-green-100 text-green-700 font-bold py-2 px-4 rounded-xl text-sm transition-colors">
          <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.825c1.63.975 3.41 1.487 5.23 1.488 5.439 0 9.861-4.422 9.863-9.861.001-2.636-1.024-5.115-2.884-6.977-1.862-1.864-4.341-2.887-6.979-2.888-5.439 0-9.861 4.422-9.863 9.862 0 1.842.511 3.641 1.478 5.187l-.995 3.637 3.73-.978zm11.367-7.643c-.31-.155-1.837-.906-2.12-.108-.285.103-.55.515-.674.654-.124.14-.248.155-.558.001-.31-.155-1.31-.483-2.498-1.543-.924-.824-1.548-1.841-1.73-2.15-.181-.31-.019-.477.135-.631.14-.139.31-.36.465-.541.155-.181.206-.31.31-.515.103-.206.052-.386-.026-.541-.077-.155-.674-1.626-.924-2.228-.243-.585-.491-.504-.674-.513-.175-.008-.375-.01-.575-.01s-.525.075-.8.375c-.275.3-1.05 1.026-1.05 2.5s1.075 2.9 1.225 3.1c.15.2 2.11 3.221 5.113 4.513.714.307 1.272.49 1.706.629.718.227 1.37.195 1.886.118.575-.085 1.837-.75 2.096-1.475.258-.725.258-1.346.181-1.475-.077-.129-.283-.206-.593-.361z"/></svg>
          WhatsApp
        </a>
      </div>
    </div>
  </div>
</main>
<div id="related-listings-section" class="border-t border-slate-100 bg-slate-50 py-14">
  <div class="max-w-6xl mx-auto px-4">
    <h3 class="text-2xl font-serif font-black mb-8 flex items-center gap-3"><span class="w-8 h-1 bg-[#ec5b13] rounded-full inline-block"></span>Similar '.$meta['label'].'</h3>
    <div id="related-listings" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="col-span-full text-center text-slate-400 py-8">Loading similar listings...</div>
    </div>
  </div>
</div>
<script>
(async function(){
  try {
    var res = await fetch("../php/api/get_related.php?type='.$type.'&exclude='.(int)$item['id'].'&limit=4");
    var data = await res.json();
    if(data.success && data.listings && data.listings.length > 0){
      var html = "";
      data.listings.forEach(function(listing){
        var slug = "'.$type.'-" + listing.id + "-" + listing.name.toLowerCase().replace(/[^a-z0-9]+/g,"-").replace(/^-|-$/g,"").substring(0,60);
        var displayImg = (listing.image_url && listing.image_url.length > 0) ? (listing.image_url.startsWith("http") ? listing.image_url : "../" + listing.image_url) : "../images/travelhub.png";
        html += "<a href=\"" + slug + ".html\" class=\"group flex flex-col bg-white rounded-2xl overflow-hidden border border-slate-100 hover:shadow-lg transition-shadow shadow-sm\">" +
          "<div class=\"aspect-video overflow-hidden relative\"><img src=\"" + displayImg + "\" alt=\"" + listing.name + "\" class=\"w-full h-full object-cover group-hover:scale-105 transition-transform duration-700\" loading=\"lazy\" onerror=\"this.src=\'../images/travelhub.png\'\"/>" +
          "<div class=\"absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity\"></div></div>" +
          "<div class=\"p-4\"><h4 class=\"text-sm font-bold line-clamp-2 group-hover:text-[#ec5b13] transition-colors\">" + listing.name + "</h4>" +
          "<div class=\"flex items-center gap-1 mt-2\"><span class=\"material-symbols-outlined text-amber-400 text-sm\">star</span><span class=\"text-xs font-bold font-display\">" + (listing.rating || 0).toFixed(1) + "</span></div>" +
          "<p class=\"text-sm font-black text-[#ec5b13] mt-2\">₹" + (listing.price || 0).toLocaleString() + "</p></div></a>";
      });
      document.getElementById("related-listings").innerHTML = html;
    } else {
      document.getElementById("related-listings-section").style.display = "none";
    }
  } catch(e){
    document.getElementById("related-listings-section").style.display = "none";
  }
})();
</script>
<script>
// ── Gallery Popup ──
var _csnGalleryImages = '.$galleryJsArr.';
var _csnCurrentIndex = 0;
var _csnZoomLevel = 1;

function openLightbox(idx){
  console.log("Opening gallery at index:", idx, "Total images:", _csnGalleryImages.length);
  _csnCurrentIndex = idx;
  _csnZoomLevel = 1;
  var modal = document.getElementById("csn-gallery-modal");
  var img = document.getElementById("csn-modal-image");
  if(!_csnGalleryImages.length){console.log("No images found");return;}
  img.src = _csnGalleryImages[_csnCurrentIndex];
  img.classList.remove("zoomed");
  img.style.transform = "scale(1)";
  modal.style.display = "flex";
  modal.classList.add("active");
  document.body.style.overflow = "hidden";
  document.documentElement.style.overflow = "hidden";
  document.getElementById("csn-total-images").textContent = _csnGalleryImages.length;
  csnUpdateCounter();
  console.log("Gallery opened successfully");
}

function csnCloseGallery(){
  console.log("Closing gallery");
  var modal = document.getElementById("csn-gallery-modal");
  modal.style.display = "none";
  modal.classList.remove("active");
  document.body.style.overflow = "auto";
  document.documentElement.style.overflow = "auto";
  _csnZoomLevel = 1;
}

function csnNextImage(){
  _csnCurrentIndex = (_csnCurrentIndex + 1) % _csnGalleryImages.length;
  _csnZoomLevel = 1;
  var img = document.getElementById("csn-modal-image");
  img.src = _csnGalleryImages[_csnCurrentIndex];
  img.classList.remove("zoomed");
  img.style.transform = "scale(1)";
  csnUpdateCounter();
}

function csnPrevImage(){
  _csnCurrentIndex = (_csnCurrentIndex - 1 + _csnGalleryImages.length) % _csnGalleryImages.length;
  _csnZoomLevel = 1;
  var img = document.getElementById("csn-modal-image");
  img.src = _csnGalleryImages[_csnCurrentIndex];
  img.classList.remove("zoomed");
  img.style.transform = "scale(1)";
  csnUpdateCounter();
}

function csnZoomIn(){
  _csnZoomLevel = Math.min(_csnZoomLevel + 0.2, 3);
  csnUpdateZoom();
}

function csnZoomOut(){
  _csnZoomLevel = Math.max(_csnZoomLevel - 0.2, 1);
  csnUpdateZoom();
}

function csnUpdateZoom(){
  var img = document.getElementById("csn-modal-image");
  if(_csnZoomLevel > 1){
    img.classList.add("zoomed");
    img.style.transform = "scale(" + _csnZoomLevel + ")";
  }else{
    img.classList.remove("zoomed");
    img.style.transform = "scale(1)";
  }
}

function csnUpdateCounter(){
  document.getElementById("csn-image-counter").textContent = _csnCurrentIndex + 1;
}

document.addEventListener("keydown",function(e){
  var modal = document.getElementById("csn-gallery-modal");
  if(modal.style.display !== "flex")return;
  if(e.key==="Escape")csnCloseGallery();
  if(e.key==="ArrowRight")csnNextImage();
  if(e.key==="ArrowLeft")csnPrevImage();
});
</script>
<script>
// ── Auth-gate booking form ──
(function(){
  var token=localStorage.getItem("csn_token");
  var user=JSON.parse(localStorage.getItem("csn_user")||"null");
  var isValid=false;
  if(token&&user){
    try{
      var parts=token.split(".");
      if(parts.length===3){
        var p=JSON.parse(atob(parts[1].replace(/-/g,"+").replace(/_/g,"/")));
        if(!p.exp||p.exp>Math.floor(Date.now()/1000))isValid=true;
      }
    }catch(e){}
  }
  var form=document.getElementById("booking-form");
  var gate=document.getElementById("booking-login-gate");
  if(isValid){
    if(form)form.classList.remove("hidden");
    if(gate)gate.classList.add("hidden");
    // Auto-fill from user data
    var nameEl=document.getElementById("b-name");
    var phoneEl=document.getElementById("b-phone");
    if(nameEl&&user.name)nameEl.value=user.name;
    if(phoneEl&&user.phone)phoneEl.value=user.phone;
  }else{
    if(gate)gate.classList.remove("hidden");
    if(form)form.classList.add("hidden");
  }
})();
</script>
<script>
// ── Booking form submit ──
var _bookingForm=document.getElementById("booking-form");
if(_bookingForm)_bookingForm.addEventListener("submit", async function(e) {
    e.preventDefault();
    var btn = this.querySelector("button[type=submit]");
    var successDiv = document.getElementById("booking-success");
    var errorDiv = document.getElementById("booking-error");
    var errorText = document.getElementById("booking-error-text");
    var token = localStorage.getItem("csn_token");
    
    btn.disabled = true; 
    btn.textContent = "Sending...";
    successDiv.classList.add("hidden");
    errorDiv.classList.add("hidden");
    
    var payload = {
        full_name: document.getElementById("b-name").value,
        phone: document.getElementById("b-phone").value,
        number_of_people: parseInt(document.getElementById("b-guests").value) || 1,
        service_type: "'.$type.'",
        listing_id: '.(int)$item['id'].',
        listing_name: "'.addslashes($item['name']).'",
    };
    '.($type === 'stays' ? '
    var checkin = document.getElementById("b-checkin");
    var checkout = document.getElementById("b-checkout");
    if(checkin) payload.checkin_date = checkin.value;
    if(checkout) payload.checkout_date = checkout.value;
    if(checkin && checkout && checkin.value && checkout.value && checkin.value >= checkout.value){
        errorText.textContent = "Check-out date must be after check-in date.";
        errorDiv.classList.remove("hidden");
        btn.disabled = false; btn.textContent = "Book Now";
        return;
    }' : '
    var dateEl = document.getElementById("b-date");
    if(dateEl) payload.booking_date = dateEl.value;').'
    try {
        var headers = {"Content-Type": "application/json"};
        if(token) headers["Authorization"] = "Bearer " + token;
        var res = await fetch("../php/api/bookings.php", { method:"POST", headers:headers, body:JSON.stringify(payload) });
        var data = await res.json();
        if (res.ok) {
            successDiv.classList.remove("hidden");
            this.reset();
            setTimeout(function(){
              successDiv.classList.add("hidden");
              btn.textContent = "Book Now";
              btn.disabled = false;
            }, 8000);
        } else {
            errorText.textContent = data.error || "Something went wrong. Please try again.";
            errorDiv.classList.remove("hidden");
            btn.disabled = false;
            btn.textContent = "Book Now";
        }
    } catch(err) {
        errorText.textContent = "Network error. Please check your connection and try again.";
        errorDiv.classList.remove("hidden");
        btn.disabled = false;
        btn.textContent = "Book Now";
    }
});
</script>';
        $html .= sharedFooter('../');

        file_put_contents($file, $html);
        $listingCount++;
    }
}
$log[] = "Listings: $listingCount HTML files → /listing-detail/";

// ── Output ────────────────────────────────────────────────────────────────────
if (php_sapi_name() === 'cli') {
    foreach ($log as $l) echo $l . PHP_EOL;
} else {
    echo '<h2 style="font-family:sans-serif">HTML Generation Complete</h2><ul style="font-family:sans-serif">';
    foreach ($log as $l) echo '<li>' . htmlspecialchars($l) . '</li>';
    echo '</ul>';
    echo '<p style="font-family:sans-serif"><a href="../../blogs.php">View Blogs</a> | <a href="../../listing.php?type=stays">View Listings</a></p>';
}
