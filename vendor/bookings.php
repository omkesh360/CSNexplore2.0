<?php
$vendor_page  = 'bookings';
$vendor_title = 'Bookings | Vendor Portal';
require 'vendor-header.php';
?>

<div class="mb-5 flex items-start justify-between gap-4 flex-wrap">
    <div>
        <h2 class="text-xl font-black text-slate-900">Bookings</h2>
        <p class="text-xs text-slate-500 mt-0.5">All customer bookings on your stay listings</p>
    </div>
    <div id="booking-summary" class="flex gap-3">
        <div class="vendor-card px-4 py-2 text-center">
            <p class="text-lg font-black text-slate-900" id="bs-total">–</p>
            <p class="text-[10px] text-slate-400 font-bold">TOTAL</p>
        </div>
        <div class="vendor-card px-4 py-2 text-center">
            <p class="text-lg font-black text-yellow-600" id="bs-pending">–</p>
            <p class="text-[10px] text-slate-400 font-bold">PENDING</p>
        </div>
        <div class="vendor-card px-4 py-2 text-center">
            <p class="text-lg font-black text-green-600" id="bs-completed">–</p>
            <p class="text-[10px] text-slate-400 font-bold">COMPLETED</p>
        </div>
    </div>
</div>

<!-- Filter bar -->
<div class="vendor-card p-3 mb-4 flex flex-wrap gap-3 items-center">
    <div class="relative flex-1 min-w-44">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-lg">search</span>
        <input id="b-search" type="text" placeholder="Search guest name or listing…" oninput="filterBookings()"
               class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
    </div>
    <select id="b-status" onchange="filterBookings()" class="px-3 py-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
    </select>
</div>

<!-- Bookings table -->
<div class="vendor-card overflow-hidden">
    <div id="bookings-loading" class="p-10 text-center text-slate-400">
        <span class="material-symbols-outlined text-4xl animate-spin">progress_activity</span>
        <p class="text-sm mt-3">Loading bookings…</p>
    </div>
    <div id="bookings-empty" class="hidden p-12 text-center text-slate-400">
        <span class="material-symbols-outlined text-5xl mb-3">event_busy</span>
        <p class="font-bold text-slate-600">No bookings found</p>
        <p class="text-sm mt-1">Bookings on your stay listings will appear here</p>
    </div>
    <div id="bookings-table" class="hidden overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr class="text-[10px] uppercase tracking-wider text-slate-500 text-left">
                    <th class="px-5 py-3">Guest</th>
                    <th class="px-4 py-3">Contact</th>
                    <th class="px-4 py-3">Listing</th>
                    <th class="px-4 py-3">Check-in / Check-out</th>
                    <th class="px-4 py-3">Guests</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Booked On</th>
                </tr>
            </thead>
            <tbody id="bookings-tbody" class="divide-y divide-slate-50"></tbody>
        </table>
    </div>
</div>

<script>
const BASE = '<?php echo VENDOR_API_BASE; ?>';
let allBookings = [];

async function loadBookings() {
    const d = await vendorApi(`${BASE}/php/api/vendor-stays.php?action=bookings&limit=200`);
    document.getElementById('bookings-loading').classList.add('hidden');

    if (!d) { document.getElementById('bookings-empty').classList.remove('hidden'); return; }
    allBookings = d.bookings || [];

    // Summary
    document.getElementById('bs-total').textContent = d.total || 0;
    const pending   = allBookings.filter(b=>b.status==='pending').length;
    const completed = allBookings.filter(b=>b.status==='completed').length;
    document.getElementById('bs-pending').textContent   = pending;
    document.getElementById('bs-completed').textContent = completed;

    renderBookings(allBookings);
}

function filterBookings() {
    const q  = document.getElementById('b-search').value.toLowerCase();
    const st = document.getElementById('b-status').value;
    renderBookings(allBookings.filter(b =>
        (!q  || b.full_name?.toLowerCase().includes(q) || b.listing_name?.toLowerCase().includes(q)) &&
        (!st || b.status === st)
    ));
}

function renderBookings(list) {
    const empty = document.getElementById('bookings-empty');
    const table = document.getElementById('bookings-table');
    const tbody = document.getElementById('bookings-tbody');

    if (list.length === 0) { empty.classList.remove('hidden'); table.classList.add('hidden'); return; }
    empty.classList.add('hidden');
    table.classList.remove('hidden');

    const statusClass = (s) => s==='completed'
        ? 'bg-green-50 text-green-700 border-green-100'
        : s==='cancelled'
        ? 'bg-red-50 text-red-700 border-red-100'
        : 'bg-yellow-50 text-yellow-700 border-yellow-100';

    tbody.innerHTML = list.map(b=>`
        <tr class="hover:bg-slate-50 transition-colors">
            <td class="px-5 py-3.5">
                <p class="font-bold text-slate-900">${b.full_name||'—'}</p>
            </td>
            <td class="px-4 py-3.5">
                <p class="text-slate-600">${b.phone||'—'}</p>
                <p class="text-slate-400">${b.email||''}</p>
            </td>
            <td class="px-4 py-3.5">
                <p class="font-medium text-slate-700">${b.listing_name||'—'}</p>
                <p class="text-slate-400 capitalize">${b.service_type||''}</p>
            </td>
            <td class="px-4 py-3.5">
                ${b.checkin_date ? `<p>${b.checkin_date} → ${b.checkout_date||'?'}</p>` : `<p class="text-slate-400">${b.booking_date||'—'}</p>`}
            </td>
            <td class="px-4 py-3.5 text-center font-bold text-slate-700">${b.number_of_people||1}</td>
            <td class="px-4 py-3.5">
                <span class="px-2 py-1 rounded-full text-[10px] font-bold border ${statusClass(b.status)} capitalize">${b.status}</span>
            </td>
            <td class="px-4 py-3.5 text-slate-400">${(b.created_at||'').substring(0,10)}</td>
        </tr>`).join('');
}

loadBookings();
</script>

<?php require 'vendor-footer.php'; ?>
