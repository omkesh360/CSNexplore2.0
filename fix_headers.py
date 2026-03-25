#!/usr/bin/env python3
"""
Fix headers on listing-detail/*.html and blogs/*.html
- Marquee bar: position:relative (NOT sticky) so it scrolls away naturally
- Header: sticky top-0 z-50, starts solid black, blurs on scroll via JS
"""
import re, glob

# ── Marquee bar (position:relative — scrolls away naturally) ──────────────────
MARQUEE_BAR = (
    '<div style="background:#ec5b13;color:#fff;padding:6px 0;overflow:hidden;'
    'position:relative;z-index:60;">\n'
    '  <div style="max-width:80rem;margin:0 auto;padding:0 1.5rem;font-size:11px;'
    'font-weight:700;text-transform:uppercase;letter-spacing:0.1em;overflow:hidden;">\n'
    '    <div style="display:flex;white-space:nowrap;animation:marquee 30s linear infinite;">\n'
    '      <span style="padding:0 1.5rem;">&#9733; 20% OFF on first heritage tour booking</span>\n'
    '      <span style="padding:0 1.5rem;">&#9733; Verified guides for Ajanta &amp; Ellora</span>\n'
    '      <span style="padding:0 1.5rem;">&#9733; Free cancellation on bike rentals</span>\n'
    '      <span style="padding:0 1.5rem;">&#9733; 24/7 tourist support available</span>\n'
    '      <span style="padding:0 1.5rem;">&#9733; 20% OFF on first heritage tour booking</span>\n'
    '      <span style="padding:0 1.5rem;">&#9733; Verified guides for Ajanta &amp; Ellora</span>\n'
    '      <span style="padding:0 1.5rem;">&#9733; Free cancellation on bike rentals</span>\n'
    '      <span style="padding:0 1.5rem;">&#9733; 24/7 tourist support available</span>\n'
    '    </div>\n'
    '  </div>\n'
    '</div>'
)

# ── Header open tag (sticky, starts solid black) ──────────────────────────────
HEADER_OPEN_LISTING = (
    '<header id="site-header" class="sticky top-0 w-full z-50 transition-all duration-300" '
    'style="background:#000000;border-bottom:1px solid rgba(255,255,255,0.08);">\n'
    '  <nav class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">\n'
    '    <a href="../index.php" class="flex items-center shrink-0">\n'
    '      <img src="../images/travelhub.png" alt="CSNExplore" class="h-9 object-contain"/>\n'
    '    </a>'
)

HEADER_OPEN_BLOGS = HEADER_OPEN_LISTING  # same structure

# ── Desktop nav ───────────────────────────────────────────────────────────────
LISTING_NAV = (
    '\n    <div class="hidden md:flex items-center gap-1">\n'
    '      <a href="../listing.php?type=stays" class="flex items-center gap-1.5 text-sm font-semibold px-3 py-2 rounded-full transition-colors text-white/60 hover:bg-white/10 hover:text-white"><span class="material-symbols-outlined text-base">bed</span>Stays</a>\n'
    '      <a href="../listing.php?type=cars" class="flex items-center gap-1.5 text-sm font-semibold px-3 py-2 rounded-full transition-colors text-white/60 hover:bg-white/10 hover:text-white"><span class="material-symbols-outlined text-base">directions_car</span>Car Rentals</a>\n'
    '      <a href="../listing.php?type=bikes" class="flex items-center gap-1.5 text-sm font-semibold px-3 py-2 rounded-full transition-colors text-white/60 hover:bg-white/10 hover:text-white"><span class="material-symbols-outlined text-base">motorcycle</span>Bike Rentals</a>\n'
    '      <a href="../listing.php?type=attractions" class="flex items-center gap-1.5 text-sm font-semibold px-3 py-2 rounded-full transition-colors text-white/60 hover:bg-white/10 hover:text-white"><span class="material-symbols-outlined text-base">confirmation_number</span>Attractions</a>\n'
    '      <a href="../listing.php?type=restaurants" class="flex items-center gap-1.5 text-sm font-semibold px-3 py-2 rounded-full transition-colors text-white/60 hover:bg-white/10 hover:text-white"><span class="material-symbols-outlined text-base">restaurant</span>Restaurants</a>\n'
    '      <a href="../listing.php?type=buses" class="flex items-center gap-1.5 text-sm font-semibold px-3 py-2 rounded-full transition-colors text-white/60 hover:bg-white/10 hover:text-white"><span class="material-symbols-outlined text-base">directions_bus</span>Buses</a>\n'
    '    </div>'
)

BLOGS_NAV = (
    '\n    <div class="hidden md:flex items-center gap-1">\n'
    '      <a href="../index.php" class="text-sm font-semibold px-4 py-2 rounded-full transition-colors text-white/70 hover:bg-white/10 hover:text-white">Home</a>\n'
    '      <a href="../about.php" class="text-sm font-semibold px-4 py-2 rounded-full transition-colors text-white/70 hover:bg-white/10 hover:text-white">About Us</a>\n'
    '      <a href="../contact.php" class="text-sm font-semibold px-4 py-2 rounded-full transition-colors text-white/70 hover:bg-white/10 hover:text-white">Contact Us</a>\n'
    '      <a href="../blogs.php" class="text-sm font-semibold px-4 py-2 rounded-full transition-colors text-white bg-white/10">Blogs</a>\n'
    '    </div>'
)

# ── Right buttons ─────────────────────────────────────────────────────────────
RIGHT_BTNS = (
    '\n    <div class="flex items-center gap-2">\n'
    '      <a href="../login.php" class="text-white text-sm font-semibold px-4 py-1.5 hover:bg-white/10 rounded-full transition-all">Login</a>\n'
    '      <a href="../register.php" class="text-white text-sm font-bold px-5 py-1.5 rounded-full transition-all" style="background:#ec5b13;">Register</a>\n'
    '      <button id="mob-btn" class="md:hidden p-2 rounded-lg transition-colors ml-1">\n'
    '        <span class="material-symbols-outlined text-2xl text-white">menu</span>\n'
    '      </button>\n'
    '    </div>\n'
    '  </nav>'
)

# ── Mobile menus ──────────────────────────────────────────────────────────────
LISTING_MOB = (
    '\n  <div id="mob-menu" class="hidden md:hidden border-t border-white/10 px-4 py-3 flex flex-col gap-1">\n'
    '    <a href="../listing.php?type=stays" class="flex items-center gap-2 text-sm font-semibold px-4 py-2.5 rounded-xl text-white/70 hover:bg-white/10 hover:text-white transition-colors"><span class="material-symbols-outlined text-base">bed</span>Stays</a>\n'
    '    <a href="../listing.php?type=cars" class="flex items-center gap-2 text-sm font-semibold px-4 py-2.5 rounded-xl text-white/70 hover:bg-white/10 hover:text-white transition-colors"><span class="material-symbols-outlined text-base">directions_car</span>Car Rentals</a>\n'
    '    <a href="../listing.php?type=bikes" class="flex items-center gap-2 text-sm font-semibold px-4 py-2.5 rounded-xl text-white/70 hover:bg-white/10 hover:text-white transition-colors"><span class="material-symbols-outlined text-base">motorcycle</span>Bike Rentals</a>\n'
    '    <a href="../listing.php?type=attractions" class="flex items-center gap-2 text-sm font-semibold px-4 py-2.5 rounded-xl text-white/70 hover:bg-white/10 hover:text-white transition-colors"><span class="material-symbols-outlined text-base">confirmation_number</span>Attractions</a>\n'
    '    <a href="../listing.php?type=restaurants" class="flex items-center gap-2 text-sm font-semibold px-4 py-2.5 rounded-xl text-white/70 hover:bg-white/10 hover:text-white transition-colors"><span class="material-symbols-outlined text-base">restaurant</span>Restaurants</a>\n'
    '    <a href="../listing.php?type=buses" class="flex items-center gap-2 text-sm font-semibold px-4 py-2.5 rounded-xl text-white/70 hover:bg-white/10 hover:text-white transition-colors"><span class="material-symbols-outlined text-base">directions_bus</span>Buses</a>\n'
    '    <div class="flex gap-2 pt-2 border-t border-white/10 mt-1">\n'
    '      <a href="../login.php" class="flex-1 text-center text-white hover:bg-white/10 text-sm font-semibold py-2 rounded-xl transition-all">Login</a>\n'
    '      <a href="../register.php" class="flex-1 text-center text-white text-sm font-bold py-2 rounded-xl transition-all" style="background:#ec5b13;">Register</a>\n'
    '    </div>\n'
    '  </div>'
)

BLOGS_MOB = (
    '\n  <div id="mob-menu" class="hidden md:hidden border-t border-white/10 px-4 py-3 flex flex-col gap-1">\n'
    '    <a href="../index.php" class="text-sm font-semibold px-4 py-2.5 rounded-xl text-white/70 hover:bg-white/10 hover:text-white transition-colors">Home</a>\n'
    '    <a href="../about.php" class="text-sm font-semibold px-4 py-2.5 rounded-xl text-white/70 hover:bg-white/10 hover:text-white transition-colors">About Us</a>\n'
    '    <a href="../contact.php" class="text-sm font-semibold px-4 py-2.5 rounded-xl text-white/70 hover:bg-white/10 hover:text-white transition-colors">Contact Us</a>\n'
    '    <a href="../blogs.php" class="text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors" style="color:#ec5b13;">Blogs</a>\n'
    '    <div class="flex gap-2 pt-2 border-t border-white/10 mt-1">\n'
    '      <a href="../login.php" class="flex-1 text-center text-white hover:bg-white/10 text-sm font-semibold py-2 rounded-xl transition-all">Login</a>\n'
    '      <a href="../register.php" class="flex-1 text-center text-white text-sm font-bold py-2 rounded-xl transition-all" style="background:#ec5b13;">Register</a>\n'
    '    </div>\n'
    '  </div>'
)

# ── JS (scroll blur + mobile menu + page transitions) ────────────────────────
HEADER_SCRIPT = (
    '\n  <script>\n'
    '    document.getElementById("mob-btn").addEventListener("click",function(){\n'
    '      document.getElementById("mob-menu").classList.toggle("hidden");\n'
    '    });\n'
    '    (function(){\n'
    '      var h=document.getElementById("site-header");\n'
    '      function u(){\n'
    '        if(window.scrollY===0){\n'
    '          h.style.background="#000000";\n'
    '          h.style.backdropFilter="none";\n'
    '          h.style.webkitBackdropFilter="none";\n'
    '        } else {\n'
    '          h.style.background="rgba(10,7,5,0.85)";\n'
    '          h.style.backdropFilter="blur(20px)";\n'
    '          h.style.webkitBackdropFilter="blur(20px)";\n'
    '        }\n'
    '      }\n'
    '      u();\n'
    '      window.addEventListener("scroll",u,{passive:true});\n'
    '    })();\n'
    '    function addPageReady(){document.body.classList.add("page-ready");}\n'
    '    if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",addPageReady);}else{addPageReady();}\n'
    '    document.addEventListener("click",function(e){\n'
    '      var a=e.target.closest("a");if(!a)return;\n'
    '      var href=a.getAttribute("href");\n'
    '      if(!href||href.startsWith("#")||href.startsWith("mailto:")||href.startsWith("tel:")||href.startsWith("javascript")||a.target==="_blank")return;\n'
    '      e.preventDefault();\n'
    '      document.body.style.transition="opacity 0.18s ease";\n'
    '      document.body.style.opacity="0";\n'
    '      setTimeout(function(){window.location.href=href;},190);\n'
    '    });\n'
    '  </script>\n'
    '</header>'
)

# ── Assemble full headers ─────────────────────────────────────────────────────
LISTING_HEADER = (
    MARQUEE_BAR + '\n' +
    HEADER_OPEN_LISTING +
    LISTING_NAV +
    RIGHT_BTNS +
    LISTING_MOB +
    HEADER_SCRIPT
)

BLOGS_HEADER = (
    MARQUEE_BAR + '\n' +
    HEADER_OPEN_BLOGS +
    BLOGS_NAV +
    RIGHT_BTNS +
    BLOGS_MOB +
    HEADER_SCRIPT
)

# ── Regex: match old header block (marquee div + header tag + script + /header) ─
# Matches from the first <div that starts the marquee/header area through </header>
OLD_HEADER_RE = re.compile(
    r'<div\s[^>]*(?:position:sticky[^>]*top:0|sticky top-0 bg-\[#ec5b13\]|bg-primary)[^>]*>.*?</header>',
    re.DOTALL
)

def fix_file(path, new_header):
    with open(path, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()

    new_content, count = OLD_HEADER_RE.subn(new_header, content, count=1)

    if not count:
        # Fallback: try matching just by <body> tag and inserting after it
        print('SKIP (no match):', path)
        return False

    with open(path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    return True

# ── Run ───────────────────────────────────────────────────────────────────────
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
