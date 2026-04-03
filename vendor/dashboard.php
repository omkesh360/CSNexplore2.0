<?php
$vendor_page = 'dashboard';
$vendor_title = 'Dashboard | Vendor Portal';
require 'vendor-header.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-900">Welcome Back!</h2>
    <p class="text-sm text-slate-500 mt-1">Manage your rooms and cars from here</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-6">
    <div class="vendor-card p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Total Rooms</p>
                <p id="stat-rooms" class="text-3xl font-bold text-slate-900">0</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600 text-2xl">hotel</span>
            </div>
        </div>
    </div>
    <div class="vendor-card p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Available Rooms</p>
                <p id="stat-rooms-available" class="text-3xl font-bold text-green-600">0</p>
            </div>
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-green-600 text-2xl">check_circle</span>
            </div>
        </div>
    </div>
    <div class="vendor-card p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Total Cars</p>
                <p id="stat-cars" class="text-3xl font-bold text-slate-900">0</p>
            </div>
            <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-purple-600 text-2xl">directions_car</span>
            </div>
        </div>
    </div>
    <div class="vendor-card p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Available Cars</p>
                <p id="stat-cars-available" class="text-3xl font-bold text-primary">0</p>
            </div>
            <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-2xl">local_shipping</span>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
    <div class="vendor-card p-6">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600 text-2xl">hotel</span>
            </div>
            <div>
                <h3 class="font-bold text-slate-900">Room Management</h3>
                <p class="text-sm text-slate-500">Manage your room types and inventory</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="rooms.php" class="flex-1 bg-primary text-white px-4 py-2.5 rounded-lg font-bold text-sm hover:bg-primary-dark transition-all text-center">
                Manage Rooms
            </a>
        </div>
    </div>

    <div class="vendor-card p-6">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-green-600 text-2xl">directions_car</span>
            </div>
            <div>
                <h3 class="font-bold text-slate-900">Car Management</h3>
                <p class="text-sm text-slate-500">Manage your car rental fleet</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="cars.php" class="flex-1 bg-primary text-white px-4 py-2.5 rounded-lg font-bold text-sm hover:bg-primary-dark transition-all text-center">
                Manage Cars
            </a>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="vendor-card">
    <div class="p-5 border-b border-slate-100">
        <h3 class="font-bold text-slate-900">Recent Listings</h3>
    </div>
    <div id="recent-listings" class="p-5">
        <div class="text-center py-8 text-slate-400">
            <span class="material-symbols-outlined text-4xl mb-2">inventory_2</span>
            <p class="text-sm">Loading recent listings...</p>
        </div>
    </div>
</div>

<script>
async function loadDashboardStats() {
    // Load rooms stats
    const roomsData = await vendorApi('<?php echo VENDOR_API_BASE; ?>/php/api/vendor-rooms.php?action=stats');
    if (roomsData) {
        document.getElementById('stat-rooms').textContent = roomsData.total || 0;
        document.getElementById('stat-rooms-available').textContent = roomsData.available || 0;
    }

    // Load cars stats
    const carsData = await vendorApi('<?php echo VENDOR_API_BASE; ?>/php/api/vendor-cars.php?action=stats');
    if (carsData) {
        document.getElementById('stat-cars').textContent = carsData.total || 0;
        document.getElementById('stat-cars-available').textContent = carsData.available || 0;
    }

    // Load recent listings
    loadRecentListings();
}

async function loadRecentListings() {
    const container = document.getElementById('recent-listings');
    
    // Fetch recent rooms and cars
    const roomsData = await vendorApi('<?php echo VENDOR_API_BASE; ?>/php/api/vendor-rooms.php?action=list&limit=3');
    const carsData = await vendorApi('<?php echo VENDOR_API_BASE; ?>/php/api/vendor-cars.php?action=list&limit=3');
    
    const rooms = roomsData?.rooms || [];
    const cars = carsData?.cars || [];
    
    if (rooms.length === 0 && cars.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-slate-400">
                <span class="material-symbols-outlined text-4xl mb-2">inventory_2</span>
                <p class="text-sm">No listings yet. Start by adding rooms or cars!</p>
            </div>
        `;
        return;
    }

    let html = '<div class="space-y-3">';
    
    rooms.forEach(room => {
        html += `
            <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-lg">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600">hotel</span>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-slate-900 text-sm">${room.name || 'Room Type'}</p>
                    <p class="text-xs text-slate-500">₹${room.base_price || 0}/night</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold ${room.is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'}">
                    ${room.is_active ? 'Active' : 'Inactive'}
                </span>
            </div>
        `;
    });
    
    cars.forEach(car => {
        html += `
            <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-lg">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600">directions_car</span>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-slate-900 text-sm">${car.name || 'Car'}</p>
                    <p class="text-xs text-slate-500">₹${car.price_per_day || 0}/day</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold ${car.is_available ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'}">
                    ${car.is_available ? 'Available' : 'Unavailable'}
                </span>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

// Load on page load
loadDashboardStats();
</script>

<?php require 'vendor-footer.php'; ?>
