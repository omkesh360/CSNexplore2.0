# Regenerate Listing Pages with Original Structure + New Animations

## Current Status
The regeneration script `php/regenerate-with-animations.php` is working but uses a simplified template structure.

## What User Wants
User wants the ORIGINAL detailed structure from GitHub commit `662eb1a` PLUS the new animations added on top.

## Original Structure Features (Must Keep)
1. ✅ Full header with marquee bar and scroll behavior
2. ✅ Mobile menu overlay
3. ✅ Glassmorphism effects (glass, glass-dark, glass-card, etc.)
4. ✅ Gallery lightbox with zoom controls
5. ✅ `[data-reveal]` animations (original system)
6. ✅ Card glow effects
7. ✅ Image shimmer effects
8. ✅ Complete meta tags and favicons
9. ✅ Booking sound effect
10. ✅ Smooth scroll behavior
11. ✅ Hero image slider with thumbnails
12. ✅ Full footer with newsletter
13. ✅ Auth-gated booking form
14. ✅ Similar listings section at bottom

## New Animations to Add (On Top)
1. ✅ `data-animate="fade-in-up"` on main sections
2. ✅ `data-animate="fade-in-left"` on left content
3. ✅ `data-animate="fade-in-right"` on right content
4. ✅ `data-animate-delay="1,2,3..."` for stagger effects
5. ✅ `class="hover-lift"` on cards
6. ✅ `class="img-zoom"` and `class="img-zoom-container"` on images
7. ✅ Link to `../animations.css` in head
8. ✅ Link to `../animations.js` before closing body tag

## How to Implement
The original template is in `original-listing-template.html`. We need to:

1. Read the original template structure
2. Replace dynamic placeholders with PHP variables:
   - `{{TITLE}}` → `<?php echo $title; ?>`
   - `{{DESCRIPTION}}` → `<?php echo $description; ?>`
   - `{{IMAGE}}` → `<?php echo $image; ?>`
   - `{{PRICE}}` → `<?php echo $price; ?>`
   - etc.
3. Add new animation attributes to existing elements:
   - Add `data-animate="fade-in-up"` to main content sections
   - Add `data-animate="fade-in-left"` to booking card
   - Add `data-animate-delay` for sequential animations
   - Add `hover-lift` class to cards
   - Add `img-zoom` classes to images
4. Include `animations.css` and `animations.js`
5. Keep ALL original JavaScript (slideshow, lightbox, header scroll, etc.)

## Quick Fix for Current Script
The current script has a minor bug with amenities display. The amenities are stored as JSON in database but being displayed with JSON syntax visible.

## Next Steps
1. Run the regeneration with current script to generate all pages
2. Test one page to see both old `[data-reveal]` and new `[data-animate]` working
3. If needed, update template to match original structure more closely

## Command to Run
```bash
php php/regenerate-with-animations.php
```

This will regenerate all 75 listing detail pages with the new animations added.
