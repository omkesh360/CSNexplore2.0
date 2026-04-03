# Multi-Vendor System Implementation Guide

## Overview
This document describes the multi-vendor system added to CSNExplore, allowing multiple vendors to manage their own rooms and cars while maintaining admin control.

## Database Changes

### 1. Run Migration
Execute the SQL migration file to add vendor tables and columns:
```bash
mysql -u your_username -p your_database < database/vendor_system_migration.sql
```

Or manually run the SQL from `database/vendor_system_migration.sql`

### Tables Added:
- `vendors` - Vendor accounts
- `room_types` - Room type definitions
- `rooms` - Individual room inventory

### Columns Added:
- `stays.vendor_id` - Links stays to vendors
- `cars.vendor_id` - Links cars to vendors
- `cars.is_available` - Availability toggle for cars

## Access Points

### Vendor Login
- URL: `/vendor/vendorlogin.php` or `/vendorlogin`
- Vendors use username/password (no registration - admin creates accounts)

### Vendor Dashboard
- URL: `/vendor/dashboard.php`
- Shows stats and quick access to rooms/cars management

### Admin Vendor Management
- URL: `/admin/vendors.php`
- Create, edit, delete vendor accounts
- View vendor statistics and listings

## Features Implemented

### 1. Vendor Authentication
- Session-based login system
- JWT token authentication
- Separate from user/admin authentication
- Only admin can create vendor accounts

### 2. Admin Vendor Management
- Create vendor accounts with username/password
- Edit vendor details (name, email, phone, business name)
- Activate/deactivate vendor accounts
- View vendor statistics (total listings, active status)
- Delete vendors (only if no listings exist)

### 3. Vendor Dashboard
- Overview of total rooms and cars
- Availability statistics
- Quick access to management pages
- Recent listings display

### 4. Room Management System
- Create room types (categories)
- Add individual rooms under each type
- Set pricing per room
- Toggle availability
- Manage amenities and descriptions
- View room inventory

### 5. Car Management System
- Add/edit car listings
- Set daily rental price
- Specify fuel type, transmission, seating
- Upload images and gallery
- Toggle availability
- Manage features and descriptions

## Security Features

### Access Control
- Vendors can only view/edit their own listings
- Admin can view/edit all listings
- Token-based authentication with expiry
- Session validation on every request

### Data Isolation
- Vendor ID filtering on all queries
- Ownership verification before updates/deletes
- Prepared statements to prevent SQL injection

## API Endpoints

### Vendor Authentication
- `POST /php/api/vendor-auth.php?action=login` - Vendor login
- `GET /php/api/vendor-auth.php?action=verify` - Verify token

### Admin Vendor Management
- `GET /php/api/vendors.php?action=list` - List all vendors
- `GET /php/api/vendors.php?action=get&id={id}` - Get vendor details
- `POST /php/api/vendors.php?action=create` - Create vendor
- `POST /php/api/vendors.php?action=update` - Update vendor
- `DELETE /php/api/vendors.php?action=delete&id={id}` - Delete vendor

### Vendor Rooms API
- `GET /php/api/vendor-rooms.php?action=stats` - Get room statistics
- `GET /php/api/vendor-rooms.php?action=list` - List room types
- `GET /php/api/vendor-rooms.php?action=get&id={id}` - Get room type details
- `POST /php/api/vendor-rooms.php?action=create_type` - Create room type
- `POST /php/api/vendor-rooms.php?action=update_type` - Update room type
- `DELETE /php/api/vendor-rooms.php?action=delete_type&id={id}` - Delete room type
- `POST /php/api/vendor-rooms.php?action=create_room` - Create room
- `POST /php/api/vendor-rooms.php?action=update_room` - Update room
- `DELETE /php/api/vendor-rooms.php?action=delete_room&id={id}` - Delete room

### Vendor Cars API
- `GET /php/api/vendor-cars.php?action=stats` - Get car statistics
- `GET /php/api/vendor-cars.php?action=list` - List cars
- `GET /php/api/vendor-cars.php?action=get&id={id}` - Get car details
- `POST /php/api/vendor-cars.php?action=create` - Create car
- `POST /php/api/vendor-cars.php?action=update` - Update car
- `DELETE /php/api/vendor-cars.php?action=delete&id={id}` - Delete car
- `POST /php/api/vendor-cars.php?action=toggle_availability` - Toggle availability

## Usage Instructions

### For Admin

1. **Create Vendor Account**
   - Go to Admin Panel → Vendors
   - Click "Add Vendor"
   - Fill in vendor details (name, username, password)
   - Set status to "Active"
   - Click "Create Vendor"

2. **Manage Vendors**
   - View all vendors and their statistics
   - Edit vendor details or reset passwords
   - Deactivate vendors to prevent login
   - Delete vendors (only if no listings)

3. **View Vendor Listings**
   - Admin can see all listings with vendor_id
   - Filter by vendor in listings page
   - Override prices or availability if needed

### For Vendors

1. **Login**
   - Go to `/vendor/vendorlogin.php`
   - Enter username and password provided by admin
   - Access vendor dashboard

2. **Manage Rooms**
   - Create room types (e.g., "Deluxe Room", "Suite")
   - Add individual rooms under each type
   - Set pricing and availability
   - Update room details anytime

3. **Manage Cars**
   - Add car listings with details
   - Upload images
   - Set daily rental price
   - Toggle availability as needed

## Backward Compatibility

- Existing listings (vendor_id = NULL) are admin-managed
- All current functionality remains intact
- Frontend displays both admin and vendor listings
- No changes required to existing booking system

## File Structure

```
/vendor/
  ├── vendorlogin.php       # Vendor login page
  ├── dashboard.php         # Vendor dashboard
  ├── rooms.php             # Room management (to be created)
  ├── cars.php              # Car management (to be created)
  ├── vendor-header.php     # Shared header
  └── vendor-footer.php     # Shared footer

/php/api/
  ├── vendor-auth.php       # Vendor authentication
  ├── vendors.php           # Admin vendor management
  ├── vendor-rooms.php      # Vendor room operations
  └── vendor-cars.php       # Vendor car operations

/admin/
  └── vendors.php           # Admin vendor management UI

/database/
  └── vendor_system_migration.sql  # Database schema updates
```

## Next Steps (To Complete)

1. Create `/vendor/rooms.php` - Full room management UI
2. Create `/vendor/cars.php` - Full car management UI
3. Update admin listings page to show vendor filter
4. Add image upload functionality for vendors
5. Implement booking notifications to vendors
6. Add vendor analytics/reports
7. Create vendor profile settings page

## Testing

### Test Admin Functions
1. Login to admin panel
2. Create a test vendor account
3. View vendor in vendors list
4. Edit vendor details
5. Try deleting vendor (should work if no listings)

### Test Vendor Functions
1. Login with vendor credentials at `/vendor/vendorlogin.php`
2. View dashboard statistics
3. Test room/car API endpoints using browser console
4. Verify data isolation (vendor can't see other vendor's data)

## Support

For issues or questions:
- Check browser console for API errors
- Verify database migration ran successfully
- Ensure vendor account is "active" status
- Check PHP error logs in `/logs/php_errors.log`

## Security Notes

- Change JWT_SECRET in production (in .env file)
- Use HTTPS in production
- Implement rate limiting on login endpoints
- Regular security audits recommended
- Keep vendor passwords secure (use strong passwords)
