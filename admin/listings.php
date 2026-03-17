<?php
$admin_page  = 'listings';
$admin_title = 'Manage Listings | CSNExplore Admin';
require 'admin-header.php';
?>

<div class="space-y-5">
<!-- Toolbar -->
<div class="flex flex-wrap items-center gap-3">
    <!-- Category tabs -->
    <div class="flex gap-1 bg-white border border-slate-200 p-1 rounded-xl overflow-x-auto">
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
        <button onclick="switchCat('<?php echo $cat['key']; ?>')"
                data-cat="<?php echo $cat['key']; ?>"
                class="cat-tab flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-500 hover:text-slate-900 whitespace-nowrap transition-all">
            <span class="material-symbols-outlined text-sm"><?php echo $cat['icon']; ?></span>
            <?php echo $cat['label']; ?>
        </button>
        <?php endforeach; ?>
    </div>
    <input id="search-input" type="text" placeholder="Search listings..."
           class="flex-1 min-w-[180px] border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
    <button onclick="openAddModal()"
            class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-orange-600 transition-all shadow-sm shadow-primary/20">
        <span class="material-symbols-outlined text-base">add</span> Add Listing
    </button>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Image</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Name</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Type</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Location</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Price</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Rating</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Status</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Actions</th>
                </tr>
            </thead>
            <tbody id="listings-tbody">
                <tr><td colspan="8" class="text-center py-12 text-slate-400">Loading...</td></tr>
            </tbody>
        </table>
    </div>
</div>
</div>

<!-- Add/Edit Modal -->
<div id="listing-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 id="modal-title" class="text-base font-bold">Add Listing</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="listing-form" class="p-6 space-y-4">
            <input type="hidden" id="edit-id"/>
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Name *</label>
                    <input id="f-name" type="text" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Type</label>
                    <input id="f-type" type="text" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Location *</label>
                    <input id="f-location" type="text" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1" id="price-label">Price *</label>
                    <input id="f-price" type="number" min="0" step="0.01" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Rating (0-5)</label>
                    <input id="f-rating" type="number" min="0" max="5" step="0.1" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Badge</label>
                    <input id="f-badge" type="text" placeholder="e.g. Top Rated" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                    <select id="f-active" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                        <option value="1">Active</option>
                        <option value="0">Hidden</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Image URL</label>
                    <div class="flex gap-2">
                        <input id="f-image" type="url" class="flex-1 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                        <button type="button" onclick="openGalleryPicker(function(url){ document.getElementById('f-image').value=url; })"
                                class="flex items-center gap-1 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-all whitespace-nowrap">
                            <span class="material-symbols-outlined text-sm">photo_library</span> Gallery
                        </button>
                    </div>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Description</label>
                    <textarea id="f-description" rows="3" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary resize-none"></textarea>
                </div>
                <!-- Extra fields shown per category -->
                <div id="extra-fields" class="col-span-2 grid grid-cols-2 gap-4"></div>
            </div>
            <div id="form-error" class="hidden text-sm text-red-600 bg-red-50 px-4 py-2 rounded-xl"></div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal()" class="flex-1 border border-slate-200 text-slate-600 py-2.5 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-all">Cancel</button>
                <button type="submit" id="save-btn" class="flex-1 bg-primary text-white py-2.5 rounded-xl text-sm font-bold hover:bg-orange-600 transition-all">Save Listing</button>
            </div>
        </form>
    </div>
</div>

<?php
$extra_js = <<<'JS'
<script>
var currentCat = 'stays';
var editingId   = null;

var priceLabels = {
    stays: 'Price / Night (₹)',
    cars: 'Price / Day (₹)',
    bikes: 'Price / Day (₹)',
    restaurants: 'Price / Person (₹)',
    attractions: 'Entry Fee (₹)',
    buses: 'Price (₹)',
};

var extraFieldsDef = {
    stays:       [['f-room_type','Room Type','text'],['f-max_guests','Max Guests','number'],['f-amenities','Amenities (comma-sep)','text']],
    cars:        [['f-fuel_type','Fuel Type','text'],['f-transmission','Transmission','text'],['f-seats','Seats','number']],
    bikes:       [['f-fuel_type','Fuel Type','text'],['f-cc','CC','text']],
    restaurants: [['f-cuisine','Cuisine','text']],
    attractions: [['f-opening_hours','Opening Hours','text'],['f-best_time','Best Time to Visit','text']],
    buses:       [['f-operator','Operator','text'],['f-from_location','From','text'],['f-to_location','To','text'],['f-departure_time','Departure','text'],['f-arrival_time','Arrival','text'],['f-duration','Duration','text']],
};

function escHtml(s) {
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function switchCat(cat) {
    currentCat = cat;
    document.querySelectorAll('.cat-tab').forEach(function(b){
        var active = b.dataset.cat === cat;
        b.classList.toggle('bg-primary', active);
        b.classList.toggle('text-white', active);
        b.classList.toggle('text-slate-500', !active);
    });
    loadListings();
}

async function loadListings() {
    var search = document.getElementById('search-input').value;
    var url = '../php/api/listings.php?category=' + currentCat + (search ? '&search=' + encodeURIComponent(search) : '');
    var tbody = document.getElementById('listings-tbody');
    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-12 text-slate-400">Loading...</td></tr>';
    var items = await api(url);
    if (!items || !items.length) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-12 text-slate-400">No listings found</td></tr>';
        return;
    }
    var priceKey = {stays:'price_per_night',cars:'price_per_day',bikes:'price_per_day',restaurants:'price_per_person',attractions:'entry_fee',buses:'price'};
    tbody.innerHTML = items.map(function(item) {
        var statusBg = item.is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500';
        var statusTxt = item.is_active ? 'Active' : 'Hidden';
        var img = item.image ? '<img src="' + escHtml(item.image) + '" class="w-10 h-10 rounded-lg object-cover"/>' : '<div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center"><span class="material-symbols-outlined text-slate-400 text-base">image</span></div>';
        return '<tr class="border-b border-slate-50 hover:bg-slate-50">' +
            '<td class="py-2.5 px-4">' + img + '</td>' +
            '<td class="py-2.5 px-4 font-medium max-w-[160px] truncate">' + escHtml(item.name) + '</td>' +
            '<td class="py-2.5 px-4 text-slate-500">' + escHtml(item.type || '—') + '</td>' +
            '<td class="py-2.5 px-4 text-slate-500 max-w-[120px] truncate">' + escHtml(item.location) + '</td>' +
            '<td class="py-2.5 px-4 font-semibold text-primary">₹' + (item[priceKey[currentCat]] || 0) + '</td>' +
            '<td class="py-2.5 px-4 text-slate-500">' + (item.rating || '—') + '</td>' +
            '<td class="py-2.5 px-4"><span class="px-2 py-0.5 rounded-full text-xs font-bold ' + statusBg + '">' + statusTxt + '</span></td>' +
            '<td class="py-2.5 px-4">' +
                '<div class="flex gap-1">' +
                '<button onclick="openEditModal(' + item.id + ')" class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"><span class="material-symbols-outlined text-base">edit</span></button>' +
                '<button onclick="toggleActive(' + item.id + ',' + (item.is_active ? 0 : 1) + ')" class="p-1.5 text-slate-400 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-all"><span class="material-symbols-outlined text-base">' + (item.is_active ? 'visibility_off' : 'visibility') + '</span></button>' +
                '<button onclick="deleteListing(' + item.id + ')" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all"><span class="material-symbols-outlined text-base">delete</span></button>' +
                '</div>' +
            '</td>' +
        '</tr>';
    }).join('');
}

function buildExtraFields(cat) {
    var container = document.getElementById('extra-fields');
    container.innerHTML = '';
    (extraFieldsDef[cat] || []).forEach(function(f) {
        var div = document.createElement('div');
        div.innerHTML = '<label class="block text-xs font-semibold text-slate-600 mb-1">' + f[1] + '</label>' +
            '<input id="' + f[0] + '" type="' + f[2] + '" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>';
        container.appendChild(div);
    });
    document.getElementById('price-label').textContent = priceLabels[cat] || 'Price';
}

function openAddModal() {
    editingId = null;
    document.getElementById('modal-title').textContent = 'Add ' + currentCat.charAt(0).toUpperCase() + currentCat.slice(1);
    document.getElementById('listing-form').reset();
    document.getElementById('edit-id').value = '';
    buildExtraFields(currentCat);
    document.getElementById('form-error').classList.add('hidden');
    document.getElementById('listing-modal').classList.remove('hidden');
}

async function openEditModal(id) {
    editingId = id;
    var item = await api('../php/api/listings.php?category=' + currentCat + '&id=' + id);
    if (!item) return;
    document.getElementById('modal-title').textContent = 'Edit Listing';
    buildExtraFields(currentCat);
    document.getElementById('edit-id').value = id;
    document.getElementById('f-name').value = item.name || '';
    document.getElementById('f-type').value = item.type || '';
    document.getElementById('f-location').value = item.location || '';
    document.getElementById('f-badge').value = item.badge || '';
    document.getElementById('f-image').value = item.image || '';
    document.getElementById('f-description').value = item.description || '';
    document.getElementById('f-rating').value = item.rating || '';
    document.getElementById('f-active').value = item.is_active ?? 1;
    var priceKey = {stays:'price_per_night',cars:'price_per_day',bikes:'price_per_day',restaurants:'price_per_person',attractions:'entry_fee',buses:'price'};
    document.getElementById('f-price').value = item[priceKey[currentCat]] || '';
    // Extra fields
    (extraFieldsDef[currentCat] || []).forEach(function(f) {
        var el = document.getElementById(f[0]);
        if (!el) return;
        var key = f[0].replace('f-','');
        var val = item[key];
        if (Array.isArray(val)) val = val.join(', ');
        el.value = val || '';
    });
    document.getElementById('form-error').classList.add('hidden');
    document.getElementById('listing-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('listing-modal').classList.add('hidden');
}

document.getElementById('listing-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    var btn = document.getElementById('save-btn');
    btn.disabled = true; btn.textContent = 'Saving...';
    var priceKey = {stays:'price_per_night',cars:'price_per_day',bikes:'price_per_day',restaurants:'price_per_person',attractions:'entry_fee',buses:'price'};
    var data = {
        name: document.getElementById('f-name').value,
        type: document.getElementById('f-type').value,
        location: document.getElementById('f-location').value,
        badge: document.getElementById('f-badge').value,
        image: document.getElementById('f-image').value,
        description: document.getElementById('f-description').value,
        rating: parseFloat(document.getElementById('f-rating').value) || 0,
        is_active: parseInt(document.getElementById('f-active').value),
    };
    data[priceKey[currentCat]] = parseFloat(document.getElementById('f-price').value) || 0;
    // Extra fields
    (extraFieldsDef[currentCat] || []).forEach(function(f) {
        var el = document.getElementById(f[0]);
        if (!el) return;
        var key = f[0].replace('f-','');
        var val = el.value;
        if (key === 'amenities' || key === 'features' || key === 'menu_highlights') {
            val = val.split(',').map(function(s){ return s.trim(); }).filter(Boolean);
        }
        data[key] = val;
    });

    try {
        var url = '../php/api/listings.php?category=' + currentCat;
        var method = 'POST';
        if (editingId) { url += '&id=' + editingId; method = 'PUT'; }
        var res = await api(url, { method: method, body: JSON.stringify(data) });
        if (res && res.error) throw new Error(res.error);
        closeModal();
        loadListings();
    } catch(ex) {
        var errEl = document.getElementById('form-error');
        errEl.textContent = ex.message;
        errEl.classList.remove('hidden');
    }
    btn.disabled = false; btn.textContent = 'Save Listing';
});

async function toggleActive(id, val) {
    await api('../php/api/listings.php?category=' + currentCat + '&id=' + id, {
        method: 'PUT', body: JSON.stringify({ is_active: val })
    });
    loadListings();
}

async function deleteListing(id) {
    if (!confirm('Delete this listing? It will be hidden from the site.')) return;
    await api('../php/api/listings.php?category=' + currentCat + '&id=' + id, { method: 'DELETE' });
    loadListings();
}

document.getElementById('search-input').addEventListener('input', function() {
    clearTimeout(window._searchTimer);
    window._searchTimer = setTimeout(loadListings, 400);
});

// Init
switchCat('stays');
</script>
JS;
require 'admin-footer.php';
?>
