<?php
// Vendor login page – accessible at /vendor/vendorlogin.php
$page_title = 'Vendor Login | CSNExplore';
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
// Redirect if already logged in as vendor
(function(){
    try {
        var token = localStorage.getItem('csn_vendor_token');
        var vendor = JSON.parse(localStorage.getItem('csn_vendor_user') || 'null');
        if (token && vendor) {
            window.location.href = 'dashboard.php';
        }
    } catch(e) {}
})();
</script>

<div class="w-full max-w-md">
    <!-- Logo -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-primary rounded-2xl mb-4 shadow-lg shadow-primary/30">
            <span class="material-symbols-outlined text-white text-3xl">store</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">Vendor Portal</h1>
        <p class="text-slate-500 text-sm mt-1">CSNExplore Vendor Management</p>
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
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Username</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-xl">person</span>
                    <input id="username" type="text" required autocomplete="username"
                           class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                           placeholder="Enter your username"/>
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
                <span id="btn-text">Sign In</span>
                <span id="btn-spinner" class="hidden material-symbols-outlined text-xl animate-spin">progress_activity</span>
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-slate-100">
            <p class="text-xs text-slate-500 text-center mb-3">Don't have an account?</p>
            <p class="text-xs text-slate-600 text-center">Contact admin to create your vendor account</p>
        </div>

        <div class="mt-4 text-center">
            <a href="../index.php" class="text-sm text-slate-500 hover:text-primary transition-colors flex items-center justify-center gap-1">
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
        var res = await fetch('../php/api/vendor-auth-simple.php?action=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                username: document.getElementById('username').value.trim(),
                password: document.getElementById('password').value,
            })
        });
        var data = await res.json();

        if (!res.ok || !data.token) {
            throw new Error(data.error || 'Invalid credentials');
        }

        localStorage.setItem('csn_vendor_token', data.token);
        localStorage.setItem('csn_vendor_user',  JSON.stringify(data.vendor));
        window.location.href = 'dashboard.php';

    } catch(ex) {
        errText.textContent = ex.message;
        err.classList.remove('hidden');
        btn.disabled = false;
        spinner.classList.add('hidden');
        btnText.textContent = 'Sign In';
    }
});
</script>
</body>
</html>
