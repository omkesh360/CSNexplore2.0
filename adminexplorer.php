<?php
// adminexplorer.php – Admin Login Page
$page_title = "Admin Login | CSNExplore";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
<title><?php echo htmlspecialchars($page_title); ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<script>
tailwind.config = {
    theme: { extend: {
        colors: { 
            primary: '#ec5b13', 
            'primary-dark': '#c94d0e'
        },
        fontFamily: { 
            sans: ['Inter','sans-serif']
        }
    }}
}
</script>
<style>
body { font-family: 'Inter', sans-serif; }
.material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }
</style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 min-h-screen flex items-center justify-center p-4">

<!-- Redirect if already logged in -->
<script>
(function(){
    var token = localStorage.getItem('csn_admin_token');
    var user  = JSON.parse(localStorage.getItem('csn_admin_user') || 'null');
    if (token && user && user.role === 'admin') {
        window.location.href = 'admin/dashboard.php';
    }
})();
</script>

<div class="w-full max-w-md">
    <!-- Logo & Title -->
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-primary rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-primary/30">
            <span class="material-symbols-outlined text-white text-3xl">admin_panel_settings</span>
        </div>
        <h1 class="text-3xl font-bold text-white mb-2">Admin Portal</h1>
        <p class="text-slate-400 text-sm">Sign in to access the dashboard</p>
    </div>

    <!-- Login Card -->
    <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 shadow-2xl">
        <form id="admin-login-form" class="space-y-5">
            <!-- Email -->
            <div>
                <label class="block text-white text-sm font-semibold mb-2">Email Address</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl">mail</span>
                    <input type="email" id="email" required
                           class="w-full bg-white/10 border border-white/20 rounded-xl pl-12 pr-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                           placeholder="admin@csnexplore.com"/>
                </div>
            </div>

            <!-- Password -->
            <div>
                <label class="block text-white text-sm font-semibold mb-2">Password</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl">lock</span>
                    <input type="password" id="password" required
                           class="w-full bg-white/10 border border-white/20 rounded-xl pl-12 pr-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                           placeholder="Enter your password"/>
                </div>
            </div>

            <!-- Error Message -->
            <div id="error-msg" class="hidden bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">error</span>
                <span id="error-text"></span>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submit-btn"
                    class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-3 px-6 rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-primary/30">
                <span class="material-symbols-outlined">login</span>
                <span>Sign In</span>
            </button>
        </form>

        <!-- Back to Site -->
        <div class="mt-6 text-center">
            <a href="index.php" class="text-slate-300 hover:text-white text-sm flex items-center justify-center gap-1 transition-colors">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                Back to Website
            </a>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center mt-8 text-slate-400 text-xs">
        <p>&copy; <?php echo date('Y'); ?> CSNExplore. All rights reserved.</p>
    </div>
</div>

<script>
document.getElementById('admin-login-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    var email = document.getElementById('email').value.trim();
    var password = document.getElementById('password').value;
    var errorMsg = document.getElementById('error-msg');
    var errorText = document.getElementById('error-text');
    var submitBtn = document.getElementById('submit-btn');
    
    // Hide previous errors
    errorMsg.classList.add('hidden');
    
    // Disable button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div><span>Signing in...</span>';
    
    try {
        var response = await fetch('php/api/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email, password: password })
        });
        
        var data = await response.json();
        
        if (response.ok && data.token && data.user) {
            // Check if user is admin
            if (data.user.role !== 'admin') {
                errorText.textContent = 'Access denied. Admin privileges required.';
                errorMsg.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span class="material-symbols-outlined">login</span><span>Sign In</span>';
                return;
            }
            
            // Store admin credentials
            localStorage.setItem('csn_admin_token', data.token);
            localStorage.setItem('csn_admin_user', JSON.stringify(data.user));
            
            // Also store in regular storage for site-wide auth
            localStorage.setItem('csn_token', data.token);
            localStorage.setItem('csn_user', JSON.stringify(data.user));
            
            // Redirect to dashboard
            window.location.href = 'admin/dashboard.php';
        } else {
            errorText.textContent = data.error || 'Invalid credentials. Please try again.';
            errorMsg.classList.remove('hidden');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span class="material-symbols-outlined">login</span><span>Sign In</span>';
        }
    } catch (error) {
        console.error('Login error:', error);
        errorText.textContent = 'Connection error. Please try again.';
        errorMsg.classList.remove('hidden');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<span class="material-symbols-outlined">login</span><span>Sign In</span>';
    }
});
</script>

</body>
</html>
