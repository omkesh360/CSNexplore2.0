# Troubleshooting Guide - Multi-Vendor System

## Common Issues and Solutions

### 1. Admin Login Not Working - "Unexpected end of JSON input"

**Symptoms:**
- Error in browser console: `Failed to execute 'json' on 'Response': Unexpected end of JSON input`
- Admin login fails
- API returns HTML instead of JSON

**Cause:**
- PHP syntax error in database.php
- Database connection error
- PHP error being output before JSON

**Solution:**
✅ **FIXED** - Removed escaped quotes from ALTER TABLE statements in `php/database.php`

**Verify Fix:**
1. Open browser console (F12)
2. Go to Network tab
3. Try to login
4. Check the response from `auth.php?action=login`
5. Should see JSON response, not HTML error

**If still not working:**
```bash
# Check PHP error logs
tail -f logs/php_errors.log

# Or check server error logs
tail -f /var/log/apache2/error.log  # Apache
tail -f /var/log/nginx/error.log    # Nginx
```

---

### 2. Database Tables Not Created

**Symptoms:**
- Error: "Table doesn't exist"
- Admin login fails with database error

**Solution:**
```sql
-- Run this SQL manually in phpMyAdmin or MySQL console
-- Copy from database/vendor_system_migration.sql
```

**Or let auto-migration work:**
1. Access any page on the site
2. Tables will be created automatically
3. Check database to verify

---

### 3. Vendor Login Fails

**Symptoms:**
- "Invalid credentials" error
- Vendor can't login

**Checklist:**
- [ ] Vendor account created by admin?
- [ ] Vendor status is "active"?
- [ ] Username/password correct?
- [ ] Check browser console for errors

**Debug:**
```javascript
// In browser console
fetch('php/api/vendor-auth.php?action=login', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        username: 'testvendor',
        password: 'test123'
    })
}).then(r => r.text()).then(console.log);
```

---

### 4. "Vendors" Menu Not Showing in Admin

**Symptoms:**
- Admin sidebar doesn't show "Vendors" option

**Solutions:**
1. **Clear browser cache** (Ctrl+Shift+Delete)
2. **Hard refresh** (Ctrl+F5)
3. **Verify you're logged in as admin** (not regular user)
4. **Check admin-header.php** was updated

**Verify:**
```javascript
// In browser console
console.log(localStorage.getItem('csn_admin_user'));
// Should show: {"role":"admin",...}
```

---

### 5. API Returns 401 Unauthorized

**Symptoms:**
- All API calls return 401
- Can't fetch data after login

**Causes:**
- Token not being sent
- Token expired
- Vendor account inactive

**Debug:**
```javascript
// Check if token exists
console.log(localStorage.getItem('csn_vendor_token'));
console.log(localStorage.getItem('csn_admin_token'));

// Test API with token
const token = localStorage.getItem('csn_vendor_token');
fetch('../php/api/vendor-rooms.php?action=stats', {
    headers: {'Authorization': 'Bearer ' + token}
}).then(r => r.json()).then(console.log);
```

**Solutions:**
- Re-login to get fresh token
- Check vendor status is "active"
- Verify token format is correct

---

### 6. Database Connection Error

**Symptoms:**
- "Connection refused"
- "Access denied for user"

**Check .env file:**
```env
DB_HOST=localhost
DB_DATABASE=csnexplore
DB_USERNAME=root
DB_PASSWORD=your_password
```

**Or check database.php defaults:**
```php
$host = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_DATABASE') ?: 'csnexplore';
$user = getenv('DB_USERNAME') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';
```

---

### 7. Vendor Can See Other Vendor's Data

**Symptoms:**
- Data isolation not working
- Vendor sees listings from other vendors

**This should NOT happen!**

**Debug:**
```javascript
// Check vendor ID in token
const token = localStorage.getItem('csn_vendor_token');
const payload = JSON.parse(atob(token.split('.')[0]));
console.log('Vendor ID:', payload.vendor_id);
```

**Verify API:**
- Check all queries include `WHERE vendor_id = ?`
- Verify ownership checks in update/delete operations

---

### 8. Images Not Uploading

**Symptoms:**
- Image upload fails
- No error message

**Note:** Image upload system not yet implemented in vendor UI

**Temporary Solution:**
- Use admin panel to upload images
- Or use direct URLs to images

**Coming Soon:**
- Vendor image upload functionality
- Gallery management

---

### 9. Booking Not Linked to Vendor

**Symptoms:**
- Bookings don't show vendor information

**Note:** Booking integration not yet implemented

**Coming Soon:**
- Vendor booking notifications
- Vendor booking dashboard

---

### 10. Performance Issues

**Symptoms:**
- Slow page loads
- API timeouts

**Solutions:**
1. **Check database indexes:**
```sql
SHOW INDEX FROM stays;
SHOW INDEX FROM cars;
SHOW INDEX FROM vendors;
```

2. **Optimize queries:**
- Ensure vendor_id columns are indexed
- Use LIMIT on large result sets

3. **Enable caching:**
- Browser caching (already configured in .htaccess)
- Consider Redis/Memcached for API responses

---

## Quick Diagnostic Commands

### Check Database Tables
```sql
SHOW TABLES LIKE 'vendors';
SHOW TABLES LIKE 'room_types';
SHOW TABLES LIKE 'rooms';
DESCRIBE stays;
DESCRIBE cars;
```

### Check Vendor Columns
```sql
SHOW COLUMNS FROM stays LIKE 'vendor_id';
SHOW COLUMNS FROM cars LIKE 'vendor_id';
SHOW COLUMNS FROM cars LIKE 'is_available';
```

### Check Admin User
```sql
SELECT id, email, role FROM users WHERE role = 'admin';
```

### Check Vendors
```sql
SELECT id, name, username, status FROM vendors;
```

---

## Browser Console Tests

### Test Admin Login
```javascript
fetch('php/api/auth.php?action=login', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        email: 'admin@csnexplore.com',
        password: 'admin123'
    })
}).then(r => r.json()).then(console.log);
```

### Test Vendor Login
```javascript
fetch('php/api/vendor-auth.php?action=login', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        username: 'testvendor',
        password: 'test123'
    })
}).then(r => r.json()).then(console.log);
```

### Test Vendor API
```javascript
const token = localStorage.getItem('csn_vendor_token');
fetch('php/api/vendor-rooms.php?action=stats', {
    headers: {'Authorization': 'Bearer ' + token}
}).then(r => r.json()).then(console.log);
```

---

## File Permissions

Ensure correct permissions:
```bash
chmod 755 vendor/
chmod 755 php/api/
chmod 755 admin/
chmod 777 logs/
chmod 777 images/uploads/
```

---

## PHP Error Logging

Enable error logging in php.ini or .htaccess:
```ini
display_errors = Off
log_errors = On
error_log = logs/php_errors.log
```

Check logs:
```bash
tail -f logs/php_errors.log
```

---

## Reset Everything

If all else fails:

### 1. Clear Browser Data
- Clear localStorage
- Clear cookies
- Clear cache

### 2. Reset Database
```sql
DROP TABLE IF EXISTS rooms;
DROP TABLE IF EXISTS room_types;
DROP TABLE IF EXISTS vendors;
ALTER TABLE stays DROP COLUMN vendor_id;
ALTER TABLE cars DROP COLUMN vendor_id;
ALTER TABLE cars DROP COLUMN is_available;
```

Then reload any page to recreate tables.

### 3. Re-login
- Login to admin panel
- Create new vendor
- Test vendor login

---

## Getting Help

### Check These First:
1. Browser console (F12) - JavaScript errors
2. Network tab - API responses
3. PHP error logs - Server errors
4. Database - Table structure

### Provide This Info:
- Error message (exact text)
- Browser console output
- PHP error log entries
- Steps to reproduce
- What you expected vs what happened

---

## Success Indicators

✅ Admin can login
✅ "Vendors" menu appears in admin sidebar
✅ Can create vendor accounts
✅ Vendor can login at /vendorlogin
✅ Vendor dashboard loads with stats
✅ API returns JSON (not HTML errors)
✅ No errors in browser console
✅ No errors in PHP logs

---

## Contact

For additional support, refer to:
- `IMPLEMENTATION_COMPLETE.md` - Full implementation guide
- `VENDOR_SYSTEM_README.md` - Technical documentation
- `VENDOR_INSTALLATION.md` - Installation steps

---

**Last Updated:** April 4, 2026
**Version:** 1.0.0
