    </main><!-- /main -->
</div><!-- /flex-1 -->
</div><!-- /flex -->

<!-- ── Gallery Picker Modal (shared) ──────────────────────────────────── -->
<div id="gallery-picker-modal" class="hidden fixed inset-0 bg-black/60 z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl flex flex-col" style="max-height:85vh">
        <!-- Header -->
        <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 shrink-0">
            <span class="material-symbols-outlined text-primary">photo_library</span>
            <h3 class="font-bold text-base">Select Image</h3>
            <div class="ml-auto flex items-center gap-2">
                <label class="flex items-center gap-1.5 bg-primary text-white px-3 py-1.5 rounded-xl text-xs font-bold hover:bg-orange-600 cursor-pointer transition-all">
                    <span class="material-symbols-outlined text-sm">upload</span> Upload New
                    <input type="file" id="picker-upload-input" accept="image/jpeg,image/png,image/webp,image/gif" multiple class="hidden"/>
                </label>
                <button onclick="closeGalleryPicker()" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-all">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>
        </div>
        <!-- Search -->
        <div class="px-5 py-3 border-b border-slate-100 shrink-0">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">search</span>
                <input id="picker-search" type="text" placeholder="Filter by filename..."
                       class="w-full border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
            </div>
        </div>
        <!-- Grid -->
        <div id="picker-grid" class="flex-1 overflow-y-auto p-4 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
            <div class="col-span-5 text-center py-12 text-slate-400">Loading...</div>
        </div>
        <!-- Upload progress -->
        <div id="picker-upload-progress" class="hidden px-5 py-3 border-t border-slate-100 text-sm text-slate-600 flex items-center gap-3 shrink-0">
            <div class="w-4 h-4 border-2 border-primary border-t-transparent rounded-full animate-spin shrink-0"></div>
            <span id="picker-upload-status">Uploading...</span>
        </div>
    </div>
</div>

<script>
// Populate user info
(function(){
    var u = window._adminUser;
    if (u) {
        var n = document.getElementById('admin-name');
        var e = document.getElementById('admin-email');
        if (n) n.textContent = u.name || 'Admin';
        if (e) e.textContent = u.email || '';
    }
})();

// Sidebar toggle (mobile)
document.getElementById('sidebar-toggle')?.addEventListener('click', function(){
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
});
document.getElementById('sidebar-close')?.addEventListener('click', function(){
    document.getElementById('sidebar').classList.add('-translate-x-full');
});

// Logout
function adminLogout() {
    localStorage.removeItem('csn_admin_token');
    localStorage.removeItem('csn_admin_user');
    window.location.href = '../adminexplorer.php';
}

// API helper
async function api(url, options = {}) {
    options.headers = Object.assign({ 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + window._adminToken }, options.headers || {});
    var res = await fetch(url, options);
    if (res.status === 401 || res.status === 403) { adminLogout(); return null; }
    return res.json();
}

// Toast
function showAdminToast(msg, type) {
    var t = document.createElement('div');
    var bg = type === 'error' ? 'bg-red-600' : 'bg-slate-900';
    t.className = 'fixed bottom-6 right-6 ' + bg + ' text-white text-sm px-5 py-3 rounded-2xl shadow-xl z-[200]';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(function(){ t.remove(); }, 2800);
}

// Load pending bookings count
async function loadPendingCount() {
    try {
        var data = await api('../php/api/bookings.php?status=pending');
        if (data && Array.isArray(data) && data.length > 0) {
            document.getElementById('pending-count').textContent = data.length;
            document.getElementById('pending-badge').classList.remove('hidden');
            document.getElementById('pending-badge').classList.add('flex');
        }
    } catch(e) {}
}
loadPendingCount();

// ── Gallery Picker ──────────────────────────────────────────────────────
var _pickerCallback = null;
var _pickerImages   = [];

function openGalleryPicker(callback) {
    _pickerCallback = callback;
    document.getElementById('gallery-picker-modal').classList.remove('hidden');
    document.getElementById('picker-search').value = '';
    loadPickerImages();
}

function closeGalleryPicker() {
    document.getElementById('gallery-picker-modal').classList.add('hidden');
    _pickerCallback = null;
}

async function loadPickerImages() {
    var grid = document.getElementById('picker-grid');
    grid.innerHTML = '<div class="col-span-5 text-center py-12 text-slate-400"><div class="w-6 h-6 border-2 border-primary border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>Loading...</div>';
    _pickerImages = await api('../php/api/gallery.php') || [];
    renderPickerGrid(_pickerImages);
}

function renderPickerGrid(images) {
    var grid = document.getElementById('picker-grid');
    if (!images.length) {
        grid.innerHTML = '<div class="col-span-5 text-center py-12 text-slate-400"><span class="material-symbols-outlined text-4xl block mb-2">photo_library</span>No images yet. Upload some first.</div>';
        return;
    }
    grid.innerHTML = images.map(function(img) {
        // Use relative path so DB stores consistent relative URLs
        var relPath = 'images/uploads/' + img.filename;
        var safeUrl = img.url.replace(/'/g, "\\'");
        var safePath = relPath.replace(/'/g, "\\'");
        return '<div class="relative group cursor-pointer rounded-xl overflow-hidden border-2 border-transparent hover:border-primary transition-all bg-slate-50" onclick="selectPickerImage(\'' + safePath + '\')">' +
            '<img src="' + safeUrl + '" class="w-full aspect-square object-cover" loading="lazy" onerror="this.parentElement.style.display=\'none\'"/>' +
            '<div class="absolute inset-0 bg-primary/30 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center">' +
                '<span class="material-symbols-outlined text-white text-3xl drop-shadow">check_circle</span>' +
            '</div>' +
            '<div class="absolute bottom-0 left-0 right-0 bg-black/60 px-1.5 py-1 opacity-0 group-hover:opacity-100 transition-all">' +
                '<p class="text-white text-[9px] truncate">' + img.filename + '</p>' +
            '</div>' +
        '</div>';
    }).join('');
}

function selectPickerImage(path) {
    if (_pickerCallback) _pickerCallback(path);
    closeGalleryPicker();
}

// Picker search filter
document.getElementById('picker-search').addEventListener('input', function() {
    var q = this.value.toLowerCase();
    var filtered = _pickerImages.filter(function(img){ return img.filename.toLowerCase().includes(q); });
    renderPickerGrid(filtered);
});

// Picker upload
document.getElementById('picker-upload-input').addEventListener('change', async function(e) {
    var files = Array.from(e.target.files);
    if (!files.length) return;
    var progress = document.getElementById('picker-upload-progress');
    var status   = document.getElementById('picker-upload-status');
    progress.classList.remove('hidden');
    var done = 0;
    for (var f of files) {
        status.textContent = 'Uploading ' + (done+1) + ' of ' + files.length + ': ' + f.name;
        var fd = new FormData();
        fd.append('file', f);
        try {
            var res = await fetch('../php/api/upload.php', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + window._adminToken },
                body: fd
            });
            var data = await res.json();
            if (data.error) showAdminToast('Error: ' + data.error, 'error');
        } catch(ex) {
            showAdminToast('Upload failed', 'error');
        }
        done++;
    }
    progress.classList.add('hidden');
    e.target.value = '';
    loadPickerImages();
});

// Close picker on backdrop click
document.getElementById('gallery-picker-modal').addEventListener('click', function(e) {
    if (e.target === this) closeGalleryPicker();
});
</script>
<?php if (!empty($extra_js)) echo $extra_js; ?>
</body>
</html>
