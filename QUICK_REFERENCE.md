# 🚀 Multi-Vendor System - Quick Reference Card

## 📍 Access Points

| What | URL | Who |
|------|-----|-----|
| Vendor Login | `/vendorlogin` | Vendors |
| Vendor Dashboard | `/vendor/dashboard.php` | Vendors |
| Admin Vendors | `/admin/vendors.php` | Admin |
| Admin Panel | `/adminexplorer.php` | Admin |

## 🔑 Default Credentials

**Admin:**
- Email: `admin@csnexplore.com`
- Password: `admin123`

**Vendor:** Created by admin (no default)

## ⚡ Quick Actions

### Create Vendor (Admin)
1. Login → Vendors → Add Vendor
2. Fill: Name, Username, Password
3. Set Status: Active
4. Click Create

### Vendor Login
1. Go to `/vendorlogin`
2. Enter username/password
3. Access dashboard

## 🗂️ File Locations

```
/vendor/              - Vendor portal
/admin/vendors.php    - Admin vendor management
/php/api/vendor-*.php - Vendor APIs
/database/            - Migration SQL
```

## 🔧 API Quick Test

```javascript
// In browser console after vendor login
const token = localStorage.getItem('csn_vendor_token');

// Test stats
fetch('../php/api/vendor-rooms.php?action=stats', {
    headers: { 'Authorization': 'Bearer ' + token }
}).then(r => r.json()).then(console.log);
```

## 📊 Database Tables

- `vendors` - Vendor accounts
- `room_types` - Room categories
- `rooms` - Room inventory
- `stays.vendor_id` - Links stays
- `cars.vendor_id` - Links cars

## ✅ Verification Steps

1. ✅ Login to admin
2. ✅ See "Vendors" in menu
3. ✅ Create test vendor
4. ✅ Login as vendor at `/vendorlogin`
5. ✅ See dashboard with stats

## 🐛 Common Issues

**Login fails:** Check vendor status is "active"
**Menu missing:** Clear cache, verify admin role
**API 401:** Check token, verify active status
**DB error:** Run migration SQL manually

## 📚 Documentation

- `IMPLEMENTATION_COMPLETE.md` - Start here
- `VENDOR_INSTALLATION.md` - Setup guide
- `VENDOR_SYSTEM_README.md` - Full docs
- `VENDOR_SYSTEM_SUMMARY.md` - Overview

## 🎯 What Works Now

✅ Vendor authentication
✅ Admin vendor management
✅ Vendor dashboard
✅ Room/Car APIs
✅ Data isolation
✅ Security

## 🔜 To Build Next

⏳ `/vendor/rooms.php` - Room UI
⏳ `/vendor/cars.php` - Car UI
⏳ Image upload system
⏳ Booking integration

## 💡 Pro Tips

- Vendors can't register (admin creates accounts)
- Admin can manage all listings
- Vendors see only their data
- Tokens expire after 7 days
- Use HTTPS in production

---

**Need Help?** Check `IMPLEMENTATION_COMPLETE.md` for full guide
