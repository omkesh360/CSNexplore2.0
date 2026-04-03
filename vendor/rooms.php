<?php
$vendor_page  = 'rooms';
$vendor_title = 'Room Management | Vendor Portal';
require 'vendor-header.php';
?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-slate-900">Room Management</h2>
        <p class="text-sm text-slate-500 mt-1">Manage your room types and individual rooms</p>
    </div>
    <button onclick="openTypeModal()"
            class="flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-orange-600 transition-all shadow-lg shadow-primary/20">
        <span class="material-symbols-outlined text-lg">add</span> Add Room Type
    </button>
</div>

<!-- Room Types List -->
<div id="room-types-container">
    <div class="vendor-card p-8 text-center text-slate-400">
        <span class="material-symbols-outlined text-4xl mb-2">hourglass_empty</span>
        <p class="text-sm">Loading room types…</p>
    </div>
</div>

<!-- ── Room Type Modal ─────────────────────────────────────────────────── -->
<div id="type-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white">
            <h3 id="type-modal-title" class="text-xl font-bold text-slate-900">Add Room Type</h3>
            <button onclick="closeTypeModal()" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="type-form" class="p-6 space-y-4">
            <input type="hidden" id="type-id"/>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Room Type Name *</label>
                <input type="text" id="type-name" required
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Description</label>
                <textarea id="type-desc" rows="3"
                          class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary resize-none"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Base Price (₹/night) *</label>
                    <input type="number" id="type-price" min="0" step="0.01" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Max Guests</label>
                    <input type="number" id="type-guests" min="1" value="2"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Amenities (comma-separated)</label>
                <input type="text" id="type-amenities" placeholder="AC, WiFi, TV, Mini Fridge"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
            </div>
            <div class="flex items-center gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="type-active" checked class="w-4 h-4 accent-primary"/>
                    <span class="text-sm font-semibold text-slate-700">Active</span>
                </label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-primary text-white px-5 py-3 rounded-xl font-bold hover:bg-orange-600 transition-all">
                    <span id="type-submit-text">Create Room Type</span>
                </button>
                <button type="button" onclick="closeTypeModal()" class="px-5 py-3 border border-slate-200 rounded-xl font-bold text-slate-600 hover:bg-slate-50">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- ── Individual Room Modal ───────────────────────────────────────────── -->
<div id="room-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white">
            <h3 id="room-modal-title" class="text-xl font-bold text-slate-900">Add Room</h3>
            <button onclick="closeRoomModal()" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="room-form" class="p-6 space-y-4">
            <input type="hidden" id="room-id"/>
            <input type="hidden" id="room-type-id-field"/>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Room Number *</label>
                    <input type="text" id="room-number" required placeholder="101"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Floor</label>
                    <input type="text" id="room-floor" placeholder="1st Floor"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Price Override (₹/night) — leave 0 to use type price</label>
                <input type="number" id="room-price" min="0" step="0.01" value="0"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status</label>
                <select id="room-status" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div class="flex items-center gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="room-available" checked class="w-4 h-4 accent-primary"/>
                    <span class="text-sm font-semibold text-slate-700">Available for booking</span>
                </label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-primary text-white px-5 py-3 rounded-xl font-bold hover:bg-orange-600 transition-all">
                    <span id="room-submit-text">Add Room</span>
                </button>
                <button type="button" onclick="closeRoomModal()" class="px-5 py-3 border border-slate-200 rounded-xl font-bold text-slate-600 hover:bg-slate-50">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
let allRoomTypes = [];

// ── Load room types ───────────────────────────────────────────────────────────
async function loadRoomTypes() {
    const data = await vendorApi('<?php echo VENDOR_API_BASE; ?>/php/api/vendor-rooms.php?action=list');
    const container = document.getElementById('room-types-container');
    if (!data || !data.rooms) {
        container.innerHTML = `<div class="vendor-card p-8 text-center text-red-400"><p>Failed to load rooms. Check console.</p></div>`;
        return;
    }
    allRoomTypes = data.rooms;
    renderRoomTypes(allRoomTypes);
}

function renderRoomTypes(types) {
    const container = document.getElementById('room-types-container');
    if (types.length === 0) {
        container.innerHTML = `
            <div class="vendor-card p-12 text-center text-slate-400">
                <span class="material-symbols-outlined text-5xl mb-3">hotel</span>
                <p class="font-semibold text-slate-600 mb-1">No room types yet</p>
                <p class="text-sm">Click "Add Room Type" to create your first room type.</p>
            </div>`;
        return;
    }

    container.innerHTML = types.map(t => `
        <div class="vendor-card mb-4">
            <!-- Room Type Header -->
            <div class="p-5 flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-blue-600 text-2xl">bed</span>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900">${t.name}</p>
                        <p class="text-xs text-slate-500 mt-0.5">₹${parseFloat(t.base_price).toLocaleString()}/night · ${t.max_guests} guests max · ${t.rooms_count || 0} rooms</p>
                        ${t.amenities ? `<p class="text-[11px] text-primary mt-0.5">${t.amenities}</p>` : ''}
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="px-2 py-1 rounded-full text-[11px] font-bold ${t.is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'}">${t.is_active ? 'Active' : 'Inactive'}</span>
                    <button onclick="openAddRoomModal(${t.id})" class="p-2 text-slate-500 hover:text-primary hover:bg-primary/5 rounded-lg transition-all" title="Add Room">
                        <span class="material-symbols-outlined text-lg">add_circle</span>
                    </button>
                    <button onclick="editType(${t.id})" class="p-2 text-slate-500 hover:text-primary hover:bg-primary/5 rounded-lg transition-all" title="Edit">
                        <span class="material-symbols-outlined text-lg">edit</span>
                    </button>
                    <button onclick="deleteType(${t.id},'${t.name.replace(/'/g,"\\'")}',${t.rooms_count||0})" class="p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete">
                        <span class="material-symbols-outlined text-lg">delete</span>
                    </button>
                </div>
            </div>
            <!-- Rooms inside this type -->
            <div id="rooms-under-${t.id}" class="border-t border-slate-100 px-5 py-3">
                <p class="text-xs text-slate-400 italic">Click the + button to add individual rooms under this type.</p>
            </div>
        </div>`).join('');

    // Load rooms per type
    types.forEach(t => loadRoomsUnderType(t.id));
}

async function loadRoomsUnderType(typeId) {
    const data = await vendorApi(`<?php echo VENDOR_API_BASE; ?>/php/api/vendor-rooms.php?action=get&id=${typeId}`);
    const el = document.getElementById(`rooms-under-${typeId}`);
    if (!el || !data || !data.rooms) return;

    if (data.rooms.length === 0) {
        el.innerHTML = `<p class="text-xs text-slate-400 italic py-2">No individual rooms added yet under this type.</p>`;
        return;
    }

    el.innerHTML = `
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 pb-2">
            ${data.rooms.map(r => `
                <div class="flex items-center justify-between bg-slate-50 rounded-lg px-4 py-3">
                    <div>
                        <p class="font-bold text-sm text-slate-900">Room ${r.room_number}</p>
                        <p class="text-xs text-slate-500">${r.floor || 'N/A'} · ${r.price > 0 ? '₹'+parseFloat(r.price).toLocaleString() : 'Type price'}</p>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold ${r.status==='available'?'bg-green-50 text-green-700':r.status==='maintenance'?'bg-yellow-50 text-yellow-700':'bg-red-50 text-red-700'}">${r.status}</span>
                    </div>
                    <div class="flex gap-1">
                        <button onclick="editRoom(${r.id},${typeId})" class="p-1.5 text-slate-400 hover:text-primary rounded-lg transition-all">
                            <span class="material-symbols-outlined text-base">edit</span>
                        </button>
                        <button onclick="deleteRoom(${r.id},'${r.room_number}',${typeId})" class="p-1.5 text-slate-400 hover:text-red-500 rounded-lg transition-all">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                    </div>
                </div>`).join('')}
        </div>`;
}

// ── Room Type Modal ───────────────────────────────────────────────────────────
function openTypeModal(id) {
    document.getElementById('type-modal').classList.remove('hidden');
    document.getElementById('type-form').reset();
    document.getElementById('type-id').value = '';
    document.getElementById('type-active').checked = true;
    document.getElementById('type-submit-text').textContent = 'Create Room Type';
    document.getElementById('type-modal-title').textContent = 'Add Room Type';

    if (id) {
        const t = allRoomTypes.find(x => x.id == id);
        if (!t) return;
        document.getElementById('type-modal-title').textContent = 'Edit Room Type';
        document.getElementById('type-submit-text').textContent = 'Update Room Type';
        document.getElementById('type-id').value = t.id;
        document.getElementById('type-name').value = t.name;
        document.getElementById('type-desc').value = t.description || '';
        document.getElementById('type-price').value = t.base_price;
        document.getElementById('type-guests').value = t.max_guests;
        document.getElementById('type-amenities').value = t.amenities || '';
        document.getElementById('type-active').checked = !!t.is_active;
    }
}
function closeTypeModal() { document.getElementById('type-modal').classList.add('hidden'); }
function editType(id) { openTypeModal(id); }

document.getElementById('type-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const id = document.getElementById('type-id').value;
    const action = id ? 'update_type' : 'create_type';
    const payload = {
        name: document.getElementById('type-name').value.trim(),
        description: document.getElementById('type-desc').value.trim(),
        base_price: parseFloat(document.getElementById('type-price').value) || 0,
        max_guests: parseInt(document.getElementById('type-guests').value) || 2,
        amenities: document.getElementById('type-amenities').value.trim(),
        is_active: document.getElementById('type-active').checked ? 1 : 0,
    };
    if (id) payload.id = parseInt(id);

    const data = await vendorApi(`<?php echo VENDOR_API_BASE; ?>/php/api/vendor-rooms.php?action=${action}`, {
        method: 'POST', body: JSON.stringify(payload)
    });
    if (data && data.success) {
        showVendorToast(data.message);
        closeTypeModal();
        loadRoomTypes();
    } else {
        showVendorToast(data?.error || 'Failed', 'error');
    }
});

async function deleteType(id, name, roomCount) {
    if (roomCount > 0) { showVendorToast(`Delete all ${roomCount} rooms first before deleting this type.`, 'error'); return; }
    if (!confirm(`Delete room type "${name}"?`)) return;
    const data = await vendorApi(`<?php echo VENDOR_API_BASE; ?>/php/api/vendor-rooms.php?action=delete_type&id=${id}`);
    if (data && data.success) { showVendorToast('Room type deleted'); loadRoomTypes(); }
    else showVendorToast(data?.error || 'Failed', 'error');
}

// ── Individual Room Modal ─────────────────────────────────────────────────────
function openAddRoomModal(typeId) {
    document.getElementById('room-modal').classList.remove('hidden');
    document.getElementById('room-form').reset();
    document.getElementById('room-id').value = '';
    document.getElementById('room-type-id-field').value = typeId;
    document.getElementById('room-available').checked = true;
    document.getElementById('room-status').value = 'available';
    document.getElementById('room-submit-text').textContent = 'Add Room';
    document.getElementById('room-modal-title').textContent = 'Add Room';
}
async function editRoom(roomId, typeId) {
    const data = await vendorApi(`<?php echo VENDOR_API_BASE; ?>/php/api/vendor-rooms.php?action=get&id=${typeId}`);
    if (!data || !data.rooms) return;
    const r = data.rooms.find(x => x.id == roomId);
    if (!r) return;

    document.getElementById('room-modal').classList.remove('hidden');
    document.getElementById('room-modal-title').textContent = 'Edit Room';
    document.getElementById('room-submit-text').textContent = 'Update Room';
    document.getElementById('room-id').value = r.id;
    document.getElementById('room-type-id-field').value = typeId;
    document.getElementById('room-number').value = r.room_number;
    document.getElementById('room-floor').value = r.floor || '';
    document.getElementById('room-price').value = r.price || 0;
    document.getElementById('room-status').value = r.status;
    document.getElementById('room-available').checked = !!r.is_available;
}
function closeRoomModal() { document.getElementById('room-modal').classList.add('hidden'); }

document.getElementById('room-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const id = document.getElementById('room-id').value;
    const typeId = document.getElementById('room-type-id-field').value;
    const action = id ? 'update_room' : 'create_room';
    const payload = {
        room_type_id: parseInt(typeId),
        room_number: document.getElementById('room-number').value.trim(),
        floor: document.getElementById('room-floor').value.trim(),
        price: parseFloat(document.getElementById('room-price').value) || 0,
        status: document.getElementById('room-status').value,
        is_available: document.getElementById('room-available').checked ? 1 : 0,
    };
    if (id) payload.id = parseInt(id);

    const data = await vendorApi(`<?php echo VENDOR_API_BASE; ?>/php/api/vendor-rooms.php?action=${action}`, {
        method: 'POST', body: JSON.stringify(payload)
    });
    if (data && data.success) {
        showVendorToast(data.message);
        closeRoomModal();
        loadRoomsUnderType(typeId);
        loadRoomTypes(); // refresh counts
    } else {
        showVendorToast(data?.error || 'Failed', 'error');
    }
});

async function deleteRoom(roomId, roomNum, typeId) {
    if (!confirm(`Delete Room ${roomNum}?`)) return;
    const data = await vendorApi(`<?php echo VENDOR_API_BASE; ?>/php/api/vendor-rooms.php?action=delete_room&id=${roomId}`);
    if (data && data.success) { showVendorToast('Room deleted'); loadRoomsUnderType(typeId); loadRoomTypes(); }
    else showVendorToast(data?.error || 'Failed', 'error');
}

loadRoomTypes();
</script>

<?php require 'vendor-footer.php'; ?>
