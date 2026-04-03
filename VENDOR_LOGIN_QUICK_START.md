# Vendor Login - Quick Start Guide

## 🚀 Get Started in 3 Steps

### Step 1: Create Test Vendor
```
1. Open: http://yoursite.com/test-simple-vendor-login.html
2. Click "Create Test Vendor" button
3. Wait for success message
```

### Step 2: Test Login
```
1. Click "Test Login" button
2. Should see "✅ Login successful!"
3. Token and vendor data displayed
```

### Step 3: Access Dashboard
```
1. Click "Go to Vendor Dashboard" link
2. Or visit: http://yoursite.com/vendor/dashboard.php
3. Should see vendor name and statistics
```

## 📝 Test Credentials

After creating test vendor:
- **Username:** `testvendor`
- **Password:** `test123`

## 🔧 What Was Fixed

The vendor login API was trying to create its own database connection with environment variables that didn't exist. Now it:
- ✅ Uses the existing Database class
- ✅ Properly loads config.php
- ✅ Returns pure JSON (no HTML errors)
- ✅ Has proper error handling

## 📂 Key Files

| File | Purpose |
|------|---------|
| `php/api/vendor-auth-simple.php` | Vendor authentication API |
| `vendor/vendorlogin.php` | Vendor login page |
| `vendor/dashboard.php` | Vendor dashboard |
| `php/api/create-test-vendor.php` | Create test vendor helper |
| `test-simple-vendor-login.html` | Test page |

## ✅ Success Indicators

You'll know it's working when:
- ✅ Test vendor created successfully
- ✅ Login returns token and vendor data
- ✅ Token saved to localStorage
- ✅ Dashboard loads with vendor name
- ✅ No HTML errors in responses

## 🐛 If Something Goes Wrong

1. **Check browser console** for JavaScript errors
2. **Check `logs/php_errors.log`** for PHP errors
3. **Verify database connection** is working
4. **Verify vendor exists** in database

## 🧹 Cleanup (After Testing)

Delete these test files when confirmed working:
- `test-simple-vendor-login.html`
- `test-vendor-api.html`
- `test-api-direct.php`
- `test-vendor-login.php`
- `php/api/test-minimal.php`
- `php/api/create-test-vendor.php` (optional)

## 📚 Full Documentation

See `VENDOR_LOGIN_FIXED.md` for complete details.

---

**Ready to test?** Open `test-simple-vendor-login.html` now!
