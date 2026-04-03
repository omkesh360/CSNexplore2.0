# Vendor Login - Final Fix

## Problem
Vendor login returns: `Unexpected token '<', "<!DOCTYPE "... is not valid JSON`

This means the API is returning HTML (error page) instead of JSON.

## Solution
I've created a **simplified vendor authentication API** that doesn't depend on complex JWT functions.

## What Changed

### New Files:
1. **php/api/vendor-auth-simple.php** - Ultra-simple vendor auth (no dependencies)
2. **test-simple-vendor-login.html** - Test the simple API

### Updated Files:
1. **vendor/vendorlogin.php** - Now uses vendor-auth-simple.php
2. **vendor/dashboard.php** - Updated to work with simple API
3. **vendor/vendor-header.php** - Simplified API helper

## How to Test

### Step 1: Open Test Page
```
http://yoursite.com/test-simple-vendor-login.html
```

### Step 2: Enter Credentials
- Username: `testvendor`
- Password: `test123`
- Click "Test Login"

### Step 3: Check Results
- Should show: `✅ Login successful!`
- Should show token and vendor name
- Should save to localStorage

## If Still Not Working

### Check 1: Vendor Exists
```sql
SELECT id, name, username, status FROM vendors WHERE username = 'testvendor';
```

If no results, create vendor in admin panel first.

### Check 2: Check Raw Response
1. Open test page
2. Click "Test Login"
3. Look at "Raw Response" section
4. If it shows HTML, there's a PHP error
5. Check `logs/php_errors.log`

### Check 3: Database Connection
The simple API will show database errors directly in the response.

## How the Simple API Works

```
POST /php/api/vendor-auth-simple.php?action=login
{
  "username": "testvendor",
  "password": "test123"
}

Response:
{
  "success": true,
  "token": "eyJ2ZW5kb3JfaWQiOjEsInVzZXJuYW1lIjoidGVzdHZlbmRvciIsInR5cGUiOiJ2ZW5kb3IiLCJleHAiOjE3MTQ4MzI0MDB9",
  "vendor": {
    "id": 1,
    "name": "Test Vendor",
    "username": "testvendor",
    "status": "active",
    ...
  }
}
```

## Troubleshooting

### Issue: "Vendors table does not exist"
**Solution:** Access any page to trigger auto-migration, or run:
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

### Issue: "Invalid username or password"
**Solution:** 
1. Check if vendor exists: `SELECT * FROM vendors WHERE username = 'testvendor';`
2. If not, create in admin panel
3. Make sure status is "active"

### Issue: "Database error"
**Solution:**
1. Check database credentials in `.env` or `php/database.php`
2. Verify MySQL is running
3. Check `logs/php_errors.log`

### Issue: Still showing HTML error
**Solution:**
1. Open `test-simple-vendor-login.html`
2. Look at "Raw Response" section
3. Copy the HTML error
4. Search for the error in `logs/php_errors.log`
5. Fix the underlying issue

## Next Steps

Once vendor login works:

1. **Delete test files:**
   - test-vendor-api.html
   - test-api-direct.php
   - test-vendor-login.php
   - test-simple-vendor-login.html
   - php/api/test-minimal.php

2. **Keep the simple API:**
   - php/api/vendor-auth-simple.php (this is now the main auth API)

3. **Test vendor dashboard:**
   - Should show vendor name
   - Should show statistics
   - Should load without errors

## Success Indicators

✅ test-simple-vendor-login.html shows "Login successful"
✅ Token appears in localStorage
✅ Vendor data appears in localStorage
✅ Can access vendor dashboard
✅ Dashboard shows vendor name
✅ No HTML errors in response

## Browser Console Test

```javascript
// Test the simple API directly
fetch('php/api/vendor-auth-simple.php?action=login', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        username: 'testvendor',
        password: 'test123'
    })
})
.then(r => r.text())
.then(text => {
    console.log('Raw:', text);
    return JSON.parse(text);
})
.then(data => {
    console.log('Parsed:', data);
    if (data.token) {
        localStorage.setItem('csn_vendor_token', data.token);
        localStorage.setItem('csn_vendor_user', JSON.stringify(data.vendor));
        console.log('✅ Saved to localStorage');
        window.location.href = 'vendor/dashboard.php';
    }
})
.catch(err => console.error('Error:', err));
```

## Why This Works

The simple API:
- ✅ No complex JWT functions
- ✅ No dependency on jwt.php
- ✅ Direct database connection
- ✅ Clear error messages
- ✅ Returns pure JSON
- ✅ No output buffering issues
- ✅ No header conflicts

## Support

If vendor login still doesn't work:

1. Open `test-simple-vendor-login.html`
2. Check the "Raw Response" section
3. If it shows HTML, copy the error
4. Search for the error in `logs/php_errors.log`
5. Fix the underlying PHP error

---

**Status:** Simplified vendor auth API created ✅
**Date:** April 4, 2026
**Version:** 1.2.0
