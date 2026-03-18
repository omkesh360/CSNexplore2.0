#!/usr/bin/env python3
"""
fix_index.py
1. Replace homepage inline footer with require 'footer.php'
2. Fix zoom glitch in generate_listings.py
3. Fix homepage mobile responsiveness
"""
import re

# ── 1. Fix index.php footer ───────────────────────────────────────────────────
with open('index.php', 'r', encoding='utf-8') as f:
    idx = f.read()

# Find the footer block
footer_start = idx.find('\n<!-- Footer -->\n<footer')
footer_end   = idx.find('\n</footer>\n', footer_start) + len('\n</footer>\n')

if footer_start == -1:
    print("ERROR: Could not find footer in index.php")
else:
    replacement = "\n<?php require 'footer.php'; ?>\n"
    idx = idx[:footer_start] + replacement + idx[footer_end:]
    print(f"Footer replaced (chars {footer_start}–{footer_end})")

# ── 2. Fix mobile responsiveness in hero section ──────────────────────────────
# The hero h1 needs better mobile sizing
idx = idx.replace(
    'class="font-serif text-4xl md:text-6xl text-white mb-4 leading-tight font-black"',
    'class="font-serif text-3xl sm:text-4xl md:text-6xl text-white mb-4 leading-tight font-black"'
)

# Fix hero section min-height on mobile
idx = idx.replace(
    'class="relative min-h-[80vh] flex flex-col items-center justify-center overflow-hidden"',
    'class="relative min-h-[100svh] md:min-h-[80vh] flex flex-col items-center justify-center overflow-hidden"'
)

# Fix hero desc text size on mobile
idx = idx.replace(
    'class="text-white/70 text-base md:text-lg mb-10 max-w-2xl mx-auto"',
    'class="text-white/70 text-sm md:text-lg mb-6 md:mb-10 max-w-2xl mx-auto px-2"'
)

# Fix hero padding on mobile
idx = idx.replace(
    'class="relative z-20 text-center px-4 w-full max-w-5xl mx-auto"',
    'class="relative z-20 text-center px-4 w-full max-w-5xl mx-auto pt-8 pb-6"'
)

with open('index.php', 'w', encoding='utf-8') as f:
    f.write(idx)
print("index.php saved")

# ── 3. Fix zoom glitch in generate_listings.py ────────────────────────────────
with open('generate_listings.py', 'r', encoding='utf-8') as f:
    gen = f.read()

# Replace the broken zoom CSS — use transform-origin and overflow on wrapper
old_lb_css = """.lb-img{{max-width:90vw;max-height:85vh;object-fit:contain;border-radius:8px;box-shadow:0 20px 60px rgba(0,0,0,0.8);transition:transform .25s ease;cursor:zoom-in;}}
.lb-img.zoomed{{transform:scale(1.8);cursor:zoom-out;}}"""

new_lb_css = """.lb-wrap{{overflow:auto;max-width:92vw;max-height:88vh;display:flex;align-items:center;justify-content:center;}}
.lb-img{{max-width:88vw;max-height:84vh;object-fit:contain;border-radius:8px;box-shadow:0 20px 60px rgba(0,0,0,0.8);transition:transform .3s ease;transform-origin:center center;cursor:zoom-in;display:block;}}
.lb-img.zoomed{{transform:scale(2.2);cursor:zoom-out;}}"""

gen = gen.replace(old_lb_css, new_lb_css)

# Wrap lb-img in lb-wrap div in the lightbox HTML
old_lb_html = """  <img id="lb-img" class="lb-img" src="" alt=""/>"""
new_lb_html = """  <div class="lb-wrap"><img id="lb-img" class="lb-img" src="" alt=""/></div>"""
gen = gen.replace(old_lb_html, new_lb_html)

# Fix the zoom hint — remove it (it overlaps prev/next labels)
gen = gen.replace(
    '  <span class="lb-zoom-hint" id="lb-zoom-hint">Click image to zoom</span>\n',
    ''
)
gen = gen.replace('.lb-zoom-hint{{position:absolute;top:20px;left:50%;transform:translateX(-50%);color:rgba(255,255,255,0.5);font-size:11px;font-weight:600;letter-spacing:.05em;text-transform:uppercase;}}\n', '')

with open('generate_listings.py', 'w', encoding='utf-8') as f:
    f.write(gen)
print("generate_listings.py saved")
print("Done.")
