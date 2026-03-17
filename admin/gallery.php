<?php
$admin_page  = 'gallery';
$admin_title = 'Image Gallery | CSNExplore Admin';
$extra_head  = '
<style>
.gallery-img { aspect-ratio:1; object-fit:cover; }
.img-card:hover .img-actions { opacity:1; }
.img-actions { opacity:0; transition:opacity .2s; }
</style>';
require 'admin-header.php';
?>

<div class="space-y-5">
    <!-- Toolbar -->
    <div class="flex flex-wrap items-center gap-3">
        <h2 class="text-sm font-bold text-slate-700">Uploaded Images</h2>
        <label class="ml-auto flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-orange-600 transition-all shadow-sm shadow-primary/20 cursor-pointer">
            <span class="material-symbols-outlined text-base">upload</span> Upload Images
            <input type="file" id="upload-input" accept="image/jpeg,image/png,image/webp,image/gif" multiple class="hidden"/>
        </label>
    </div>

    <!-- Upload progress -->
    <div id="upload-progress" class="hidden bg-white border border-slate-200 rounded-2xl p-4 text-sm text-slate-600">
        <div class="flex items-center gap-3">
            <div class="w-5 h-5 border-2 border-primary border-t-transparent rounded-full animate-spin shrink-0"></div>
            <span id="upload-status">Uploading...</span>
        </div>
    </div>

    <!-- Gallery grid -->
    <div id="gallery-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-3">
        <div class="col-span-6 text-center py-16 text-slate-400">Loading...</div>
    </div>
</div>

<?php
$extra_js = <<<'JS'
<script>
async function loadGallery() {
    var grid = document.getElementById('gallery-grid');
    var images = await api('../php/api/gallery.php');
    if (!images || !images.length) {
        grid.innerHTML = '<div class="col-span-6 text-center py-16 text-slate-400"><span class="material-symbols-outlined text-5xl block mb-3">photo_library</span>No images yet. Upload some!</div>';
        return;
    }
    grid.innerHTML = images.map(function(img) {
        var kb = Math.round(img.size / 1024);
        return '<div class="img-card relative bg-white rounded-2xl border border-slate-100 overflow-hidden group">' +
            '<img src="' + img.url + '" class="gallery-img w-full" loading="lazy" onerror="this.src=\'\'"/>' +
            '<div class="img-actions absolute inset-0 bg-black/50 flex flex-col items-center justify-center gap-2 p-2">' +
                '<button onclick="copyUrl(\'' + img.url + '\')" class="w-full bg-white text-slate-900 text-xs font-bold py-1.5 rounded-lg hover:bg-slate-100 transition-all flex items-center justify-center gap-1"><span class="material-symbols-outlined text-sm">content_copy</span>Copy URL</button>' +
                '<button onclick="deleteImage(\'' + img.filename + '\')" class="w-full bg-red-500 text-white text-xs font-bold py-1.5 rounded-lg hover:bg-red-600 transition-all flex items-center justify-center gap-1"><span class="material-symbols-outlined text-sm">delete</span>Delete</button>' +
            '</div>' +
            '<div class="p-2 border-t border-slate-100">' +
                '<p class="text-[10px] text-slate-400 truncate">' + img.filename + '</p>' +
                '<p class="text-[10px] text-slate-400">' + kb + ' KB</p>' +
            '</div>' +
        '</div>';
    }).join('');
}

function copyUrl(url) {
    navigator.clipboard.writeText(url).then(function() {
        showToast('URL copied to clipboard');
    });
}

async function deleteImage(filename) {
    if (!confirm('Delete this image? This cannot be undone.')) return;
    await api('../php/api/gallery.php', { method: 'DELETE', body: JSON.stringify({ filename: filename }) });
    loadGallery();
}

function showToast(msg) {
    var t = document.createElement('div');
    t.className = 'fixed bottom-6 right-6 bg-slate-900 text-white text-sm px-5 py-3 rounded-2xl shadow-xl z-50 transition-all';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(function(){ t.remove(); }, 2500);
}

// Upload handler
document.getElementById('upload-input').addEventListener('change', async function(e) {
    var files = Array.from(e.target.files);
    if (!files.length) return;
    var progress = document.getElementById('upload-progress');
    var status   = document.getElementById('upload-status');
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
            if (data.error) showToast('Error: ' + data.error);
        } catch(ex) {
            showToast('Upload failed: ' + ex.message);
        }
        done++;
    }
    progress.classList.add('hidden');
    e.target.value = '';
    loadGallery();
});

loadGallery();
</script>
JS;
require 'admin-footer.php';
?>
