# Vendor Portal - Quick Start Guide

**Last Updated:** April 4, 2026

---

## 🚀 Getting Started in 5 Minutes

### Step 1: Admin Creates Vendor Account

1. Go to: `http://yoursite.com/adminexplorer.php`
2. Login with admin credentials
3. Click **"Vendors"** in the sidebar
4. Click **"Add Vendor"** button
5. Fill in the form:
   ```
   Name: John's Hotels
   Username: johnshotels
   Password: SecurePass123
   Email: john@example.com
   Phone: +91-9876543210
   Business Name: John's Premium Hotels
   Status: Active
   ```
6. Click **"Create Vendor"**

✅ Vendor account created!

---

### Step 2: Vendor Logs In

1. Go to: `http://yoursite.com/vendor/vendorlogin.php`
2. Enter username: `johnshotels`
3. Enter password: `SecurePass123`
4. Click **"Sign In"**

✅ Logged in! You're now on the dashboard.

---

### Step 3: Add Your First Listing

#### Option A: Add a Hotel/Stay Listing

1. Click **"Hotel & Stays"** in the sidebar
2. Click **"Add Stay Listing"** button
3. Fill in the form:
   ```
   Property Name: The Royal Heritage Hotel
   Type: Hotel
   Location: Chhatrapati Sambhajinagar
   Price per Night: ₹2,500
   Max Guests: 4
   Description: Luxury 5-star hotel with modern amenities
   Amenities: Free WiFi, Breakfast, Parking, AC, Swimming Pool
   Main Image URL: https://images.unsplash.com/...
   Badge: Popular
   Active: ✓ (checked)
   ```
4. Click **"Create Listing"**

✅ Stay listing created!

#### Option B: Add a Car Rental

1. Click **"Cars"** in the sidebar
2. Click **"Add Car"** button
3. Fill in the form:
   ```
   Car Name: Toyota Innova
   Car Type: MUV
   Location: Chhatrapati Sambhajinagar
   Price per Day: ₹1,500
   Fuel Type: Diesel
   Transmission: Manual
   Seats: 7
   Description: Spacious family car, perfect for group travel
   Image URL: https://images.unsplash.com/...
   Features: AC, GPS, Bluetooth, Child Seat
   Available: ✓ (checked)
   ```
4. Click **"Add Car"**

✅ Car listing created!

---

## 📊 Dashboard Overview

When you login, you see:

- **Rooms:** Total rooms you've created
- **Cars:** Total cars in your fleet
- **Stays:** Total hotel/stay listings
- **Bookings:** Total bookings on your listings

Plus:
- Recent listings display
- Quick action buttons
- View site link
- Profile access

---

## 🏨 Managing Hotel Listings

### Create a Stay Listing
1. Go to **"Hotel & Stays"**
2. Click **"Add Stay Listing"**
3. Fill in all details
4. Click **"Create Listing"**

### Edit a Listing
1. Go to **"Hotel & Stays"**
2. Click the **edit icon** (pencil) on any listing
3. Update the details
4. Click **"Update Listing"**

### Hide/Show a Listing
1. Go to **"Hotel & Stays"**
2. Click **"Hide"** or **"Activate"** button on any listing
3. Listing visibility toggles instantly

### Delete a Listing
1. Go to **"Hotel & Stays"**
2. Click the **delete icon** (trash) on any listing
3. Confirm deletion
4. Listing is removed

### Search Listings
1. Go to **"Hotel & Stays"**
2. Type in the search box to find by name or location
3. Use filters to show only Active or Hidden listings
4. Filter by property type (Hotel, Resort, etc.)

---

## 🛏️ Managing Rooms

### Create a Room Type
1. Go to **"Rooms"**
2. Click **"Add Room Type"**
3. Fill in:
   ```
   Room Type Name: Deluxe Room
   Description: Spacious room with city view
   Base Price: ₹2,000/night
   Max Guests: 2
   Amenities: AC, WiFi, TV, Mini Fridge
   Active: ✓
   ```
4. Click **"Create Room Type"**

### Add Individual Rooms
1. Go to **"Rooms"**
2. Find your room type
3. Click the **"+"** button to add a room
4. Fill in:
   ```
   Room Number: 101
   Floor: 1st Floor
   Price Override: 0 (uses type price)
   Status: Available
   Available for booking: ✓
   ```
5. Click **"Add Room"**

### Edit a Room
1. Go to **"Rooms"**
2. Find the room under its type
3. Click the **edit icon** on the room
4. Update details
5. Click **"Update Room"**

### Delete a Room
1. Go to **"Rooms"**
2. Find the room
3. Click the **delete icon**
4. Confirm deletion

---

## 🚗 Managing Cars

### Add a Car
1. Go to **"Cars"**
2. Click **"Add Car"**
3. Fill in all details
4. Click **"Add Car"**

### Edit a Car
1. Go to **"Cars"**
2. Click the **edit icon** on any car
3. Update details
4. Click **"Update Car"**

### Toggle Availability
1. Go to **"Cars"**
2. Click **"Mark Unavailable"** or **"Mark Available"**
3. Availability updates instantly

### Delete a Car
1. Go to **"Cars"**
2. Click the **delete icon**
3. Confirm deletion

---

## 📅 Viewing Bookings

1. Go to **"Bookings"** in the sidebar
2. See all bookings on your listings
3. View summary: Total, Pending, Completed
4. Search by guest name or listing name
5. Filter by status: Pending, Completed, Cancelled

**Booking Details Include:**
- Guest name and contact info
- Listing name
- Check-in and check-out dates
- Number of guests
- Booking status
- Date booked

---

## 👤 Managing Your Profile

### View Profile
1. Click your avatar in the top-right corner
2. Or go to **"Profile & Security"** in sidebar

### Edit Profile
1. Go to **"Profile & Security"**
2. Update your information:
   - Full Name
   - Business Name
   - Email
   - Phone
3. Click **"Save Changes"**

### Change Password
1. Go to **"Profile & Security"**
2. Enter current password
3. Enter new password (min 8 characters)
4. Confirm new password
5. Click **"Update Password"**
6. You'll be logged out and need to login again

### Sign Out
1. Click **"Sign Out"** button in profile section
2. Or click the logout button in sidebar
3. You'll be redirected to login page

---

## 🔍 Search & Filter

### Search Listings
- Type in the search box
- Searches by name and location
- Results update in real-time

### Filter by Status
- **All Status:** Show all listings
- **Active:** Show only visible listings
- **Hidden:** Show only hidden listings

### Filter by Type
- **All Types:** Show all types
- **Hotel, Resort, Hostel, etc.:** Show specific type

---

## 💡 Tips & Tricks

### Image URLs
- Use high-quality images (at least 800x600px)
- Recommended sources:
  - Unsplash: `https://unsplash.com`
  - Pexels: `https://pexels.com`
  - Your own server

### Pricing Strategy
- Set competitive prices
- Consider location and amenities
- Update prices seasonally
- Monitor competitor prices

### Amenities
- List all major amenities
- Use comma-separated format
- Be specific (e.g., "Free WiFi" not just "WiFi")
- Include unique features

### Descriptions
- Write clear, engaging descriptions
- Highlight unique features
- Mention nearby attractions
- Include house rules if applicable

### Bookings
- Check bookings regularly
- Respond to inquiries promptly
- Keep availability updated
- Manage cancellations professionally

---

## ⚠️ Common Issues & Solutions

### "Invalid credentials" on login
- Check username is correct
- Check password is correct
- Ask admin to verify account is active
- Try resetting password

### Dashboard not loading
- Refresh the page (F5)
- Clear browser cache
- Check internet connection
- Try a different browser

### Can't create a listing
- Fill in all required fields (marked with *)
- Check image URL is valid
- Verify price is a number
- Check for error messages

### Listing not showing on website
- Make sure listing is marked "Active"
- Check if it's in the correct category
- Wait a few minutes for cache to clear
- Contact admin if still not visible

### Can't delete a room type
- Delete all individual rooms first
- Then delete the room type

---

## 🔐 Security Tips

✅ **Do:**
- Use a strong password (mix of letters, numbers, symbols)
- Change password regularly
- Keep your login credentials private
- Logout when done
- Use HTTPS (secure connection)

❌ **Don't:**
- Share your password with anyone
- Use the same password as other accounts
- Leave your account logged in on shared computers
- Click suspicious links
- Download files from untrusted sources

---

## 📞 Need Help?

### Check These First:
1. Browser console (F12) for errors
2. Network tab to see API responses
3. PHP error logs on server
4. Database to verify data

### Contact Admin:
- Email: admin@csnexplore.com
- Phone: +91-XXXXXXXXXX
- Support: support@csnexplore.com

---

## 🎯 Next Steps

1. ✅ Create your vendor account
2. ✅ Login to the portal
3. ✅ Add your first listing
4. ✅ Add rooms or cars
5. ✅ Update your profile
6. ✅ Monitor bookings
7. ✅ Manage availability

---

## 📚 Full Documentation

For detailed technical information, see:
- `VENDOR_PORTAL_FIXES.md` - Complete system documentation
- `VENDOR_SYSTEM_README.md` - Technical details
- `TROUBLESHOOTING.md` - Troubleshooting guide

---

**Happy listing! 🎉**

**Last Updated:** April 4, 2026  
**Version:** 1.0.0
