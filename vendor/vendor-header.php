<?php
// vendor/vendor-header.php – shared vendor layout
$vendor_page = $vendor_page ?? '';
$vendor_title = $vendor_title ?? 'Vendor Dashboard | CSNExplore';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
<title><?php echo htmlspecialchars($vendor_title); ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<script>
tailwind.config = {
    theme: { extend: {
        colors: { 
            primary: '#ec5b13', 
            'primary-dark': '#c94d0e',
            'vendor-bg': '#f8fafc',
            'sidebar-bg': '#ffffff'
        },
        fontFamily: { 
            sans: ['Inter','sans-serif']
        }
    }}
}
</script>
<style>
body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; }
.material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }
.sidebar-link { transition: all 0.2s; }
.sidebar-link.active { background: #f1f5f9; color: #ec5b13; }
.sidebar-link.active .material-symbols-outlined { color: #ec5b13; }
.vendor-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); }
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>
<?php if (!empty($extra_head)) echo $extra_head; ?>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen">

<!-- Auth guard -->
<script>
(function(){
    var token = localStorage.getItem('csn_vendor_token');
    var vendor = JSON.parse(localStorage.getItem('csn_vendor_user') || 'null');
    if (!token || !vendor) {
        window.location.href = 'vendorlogin.php';
    }
    window._vendorToken = token;
    window._vendorUser = vendor;
})();
</script>

<div class="flex h-screen overflow-hidden bg-vendor-bg">
<!-- ── Sidebar ──────────────────────────────────────────────────────────── -->
<aside id="sidebar" class="w-72 bg-white border-r border-slate-200 flex flex-col shrink-0 z-30">
    <!-- Logo -->
    <div class="h-20 flex items-center gap-3 px-6 border-b border-slate-50">
        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-white text-2xl">store</span>
        </div>
        <div>
            <p class="font-bold text-lg text-slate-900 leading-tight">CSNExplore</p>
            <p class="text-[11px] text-slate-500 font-medium">Vendor Portal</p>
        </div>
        <button id="sidebar-close" class="ml-auto md:hidden text-slate-400">
            <span class="material-symbols-outlined text-xl">close</span>
        </button>
    </div>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1.5 custom-scrollbar">
        <?php
        $nav = [
            ['href'=>'dashboard.php', 'icon'=>'grid_view',      'label'=>'Dashboard',    'key'=>'dashboard'],
            ['href'=>'rooms.php',     'icon'=>'hotel',          'label'=>'Rooms',        'key'=>'rooms'],
            ['href'=>'cars.php',      'icon'=>'directions_car', 'label'=>'Cars',         'key'=>'cars'],
        ];
        foreach ($nav as $n):
            $active = ($vendor_page === $n['key']) ? 'active' : '';
        ?>
        <a href="<?php echo $n['href']; ?>"
           class="sidebar-link <?php echo $active; ?> flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 group">
            <span class="material-symbols-outlined text-[20px]"><?php echo $n['icon']; ?></span>
            <span class="flex-1"><?php echo $n['label']; ?></span>
        </a>
        <?php endforeach; ?>
    </nav>

    <!-- Vendor Profile -->
    <div class="mx-4 mb-6 p-4 bg-slate-50/80 border border-slate-100 rounded-3xl">
        <div class="flex items-center gap-3 mb-4">
            <div class="relative">
                <div class="w-10 h-10 bg-white border-2 border-primary/20 rounded-2xl flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-primary text-xl">store</span>
                </div>
                <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full"></div>
            </div>
            <div class="min-w-0">
                <p id="vendor-name" class="text-[13px] font-bold text-slate-900 truncate">Vendor</p>
                <p id="vendor-username" class="text-[10px] text-slate-400 truncate tracking-tight">@username</p>
            </div>
        </div>
        <button onclick="vendorLogout()"
                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-red-500 hover:bg-red-50 hover:border-red-100 rounded-2xl transition-all font-bold text-xs shadow-sm">
            <span class="material-symbols-outlined text-base">logout</span> Sign Out
        </button>
    </div>
</aside>

<!-- ── Main ─────────────────────────────────────────────────────────────── -->
<div class="flex-1 flex flex-col overflow-hidden relative">
    <!-- Top bar -->
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 z-20">
        <div class="flex items-center gap-4">
            <button id="sidebar-toggle" class="md:hidden text-slate-500 hover:text-slate-700">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <h1 class="text-base font-bold text-slate-900"><?php echo htmlspecialchars($vendor_title); ?></h1>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="hidden lg:flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 rounded-full border border-green-100">
                <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-[10px] font-bold">Active</span>
            </div>

            <div class="h-6 w-px bg-slate-200"></div>

            <a href="../index.php" target="_blank"
               class="text-xs font-semibold text-slate-500 hover:text-primary transition-all flex items-center gap-1.5">
                <span class="material-symbols-outlined text-base">open_in_new</span> View Site
            </a>
        </div>
    </header>

<script>
// Update vendor info in sidebar
if (window._vendorUser) {
    document.getElementById('vendor-name').textContent = window._vendorUser.name || 'Vendor';
    document.getElementById('vendor-username').textContent = '@' + (window._vendorUser.username || 'vendor');
}

// API helper for vendor
async function vendorApi(url, options = {}) {
    try {
        options.headers = options.headers || {};
        options.headers['Content-Type'] = 'application/json';
        
        if (window._vendorToken) {
            options.headers['Authorization'] = 'Bearer ' + window._vendorToken;
        }
        
        var res = await fetch(url, options);
        
        if (res.status === 401 || res.status === 403) { 
            vendorLogout(); 
            return null; 
        }
        
        var contentType = res.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return await res.json();
        }
        return null;
    } catch (error) {
        console.error('[Vendor API] Exception:', error);
        return null;
    }
}

// Toast notification
function showVendorToast(msg, type) {
    var t = document.createElement('div');
    var bg = type === 'error' ? 'bg-red-600' : 'bg-slate-900';
    t.className = 'fixed bottom-6 right-6 ' + bg + ' text-white text-sm px-5 py-3 rounded-2xl shadow-xl z-[200]';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(function(){ t.remove(); }, 2800);
}

// Logout
function vendorLogout() {
    localStorage.removeItem('csn_vendor_token');
    localStorage.removeItem('csn_vendor_user');
    window.location.href = 'vendorlogin.php';
}
</script>

    <!-- Page content -->
    <main class="flex-1 overflow-y-auto p-6">
