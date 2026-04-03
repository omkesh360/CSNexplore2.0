# Vendor Login - Debug Steps

## Error: "Unexpected token '<', "<!DOCTYPE "... is not valid JSON"

This error means the API is returning HTML instead of JSON, usually due to a PHP error.

## 🔧 Step-by-Step Fix

### Step 1: Run the Test Suite

Open in your browser:
```
http://yoursite.com/test-vendor-api.html
```

This will automatically run 3 tests:
1. ✅ Test minimal API (no dependencies)
2. ✅ Check if vendors table exists  
3. ✅ Test vendor login with credentials

### Step 2: Check What's Failing

The test page will show you exactly which part is failing:

**If Minimal API fails:**
- Problem: Basic PHP/server configuration
- Solution: Check PHP error logs, file permissions

**If Vendors Table check fails:**
- Problem: Database not set up
- Solution: Access any page to trigger auto-migration

**If Login test fails:**
- Problem: Vendor doesn't exist or wrong credentials
- Solution: Create vendor in admin panel

### Step 3: Create a Vendor

1. Login to admin: `/adminexplorer.php`
   - Email: `admin@csnexplore.com`
   - Password: `admin123`

2. Click "Vendors" in sidebar

3. Click "Add Vendor"

4. Fill in:
   - Name: `Test Vendor`
   - Username: `testvendor`
   - Password: `test123`
   - Status: `Active`

5. Click "Create Vendor"

### Step 4: Test Login Again

Go to: `/vendorlogin`
- Username: `testvendor`
- Password: `test123`
- Click "Sign In"

Should redirect to vendor dashboard!

---

## 🔍 Additional Debug Tools

### Tool 1: test-vendor-api.html
**URL:** `/test-vendor-api.html`
**Purpose:** Interactive browser-based testing
**Features:**
- Tests minimal API
- Checks database
- Tests login with custom credentials
- Shows detailed error messages

### Tool 2: test-api-direct.php
**URL:** `/test-api-direct.php`
**Purpose:** Server-side PHP testing
**Features:**
- Direct PHP include test
- File existence check
- BOM detection
- Database connection test
- PHP configuration display

### Tool 3: test-vendor-login.php
**URL:** `/test-vendor-login.php`
**Purpose:** Database and JWT testing
**Features:**
- Lists all vendors
- Tests JWT creation/verification
- Shows configuration

---

## 📋 Common Issues & Solutions

### Issue 1: "Unexpected token '<'"

**Cause:** PHP error before JSON output

**Debug:**
1. Open `/test-api-direct.php`
2. Look for error messages
3. Check "Output before error" section

**Common causes:**
- Missing vendor in database
- Database connection error
- PHP syntax error
- Missing required files

### Issue 2: Minimal API works, but vendor-auth fails

**Cause:** Issue in vendor-auth.php or dependencies

**Debug:**
1. Check PHP error logs: `logs/php_errors.log`
2. Look for lines with `[Vendor Auth]`
3. Check if all files exist:
   - `php/config.php`
   - `php/jwt.php`
   - `php/database.php`
   - `php/api/vendor-auth.php`

### Issue 3: "Invalid username or password"

**Cause:** Vendor doesn't exist or wrong credentials

**Solution:**
1. Open `/test-vendor-login.php`
2. Check if vendor exists in database
3. Verify username and status
4. Create vendor if missing

### Issue 4: Login works but redirects back

**Cause:** Token not being saved to localStorage

**Debug:**
1. Open browser console (F12)
2. Check for JavaScript errors
3. Try: `console.log(localStorage.getItem('csn_vendor_token'))`
4. Should show a token, not null

---

## 🧪 Manual Testing

### Test 1: Minimal API
```javascript
fetch('php/api/test-minimal.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({test: 'data'})
})
.then(r => r.json())
.then(console.log)
.catch(console.error);
```

Expected: `{success: true, message: "Minimal API working", ...}`

### Test 2: Vendor Login
```javascript
fetch('php/api/vendor-auth.php?action=login', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        username: 'testvendor',
        password: 'test123'
    })
})
.then(r => r.text())
.then(text => {
    console.log('Raw response:', text);
    return JSON.parse(text);
})
.then(console.log)
.catch(console.error);
```

Expected: `{token: "...", vendor: {...}}`

---

## 📝 Error Log Monitoring

Watch errors in real-time:
```bash
tail -f logs/php_errors.log
```

Look for:
```
[Vendor Auth] Request received: POST
[Vendor Auth] Action: login
[Vendor Auth] Login attempt for username: testvendor
[Vendor Auth] Vendor found: Test Vendor (Status: active)
[Vendor Auth] Login successful for: testvendor
[Vendor Auth] Token generated, sending response
```

---

## ✅ Success Checklist

- [ ] Minimal API returns JSON (test-vendor-api.html)
- [ ] Vendors table exists (test-vendor-login.php)
- [ ] At least one vendor with status "active"
- [ ] vendor-auth.php returns JSON (not HTML)
- [ ] Login returns token
- [ ] Token saved to localStorage
- [ ] Redirects to vendor dashboard
- [ ] Dashboard shows vendor name

---

## 🗑️ Cleanup After Fixing

Once vendor login works, delete these test files:
- `test-vendor-api.html`
- `test-api-direct.php`
- `test-vendor-login.php`
- `php/api/test-minimal.php`
- `VENDOR_LOGIN_DEBUG_STEPS.md` (this file)

---

## 🆘 Still Not Working?

### Last Resort Steps:

1. **Check file encoding:**
   - All PHP files should be UTF-8 without BOM
   - Use a text editor to check/fix

2. **Check file permissions:**
   ```bash
   chmod 755 php/api/
   chmod 644 php/api/*.php
   ```

3. **Clear everything:**
   ```javascript
   localStorage.clear();
   sessionStorage.clear();
   ```
   Then hard refresh: Ctrl+Shift+R

4. **Check .htaccess:**
   Make sure it's not blocking API requests

5. **Try different browser:**
   Test in incognito mode or different browser

6. **Check PHP version:**
   Requires PHP 7.4 or higher

---

**Last Updated:** April 4, 2026
**Status:** Enhanced debugging tools added
