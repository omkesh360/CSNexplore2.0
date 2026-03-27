<?php
// admin/admin-header.php – shared admin layout
// Set $admin_page before including (e.g. 'dashboard', 'listings', 'bookings', 'blogs', 'users', 'content')
$admin_page = $admin_page ?? '';
$admin_title = $admin_title ?? 'Admin | CSNExplore';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
<title><?php echo htmlspecialchars($admin_title); ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<script>
tailwind.config = {
    theme: { extend: {
        colors: { primary: '#ec5b13', 'primary-dark': '#c94d0e' },
        fontFamily: { display: ['Inter','sans-serif'] }
    }}
}
</script>
<style>
body { font-family: 'Inter', sans-serif; }
.material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }
.sidebar-link.active { background: rgba(236,91,19,0.12); color: #ec5b13; }
.sidebar-link.active .material-symbols-outlined { color: #ec5b13; }
@keyframes spin { to { transform: rotate(360deg); } }
.draggable-row { cursor: move; transition: all 0.2s; }
.draggable-row.dragging { opacity: 0.5; background: #f8fafc; }
.draggable-row.drag-over { border-top: 3px solid #ec5b13; }
.drag-handle { cursor: grab; }
.drag-handle:active { cursor: grabbing; }
</style>
<?php if (!empty($extra_head)) echo $extra_head; ?>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen">

<!-- Auth guard -->
<script>
(function(){
    var token = localStorage.getItem('csn_admin_token');
    var user  = JSON.parse(localStorage.getItem('csn_admin_user') || 'null');
    if (!token || !user || user.role !== 'admin') {
        window.location.href = '../adminexplorer.php';
    }
    window._adminToken = token;
    window._adminUser  = user;
})();
</script>

<div class="flex h-screen overflow-hidden">
<!-- ── Sidebar ──────────────────────────────────────────────────────────── -->
<aside id="sidebar" class="w-64 bg-white border-r border-slate-200 flex flex-col shrink-0 z-30 transition-transform duration-300">
    <!-- Logo -->
    <div class="h-16 flex items-center gap-2.5 px-5 border-b border-slate-100">
        <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-white text-lg">explore</span>
        </div>
        <div>
            <p class="font-bold text-sm text-slate-900 leading-none">CSNExplore</p>
            <p class="text-[10px] text-slate-400 font-medium">Admin Panel</p>
        </div>
        <button id="sidebar-close" class="ml-auto md:hidden text-slate-400 hover:text-slate-600">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">
        <?php
        $nav = [
            ['href'=>'dashboard.php', 'icon'=>'dashboard',       'label'=>'Dashboard',    'key'=>'dashboard'],
            ['href'=>'listings.php',  'icon'=>'list_alt',         'label'=>'Listings',     'key'=>'listings'],
            ['href'=>'bookings.php',  'icon'=>'book_online',      'label'=>'Bookings',     'key'=>'bookings',  'badge'=>true],
            ['href'=>'blogs.php',     'icon'=>'article',          'label'=>'Blogs',        'key'=>'blogs'],
            ['href'=>'gallery.php',   'icon'=>'photo_library',    'label'=>'Gallery',      'key'=>'gallery'],
            ['href'=>'users.php',     'icon'=>'group',            'label'=>'Users',        'key'=>'users'],
            ['href'=>'content.php',   'icon'=>'edit_note',        'label'=>'Page Content', 'key'=>'content'],
            ['href'=>'performance.php', 'icon'=>'speed',          'label'=>'Performance',  'key'=>'performance'],
        ];
        foreach ($nav as $n):
            $active = ($admin_page === $n['key']) ? 'active' : '';
        ?>
        <a href="<?php echo $n['href']; ?>"
           class="sidebar-link <?php echo $active; ?> flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
            <span class="material-symbols-outlined text-xl text-slate-400"><?php echo $n['icon']; ?></span>
            <?php echo $n['label']; ?>
            <?php if (!empty($n['badge'])): ?>
            <span id="sidebar-pending-badge" class="hidden ml-auto bg-primary text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center leading-tight"></span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </nav>

    <!-- User + Logout -->
    <div class="p-4 border-t border-slate-100">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-primary text-base">person</span>
            </div>
            <div class="min-w-0">
                <p id="admin-name" class="text-xs font-semibold text-slate-900 truncate">Admin</p>
                <p id="admin-email" class="text-[10px] text-slate-400 truncate">admin@csnexplore.com</p>
            </div>
        </div>
        <button onclick="adminLogout()"
                class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-500 hover:bg-red-50 rounded-xl transition-all font-medium">
            <span class="material-symbols-outlined text-base">logout</span> Sign Out
        </button>
    </div>
</aside>

<!-- ── Main ─────────────────────────────────────────────────────────────── -->
<div class="flex-1 flex flex-col overflow-hidden">
    <!-- Top bar -->
    <header class="h-16 bg-white border-b border-slate-200 flex items-center gap-4 px-6 shrink-0">
        <button id="sidebar-toggle" class="md:hidden text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <h1 class="text-base font-bold text-slate-900"><?php echo htmlspecialchars($admin_title); ?></h1>
        <div class="ml-auto flex items-center gap-3">
            <a href="../index.php" target="_blank"
               class="text-xs text-slate-500 hover:text-primary flex items-center gap-1 transition-colors">
                <span class="material-symbols-outlined text-base">open_in_new</span> View Site
            </a>
            <div id="pending-badge" class="hidden items-center gap-1 bg-orange-50 text-primary text-xs font-bold px-3 py-1.5 rounded-full border border-primary/20">
                <span class="material-symbols-outlined text-sm">notifications</span>
                <span id="pending-count">0</span> pending
            </div>
        </div>
    </header>

<script>
// API helper - moved to header to ensure availability before inline scripts
async function api(url, options = {}) {
    try {
        console.log('[API] Calling:', url, options);
        options.headers = options.headers || {};
        options.headers['Content-Type'] = 'application/json';
        
        // Only add Authorization header if token exists
        if (window._adminToken) {
            options.headers['Authorization'] = 'Bearer ' + window._adminToken;
        }
        
        var res = await fetch(url, options);
        console.log('[API] Response status:', res.status, res.statusText);
        
        if (res.status === 401 || res.status === 403) { 
            console.error('[API] Unauthorized, logging out');
            adminLogout(); 
            return null; 
        }
        
        // Check if response is JSON
        var contentType = res.headers.get('content-type');
        console.log('[API] Content-Type:', contentType);
        
        if (contentType && contentType.includes('application/json')) {
            var data = await res.json();
            console.log('[API] Response data:', data);
            return data;
        } else {
            var text = await res.text();
            console.error('[API] Invalid response type:', contentType);
            console.error('[API] Response text:', text.substring(0, 500));
            return null;
        }
    } catch (error) {
        console.error('[API] Exception:', error);
        return null;
    }
}

// Toast - moved to header to ensure availability before inline scripts
function showAdminToast(msg, type) {
    console.log('[Toast]', type || 'info', ':', msg);
    var t = document.createElement('div');
    var bg = type === 'error' ? 'bg-red-600' : 'bg-slate-900';
    t.className = 'fixed bottom-6 right-6 ' + bg + ' text-white text-sm px-5 py-3 rounded-2xl shadow-xl z-[200]';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(function(){ t.remove(); }, 2800);
}
</script>

    <!-- Page content -->
    <main class="flex-1 overflow-y-auto p-6">
