#!/usr/bin/env python3
"""
Fix headers on listing-detail/*.html and blogs/*.html
Simple clean header - no transformations, no pill shape
"""
import re, glob

# Simple CSS - no transformations
HEADER_CSS = (
    '.glass{background:rgba(255,255,255,0.07);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,0.12);}'
    '.glass-dark{background:rgba(10,7,5,0.7);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(236,91,19,0.1);}'
    '.header-solid{background:#000000!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important;}'
    '@keyframes marquee{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}'
    '.material-symbols-outlined{font-variation-settings:"FILL" 0,"wght" 400,"GRAD" 0,"opsz" 24;font-family:"Material Symbols Outlined";font-style:normal;display:inline-block;line-height:1;}'
)

MARQUEE_BAR = (
    '<div class="bg-primary text-white py-1.5 overflow-hidden relative z-[60]">\n'
    '    <div class="max-w-7xl mx-auto px-6 flex justify-between items-center gap-4 text-[11px] font-semibold uppercase tracking-widest">\n'
    '        <div class="flex-1 overflow-hidden">\n'
    '            <div class="flex whitespace-nowrap marquee">\n'
    '                <style>@keyframes marquee{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}</style>\n'
    '                <span class="px-6">★ 20% OFF on first heritage tour booking</span>\n'
    '                <span class="px-6">★ Verified guides for Ajanta &amp; Ellora</span>\n'
    '                <span class="px-6">★ Free cancellation on bike rentals</span>\n'
    '                <span class="px-6">★ 24/7 tourist support available</span>\n'
    '                <span class="px-6">★ 20% OFF on first heritage tour booking</span>\n'
    '                <span class="px-6">★ Verified guides for Ajanta &amp; Ellora</span>\n'
    '                <span class="px-6">★ Free cancellation on bike rentals</span>\n'
    '                <span class="px-6">★ 24/7 tourist support available</span>\n'
    '            </div>\n'
    '        </div>\n'
    '    </div>\n'
    '</div>'
)

HEADER_SCRIPT = (
    '    <script>\n'
    '        document.getElementById("mob-btn").addEventListener("click", function(){\n'
    '            document.getElementById("mob-menu").classList.toggle("hidden");\n'
    '        });\n'
    '        (function(){\n'
    '            var h = document.getElementById("site-header");\n'
    '            function updateHeader() {\n'
    '                if (window.scrollY === 0) { h.classList.add("header-solid"); }\n'
    '                else { h.classList.remove("header-solid"); }\n'
    '            }\n'
    '            updateHeader();\n'
    '            window.addEventListener("scroll", updateHeader, {passive:true});\n'
    '        })();\n'
    '        function addPageReady(){document.body.classList.add("page-ready");}\n'
    '        if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",addPageReady);}else{addPageReady();}\n'
    '        document.addEventListener("click", function(e) {\n'
    '            var a = e.target.closest("a");\n'
    '            if (!a) return;\n'
    '            var href = a.getAttribute("href");\n'
    '            if (!href || href.startsWith("#") || href.startsWith("mailto:") || href.startsWith("tel:") || href.startsWith("javascript") || a.target === "_blank") return;\n'
    '            e.preventDefault();\n'
    '            document.body.style.transition = "opacity 0.18s ease";\n'
    '            document.body.style.opacity = "0";\n'
    '            setTimeout(function(){ window.location.href = href; }, 190);\n'
    '        });\n'
    '    </script>\n'
    '</header>'
)

LISTING_NAV = (
    '        <div class="hidden md:flex items-center gap-1">\n'
    '            <a href="../listing.php?type=stays" class="flex items-center gap-1.5 text-sm font-semibold px-3 py-2 rounded-full transition-colors text-white/60 hover:bg-white/10 hover:text-white"><span class="material-symbols-outlined text-base">bed</span>Stays</a>\n'
    '            <a href="../listing.php?type=cars" class="flex items-center gap-1.5 text-sm font-semibold px-3 py-2 rounded-full transition-colors text-white/60 hover:bg-white/10 hover:text-white"><span class="material-symbols-outlined text-base">directions_car</span>Car Rentals</a>\n'
    '            <a href="../listing.php?type=bikes" class="flex items-center gap-1.5 text-sm font-semibold px-3 py-2 rounded-full transition-colors text-white/60 hover:bg-white/10 hover:text-white"><span class="material-symbols-outlined text-base">motorcycle</span>Bike Rentals</a>\n'
    '            <a href="../listing.php?type=attractions" class="flex items-center gap-1.5 text-sm font-semibold px-3 py-2 rounded-full transition-colors text-white/60 hover:bg-white/10 hover:text-white"><span class="material-symbols-outlined text-base">confirmation_number</span>Attractions</a>\n'
    '            <a href="../listing.php?type=restaurants" class="flex items-center gap-1.5 text-sm font-semibold px-3 py-2 rounded-full transition-colors text-white/60 hover:bg-white/10 hover:text-white"><span class="material-symbols-outlined text-base">restaurant</span>Restaurants</a>\n'
    '            <a href="../listing.php?type=buses" class="flex items-center gap-1.5 text-sm font-semibold px-3 py-2 rounded-full transition-colors text-white/60 hover:bg-white/10 hover:text-white"><span class="material-symbols-outlined text-base">directions_bus</span>Buses</a>\n'
    '        </div>'
)

LISTING_MOB = (
    '    <div id="mob-menu" class="hidden md:hidden border-t border-white/10 px-4 py-3 flex flex-col gap-1">\n'
    '        <a href="../listing.php?type=stays" class="flex items-center gap-2 text-sm font-semibold px-4 py-2.5 rounded-xl text-white/70 hover:bg-white/10 hover:text-white transition-colors"><span class="material-symbols-outlined text-base">bed</span>Stays</a>\n'
    '        <a href="../listing.php?type=cars" class="flex items-center gap-2 text-sm font-semibold px-4 py-2.5 rounded-xl text-white/70 hover:bg-white/10 hover:text-white transition-colors"><span class="material-symbols-outlined text-base">directions_car</span>Car Rentals</a>\n'
    '        <a href="../listing.php?type=bikes" class="flex items-center gap-2 text-sm font-semibold px-4 py-2.5 rounded-xl text-white/70 hover:bg-white/10 hover:text-white transition-colors"><span class="material-symbols-outlined text-base">motorcycle</span>Bike Rentals</a>\n'
    '        <a href="../listing.php?type=attractions" class="flex items-center gap-2 text-sm font-semibold px-4 py-2.5 rounded-xl text-white/70 hover:bg-white/10 hover:text-white transition-colors"><span class="material-symbols-outlined text-base">confirmation_number</span>Attractions</a>\n'
    '        <a href="../listing.php?type=restaurants" class="flex items-center gap-2 text-sm font-semibold px-4 py-2.5 rounded-xl text-white/70 hover:bg-white/10 hover:text-white transition-colors"><span class="material-symbols-outlined text-base">restaurant</span>Restaurants</a>\n'
    '        <a href="../listing.php?type=buses" class="flex items-center gap-2 text-sm font-semibold px-4 py-2.5 rounded-xl text-white/70 hover:bg-white/10 hover:text-white transition-colors"><span class="material-symbols-outlined text-base">directions_bus</span>Buses</a>\n'
    '        <div class="flex gap-2 pt-2 border-t border-white/10 mt-1">\n'
    '            <a href="../login.php" class="flex-1 text-center text-white hover:bg-white/10 text-sm font-semibold py-2 rounded-xl transition-all">Login</a>\n'
    '            <a href="../register.php" class="flex-1 text-center bg-primary text-white text-sm font-bold py-2 rounded-xl hover:bg-orange-600 transition-all">Register</a>\n'
    '        </div>\n'
    '    </div>'
)

BLOGS_NAV = (
    '        <div class="hidden md:flex items-center gap-1">\n'
    '            <a href="../index.php" class="text-sm font-semibold px-4 py-2 rounded-full transition-colors text-white/70 hover:bg-white/10 hover:text-white">Home</a>\n'
    '            <a href="../about.php" class="text-sm font-semibold px-4 py-2 rounded-full transition-colors text-white/70 hover:bg-white/10 hover:text-white">About Us</a>\n'
    '            <a href="../contact.php" class="text-sm font-semibold px-4 py-2 rounded-full transition-colors text-white/70 hover:bg-white/10 hover:text-white">Contact Us</a>\n'
    '            <a href="../blogs.php" class="text-sm font-semibold px-4 py-2 rounded-full transition-colors text-white bg-white/10">Blogs</a>\n'
    '        </div>'
)

BLOGS_MOB = (
    '    <div id="mob-menu" class="hidden md:hidden border-t border-white/10 px-4 py-3 flex flex-col gap-1">\n'
    '        <a href="../index.php" class="text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors text-white/70 hover:bg-white/10 hover:text-white">Home</a>\n'
    '        <a href="../about.php" class="text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors text-white/70 hover:bg-white/10 hover:text-white">About Us</a>\n'
    '        <a href="../contact.php" class="text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors text-white/70 hover:bg-white/10 hover:text-white">Contact Us</a>\n'
    '        <a href="../blogs.php" class="text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors text-primary">Blogs</a>\n'
    '        <div class="flex gap-2 pt-2 border-t border-white/10 mt-1">\n'
    '            <a href="../login.php" class="flex-1 text-center text-white hover:bg-white/10 text-sm font-semibold py-2 rounded-xl transition-all">Login</a>\n'
    '            <a href="../register.php" class="flex-1 text-center bg-primary text-white text-sm font-bold py-2 rounded-xl hover:bg-orange-600 transition-all">Register</a>\n'
    '        </div>\n'
    '    </div>'
)

RIGHT_BTNS = (
    '        <div class="flex items-center gap-2">\n'
    '            <a href="../login.php" class="text-white text-sm font-semibold px-4 py-1.5 hover:bg-white/10 rounded-full transition-all">Login</a>\n'
    '            <a href="../register.php" class="bg-primary text-white text-sm font-bold px-5 py-1.5 rounded-full hover:bg-orange-600 transition-all shadow-lg shadow-primary/20">Register</a>\n'
    '            <button id="mob-btn" class="md:hidden p-2 rounded-lg transition-colors ml-1">\n'
    '                <span class="material-symbols-outlined text-2xl text-white">menu</span>\n'
    '            </button>\n'
    '        </div>'
)

HEADER_OPEN = (
    '<header id="site-header" class="sticky top-0 w-full z-50 transition-all duration-300 glass-dark border-b border-white/5">\n'
    '    <nav class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">\n'
    '        <a href="../index.php" class="flex items-center shrink-0">\n'
    '            <img src="../images/travelhub.png" alt="CSNExplore" class="h-9 object-contain"/>\n'
    '        </a>'
)

def build_header(nav, mob):
    return (
        MARQUEE_BAR + '\n' +
        HEADER_OPEN + '\n' +
        nav + '\n' +
        RIGHT_BTNS + '\n' +
        '    </nav>\n' +
        mob + '\n' +
        HEADER_SCRIPT
    )

LISTING_HEADER = build_header(LISTING_NAV, LISTING_MOB)
BLOGS_HEADER = build_header(BLOGS_NAV, BLOGS_MOB)

# Match old header block (from marquee through </header>)
OLD_HEADER_RE = re.compile(
    r'<div[^>]*(?:bg-primary|background:#ec5b13)[^>]*>.*?</header>',
    re.DOTALL
)

def fix_file(path, new_header):
    with open(path, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()
    
    # Remove old header
    new_content, count = OLD_HEADER_RE.subn(new_header, content, count=1)
    
    if not count:
        print('SKIP (no match):', path)
        return False
    
    # Clean up CSS - remove duplicates
    new_content = re.sub(r'(@keyframes marquee[^}]*})+', '@keyframes marquee{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}', new_content)
    
    with open(path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    return True

# Run
ld_ok = ld_skip = 0
for f in sorted(glob.glob('listing-detail/*.html')):
    if fix_file(f, LISTING_HEADER):
        ld_ok += 1
    else:
        ld_skip += 1

bl_ok = bl_skip = 0
for f in sorted(glob.glob('blogs/*.html')):
    if fix_file(f, BLOGS_HEADER):
        bl_ok += 1
    else:
        bl_skip += 1

print(f'listing-detail: {ld_ok} updated, {ld_skip} skipped')
print(f'blogs:          {bl_ok} updated, {bl_skip} skipped')
