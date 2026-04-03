<?php
$vendor_page  = 'cars';
$vendor_title = 'Car Management | Vendor Portal';
require 'vendor-header.php';
?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-slate-900">Car Management</h2>
        <p class="text-sm text-slate-500 mt-1">Manage your car rental fleet</p>
    </div>
    <button onclick="openCarModal()"
            class="flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-orange-600 transition-all shadow-lg shadow-primary/20">
        <span class="material-symbols-outlined text-lg">add</span> Add Car
    </button>
</div>

<!-- Cars Grid -->
<div id="cars-container">
    <div class="vendor-card p-8 text-center text-slate-400">
        <span class="material-symbols-outlined text-4xl mb-2">hourglass_empty</span>
        <p class="text-sm">Loading cars…</p>
    </div>
</div>

<!-- ── Car Modal ──────────────────────────────────────────────────────── -->
<div id="car-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[92vh] overflow-y-auto">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
            <h3 id="car-modal-title" class="text-xl font-bold text-slate-900">Add New Car</h3>
            <button onclick="closeCarModal()" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="car-form" class="p-6 space-y-5">
            <input type="hidden" id="car-id"/>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Car Name *</label>
                    <input type="text" id="car-name" required placeholder="e.g. Toyota Innova"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Car Type</label>
                    <select id="car-type" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                        <option value="Sedan">Sedan</option>
                        <option value="SUV">SUV</option>
                        <option value="Hatchback">Hatchback</option>
                        <option value="MUV">MUV</option>
                        <option value="Luxury">Luxury</option>
                        <option value="Van">Van</option>
                        <option value="Electric">Electric</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Location *</label>
                    <input type="text" id="car-location" required placeholder="Chhatrapati Sambhajinagar"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Price per Day (₹) *</label>
                    <input type="number" id="car-price" min="0" step="0.01" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Fuel Type</label>
                    <select id="car-fuel" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                        <option value="Petrol">Petrol</option>
                        <option value="Diesel">Diesel</option>
                        <option value="CNG">CNG</option>
                        <option value="Electric">Electric</option>
                        <option value="Hybrid">Hybrid</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Transmission</label>
                    <select id="car-transmission" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                        <option value="Manual">Manual</option>
                        <option value="Automatic">Automatic</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Seats</label>
                    <input type="number" id="car-seats" min="2" max="20" value="5"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Description</label>
                <textarea id="car-desc" rows="3" placeholder="Brief description of the car…"
                          class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary resize-none"></textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Image URL</label>
                <input type="url" id="car-image" placeholder="https://..."
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Features (comma-separated)</label>
                <input type="text" id="car-features" placeholder="AC, GPS, Bluetooth, Child Seat"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
            </div>

            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="car-available" checked class="w-4 h-4 accent-primary"/>
                    <span class="text-sm font-semibold text-slate-700">Available</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="car-active" checked class="w-4 h-4 accent-primary"/>
                    <span class="text-sm font-semibold text-slate-700">Active (visible on site)</span>
                </label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-primary text-white px-5 py-3 rounded-xl font-bold hover:bg-orange-600 transition-all">
                    <span id="car-submit-text">Add Car</span>
                </button>
                <button type="button" onclick="closeCarModal()" class="px-5 py-3 border border-slate-200 rounded-xl font-bold text-slate-600 hover:bg-slate-50">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
let allCars = [];

async function loadCars() {
    const data = await vendorApi('<?php echo VENDOR_API_BASE; ?>/php/api/vendor-cars.php?action=list');
    const container = document.getElementById('cars-container');
    if (!data || !data.cars) {
        container.innerHTML = `<div class="vendor-card p-8 text-center text-red-400"><p>Failed to load cars.</p></div>`;
        return;
    }
    allCars = data.cars;
    renderCars(allCars);
}

function renderCars(cars) {
    const container = document.getElementById('cars-container');
    if (cars.length === 0) {
        container.innerHTML = `
            <div class="vendor-card p-12 text-center text-slate-400">
                <span class="material-symbols-outlined text-5xl mb-3">directions_car</span>
                <p class="font-semibold text-slate-600 mb-1">No cars yet</p>
                <p class="text-sm">Click "Add Car" to add your first car listing.</p>
            </div>`;
        return;
    }

    container.innerHTML = `<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        ${cars.map(c => `
        <div class="vendor-card overflow-hidden flex flex-col">
            <div class="relative h-40 bg-slate-100">
                ${c.image ? `<img src="${c.image}" alt="${c.name}" class="w-full h-full object-cover"/>` :
                    `<div class="w-full h-full flex items-center justify-center"><span class="material-symbols-outlined text-slate-300 text-5xl">directions_car</span></div>`}
                <div class="absolute top-2 right-2 flex gap-1.5">
                    <span class="px-2 py-1 rounded-full text-[10px] font-bold shadow ${c.is_available ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}">${c.is_available ? 'Available' : 'Unavailable'}</span>
                    <span class="px-2 py-1 rounded-full text-[10px] font-bold shadow ${c.is_active ? 'bg-blue-500 text-white' : 'bg-slate-400 text-white'}">${c.is_active ? 'Active' : 'Inactive'}</span>
                </div>
            </div>
            <div class="p-4 flex-1 flex flex-col">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-bold text-slate-900">${c.name}</p>
                        <p class="text-xs text-slate-500">${c.type || ''} · ${c.fuel_type || ''} · ${c.transmission || ''} · ${c.seats || 5} seats</p>
                    </div>
                    <p class="text-primary font-black text-lg">₹${parseFloat(c.price_per_day).toLocaleString()}<span class="text-xs font-normal text-slate-400">/day</span></p>
                </div>
                <p class="text-xs text-slate-500 flex items-center gap-1 mb-3">
                    <span class="material-symbols-outlined text-sm">location_on</span>${c.location}
                </p>
                ${c.features ? `<p class="text-[11px] text-slate-500 mb-3 line-clamp-1">${c.features}</p>` : ''}
                <div class="mt-auto flex gap-2">
                    <button onclick="toggleAvailability(${c.id})" class="flex-1 py-2 border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all">
                        ${c.is_available ? 'Mark Unavailable' : 'Mark Available'}
                    </button>
                    <button onclick="editCar(${c.id})" class="p-2 text-primary hover:bg-primary/5 rounded-lg transition-all">
                        <span class="material-symbols-outlined text-lg">edit</span>
                    </button>
                    <button onclick="deleteCar(${c.id},'${c.name.replace(/'/g,"\\'")}') " class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-all">
                        <span class="material-symbols-outlined text-lg">delete</span>
                    </button>
                </div>
            </div>
        </div>`).join('')}
    </div>`;
}

// ── Car Modal ─────────────────────────────────────────────────────────────────
function openCarModal(id) {
    document.getElementById('car-modal').classList.remove('hidden');
    document.getElementById('car-form').reset();
    document.getElementById('car-id').value = '';
    document.getElementById('car-available').checked = true;
    document.getElementById('car-active').checked = true;
    document.getElementById('car-seats').value = 5;
    document.getElementById('car-submit-text').textContent = 'Add Car';
    document.getElementById('car-modal-title').textContent = 'Add New Car';

    if (id) {
        const c = allCars.find(x => x.id == id);
        if (!c) return;
        document.getElementById('car-modal-title').textContent = 'Edit Car';
        document.getElementById('car-submit-text').textContent = 'Update Car';
        document.getElementById('car-id').value = c.id;
        document.getElementById('car-name').value = c.name;
        document.getElementById('car-type').value = c.type || 'Sedan';
        document.getElementById('car-location').value = c.location;
        document.getElementById('car-price').value = c.price_per_day;
        document.getElementById('car-fuel').value = c.fuel_type || 'Petrol';
        document.getElementById('car-transmission').value = c.transmission || 'Manual';
        document.getElementById('car-seats').value = c.seats || 5;
        document.getElementById('car-desc').value = c.description || '';
        document.getElementById('car-image').value = c.image || '';
        document.getElementById('car-features').value = c.features || '';
        document.getElementById('car-available').checked = !!c.is_available;
        document.getElementById('car-active').checked = !!c.is_active;
    }
}
function closeCarModal() { document.getElementById('car-modal').classList.add('hidden'); }
function editCar(id) { openCarModal(id); }

document.getElementById('car-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const id = document.getElementById('car-id').value;
    const action = id ? 'update' : 'create';
    const payload = {
        name: document.getElementById('car-name').value.trim(),
        type: document.getElementById('car-type').value,
        location: document.getElementById('car-location').value.trim(),
        price_per_day: parseFloat(document.getElementById('car-price').value) || 0,
        fuel_type: document.getElementById('car-fuel').value,
        transmission: document.getElementById('car-transmission').value,
        seats: parseInt(document.getElementById('car-seats').value) || 5,
        description: document.getElementById('car-desc').value.trim(),
        image: document.getElementById('car-image').value.trim(),
        features: document.getElementById('car-features').value.trim(),
        is_available: document.getElementById('car-available').checked ? 1 : 0,
        is_active: document.getElementById('car-active').checked ? 1 : 0,
    };
    if (id) payload.id = parseInt(id);

    const data = await vendorApi(`<?php echo VENDOR_API_BASE; ?>/php/api/vendor-cars.php?action=${action}`, {
        method: 'POST', body: JSON.stringify(payload)
    });
    if (data && data.success) {
        showVendorToast(data.message);
        closeCarModal();
        loadCars();
    } else {
        showVendorToast(data?.error || 'Failed', 'error');
    }
});

async function toggleAvailability(id) {
    const data = await vendorApi(`<?php echo VENDOR_API_BASE; ?>/php/api/vendor-cars.php?action=toggle_availability&id=${id}`);
    if (data && data.success) { showVendorToast('Availability updated'); loadCars(); }
    else showVendorToast(data?.error || 'Failed', 'error');
}

async function deleteCar(id, name) {
    if (!confirm(`Delete "${name}"? This cannot be undone.`)) return;
    const data = await vendorApi(`<?php echo VENDOR_API_BASE; ?>/php/api/vendor-cars.php?action=delete&id=${id}`);
    if (data && data.success) { showVendorToast('Car deleted'); loadCars(); }
    else showVendorToast(data?.error || 'Failed', 'error');
}

loadCars();
</script>

<?php require 'vendor-footer.php'; ?>
