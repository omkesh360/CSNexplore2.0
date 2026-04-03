# Vendor Login Troubleshooting Guide

## Quick Fix Steps

### Step 1: Test the System
Open in your browser: `http://yoursite.com/test-vendor-api.html`

This will:
- ✅ Check if vendors table exists
- ✅ Test the login API
- ✅ Show detailed error messages

### Step 2: Check if Vendor Exists

1. **Login to admin panel:** `/adminexplorer.php`
2. **Go to Vendors page**
3. **Create a test vendor:**
   - Name: Test Vendor
   - Username: testvendor
   - Password: test123
   - Status: Active
4. **Click "Create Vendor"**

### Step 3: Test Vendor Login

1. **Go to:** `/vendor/vendorlogin.php` or `/vendorlogin`
2. **Enter credentials:**
   - Username: testvendor
   - Password: test123
3. **Click "Sign In"**

---

## Common Issues & Solutions

### Issue 1: "Invalid username or password"

**Possible Causes:**
- Vendor doesn't exist in database
- Wrong username/password
- Vendor status is "inactive"

**Solution:**
```sql
-- Check if vendor exists
SELECT id, name, username, status FROM vendors;

-- If no vendors, create one in admin panel first
```

**Or use admin panel:**
1. Login to admin
2. Go to Vendors
3. Create a vendor
4. Make sure status is "Active"

---

### Issue 2: "Unexpected end of JSON input"

**Possible Causes:**
- PHP error before JSON output
- Database connection error
- Missing vendor-auth.php file

**Solution:**
1. Check PHP error logs: `logs/php_errors.log`
2. Look for lines with `[Vendor Auth]`
3. Check if file exists: `php/api/vendor-auth.php`

---

### Issue 3: Vendor table doesn't exist

**Solution:**
The table should be created automatically. If not:

```sql
CREATE TABLE IF NOT EXISTS `vendors` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `username` VARCHAR(100) UNIQUE NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255),
  `phone` VARCHAR(50),
  `business_name` VARCHAR(255),
  `status` ENUM('active','inactive') DEFAULT 'active',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

### Issue 4: Login redirects to login page

**Possible Causes:**
- Token not being saved
- JavaScript error
- Browser blocking localStorage

**Solution:**
1. Open browser console (F12)
2. Check for JavaScript errors
3. Try in incognito mode
4. Check if localStorage is enabled

---

### Issue 5: "Your account is inactive"

**Solution:**
1. Login to admin panel
2. Go to Vendors
3. Find the vendor
4. Click Edit
5. Change Status to "Active"
6. Save

---

## Debug Commands

### Check Vendors in Database
```sql
SELECT id, name, username, status, created_at 
FROM vendors 
ORDER BY created_at DESC;
```

### Check Vendor Password Hash
```sql
SELECT username, password_hash 
FROM vendors 
WHERE username = 'testvendor';
```

### Create Test Vendor Manually
```sql
INSERT INTO vendors (name, username, password_hash, status) 
VALUES (
  'Test Vendor',
  'testvendor',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
  'active'
);
```

---

## Test Files Created

### 1. test-vendor-api.html
**URL:** `/test-vendor-api.html`
**Purpose:** Interactive testing of vendor login API
**Features:**
- Check if vendors table exists
- Test login with custom credentials
- View detailed error messages
- Links to admin and vendor pages

### 2. test-vendor-login.php
**URL:** `/test-vendor-login.php`
**Purpose:** Server-side testing
**Features:**
- Check database tables
- List all vendors
- Test JWT functions
- Show configuration

---

## Verification Checklist

- [ ] Vendors table exists in database
- [ ] At least one vendor with status "active"
- [ ] vendor-auth.php file exists
- [ ] JWT functions working (test with test-vendor-login.php)
- [ ] No PHP errors in logs
- [ ] Browser console shows no JavaScript errors
- [ ] localStorage is enabled in browser

---

## Expected Behavior

### Successful Login Flow:
1. User enters username/password
2. POST request to `/php/api/vendor-auth.php?action=login`
3. API validates credentials
4. API generates JWT token
5. Token saved to localStorage as `csn_vendor_token`
6. Vendor data saved to localStorage as `csn_vendor_user`
7. Redirect to `/vendor/dashboard.php`
8. Dashboard loads vendor statistics

### Failed Login Flow:
1. User enters wrong credentials
2. API returns 401 error
3. Error message displayed: "Invalid username or password"
4. User stays on login page

---

## API Endpoints

### Login
```
POST /php/api/vendor-auth.php?action=login
Content-Type: application/json

{
  "username": "testvendor",
  "password": "test123"
}

Response (Success):
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "vendor": {
    "id": 1,
    "name": "Test Vendor",
    "username": "testvendor",
    "status": "active",
    ...
  }
}

Response (Error):
{
  "error": "Invalid username or password"
}
```

### Verify Token
```
GET /php/api/vendor-auth.php?action=verify
Authorization: Bearer {token}

Response:
{
  "vendor": {
    "id": 1,
    "name": "Test Vendor",
    ...
  }
}
```

---

## Browser Console Tests

### Test Login (in browser console)
```javascript
fetch('php/api/vendor-auth.php?action=login', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        username: 'testvendor',
        password: 'test123'
    })
})
.then(r => r.json())
.then(data => {
    console.log('Login response:', data);
    if (data.token) {
        localStorage.setItem('csn_vendor_token', data.token);
        localStorage.setItem('csn_vendor_user', JSON.stringify(data.vendor));
        console.log('✅ Login successful!');
        window.location.href = 'vendor/dashboard.php';
    }
})
.catch(err => console.error('Login error:', err));
```

### Check Stored Token
```javascript
console.log('Token:', localStorage.getItem('csn_vendor_token'));
console.log('Vendor:', JSON.parse(localStorage.getItem('csn_vendor_user')));
```

### Clear Stored Data
```javascript
localStorage.removeItem('csn_vendor_token');
localStorage.removeItem('csn_vendor_user');
console.log('✅ Cleared vendor data');
```

---

## PHP Error Log Monitoring

Watch the error log in real-time:
```bash
tail -f logs/php_errors.log | grep "Vendor Auth"
```

You should see:
```
[Vendor Auth] Request received: POST
[Vendor Auth] Action: login
[Vendor Auth] Login attempt for username: testvendor
[Vendor Auth] Vendor found: Test Vendor (Status: active)
[Vendor Auth] Login successful for: testvendor
[Vendor Auth] Token generated, sending response
```

---

## Still Not Working?

### 1. Check File Permissions
```bash
chmod 755 vendor/
chmod 644 vendor/vendorlogin.php
chmod 755 php/api/
chmod 644 php/api/vendor-auth.php
```

### 2. Check .htaccess
Make sure this line exists:
```apache
RewriteRule ^vendorlogin/?$ vendor/vendorlogin.php [L]
```

### 3. Clear Everything
```javascript
// In browser console
localStorage.clear();
sessionStorage.clear();
// Then hard refresh: Ctrl+Shift+R
```

### 4. Check Database Connection
```php
// In test-vendor-login.php
$db = getDB();
echo "Database connected: " . ($db ? "YES" : "NO");
```

---

## Success Indicators

✅ test-vendor-api.html shows "Login successful"
✅ Token appears in localStorage
✅ Vendor data appears in localStorage
✅ Redirects to vendor dashboard
✅ Dashboard shows vendor name
✅ No errors in browser console
✅ No errors in PHP logs

---

## Delete Test Files After Fixing

Once vendor login works, delete these test files:
- `test-vendor-api.html`
- `test-vendor-login.php`

---

**Last Updated:** April 4, 2026
**Status:** Troubleshooting guide complete
