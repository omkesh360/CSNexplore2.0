<?php
$vendor_page  = 'stays';
$vendor_title = 'Hotel & Stay Listings | Vendor Portal';
require 'vendor-header.php';
?>

<div class="mb-5 flex items-start justify-between gap-4 flex-wrap">
    <div>
        <h2 class="text-xl font-black text-slate-900">Hotel &amp; Stay Listings</h2>
        <p class="text-xs text-slate-500 mt-0.5">Create and manage your property listings shown on the website</p>
    </div>
    <button onclick="openModal()" class="flex items-center gap-2 bg-primary text-white px-4 py-2.5 rounded-xl text-xs font-bold hover:bg-orange-600 transition-all shadow-sm shadow-primary/30">
        <span class="material-symbols-outlined text-base">add</span> Add Stay Listing
    </button>
</div>

<!-- Search + filters bar -->
<div class="vendor-card p-3 mb-4 flex items-center gap-3 flex-wrap">
    <div class="relative flex-1 min-w-48">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-lg">search</span>
        <input id="search-input" type="text" placeholder="Search stays…" oninput="filterStays()"
               class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
    </div>
    <select id="filter-status" onchange="filterStays()" class="px-3 py-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
        <option value="">All Status</option>
        <option value="1">Active</option>
        <option value="0">Hidden</option>
    </select>
    <select id="filter-type" onchange="filterStays()" class="px-3 py-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
        <option value="">All Types</option>
        <option>Hotel</option><option>Guest House</option><option>Resort</option>
        <option>Hostel</option><option>Homestay</option><option>Villa</option>
    </select>
</div>

<!-- Grid -->
<div id="stays-container">
    <div class="vendor-card p-10 text-center text-slate-400">
        <span class="material-symbols-outlined text-4xl animate-spin">progress_activity</span>
        <p class="text-sm mt-3">Loading listings…</p>
    </div>
</div>
<div id="no-stays" class="hidden vendor-card p-12 text-center text-slate-400">
    <span class="material-symbols-outlined text-5xl mb-3">apartment</span>
    <p class="font-bold text-slate-600 mb-1">No stays found</p>
    <p class="text-sm mb-4">Add your first hotel or property listing</p>
    <button onclick="openModal()" class="bg-primary text-white px-5 py-2 rounded-xl text-xs font-bold hover:bg-orange-600 transition-all">
        + Add Stay Listing
    </button>
</div>

<!-- ── Modal ─────────────────────────────────────────────────────────────── -->
<div id="modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-start justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-4">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10 rounded-t-2xl">
            <h3 id="modal-title" class="text-lg font-black text-slate-900">Add Stay Listing</h3>
            <button onclick="closeModal()" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="stay-form" onsubmit="submitForm(event)" class="p-5 space-y-4">
            <input type="hidden" id="stay-id"/>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Property Name *</label>
                    <input id="s-name" required placeholder="e.g. The Royal Heritage Hotel"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Type *</label>
                    <select id="s-type" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <option>Hotel</option><option>Guest House</option><option>Resort</option>
                        <option>Hostel</option><option>Homestay</option><option>Villa</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Location *</label>
                    <input id="s-location" required placeholder="Chhatrapati Sambhajinagar"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Price per Night (₹) *</label>
                    <input id="s-price" type="number" min="1" step="0.01" required
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Max Guests</label>
                    <input id="s-guests" type="number" min="1" value="2"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                    <textarea id="s-desc" rows="3" placeholder="Describe your property…"
                              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"></textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Amenities (comma-separated)</label>
                    <input id="s-amenities" placeholder="Free WiFi, Breakfast, Parking, AC, AC, Swimming Pool"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Main Image URL</label>
                    <input id="s-image" type="url" placeholder="https://images.unsplash.com/..."
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                    <div id="img-preview" class="hidden mt-2 rounded-lg overflow-hidden h-32 bg-slate-100">
                        <img id="img-preview-img" class="w-full h-full object-cover"/>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Badge Label</label>
                    <input id="s-badge" placeholder="e.g. Popular, Budget Friendly"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                </div>
                <div class="flex items-center gap-6 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="s-active" checked class="w-4 h-4 accent-primary"/>
                        <span class="text-xs font-bold text-slate-700">Active (visible on site)</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" id="submit-btn" class="flex-1 bg-primary text-white px-5 py-3 rounded-xl font-bold text-sm hover:bg-orange-600 transition-all">
                    <span id="submit-text">Create Listing</span>
                </button>
                <button type="button" onclick="closeModal()" class="px-5 py-3 border border-slate-200 rounded-xl font-bold text-sm text-slate-600 hover:bg-slate-50">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
const BASE = '<?php echo VENDOR_API_BASE; ?>';
let allStays = [];

async function loadStays() {
    const d = await vendorApi(`${BASE}/php/api/vendor-stays.php?action=list&limit=100`);
    if (!d) { document.getElementById('stays-container').innerHTML = `<div class="vendor-card p-8 text-center text-red-400"><p>Failed to load stays. Please refresh.</p></div>`; return; }
    allStays = d.stays || [];
    renderStays(allStays);
}

function filterStays() {
    const q    = document.getElementById('search-input').value.toLowerCase();
    const stat = document.getElementById('filter-status').value;
    const type = document.getElementById('filter-type').value;
    renderStays(allStays.filter(s =>
        (!q    || s.name.toLowerCase().includes(q) || (s.location||'').toLowerCase().includes(q)) &&
        (stat === '' || String(s.is_active) === stat) &&
        (!type || s.type === type)
    ));
}

function renderStays(list) {
    const c = document.getElementById('stays-container');
    const n = document.getElementById('no-stays');
    if (list.length === 0) { c.innerHTML=''; n.classList.remove('hidden'); return; }
    n.classList.add('hidden');
    c.innerHTML = `<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    ${list.map(s=>`
        <div class="vendor-card overflow-hidden flex flex-col group">
            <div class="relative h-44 bg-slate-100 overflow-hidden">
                ${s.image ? `<img src="${s.image}" alt="${s.name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"/>` :
                    `<div class="w-full h-full flex items-center justify-center"><span class="material-symbols-outlined text-slate-300 text-5xl">apartment</span></div>`}
                <div class="absolute top-2 left-2 flex gap-1.5">
                    ${s.badge ? `<span class="px-2 py-0.5 bg-primary text-white text-[9px] font-bold rounded-full shadow">${s.badge}</span>` : ''}
                </div>
                <div class="absolute top-2 right-2">
                    <span class="px-2 py-1 rounded-full text-[10px] font-bold shadow ${s.is_active ? 'bg-green-500 text-white':'bg-slate-600 text-white'}">${s.is_active?'Active':'Hidden'}</span>
                </div>
            </div>
            <div class="p-4 flex-1 flex flex-col">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <p class="font-bold text-slate-900 text-sm leading-tight flex-1">${s.name}</p>
                    <p class="text-primary font-black text-base shrink-0">${fmt(s.price_per_night)}<span class="text-[10px] text-slate-400 font-normal">/night</span></p>
                </div>
                <p class="text-[11px] text-slate-500 flex items-center gap-1 mb-1">
                    <span class="material-symbols-outlined text-sm">location_on</span>${s.location}
                </p>
                <p class="text-[10px] text-slate-400 mb-3">${s.type}</p>
                <div class="mt-auto flex gap-1.5">
                    <button onclick="toggleStay(${s.id})" class="flex-1 py-2 border border-slate-200 rounded-lg text-[11px] font-bold text-slate-600 hover:bg-slate-50 transition-all">
                        ${s.is_active ? 'Hide' : 'Activate'}
                    </button>
                    <button onclick="editStay(${s.id})" class="p-2 text-primary hover:bg-primary/5 rounded-lg transition-all" title="Edit">
                        <span class="material-symbols-outlined text-lg">edit</span>
                    </button>
                    <button onclick="deleteStay(${s.id},'${s.name.replace(/'/g,"\\'")}')" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Delete">
                        <span class="material-symbols-outlined text-lg">delete</span>
                    </button>
                </div>
            </div>
        </div>`).join('')}
    </div>`;
}

// Image URL preview
document.getElementById('s-image').addEventListener('input', function(){
    var p = document.getElementById('img-preview');
    var img = document.getElementById('img-preview-img');
    if (this.value) { img.src = this.value; p.classList.remove('hidden'); }
    else p.classList.add('hidden');
});

function openModal(id) {
    document.getElementById('modal').classList.remove('hidden');
    document.getElementById('stay-form').reset();
    document.getElementById('stay-id').value = '';
    document.getElementById('s-active').checked = true;
    document.getElementById('img-preview').classList.add('hidden');
    document.getElementById('modal-title').textContent = 'Add Stay Listing';
    document.getElementById('submit-text').textContent = 'Create Listing';
    if (!id) return;

    const s = allStays.find(x=>x.id==id);
    if (!s) return;
    document.getElementById('modal-title').textContent = 'Edit Stay Listing';
    document.getElementById('submit-text').textContent = 'Update Listing';
    document.getElementById('stay-id').value = s.id;
    document.getElementById('s-name').value     = s.name||'';
    document.getElementById('s-type').value     = s.type||'Hotel';
    document.getElementById('s-location').value = s.location||'';
    document.getElementById('s-price').value    = s.price_per_night||0;
    document.getElementById('s-guests').value   = s.max_guests||2;
    document.getElementById('s-amenities').value= s.amenities||'';
    document.getElementById('s-image').value    = s.image||'';
    document.getElementById('s-badge').value    = s.badge||'';
    document.getElementById('s-active').checked = !!s.is_active;
    if (s.image) { document.getElementById('img-preview-img').src=s.image; document.getElementById('img-preview').classList.remove('hidden'); }
    // Load description (needs a separate get call)
    vendorApi(`${BASE}/php/api/vendor-stays.php?action=get&id=${id}`).then(d=>{
        if (d?.stay) document.getElementById('s-desc').value = d.stay.description||'';
    });
}
function closeModal() { document.getElementById('modal').classList.add('hidden'); }
function editStay(id) { openModal(id); }

async function submitForm(e) {
    e.preventDefault();
    const id = document.getElementById('stay-id').value;
    const payload = {
        name:           document.getElementById('s-name').value.trim(),
        type:           document.getElementById('s-type').value,
        location:       document.getElementById('s-location').value.trim(),
        price_per_night:parseFloat(document.getElementById('s-price').value)||0,
        max_guests:     parseInt(document.getElementById('s-guests').value)||2,
        description:    document.getElementById('s-desc').value.trim(),
        amenities:      document.getElementById('s-amenities').value.trim(),
        image:          document.getElementById('s-image').value.trim(),
        badge:          document.getElementById('s-badge').value.trim(),
        is_active:      document.getElementById('s-active').checked ? 1 : 0,
    };
    if (id) payload.id = parseInt(id);
    const action = id ? 'update' : 'create';
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;

    const d = await vendorApi(`${BASE}/php/api/vendor-stays.php?action=${action}`, {method:'POST', body:JSON.stringify(payload)});
    btn.disabled = false;
    if (d?.success) {
        showVendorToast(d.message, 'success');
        closeModal();
        loadStays();
    } else {
        showVendorToast(d?.error || 'Failed to save', 'error');
    }
}

async function toggleStay(id) {
    const d = await vendorApi(`${BASE}/php/api/vendor-stays.php?action=toggle&id=${id}`);
    if (d?.success) { showVendorToast(d.message); loadStays(); }
    else showVendorToast(d?.error||'Failed', 'error');
}

async function deleteStay(id, name) {
    if (!confirmAction(`Delete "${name}"? This cannot be undone.`)) return;
    const d = await vendorApi(`${BASE}/php/api/vendor-stays.php?action=delete&id=${id}`);
    if (d?.success) { showVendorToast('Listing deleted'); loadStays(); }
    else showVendorToast(d?.error||'Failed', 'error');
}

loadStays();
</script>

<?php require 'vendor-footer.php'; ?>
