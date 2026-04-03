<?php
$vendor_page  = 'dashboard';
$vendor_title = 'Dashboard | Vendor Portal';
require 'vendor-header.php';
?>

<div id="dash-loading" class="flex items-center justify-center h-64 text-slate-400">
    <div class="text-center">
        <span class="material-symbols-outlined text-5xl animate-spin">progress_activity</span>
        <p class="mt-3 text-sm font-medium">Loading your dashboard…</p>
    </div>
</div>
<div id="dash-content" class="hidden">

<!-- Welcome row -->
<div class="flex items-start justify-between mb-6 gap-4 flex-wrap">
    <div>
        <h2 class="text-2xl font-black text-slate-900" id="greeting-text">Good morning!</h2>
        <p class="text-sm text-slate-500 mt-0.5">Here's what's happening with your listings today.</p>
    </div>
    <div class="flex gap-2">
        <a href="stays.php" class="flex items-center gap-1.5 bg-primary text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-orange-600 transition-all shadow-sm shadow-primary/30">
            <span class="material-symbols-outlined text-base">add</span> Add Stay
        </a>
        <a href="cars.php" class="flex items-center gap-1.5 bg-slate-800 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-slate-700 transition-all">
            <span class="material-symbols-outlined text-base">add</span> Add Car
        </a>
    </div>
</div>

<!-- KPI cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="vendor-card p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-blue-500 text-2xl">apartment</span>
        </div>
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Stays</p>
            <p class="text-2xl font-black text-slate-900" id="k-stays">–</p>
            <p class="text-[10px] text-green-600 font-bold" id="k-stays-sub">– active</p>
        </div>
    </div>
    <div class="vendor-card p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-purple-500 text-2xl">bed</span>
        </div>
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Rooms</p>
            <p class="text-2xl font-black text-slate-900" id="k-rooms">–</p>
            <p class="text-[10px] text-green-600 font-bold" id="k-rooms-sub">– available</p>
        </div>
    </div>
    <div class="vendor-card p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-primary text-2xl">directions_car</span>
        </div>
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Cars</p>
            <p class="text-2xl font-black text-slate-900" id="k-cars">–</p>
            <p class="text-[10px] text-green-600 font-bold" id="k-cars-sub">– available</p>
        </div>
    </div>
    <div class="vendor-card p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-green-50 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-green-500 text-2xl">calendar_today</span>
        </div>
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Bookings</p>
            <p class="text-2xl font-black text-slate-900" id="k-bookings">–</p>
            <p class="text-[10px] text-slate-400 font-bold">on your stays</p>
        </div>
    </div>
</div>

<!-- Quick Actions + Recent Listings -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

    <!-- Quick actions -->
    <div class="vendor-card p-5">
        <p class="text-sm font-bold text-slate-700 mb-4">Quick Actions</p>
        <div class="space-y-2">
            <a href="stays.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-all group">
                <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-blue-100 transition-all">
                    <span class="material-symbols-outlined text-blue-600 text-lg">apartment</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-800">Manage Hotel Stays</p>
                    <p class="text-[10px] text-slate-400">Add / edit your property listings</p>
                </div>
                <span class="material-symbols-outlined text-slate-300 text-lg">chevron_right</span>
            </a>
            <a href="rooms.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-all group">
                <div class="w-9 h-9 bg-purple-50 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-purple-100 transition-all">
                    <span class="material-symbols-outlined text-purple-600 text-lg">bed</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-800">Manage Rooms</p>
                    <p class="text-[10px] text-slate-400">Room types and individual rooms</p>
                </div>
                <span class="material-symbols-outlined text-slate-300 text-lg">chevron_right</span>
            </a>
            <a href="cars.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-all group">
                <div class="w-9 h-9 bg-orange-50 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-orange-100 transition-all">
                    <span class="material-symbols-outlined text-primary text-lg">directions_car</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-800">Manage Cars</p>
                    <p class="text-[10px] text-slate-400">Car fleet and availability</p>
                </div>
                <span class="material-symbols-outlined text-slate-300 text-lg">chevron_right</span>
            </a>
            <a href="bookings.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-all group">
                <div class="w-9 h-9 bg-green-50 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-green-100 transition-all">
                    <span class="material-symbols-outlined text-green-600 text-lg">calendar_today</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-800">View Bookings</p>
                    <p class="text-[10px] text-slate-400">See all customer bookings</p>
                </div>
                <span class="material-symbols-outlined text-slate-300 text-lg">chevron_right</span>
            </a>
        </div>
    </div>

    <!-- Recent stays -->
    <div class="vendor-card lg:col-span-2">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <p class="text-sm font-bold text-slate-700">Recent Stay Listings</p>
            <a href="stays.php" class="text-[11px] font-bold text-primary hover:underline">View all</a>
        </div>
        <div id="recent-stays" class="p-4">
            <div class="flex items-center gap-3 py-6 justify-center text-slate-400">
                <span class="material-symbols-outlined animate-spin">progress_activity</span>
                <span class="text-sm">Loading…</span>
            </div>
        </div>
    </div>
</div>

<!-- Recent Cars -->
<div class="vendor-card mb-5">
    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
        <p class="text-sm font-bold text-slate-700">Recent Cars</p>
        <a href="cars.php" class="text-[11px] font-bold text-primary hover:underline">View all</a>
    </div>
    <div id="recent-cars" class="p-4">
        <div class="flex items-center gap-3 py-6 justify-center text-slate-400">
            <span class="material-symbols-outlined animate-spin">progress_activity</span>
        </div>
    </div>
</div>

<!-- Recent Bookings -->
<div class="vendor-card">
    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
        <p class="text-sm font-bold text-slate-700">Recent Bookings</p>
        <a href="bookings.php" class="text-[11px] font-bold text-primary hover:underline">View all</a>
    </div>
    <div id="recent-bookings" class="p-4">
        <div class="flex items-center gap-3 py-6 justify-center text-slate-400">
            <span class="material-symbols-outlined animate-spin">progress_activity</span>
        </div>
    </div>
</div>

</div><!-- end #dash-content -->

<script>
// Greeting
(function(){
    var h = new Date().getHours();
    var name = (window._vendorUser?.name || '').split(' ')[0] || 'there';
    var g = h < 12 ? 'Good morning' : h < 17 ? 'Good afternoon' : 'Good evening';
    document.getElementById('greeting-text').textContent = g + ', ' + name + '!';
})();

const BASE = '<?php echo VENDOR_API_BASE; ?>';

async function loadDashboard() {
    // Load all summary in one call
    const s = await vendorApi(`${BASE}/php/api/vendor-profile.php?action=summary`);
    document.getElementById('dash-loading').classList.add('hidden');
    document.getElementById('dash-content').classList.remove('hidden');

    if (s) {
        document.getElementById('k-stays').textContent    = s.stays;
        document.getElementById('k-stays-sub').textContent = s.stays_avail + ' active';
        document.getElementById('k-rooms').textContent    = s.rooms;
        document.getElementById('k-rooms-sub').textContent = s.rooms_avail + ' available';
        document.getElementById('k-cars').textContent     = s.cars;
        document.getElementById('k-cars-sub').textContent  = s.cars_avail + ' available';
        document.getElementById('k-bookings').textContent = s.bookings;
    }

    // Load recent stays
    const stayData = await vendorApi(`${BASE}/php/api/vendor-stays.php?action=list&limit=5`);
    const stayEl = document.getElementById('recent-stays');
    if (!stayData || !stayData.stays || stayData.stays.length===0) {
        stayEl.innerHTML = `<div class="text-center py-6 text-slate-400"><span class="material-symbols-outlined text-3xl">apartment</span><p class="text-xs mt-2">No stays yet. <a href="stays.php" class="text-primary font-bold">Add your first listing</a></p></div>`;
    } else {
        stayEl.innerHTML = `<div class="divide-y divide-slate-50">${stayData.stays.map(s=>`
            <div class="flex items-center gap-3 py-3">
                <div class="w-10 h-10 rounded-lg overflow-hidden bg-slate-100 shrink-0">
                    ${s.image ? `<img src="${s.image}" class="w-full h-full object-cover"/>` : `<div class="w-full h-full flex items-center justify-center"><span class="material-symbols-outlined text-slate-300 text-xl">apartment</span></div>`}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-900 truncate">${s.name}</p>
                    <p class="text-[10px] text-slate-400 truncate">${s.location} · ${fmt(s.price_per_night)}/night</p>
                </div>
                <span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full border ${s.is_active ? 'bg-green-50 text-green-700 border-green-100' : 'bg-slate-50 text-slate-400 border-slate-100'}">${s.is_active?'Active':'Hidden'}</span>
            </div>`).join('')}</div>`;
    }

    // Recent cars
    const carData = await vendorApi(`${BASE}/php/api/vendor-cars.php?action=list&limit=5`);
    const carEl = document.getElementById('recent-cars');
    if (!carData || !carData.cars || carData.cars.length===0) {
        carEl.innerHTML = `<div class="text-center py-6 text-slate-400"><span class="material-symbols-outlined text-3xl">directions_car</span><p class="text-xs mt-2">No cars yet. <a href="cars.php" class="text-primary font-bold">Add your first car</a></p></div>`;
    } else {
        carEl.innerHTML = `<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">${carData.cars.map(c=>`
            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                <div class="w-10 h-10 rounded-lg overflow-hidden bg-slate-200 shrink-0">
                    ${c.image ? `<img src="${c.image}" class="w-full h-full object-cover"/>` : `<div class="w-full h-full flex items-center justify-center"><span class="material-symbols-outlined text-slate-300 text-lg">directions_car</span></div>`}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-900 truncate">${c.name}</p>
                    <p class="text-[10px] text-primary font-bold">${fmt(c.price_per_day)}/day</p>
                    <span class="text-[9px] font-bold ${c.is_available?'text-green-600':'text-red-500'}">${c.is_available?'Available':'Unavailable'}</span>
                </div>
            </div>`).join('')}</div>`;
    }

    // Recent bookings
    const bData = await vendorApi(`${BASE}/php/api/vendor-stays.php?action=bookings&limit=5`);
    const bEl = document.getElementById('recent-bookings');
    if (!bData || !bData.bookings || bData.bookings.length===0) {
        bEl.innerHTML = `<div class="text-center py-6 text-slate-400"><span class="material-symbols-outlined text-3xl">event_note</span><p class="text-xs mt-2">No bookings yet</p></div>`;
    } else {
        bEl.innerHTML = `<div class="overflow-x-auto"><table class="w-full text-xs"><thead><tr class="text-left text-[10px] uppercase tracking-wider text-slate-400 border-b border-slate-100">
            <th class="pb-2 pr-4">Guest</th><th class="pb-2 pr-4">Listing</th><th class="pb-2 pr-4">Date</th><th class="pb-2 pr-4">Guests</th><th class="pb-2">Status</th>
        </tr></thead><tbody class="divide-y divide-slate-50">${bData.bookings.map(b=>`
            <tr class="hover:bg-slate-50">
                <td class="py-3 pr-4 font-semibold">${b.full_name||'—'}</td>
                <td class="py-3 pr-4 text-slate-500">${b.listing_name||'—'}</td>
                <td class="py-3 pr-4 text-slate-500">${b.booking_date||'—'}</td>
                <td class="py-3 pr-4 text-slate-500">${b.number_of_people||1}</td>
                <td class="py-3"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold border ${b.status==='completed'?'bg-green-50 text-green-700 border-green-100':b.status==='cancelled'?'bg-red-50 text-red-700 border-red-100':'bg-yellow-50 text-yellow-700 border-yellow-100'}">${b.status}</span></td>
            </tr>`).join('')}
        </tbody></table></div>`;
    }
}

loadDashboard();
</script>

<?php require 'vendor-footer.php'; ?>
