# Regenerate Listing Pages with Original GitHub Structure + Animations

## Issue
The current regenerated pages are missing the original detailed structure from GitHub commit `662eb1a`.

## Original Structure Includes:
1. ✅ Full header with marquee bar
2. ✅ Mobile menu overlay
3. ✅ Glassmorphism effects (glass, glass-dark, glass-card, etc.)
4. ✅ Gallery lightbox with zoom controls
5. ✅ Reveal animations ([data-reveal])
6. ✅ Card glow effects
7. ✅ Image shimmer effects
8. ✅ Complete meta tags (Open Graph, Twitter, Schema.org)
9. ✅ Favicon links
10. ✅ Booking sound effect
11. ✅ Scroll behavior for header (pill mode)

## What Needs to Be Done

### Step 1: Extract Full Original Template
Get the complete HTML structure from GitHub commit `662eb1a` for one listing page.

### Step 2: Add New Animations
Add these animation attributes to the original structure:
- `data-animate="fade-in-up"` on main sections
- `data-animate="fade-in-left"` on left content
- `data-animate="fade-in-right"` on right content
- `data-animate-delay="1,2,3..."` for stagger effects
- `class="hover-lift"` on cards
- `class="img-zoom"` and `class="img-zoom-container"` on images

### Step 3: Keep Original Features
- Keep all [data-reveal] animations
- Keep gallery lightbox functionality
- Keep glassmorphism CSS
- Keep header scroll behavior
- Keep mobile menu
- Keep all original JavaScript

### Step 4: Regenerate All Pages
Run regeneration with the corrected template.

## Command to Get Original Structure

```bash
# Get full original HTML from GitHub
git show 662eb1a:listing-detail/attractions-1-ajanta-caves.html > original-template.html

# Review the structure
cat original-template.html
```

## Next Steps

1. Extract complete original HTML template from GitHub
2. Identify where to add new animation attributes
3. Update regeneration script with original template + new animations
4. Regenerate all 75 listing pages + 300 blog pages
5. Test one page to verify structure is correct

## Files to Update

- `php/regenerate-with-animations.php` - Use original template
- `php/regenerate-blogs-with-animations.php` - Use original blog template

## Expected Result

Pages will have:
- ✅ Original detailed structure from GitHub
- ✅ All original features (lightbox, glassmorphism, etc.)
- ✅ NEW smooth scroll animations added
- ✅ NEW hover effects added
- ✅ Both old [data-reveal] AND new [data-animate] working together
