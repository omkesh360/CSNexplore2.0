<?php
$admin_page  = 'dashboard';
$admin_title = 'Dashboard | CSNExplore Admin';
require 'admin-header.php';
?>

<div class="space-y-6">
<!-- Stats grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4" id="stats-grid">
    <?php
    $cards = [
        ['id'=>'stat-bookings', 'icon'=>'book_online',  'label'=>'Total Bookings',  'color'=>'bg-blue-50 text-blue-600'],
        ['id'=>'stat-pending',  'icon'=>'pending',       'label'=>'Pending',         'color'=>'bg-orange-50 text-primary'],
        ['id'=>'stat-users',    'icon'=>'group',         'label'=>'Users',           'color'=>'bg-green-50 text-green-600'],
        ['id'=>'stat-blogs',    'icon'=>'article',       'label'=>'Published Blogs', 'color'=>'bg-purple-50 text-purple-600'],
    ];
    foreach ($cards as $card): ?>
    <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 <?php echo $card['color']; ?> rounded-xl flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-2xl"><?php echo $card['icon']; ?></span>
        </div>
        <div>
            <p id="<?php echo $card['id']; ?>" class="text-2xl font-black text-slate-900">—</p>
            <p class="text-xs text-slate-500 font-medium"><?php echo $card['label']; ?></p>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Listings overview -->
<div class="bg-white rounded-2xl border border-slate-100 p-6">
    <h2 class="text-base font-bold mb-4">Active Listings</h2>
    <div class="grid grid-cols-3 md:grid-cols-6 gap-3" id="listings-grid">
        <?php
        $cats = [
            ['key'=>'stays',       'icon'=>'bed',                'label'=>'Stays'],
            ['key'=>'cars',        'icon'=>'directions_car',     'label'=>'Cars'],
            ['key'=>'bikes',       'icon'=>'motorcycle',         'label'=>'Bikes'],
            ['key'=>'restaurants', 'icon'=>'restaurant',         'label'=>'Restaurants'],
            ['key'=>'attractions', 'icon'=>'confirmation_number','label'=>'Attractions'],
            ['key'=>'buses',       'icon'=>'directions_bus',     'label'=>'Buses'],
        ];
        foreach ($cats as $cat): ?>
        <div class="text-center p-4 bg-slate-50 rounded-xl">
            <span class="material-symbols-outlined text-primary text-2xl"><?php echo $cat['icon']; ?></span>
            <p id="stat-<?php echo $cat['key']; ?>" class="text-xl font-black text-slate-900 mt-1">—</p>
            <p class="text-xs text-slate-500"><?php echo $cat['label']; ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Recent bookings -->
<div class="bg-white rounded-2xl border border-slate-100 p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold">Recent Bookings</h2>
        <a href="bookings.php" class="text-xs text-primary font-semibold hover:underline">View all →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="text-left py-2 px-3 text-xs font-semibold text-slate-500">Name</th>
                    <th class="text-left py-2 px-3 text-xs font-semibold text-slate-500">Phone</th>
                    <th class="text-left py-2 px-3 text-xs font-semibold text-slate-500">Service</th>
                    <th class="text-left py-2 px-3 text-xs font-semibold text-slate-500">Date</th>
                    <th class="text-left py-2 px-3 text-xs font-semibold text-slate-500">Status</th>
                </tr>
            </thead>
            <tbody id="recent-bookings"></tbody>
        </table>
    </div>
</div>
</div>

<?php
$extra_js = <<<'JS'
<script>
async function loadDashboard() {
    var data = await api('../php/api/dashboard.php');
    if (!data) return;

    document.getElementById('stat-bookings').textContent = data.bookings?.total ?? 0;
    document.getElementById('stat-pending').textContent  = data.bookings?.pending ?? 0;
    document.getElementById('stat-users').textContent    = data.users ?? 0;
    document.getElementById('stat-blogs').textContent    = data.blogs ?? 0;

    var cats = ['stays','cars','bikes','restaurants','attractions','buses'];
    cats.forEach(function(c) {
        var el = document.getElementById('stat-' + c);
        if (el) el.textContent = data.listings?.[c] ?? 0;
    });

    var tbody = document.getElementById('recent-bookings');
    var bookings = data.recent_bookings || [];
    if (!bookings.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-slate-400 text-sm">No bookings yet</td></tr>';
        return;
    }
    tbody.innerHTML = bookings.map(function(b) {
        var statusColor = b.status === 'pending' ? 'bg-orange-100 text-orange-700' : b.status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
        return '<tr class="border-b border-slate-50 hover:bg-slate-50">' +
            '<td class="py-2.5 px-3 font-medium">' + escHtml(b.full_name) + '</td>' +
            '<td class="py-2.5 px-3 text-slate-500">' + escHtml(b.phone) + '</td>' +
            '<td class="py-2.5 px-3 text-slate-500">' + escHtml(b.service_type || b.listing_name || '—') + '</td>' +
            '<td class="py-2.5 px-3 text-slate-500">' + escHtml(b.booking_date || b.created_at?.split(' ')[0] || '—') + '</td>' +
            '<td class="py-2.5 px-3"><span class="px-2 py-0.5 rounded-full text-xs font-bold ' + statusColor + '">' + escHtml(b.status) + '</span></td>' +
        '</tr>';
    }).join('');
}

function escHtml(s) {
    return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

loadDashboard();
</script>
JS;
require 'admin-footer.php';
?>
