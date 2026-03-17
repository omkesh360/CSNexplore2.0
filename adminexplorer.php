<?php
// Admin login gateway – accessible at /adminexplorer.php
// Redirects to admin dashboard if already logged in (checked client-side)
$page_title = 'Admin Login | CSNExplore';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?php echo $page_title; ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<script>
tailwind.config = { theme: { extend: { colors: { primary: '#ec5b13' }, fontFamily: { display: ['Inter','sans-serif'] } } } }
</script>
<style>
body { font-family: 'Inter', sans-serif; }
.material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }
</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

<script>
// Redirect if already logged in as admin
(function(){
    try {
        var token = localStorage.getItem('csn_admin_token');
        var user  = JSON.parse(localStorage.getItem('csn_admin_user') || 'null');
        if (token && user && user.role === 'admin') {
            window.location.href = 'admin/dashboard.php';
        }
    } catch(e) {}
})();
</script>

<div class="w-full max-w-md">
    <!-- Logo -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-primary rounded-2xl mb-4 shadow-lg shadow-primary/30">
            <span class="material-symbols-outlined text-white text-3xl">admin_panel_settings</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">Admin Portal</h1>
        <p class="text-slate-500 text-sm mt-1">CSNExplore Management System</p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8">
        <!-- Error -->
        <div id="err" class="hidden mb-5 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl flex items-center gap-2">
            <span class="material-symbols-outlined text-base">error</span>
            <span id="err-text">Invalid credentials</span>
        </div>

        <form id="login-form" class="space-y-5">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email Address</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-xl">mail</span>
                    <input id="email" type="email" required autocomplete="email"
                           class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                           placeholder="admin@example.com"/>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-xl">lock</span>
                    <input id="password" type="password" required autocomplete="current-password"
                           class="w-full pl-10 pr-10 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                           placeholder="••••••••"/>
                    <button type="button" id="toggle-pw" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-xl">visibility</span>
                    </button>
                </div>
            </div>
            <button type="submit" id="submit-btn"
                    class="w-full bg-primary text-white font-bold py-3 rounded-xl hover:bg-orange-600 transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
                <span id="btn-text">Sign In to Admin</span>
                <span id="btn-spinner" class="hidden material-symbols-outlined text-xl animate-spin">progress_activity</span>
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-slate-100 text-center">
            <a href="index.php" class="text-sm text-slate-500 hover:text-primary transition-colors flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-base">arrow_back</span> Back to Website
            </a>
        </div>
    </div>
</div>

<script>
document.getElementById('toggle-pw').addEventListener('click', function(){
    var pw = document.getElementById('password');
    var icon = this.querySelector('.material-symbols-outlined');
    if (pw.type === 'password') { pw.type = 'text'; icon.textContent = 'visibility_off'; }
    else { pw.type = 'password'; icon.textContent = 'visibility'; }
});

document.getElementById('login-form').addEventListener('submit', async function(e){
    e.preventDefault();
    var btn     = document.getElementById('submit-btn');
    var spinner = document.getElementById('btn-spinner');
    var btnText = document.getElementById('btn-text');
    var err     = document.getElementById('err');
    var errText = document.getElementById('err-text');

    btn.disabled = true;
    spinner.classList.remove('hidden');
    btnText.textContent = 'Signing in...';
    err.classList.add('hidden');

    try {
        var res = await fetch('php/api/auth.php?action=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email:    document.getElementById('email').value.trim(),
                password: document.getElementById('password').value,
            })
        });
        var data = await res.json();

        if (!res.ok || !data.token) {
            throw new Error(data.error || 'Invalid credentials');
        }

        if (data.user.role !== 'admin') {
            throw new Error('Access denied. Admin account required.');
        }

        localStorage.setItem('csn_admin_token', data.token);
        localStorage.setItem('csn_admin_user',  JSON.stringify(data.user));
        window.location.href = 'admin/dashboard.php';

    } catch(ex) {
        errText.textContent = ex.message;
        err.classList.remove('hidden');
        btn.disabled = false;
        spinner.classList.add('hidden');
        btnText.textContent = 'Sign In to Admin';
    }
});
</script>
</body>
</html>
