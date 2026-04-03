# Vendor System - Quick Installation Guide

## Step 1: Database Migration

The vendor system tables will be automatically created when you access the site. However, you need to add the `vendor_id` columns to existing tables.

### Option A: Automatic (Recommended)
The system will attempt to add columns automatically on first load. Just access any page and the columns will be added if they don't exist.

### Option B: Manual SQL
If you prefer manual control, run this SQL:

```sql
-- Add vendor_id to stays table
ALTER TABLE `stays` 
ADD COLUMN `vendor_id` INT NULL AFTER `id`,
ADD INDEX `idx_vendor_stays` (`vendor_id`);

-- Add vendor_id to cars table
ALTER TABLE `cars` 
ADD COLUMN `vendor_id` INT NULL AFTER `id`,
ADD INDEX `idx_vendor_cars` (`vendor_id`);

-- Add is_available to cars table
ALTER TABLE `cars` 
ADD COLUMN `is_available` TINYINT(1) DEFAULT 1 AFTER `is_active`;
```

## Step 2: Verify Installation

1. Login to admin panel: `/adminexplorer.php`
2. Check if "Vendors" menu appears in sidebar
3. Click on "Vendors" - you should see the vendor management page

## Step 3: Create First Vendor

1. In Admin Panel → Vendors
2. Click "Add Vendor"
3. Fill in details:
   - Name: Test Vendor
   - Username: testvendor
   - Password: test123
   - Status: Active
4. Click "Create Vendor"

## Step 4: Test Vendor Login

1. Go to `/vendor/vendorlogin.php`
2. Login with:
   - Username: testvendor
   - Password: test123
3. You should see the vendor dashboard

## Step 5: Verify Everything Works

### Admin Side:
- ✅ Can create vendors
- ✅ Can edit vendors
- ✅ Can view vendor statistics
- ✅ Can deactivate vendors

### Vendor Side:
- ✅ Can login
- ✅ Can see dashboard
- ✅ Can access rooms page (API ready)
- ✅ Can access cars page (API ready)

## Troubleshooting

### "Vendors" menu not showing in admin
- Clear browser cache
- Check if you're logged in as admin (not regular user)
- Verify admin-header.php was updated

### Vendor login fails
- Check if vendor status is "active"
- Verify username/password are correct
- Check browser console for errors

### Database errors
- Ensure MySQL user has ALTER table permissions
- Check if tables were created successfully
- Look at PHP error logs in `/logs/php_errors.log`

### API returns 401 Unauthorized
- Check if vendor token is being sent
- Verify token hasn't expired
- Check if vendor account is active

## Default Credentials

### Admin
- URL: `/adminexplorer.php`
- Email: admin@csnexplore.com
- Password: admin123

### Test Vendor (after creation)
- URL: `/vendor/vendorlogin.php`
- Username: (as created by admin)
- Password: (as set by admin)

## File Permissions

Ensure these directories are writable:
```bash
chmod 755 vendor/
chmod 755 php/api/
chmod 755 database/
chmod 777 logs/
```

## Next Steps

After installation:
1. Create vendor UI pages for rooms and cars management
2. Add image upload functionality
3. Integrate vendor listings with frontend
4. Set up booking notifications for vendors
5. Add vendor analytics

## Security Checklist

- [ ] Change default admin password
- [ ] Set strong JWT_SECRET in .env
- [ ] Use HTTPS in production
- [ ] Restrict database user permissions
- [ ] Enable PHP error logging (not display)
- [ ] Regular backups of database

## Support

If you encounter issues:
1. Check browser console (F12)
2. Check PHP error logs: `/logs/php_errors.log`
3. Verify database tables exist
4. Test API endpoints directly
5. Review VENDOR_SYSTEM_README.md for detailed documentation

## Quick Test Commands

### Test Vendor API (in browser console):
```javascript
// After vendor login
fetch('../php/api/vendor-rooms.php?action=stats', {
    headers: {
        'Authorization': 'Bearer ' + localStorage.getItem('csn_vendor_token')
    }
}).then(r => r.json()).then(console.log);
```

### Test Admin Vendor API (in browser console):
```javascript
// After admin login
fetch('../php/api/vendors.php?action=list', {
    headers: {
        'Authorization': 'Bearer ' + localStorage.getItem('csn_admin_token')
    }
}).then(r => r.json()).then(console.log);
```

## Success Indicators

✅ Vendor tables created in database
✅ Admin can access vendor management page
✅ Can create vendor accounts
✅ Vendor can login successfully
✅ Vendor dashboard loads with stats
✅ API endpoints return data (not 401/403)
✅ No errors in browser console
✅ No errors in PHP logs

Installation complete! 🎉
