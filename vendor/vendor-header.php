<?php
// vendor/vendor-header.php – shared advanced vendor layout
$vendor_page  = $vendor_page  ?? '';
$vendor_title = $vendor_title ?? 'Vendor Dashboard | CSNExplore';

if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/../php/config.php';
}

if (!defined('VENDOR_API_BASE')) {
    $_vscript  = $_SERVER['SCRIPT_NAME'] ?? '';
    $_site_base = dirname(dirname($_vscript));
    if ($_site_base === '/' || $_site_base === '\\') $_site_base = '';
    define('VENDOR_API_BASE', rtrim($_site_base, '/'));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?php echo htmlspecialchars($vendor_title); ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<script>
tailwind.config = {
    theme: { extend: {
        colors: { primary:'#ec5b13','primary-dark':'#c94d0e' },
        fontFamily: { sans:['Inter','sans-serif'] }
    }}
}
</script>
<style>
*{box-sizing:border-box}
body{font-family:'Inter',sans-serif;background:#f1f5f9;color:#1e293b}
.material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24}
.vendor-card{background:white;border:1px solid #e2e8f0;border-radius:16px;box-shadow:0 1px 4px rgba(0,0,0,.07)}
.sidebar-link{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:12px;font-size:.8rem;font-weight:600;color:#64748b;transition:all .15s;cursor:pointer;text-decoration:none}
.sidebar-link:hover{background:#f1f5f9;color:#1e293b}
.sidebar-link.active{background:linear-gradient(135deg,#fff3ed,#ffe8d6);color:#ec5b13;border:1px solid #ffd9be}
.sidebar-link.active .material-symbols-outlined{color:#ec5b13}
.sidebar-link .material-symbols-outlined{font-size:20px;color:#94a3b8;transition:color .15s}
.sidebar-link:hover .material-symbols-outlined,.sidebar-link.active .material-symbols-outlined{color:inherit}
.sidebar-section{font-size:.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;padding:0 14px;margin:16px 0 6px}
#sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:40}
#sidebar-overlay.show{display:block}
.toast{position:fixed;bottom:24px;right:24px;padding:12px 20px;border-radius:14px;font-size:.8rem;font-weight:600;color:white;z-index:9999;box-shadow:0 8px 32px rgba(0,0,0,.18);animation:toastIn .25s ease;pointer-events:none}
@keyframes toastIn{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
.badge-dot{width:8px;height:8px;border-radius:50%;display:inline-block}
::-webkit-scrollbar{width:5px;height:5px}
::-webkit-scrollbar-track{background:#f1f5f9}
::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:10px}
</style>
<?php if (!empty($extra_head)) echo $extra_head; ?>
</head>
<body>

<!-- Auth guard -->
<script>
(function(){
    var token  = localStorage.getItem('csn_vendor_token');
    var vendor = JSON.parse(localStorage.getItem('csn_vendor_user') || 'null');
    if (!token || !vendor) return (window.location.href = '<?php echo VENDOR_API_BASE; ?>/vendor/vendorlogin.php');
    window._vendorToken = token;
    window._vendorUser  = vendor;
    // Validate JWT expiry client-side
    try {
        var p = JSON.parse(atob(token.split('.')[1].replace(/-/g,'+').replace(/_/g,'/')));
        if (p.exp && p.exp < Math.floor(Date.now()/1000)) {
            localStorage.removeItem('csn_vendor_token');
            localStorage.removeItem('csn_vendor_user');
            window.location.href = 'vendorlogin.php';
        }
    } catch(e){}
})();
</script>

<!-- Mobile overlay -->
<div id="sidebar-overlay" onclick="closeSidebar()"></div>

<div class="flex h-screen overflow-hidden">

<!-- ══ Sidebar ══════════════════════════════════════════════════════════════ -->
<aside id="sidebar"
       class="w-64 bg-white border-r border-slate-200 flex flex-col shrink-0 z-50 fixed lg:static inset-y-0 left-0 -translate-x-full lg:translate-x-0 transition-transform duration-300">

    <!-- Logo -->
    <div class="h-16 flex items-center gap-3 px-5 border-b border-slate-100 shrink-0">
        <div class="w-9 h-9 bg-primary rounded-xl flex items-center justify-center shrink-0 shadow-sm shadow-primary/30">
            <span class="material-symbols-outlined text-white text-xl">storefront</span>
        </div>
        <div class="min-w-0">
            <p class="font-black text-sm text-slate-900 leading-tight truncate">CSNExplore</p>
            <p class="text-[10px] text-primary font-bold tracking-wide">Vendor Portal</p>
        </div>
        <button id="sidebar-close" onclick="closeSidebar()" class="ml-auto lg:hidden p-1 text-slate-400 hover:text-slate-600">
            <span class="material-symbols-outlined text-xl">close</span>
        </button>
    </div>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto py-4 px-3">

        <div class="sidebar-section">Overview</div>
        <a href="dashboard.php" class="sidebar-link <?php echo $vendor_page==='dashboard' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined">dashboard</span><span>Dashboard</span>
        </a>

        <div class="sidebar-section">Listings</div>
        <a href="stays.php" class="sidebar-link <?php echo $vendor_page==='stays' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined">apartment</span><span>Hotel / Stays</span>
        </a>
        <a href="rooms.php" class="sidebar-link <?php echo $vendor_page==='rooms' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined">bed</span><span>Room Types &amp; Rooms</span>
        </a>
        <a href="cars.php" class="sidebar-link <?php echo $vendor_page==='cars' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined">directions_car</span><span>Cars</span>
        </a>

        <div class="sidebar-section">Business</div>
        <a href="bookings.php" class="sidebar-link <?php echo $vendor_page==='bookings' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined">calendar_today</span>
            <span>Bookings</span>
            <span id="booking-count-badge" class="ml-auto hidden text-[10px] font-bold bg-primary text-white px-1.5 py-0.5 rounded-full"></span>
        </a>
        <a href="profile.php" class="sidebar-link <?php echo $vendor_page==='profile' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined">manage_accounts</span><span>My Profile</span>
        </a>
    </nav>

    <!-- Vendor card at bottom -->
    <div class="mx-3 mb-4 p-3 rounded-2xl bg-slate-50 border border-slate-100 shrink-0">
        <div class="flex items-center gap-2.5 mb-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary to-orange-400 flex items-center justify-center text-white font-black text-sm shrink-0" id="vendor-avatar">V</div>
            <div class="min-w-0">
                <p id="vendor-name" class="text-xs font-bold text-slate-900 truncate">Vendor</p>
                <p id="vendor-username" class="text-[10px] text-slate-400 truncate">@username</p>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-1.5">
            <a href="profile.php" class="flex items-center justify-center gap-1 py-1.5 bg-white border border-slate-200 rounded-xl text-[10px] font-bold text-slate-600 hover:bg-slate-50 transition-all">
                <span class="material-symbols-outlined text-sm">settings</span> Settings
            </a>
            <button onclick="vendorLogout()" class="flex items-center justify-center gap-1 py-1.5 bg-white border border-red-100 rounded-xl text-[10px] font-bold text-red-500 hover:bg-red-50 transition-all">
                <span class="material-symbols-outlined text-sm">logout</span> Sign Out
            </button>
        </div>
    </div>
</aside>

<!-- ══ Main area ════════════════════════════════════════════════════════════ -->
<div class="flex-1 flex flex-col overflow-hidden min-w-0">

    <!-- Topbar -->
    <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-5 z-20 shrink-0">
        <div class="flex items-center gap-3">
            <button onclick="openSidebar()" class="lg:hidden p-1.5 text-slate-500 hover:bg-slate-100 rounded-lg">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <h1 class="text-sm font-bold text-slate-900"><?php echo htmlspecialchars($vendor_title); ?></h1>
        </div>

        <div class="flex items-center gap-2">
            <div class="hidden sm:flex items-center gap-1.5 text-[10px] font-bold text-green-700 bg-green-50 border border-green-100 px-2.5 py-1 rounded-full">
                <span class="badge-dot bg-green-500 animate-pulse"></span> Active Vendor
            </div>
            <div class="h-5 w-px bg-slate-200 mx-1 hidden sm:block"></div>
            <a href="<?php echo VENDOR_API_BASE; ?>/index.php" target="_blank"
               class="flex items-center gap-1 text-[11px] font-semibold text-slate-500 hover:text-primary transition-colors px-2 py-1 rounded-lg hover:bg-slate-50">
                <span class="material-symbols-outlined text-base">open_in_new</span>
                <span class="hidden sm:inline">View Site</span>
            </a>
        </div>
    </header>

    <!-- Page content -->
    <main class="flex-1 overflow-y-auto p-5">

<script>
// ── Populate sidebar info ──────────────────────────────────────────────────────
(function(){
    var u = window._vendorUser;
    if (!u) return;
    var name = u.name || u.username || 'Vendor';
    document.getElementById('vendor-name').textContent = name;
    document.getElementById('vendor-username').textContent = '@'+(u.username||'vendor');
    document.getElementById('vendor-avatar').textContent = name.charAt(0).toUpperCase();
})();

// ── Sidebar mobile toggle ──────────────────────────────────────────────────────
function openSidebar(){
    document.getElementById('sidebar').classList.remove('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.add('show');
}
function closeSidebar(){
    document.getElementById('sidebar').classList.add('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.remove('show');
}

// ── Vendor API helper ─────────────────────────────────────────────────────────
async function vendorApi(url, options = {}) {
    try {
        options.headers = options.headers || {};
        if (!(options.body instanceof FormData)) {
            options.headers['Content-Type'] = 'application/json';
        }
        if (window._vendorToken) {
            options.headers['Authorization'] = 'Bearer ' + window._vendorToken;
        }
        var res = await fetch(url, options);
        if (res.status === 401 || res.status === 403) {
            vendorLogout(); return null;
        }
        var ct = res.headers.get('content-type');
        if (ct && ct.includes('application/json')) return await res.json();
        return null;
    } catch(e) {
        console.error('[VendorAPI]', e);
        return null;
    }
}

// ── Toast ─────────────────────────────────────────────────────────────────────
function showVendorToast(msg, type) {
    var t = document.createElement('div');
    t.className = 'toast';
    t.style.background = type==='error' ? '#dc2626' : (type==='warn' ? '#d97706' : '#1e293b');
    t.innerHTML = `<span class="material-symbols-outlined" style="font-size:16px;vertical-align:middle;margin-right:6px">${type==='error'?'error':type==='warn'?'warning':'check_circle'}</span>${msg}`;
    document.body.appendChild(t);
    setTimeout(()=>t.remove(), 3000);
}

// ── Logout ────────────────────────────────────────────────────────────────────
function vendorLogout() {
    ['csn_vendor_token','csn_vendor_user'].forEach(k=>localStorage.removeItem(k));
    window.location.href = '<?php echo VENDOR_API_BASE; ?>/vendor/vendorlogin.php';
}

// ── Currency format helper ────────────────────────────────────────────────────
function fmt(n) { return '₹' + parseFloat(n||0).toLocaleString('en-IN',{minimumFractionDigits:0,maximumFractionDigits:0}); }

// ── Confirm dialog helper ─────────────────────────────────────────────────────
function confirmAction(msg) { return confirm(msg); }

// ── Booking badge (load async) ────────────────────────────────────────────────
async function loadBookingBadge() {
    var d = await vendorApi('<?php echo VENDOR_API_BASE; ?>/php/api/vendor-stays.php?action=bookings&limit=1');
    if (d && d.total > 0) {
        var b = document.getElementById('booking-count-badge');
        if (b) { b.textContent = d.total > 99 ? '99+' : d.total; b.classList.remove('hidden'); }
    }
}
loadBookingBadge();
</script>
