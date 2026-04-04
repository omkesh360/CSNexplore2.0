# CSNExplore - Global Animation System Implementation Guide

## ✅ What's Been Done

1. **Created Animation Files:**
   - `animations.css` - Complete CSS animation library
   - `animations.js` - JavaScript animation controller with IntersectionObserver
   
2. **Integrated into Global Files:**
   - `header.php` - Added animations.css link
   - `footer.php` - Added animations.js script

3. **Animation System is Now Active** on all pages that use header.php and footer.php

## 🎨 Available Animation Types

### Scroll-Based Animations (data-animate)
Use these attributes on any HTML element:

```html
<!-- Fade animations -->
<div data-animate="fade-in">Fades in from bottom</div>
<div data-animate="fade-in-left">Slides in from left</div>
<div data-animate="fade-in-right">Slides in from right</div>
<div data-animate="fade-in-up">Slides in from bottom</div>
<div data-animate="fade-in-down">Slides in from top</div>

<!-- Scale & Zoom -->
<div data-animate="scale-in">Scales up</div>
<div data-animate="zoom-in">Zooms in</div>
<div data-animate="rotate-in">Rotates in</div>

<!-- Slide animations -->
<div data-animate="slide-in-left">Slides from left</div>
<div data-animate="slide-in-right">Slides from right</div>
```

### Stagger Delays
Add sequential delays to multiple elements:

```html
<div data-animate="fade-in-up" data-animate-delay="1">First</div>
<div data-animate="fade-in-up" data-animate-delay="2">Second</div>
<div data-animate="fade-in-up" data-animate-delay="3">Third</div>
```

### Hover Effects (CSS Classes)
```html
<!-- Lift on hover -->
<div class="hover-lift">Card lifts up</div>

<!-- Scale on hover -->
<div class="hover-scale">Scales up</div>

<!-- Glow on hover -->
<div class="hover-glow">Glows with primary color</div>

<!-- Image zoom -->
<div class="img-zoom-container">
    <img src="image.jpg" class="img-zoom" />
</div>
```

### Continuous Animations
```html
<div class="animate-float">Floats up and down</div>
<div class="animate-pulse">Pulses</div>
<div class="animate-bounce">Bounces</div>
```

### Stagger Children
Automatically animates all children with delays:

```html
<div class="stagger-children">
    <div>Child 1 - animates first</div>
    <div>Child 2 - animates second</div>
    <div>Child 3 - animates third</div>
</div>
```

## 📝 How to Add Animations to Your Pages

### Example 1: Homepage Sections
```php
<section class="py-16 bg-white">
    <div class="max-w-[1140px] mx-auto px-5">
        <!-- Section Header -->
        <div data-animate="fade-in-up">
            <h2 class="text-3xl font-bold mb-4">Our Services</h2>
            <p class="text-gray-600">Discover amazing experiences</p>
        </div>
        
        <!-- Cards Grid -->
        <div class="grid grid-cols-3 gap-6 mt-8">
            <div data-animate="fade-in-up" data-animate-delay="1" class="hover-lift">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3>Service 1</h3>
                </div>
            </div>
            <div data-animate="fade-in-up" data-animate-delay="2" class="hover-lift">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3>Service 2</h3>
                </div>
            </div>
            <div data-animate="fade-in-up" data-animate-delay="3" class="hover-lift">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3>Service 3</h3>
                </div>
            </div>
        </div>
    </div>
</section>
```

### Example 2: Listing Pages
```php
<!-- Listing Cards -->
<div class="grid grid-cols-3 gap-6">
    <?php foreach ($listings as $index => $listing): ?>
        <a href="..." 
           data-animate="fade-in-up" 
           data-animate-delay="<?php echo min($index + 1, 6); ?>"
           class="hover-lift">
            <div class="bg-white rounded-lg overflow-hidden shadow">
                <div class="img-zoom-container">
                    <img src="<?php echo $listing['image']; ?>" 
                         class="img-zoom w-full h-48 object-cover" />
                </div>
                <div class="p-4">
                    <h3><?php echo $listing['name']; ?></h3>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>
```

### Example 3: About Page
```php
<section class="py-16">
    <div class="max-w-[1140px] mx-auto px-5">
        <div class="grid grid-cols-2 gap-12 items-center">
            <!-- Text Content -->
            <div data-animate="fade-in-left">
                <h2 class="text-4xl font-bold mb-6">About Us</h2>
                <p class="text-gray-600 mb-4">We are passionate about...</p>
                <button class="hover-glow bg-primary text-white px-6 py-3 rounded-lg">
                    Learn More
                </button>
            </div>
            
            <!-- Image -->
            <div data-animate="fade-in-right" class="img-zoom-container rounded-lg overflow-hidden">
                <img src="about.jpg" class="img-zoom w-full" />
            </div>
        </div>
    </div>
</section>
```

### Example 4: Contact Page
```php
<section class="py-16">
    <div class="max-w-[1140px] mx-auto px-5">
        <div data-animate="fade-in-up" class="text-center mb-12">
            <h2 class="text-4xl font-bold mb-4">Get In Touch</h2>
            <p class="text-gray-600">We'd love to hear from you</p>
        </div>
        
        <div class="grid grid-cols-3 gap-6">
            <div data-animate="scale-in" data-animate-delay="1" class="hover-lift">
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <span class="material-symbols-outlined text-4xl text-primary mb-4">call</span>
                    <h3 class="font-bold mb-2">Phone</h3>
                    <p>+91 86009 68888</p>
                </div>
            </div>
            <div data-animate="scale-in" data-animate-delay="2" class="hover-lift">
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <span class="material-symbols-outlined text-4xl text-primary mb-4">mail</span>
                    <h3 class="font-bold mb-2">Email</h3>
                    <p>support@csnexplore.com</p>
                </div>
            </div>
            <div data-animate="scale-in" data-animate-delay="3" class="hover-lift">
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <span class="material-symbols-outlined text-4xl text-primary mb-4">location_on</span>
                    <h3 class="font-bold mb-2">Location</h3>
                    <p>Chhatrapati Sambhajinagar</p>
                </div>
            </div>
        </div>
    </div>
</section>
```

### Example 5: Blog Pages
```php
<!-- Blog Grid -->
<div class="grid grid-cols-3 gap-6">
    <?php foreach ($blogs as $index => $blog): ?>
        <article data-animate="fade-in-up" 
                 data-animate-delay="<?php echo ($index % 3) + 1; ?>"
                 class="hover-lift">
            <div class="bg-white rounded-lg overflow-hidden shadow">
                <div class="img-zoom-container">
                    <img src="<?php echo $blog['image']; ?>" 
                         class="img-zoom w-full h-48 object-cover" />
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2"><?php echo $blog['title']; ?></h3>
                    <p class="text-gray-600 mb-4"><?php echo $blog['excerpt']; ?></p>
                    <a href="..." class="text-primary font-bold hover:underline">
                        Read More →
                    </a>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</div>
```

## 🎯 Quick Implementation Checklist

### For Each Page:

1. **Headers/Titles:**
   ```html
   <h1 data-animate="fade-in-up">Page Title</h1>
   ```

2. **Content Sections:**
   ```html
   <div data-animate="fade-in-left">Left content</div>
   <div data-animate="fade-in-right">Right content</div>
   ```

3. **Cards/Grid Items:**
   ```html
   <div data-animate="fade-in-up" data-animate-delay="1" class="hover-lift">
       Card content
   </div>
   ```

4. **Images:**
   ```html
   <div class="img-zoom-container">
       <img src="..." class="img-zoom" />
   </div>
   ```

5. **Buttons:**
   ```html
   <button class="hover-glow">Click Me</button>
   ```

## 🔧 Advanced Features

### Counter Animation
For statistics/numbers:
```html
<div data-counter data-count="500">0</div>
```

### Parallax Effect
For hero sections:
```html
<div data-parallax="0.5">
    <!-- Content moves slower than scroll -->
</div>
```

### Manual Control (JavaScript)
```javascript
// Animate an element manually
CSNAnimations.animateElement(document.querySelector('.my-element'));

// Reset animation
CSNAnimations.resetElement(document.querySelector('.my-element'));
```

## 📱 Mobile Optimization

The animation system automatically:
- Reduces animation duration on mobile (0.5s vs 0.8s)
- Reduces movement distance (20px vs 40px)
- Respects `prefers-reduced-motion` for accessibility

## 🎨 Customization

### Change Animation Duration
```html
<div data-animate="fade-in-up" style="animation-duration: 1.2s;">
    Slower animation
</div>
```

### Change Animation Timing
```html
<div data-animate="fade-in-up" style="animation-timing-function: ease-in-out;">
    Different easing
</div>
```

## ✅ Pages to Update

Apply animations to these pages:

- [x] index.php (Homepage) - Already has data-reveal
- [ ] about.php
- [ ] contact.php
- [ ] blogs.php
- [ ] blog-detail.php
- [ ] listing pages (stays, cars, bikes, etc.)
- [ ] listing-detail.php
- [ ] my-booking.php
- [ ] login.php / register.php
- [ ] Admin pages (admin/*.php)
- [ ] Vendor pages (vendor/*.php)

## 🚀 Performance Tips

1. **Don't overuse animations** - Too many can be distracting
2. **Use delays wisely** - Max 6 delays per section
3. **Combine with hover effects** - Makes UI feel responsive
4. **Test on mobile** - Ensure animations don't slow down mobile devices

## 🐛 Troubleshooting

### Animations not working?
1. Check browser console for errors
2. Verify animations.css and animations.js are loaded
3. Check that elements have `data-animate` attribute
4. Ensure IntersectionObserver is supported (or use fallback)

### Animations too fast/slow?
Edit `animations.css` and change:
```css
[data-animate].animate-visible {
    animation-duration: 0.8s; /* Change this value */
}
```

### Want to disable animations?
Add to your CSS:
```css
[data-animate] {
    opacity: 1 !important;
    transform: none !important;
}
```

## 📚 Resources

- Animation CSS: `/animations.css`
- Animation JS: `/animations.js`
- Examples: This guide
- Browser Support: All modern browsers + IE11 fallback

---

**Need Help?** Check the console for `✨ CSNExplore animations initialized` message to confirm the system is loaded.
