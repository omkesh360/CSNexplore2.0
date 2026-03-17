<?php
$admin_page  = 'blogs';
$admin_title = 'Blogs | CSNExplore Admin';
require 'admin-header.php';
?>
<div class="space-y-5">
<div class="flex flex-wrap gap-3 items-center">
    <div class="flex gap-1 bg-white border border-slate-200 p-1 rounded-xl">
        <?php foreach (['all'=>'All','published'=>'Published','draft'=>'Drafts'] as $k=>$v): ?>
        <button onclick="filterBlogs('<?php echo $k; ?>')" data-status="<?php echo $k; ?>"
                class="blog-tab px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-500 hover:text-slate-900 transition-all">
            <?php echo $v; ?>
        </button>
        <?php endforeach; ?>
    </div>
    <button onclick="openBlogModal()"
            class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-orange-600 transition-all shadow-sm shadow-primary/20 ml-auto">
        <span class="material-symbols-outlined text-base">add</span> New Blog
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4" id="blogs-grid">
    <div class="col-span-3 text-center py-12 text-slate-400">Loading...</div>
</div>
</div>

<!-- Blog Modal -->
<div id="blog-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 id="blog-modal-title" class="text-base font-bold">New Blog Post</h3>
            <button onclick="closeBlogModal()" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="blog-form" class="p-6 space-y-4">
            <input type="hidden" id="blog-edit-id"/>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Title *</label>
                <input id="b-title" type="text" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Author</label>
                    <input id="b-author" type="text" value="Admin" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Category</label>
                    <input id="b-category" type="text" value="General" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Read Time</label>
                    <input id="b-read_time" type="text" placeholder="5 min read" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                    <select id="b-status" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Featured Image URL</label>
                <input id="b-image" type="url" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tags (comma-separated)</label>
                <input id="b-tags" type="text" placeholder="travel, heritage, food" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Meta Description</label>
                <input id="b-meta" type="text" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Content *</label>
                <textarea id="b-content" rows="8" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary resize-y"></textarea>
            </div>
            <div id="blog-error" class="hidden text-sm text-red-600 bg-red-50 px-4 py-2 rounded-xl"></div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeBlogModal()" class="flex-1 border border-slate-200 text-slate-600 py-2.5 rounded-xl text-sm font-semibold hover:bg-slate-50">Cancel</button>
                <button type="submit" id="blog-save-btn" class="flex-1 bg-primary text-white py-2.5 rounded-xl text-sm font-bold hover:bg-orange-600">Save Blog</button>
            </div>
        </form>
    </div>
</div>
<?php
$extra_js = <<<'JS'
<script>
var currentBlogStatus = 'all';
var editingBlogId = null;

function escHtml(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

function filterBlogs(s) {
    currentBlogStatus = s;
    document.querySelectorAll('.blog-tab').forEach(function(b){
        var active = b.dataset.status === s;
        b.classList.toggle('bg-primary', active);
        b.classList.toggle('text-white', active);
        b.classList.toggle('text-slate-500', !active);
    });
    loadBlogs();
}

async function loadBlogs() {
    var url = '../php/api/blogs.php' + (currentBlogStatus !== 'all' ? '?status=' + currentBlogStatus : '');
    var grid = document.getElementById('blogs-grid');
    grid.innerHTML = '<div class="col-span-3 text-center py-12 text-slate-400">Loading...</div>';
    var blogs = await api(url);
    if (!blogs || !blogs.length) {
        grid.innerHTML = '<div class="col-span-3 text-center py-12 text-slate-400">No blogs found</div>';
        return;
    }
    grid.innerHTML = blogs.map(function(b) {
        var sc = b.status === 'published' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500';
        var img = b.image ? '<img src="' + escHtml(b.image) + '" class="w-full h-36 object-cover"/>' : '<div class="w-full h-36 bg-slate-100 flex items-center justify-center"><span class="material-symbols-outlined text-slate-300 text-4xl">article</span></div>';
        return '<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">' +
            img +
            '<div class="p-4">' +
                '<div class="flex items-start justify-between gap-2 mb-2">' +
                    '<h3 class="text-sm font-bold text-slate-900 line-clamp-2">' + escHtml(b.title) + '</h3>' +
                    '<span class="px-2 py-0.5 rounded-full text-xs font-bold shrink-0 ' + sc + '">' + escHtml(b.status) + '</span>' +
                '</div>' +
                '<p class="text-xs text-slate-400 mb-3">' + escHtml(b.author) + ' · ' + escHtml(b.category) + (b.read_time ? ' · ' + escHtml(b.read_time) : '') + '</p>' +
                '<div class="flex gap-2">' +
                    '<button onclick="openEditBlog(' + b.id + ')" class="flex-1 text-xs font-semibold text-primary border border-primary/30 py-1.5 rounded-lg hover:bg-primary/10 transition-all">Edit</button>' +
                    '<button onclick="deleteBlog(' + b.id + ')" class="px-3 text-xs font-semibold text-red-500 border border-red-200 py-1.5 rounded-lg hover:bg-red-50 transition-all">Delete</button>' +
                '</div>' +
            '</div>' +
        '</div>';
    }).join('');
}

function openBlogModal() {
    editingBlogId = null;
    document.getElementById('blog-modal-title').textContent = 'New Blog Post';
    document.getElementById('blog-form').reset();
    document.getElementById('blog-edit-id').value = '';
    document.getElementById('blog-error').classList.add('hidden');
    document.getElementById('blog-modal').classList.remove('hidden');
}

async function openEditBlog(id) {
    editingBlogId = id;
    var b = await api('../php/api/blogs.php?id=' + id);
    if (!b) return;
    document.getElementById('blog-modal-title').textContent = 'Edit Blog';
    document.getElementById('blog-edit-id').value = id;
    document.getElementById('b-title').value = b.title || '';
    document.getElementById('b-author').value = b.author || 'Admin';
    document.getElementById('b-category').value = b.category || 'General';
    document.getElementById('b-read_time').value = b.read_time || '';
    document.getElementById('b-status').value = b.status || 'published';
    document.getElementById('b-image').value = b.image || '';
    document.getElementById('b-tags').value = (b.tags || []).join(', ');
    document.getElementById('b-meta').value = b.meta_description || '';
    document.getElementById('b-content').value = b.content || '';
    document.getElementById('blog-error').classList.add('hidden');
    document.getElementById('blog-modal').classList.remove('hidden');
}

function closeBlogModal() { document.getElementById('blog-modal').classList.add('hidden'); }

document.getElementById('blog-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    var btn = document.getElementById('blog-save-btn');
    btn.disabled = true; btn.textContent = 'Saving...';
    var tags = document.getElementById('b-tags').value.split(',').map(function(s){ return s.trim(); }).filter(Boolean);
    var data = {
        title: document.getElementById('b-title').value,
        author: document.getElementById('b-author').value,
        category: document.getElementById('b-category').value,
        read_time: document.getElementById('b-read_time').value,
        status: document.getElementById('b-status').value,
        image: document.getElementById('b-image').value,
        tags: tags,
        meta_description: document.getElementById('b-meta').value,
        content: document.getElementById('b-content').value,
    };
    try {
        var url = '../php/api/blogs.php';
        var method = 'POST';
        if (editingBlogId) { url += '?id=' + editingBlogId; method = 'PUT'; data.id = editingBlogId; }
        var res = await api(url, { method: method, body: JSON.stringify(data) });
        if (res && res.error) throw new Error(res.error);
        closeBlogModal();
        loadBlogs();
    } catch(ex) {
        var errEl = document.getElementById('blog-error');
        errEl.textContent = ex.message;
        errEl.classList.remove('hidden');
    }
    btn.disabled = false; btn.textContent = 'Save Blog';
});

async function deleteBlog(id) {
    if (!confirm('Delete this blog post?')) return;
    await api('../php/api/blogs.php?id=' + id, { method: 'DELETE' });
    loadBlogs();
}

filterBlogs('all');
</script>
JS;
require 'admin-footer.php';
?>
