<?php
$admin_page  = 'bookings';
$admin_title = 'Bookings | CSNExplore Admin';
require 'admin-header.php';
?>

<div class="space-y-5">
<!-- Filters -->
<div class="flex flex-wrap gap-3 items-center">
    <div class="flex gap-1 bg-white border border-slate-200 p-1 rounded-xl">
        <?php foreach (['all'=>'All','pending'=>'Pending','completed'=>'Completed','cancelled'=>'Cancelled'] as $k=>$v): ?>
        <button onclick="filterStatus('<?php echo $k; ?>')" data-status="<?php echo $k; ?>"
                class="status-tab px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-500 hover:text-slate-900 transition-all">
            <?php echo $v; ?>
        </button>
        <?php endforeach; ?>
    </div>
    <input id="search-input" type="text" placeholder="Search name, phone, email..."
           class="flex-1 min-w-[200px] border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">#</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Customer</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Phone</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Listing / Service</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Category</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Date</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">People</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Status</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Actions</th>
                </tr>
            </thead>
            <tbody id="bookings-tbody">
                <tr><td colspan="9" class="text-center py-12 text-slate-400">Loading...</td></tr>
            </tbody>
        </table>
    </div>
</div>
</div>

<!-- Detail Modal -->
<div id="booking-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 class="text-base font-bold">Booking Details</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div id="modal-body" class="p-6 space-y-4"></div>
        <div class="p-6 pt-0 flex gap-3">
            <button onclick="updateStatus('completed')" class="flex-1 bg-green-500 text-white py-2.5 rounded-xl text-sm font-bold hover:bg-green-600 transition-all">Mark Completed</button>
            <button onclick="updateStatus('cancelled')" class="flex-1 bg-red-500 text-white py-2.5 rounded-xl text-sm font-bold hover:bg-red-600 transition-all">Cancel</button>
            <button onclick="updateStatus('pending')" class="flex-1 border border-slate-200 text-slate-600 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-50 transition-all">Pending</button>
        </div>
    </div>
</div>

<?php
$extra_js = <<<'JS'
<script>
var currentStatus = 'all';
var currentBookingId = null;

function escHtml(s) {
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function filterStatus(s) {
    currentStatus = s;
    document.querySelectorAll('.status-tab').forEach(function(b){
        var active = b.dataset.status === s;
        b.classList.toggle('bg-primary', active);
        b.classList.toggle('text-white', active);
        b.classList.toggle('text-slate-500', !active);
    });
    loadBookings();
}

async function loadBookings() {
    var search = document.getElementById('search-input').value;
    var url = '../php/api/bookings.php?';
    if (currentStatus !== 'all') url += 'status=' + currentStatus + '&';
    if (search) url += 'search=' + encodeURIComponent(search);
    var tbody = document.getElementById('bookings-tbody');
    tbody.innerHTML = '<tr><td colspan="9" class="text-center py-12 text-slate-400">Loading...</td></tr>';
    var items = await api(url);
    if (!items || !items.length) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-12 text-slate-400">No bookings found</td></tr>';
        return;
    }
    // Cache all loaded bookings for fast modal access
    items.forEach(function(b){ bookingsCache[b.id] = b; });
    tbody.innerHTML = items.map(function(b) {
        var sc = b.status === 'pending' ? 'bg-orange-100 text-orange-700' : b.status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
        var catLabels = {stays:'Stay', cars:'Car', bikes:'Bike', restaurants:'Restaurant', attractions:'Attraction', buses:'Bus'};
        var catLabel = catLabels[b.service_type] || b.service_type || '—';
        return '<tr class="border-b border-slate-50 hover:bg-slate-50 cursor-pointer" onclick="openModal(' + b.id + ')">' +
            '<td class="py-2.5 px-4 text-slate-400 text-xs">#' + b.id + '</td>' +
            '<td class="py-2.5 px-4 font-medium">' + escHtml(b.full_name) + '</td>' +
            '<td class="py-2.5 px-4"><a href="tel:' + escHtml(b.phone) + '" onclick="event.stopPropagation()" class="text-primary hover:underline">' + escHtml(b.phone) + '</a></td>' +
            '<td class="py-2.5 px-4 text-slate-500 max-w-[140px] truncate">' + escHtml(b.listing_name || b.service_type || '—') + '</td>' +
            '<td class="py-2.5 px-4"><span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">' + escHtml(catLabel) + '</span></td>' +
            '<td class="py-2.5 px-4 text-slate-500">' + escHtml(b.booking_date || b.created_at || '—') + '</td>' +
            '<td class="py-2.5 px-4 text-slate-500">' + (b.number_of_people || 1) + '</td>' +
            '<td class="py-2.5 px-4"><span class="px-2 py-0.5 rounded-full text-xs font-bold ' + sc + '">' + escHtml(b.status) + '</span></td>' +
            '<td class="py-2.5 px-4">' +
                '<button onclick="event.stopPropagation(); deleteBooking(' + b.id + ')" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all"><span class="material-symbols-outlined text-base">delete</span></button>' +
            '</td>' +
        '</tr>';
    }).join('');
}

var bookingsCache = {};
async function openModal(id) {
    currentBookingId = id;
    // Use cached data if available, otherwise fetch single booking
    var b = bookingsCache[id];
    if (!b) {
        var all = await api('../php/api/bookings.php');
        (all || []).forEach(function(x){ bookingsCache[x.id] = x; });
        b = bookingsCache[id];
    }
    if (!b) return;
    var sc = b.status === 'pending' ? 'bg-orange-100 text-orange-700' : b.status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';

    // Build category label from service_type
    var catLabels = {stays:'Hotel Stay', cars:'Car Rental', bikes:'Bike Rental', restaurants:'Restaurant', attractions:'Attraction', buses:'Bus'};
    var catLabel = catLabels[b.service_type] || b.service_type || '—';

    document.getElementById('modal-body').innerHTML =
        '<div class="grid grid-cols-2 gap-3 text-sm">' +
        row('Booking #', '#' + b.id) +
        row('Status', '<span class="px-2 py-0.5 rounded-full text-xs font-bold ' + sc + '">' + escHtml(b.status) + '</span>') +
        row('Customer Name', b.full_name) +
        row('Phone', '<a href="tel:' + escHtml(b.phone) + '" class="text-primary hover:underline">' + escHtml(b.phone) + '</a>') +
        row('Email', b.email || '—') +
        row('Category', catLabel) +
        row('Listing / Service', b.listing_name || b.service_type || '—') +
        row('Listing ID', b.listing_id ? '#' + b.listing_id : '—') +
        row('Booking Date', b.booking_date || b.created_at || '—') +
        row('No. of People', b.number_of_people || 1) +
        row('Booked On', b.created_at || '—') +
        row('Last Updated', b.updated_at || '—') +
        '</div>' +
        (b.notes ? '<div class="mt-3 p-3 bg-slate-50 rounded-xl text-sm text-slate-600"><span class="font-semibold">Notes:</span> ' + escHtml(b.notes) + '</div>' : '');
    document.getElementById('booking-modal').classList.remove('hidden');
}

function row(label, val) {
    return '<div><p class="text-xs text-slate-400 font-medium">' + label + '</p><p class="font-semibold mt-0.5">' + val + '</p></div>';
}

function closeModal() {
    document.getElementById('booking-modal').classList.add('hidden');
}

async function updateStatus(status) {
    if (!currentBookingId) return;
    await api('../php/api/bookings.php?id=' + currentBookingId, {
        method: 'PUT', body: JSON.stringify({ status: status })
    });
    closeModal();
    loadBookings();
}

async function deleteBooking(id) {
    if (!confirm('Delete this booking?')) return;
    await api('../php/api/bookings.php?id=' + id, { method: 'DELETE' });
    loadBookings();
}

document.getElementById('search-input').addEventListener('input', function(){
    clearTimeout(window._st);
    window._st = setTimeout(loadBookings, 400);
});

filterStatus('all');
</script>
JS;
require 'admin-footer.php';
?>
