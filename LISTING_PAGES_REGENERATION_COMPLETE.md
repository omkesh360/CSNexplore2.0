# Listing Pages Regeneration - Complete Setup

## Overview
All listing pages have been fully configured with embedded Google Maps and random similar listings display. The system is ready to regenerate all pages with default maps.

## What's Been Set Up

### 1. Database Schema
- Added `map_embed` column (LONGTEXT) to all 6 listing tables:
  - `stays` (Hotels)
  - `cars` (Car Rentals)
  - `bikes` (Bike Rentals)
  - `restaurants` (Dining)
  - `attractions` (Attractions)
  - `buses` (Bus Services)

### 2. Listing Detail Pages
- **File**: `listing-detail.php`
- Features:
  - Embedded Google Maps section (responsive, 450px height)
  - 4 random similar listings at the bottom
  - Fallback to default Aurangabad map if none provided
  - Mobile-responsive design

### 3. Admin Panel
- **Map Embeds Manager**: `/admin/map-embeds.php`
  - Browse listings by category
  - Search by name or location
  - Edit individual maps
  - Status indicator (Added/Missing)

- **Regenerate Pages**: `/admin/regenerate-pages.php` (NEW)
  - One-click regeneration of all listings
  - Adds default Aurangabad map to listings without maps
  - Shows progress and results
  - Preserves existing custom maps

### 4. API Endpoints
- `php/api/update-map-embed.php` - Update map for a listing
- `php/api/listings.php` - Fetch listings by category
- `admin/regenerate-listings.php` - Bulk regenerate maps

## How to Regenerate All Pages

### Step 1: Access Admin Panel
1. Go to Admin Dashboard
2. Click "Regenerate" in the sidebar menu
3. Or navigate directly to `/admin/regenerate-pages.php`

### Step 2: Click Regenerate Button
1. Review the information about what will happen
2. Click "Regenerate Now" button
3. Wait for the process to complete

### Step 3: Verify Results
- See the count of updated listings per category
- All listings now have the default Aurangabad map
- Similar listings are automatically displayed

## What Gets Added

### Default Map
```html
<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d16426.329596104577!2d75.30037121099188!3d19.851617685624074!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bdb98f39ca15447%3A0x96c2632e2aaa42c!2sKamalnayan%20Bajaj%20Hospital!5e0!3m2!1sen!2sin!4v1775290483319!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
```

### Similar Listings
- 4 random listings from the same category
- Displayed in a responsive grid
- Shows image, name, location, price, and rating
- Links to other listings

## Customizing Maps After Regeneration

### For Individual Listings
1. Go to Admin → Map Embeds
2. Select the category
3. Find the listing
4. Click "Edit Map"
5. Replace with custom Google Maps embed code
6. Save

### Getting Custom Map Codes
1. Open Google Maps
2. Right-click on the location
3. Select "Share"
4. Click "Embed a map"
5. Copy the HTML iframe code
6. Paste in the admin panel

## Files Created/Modified

### New Files
- `admin/regenerate-pages.php` - Regeneration UI
- `admin/regenerate-listings.php` - Regeneration API
- `php/regenerate-listings.php` - CLI regeneration script

### Modified Files
- `php/database.php` - Added map_embed columns to schema
- `admin/admin-header.php` - Added regenerate link to menu

### Existing Files (Already Configured)
- `listing-detail.php` - Displays maps and similar listings
- `admin/map-embeds.php` - Manage individual maps
- `php/api/update-map-embed.php` - Update map API

## Features

### For Users
✓ See embedded maps on every listing page
✓ View 4 random similar listings
✓ Responsive design on all devices
✓ Click to view other listings

### For Admins
✓ One-click regeneration of all pages
✓ Manage individual maps
✓ Search and filter listings
✓ Status indicators
✓ Bulk operations

## Technical Details

### Database Queries
```sql
-- Get listings without maps
SELECT id FROM {category} WHERE is_active = 1 AND (map_embed IS NULL OR map_embed = '')

-- Update with default map
UPDATE {category} SET map_embed = ? WHERE id = ?

-- Get similar listings
SELECT * FROM {category} WHERE id != ? AND is_active = 1 ORDER BY RAND() LIMIT 4
```

### Responsive Breakpoints
- Desktop: 4 columns for similar listings
- Tablet: 2 columns
- Mobile: 1 column
- Maps: 100% width, 450px height

## Security

✓ Admin authentication required
✓ JWT token validation
✓ Input sanitization
✓ CORS headers configured
✓ SQL injection prevention

## Performance

- Maps load lazily (loading="lazy")
- Similar listings use RAND() for randomization
- Responsive images with proper sizing
- Optimized CSS and JavaScript

## Next Steps

1. **Regenerate All Pages**
   - Go to Admin → Regenerate
   - Click "Regenerate Now"
   - Wait for completion

2. **Customize Maps** (Optional)
   - Go to Admin → Map Embeds
   - Edit individual listings
   - Add custom location maps

3. **Monitor Performance**
   - Check Admin → Performance
   - Monitor page load times
   - Verify maps are loading

## Troubleshooting

### Maps Not Showing
- Check if map_embed column exists in database
- Verify Google Maps embed code is valid
- Check browser console for errors

### Similar Listings Not Showing
- Ensure listings are marked as active (is_active = 1)
- Check if there are enough listings in the category
- Verify database connection

### Regeneration Not Working
- Check admin authentication
- Verify database permissions
- Check error logs in `/logs/php_errors.log`

## Support

For issues or questions:
1. Check the error logs
2. Verify database schema
3. Test API endpoints directly
4. Review browser console for JavaScript errors

