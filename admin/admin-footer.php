    </main><!-- /main -->
</div><!-- /flex-1 -->
</div><!-- /flex -->

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
</script>
<?php if (!empty($extra_js)) echo $extra_js; ?>
</body>
</html>
