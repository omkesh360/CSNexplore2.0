<?php
$page_title = "Login | CSNExplore";
$extra_styles = "
    @keyframes slow-zoom { 0%{transform:scale(1)} 100%{transform:scale(1.1)} }
    .animate-slow-zoom { animation: slow-zoom 20s linear infinite alternate; }
";
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: { extend: { colors: { "primary": "#ec5b13" }, fontFamily: { "display": ["Inter","sans-serif"], "serif": ["Playfair Display","serif"] } } }
        }
    </script>
    <link rel="stylesheet" href="mobile-responsive.css"/>
    <style>
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; font-family:'Material Symbols Outlined'; font-style:normal; display:inline-block; line-height:1; }
        @keyframes slow-zoom { 0%{transform:scale(1)} 100%{transform:scale(1.1)} }
        .animate-slow-zoom { animation: slow-zoom 20s linear infinite alternate; }
    </style>
</head>
<body class="bg-white font-display text-slate-900 antialiased min-h-screen">
<script>
// If already logged in, redirect away immediately
(function(){
    var token = localStorage.getItem('csn_token');
    var user  = JSON.parse(localStorage.getItem('csn_user') || 'null');
    if (token && user) {
        try {
            var parts = token.split('.');
            if (parts.length === 3) {
                var b64 = parts[1].replace(/-/g,'+').replace(/_/g,'/'); while(b64.length%4) b64+='=';
                var p = JSON.parse(atob(b64));
                if (!p.exp || p.exp > Math.floor(Date.now()/1000)) {
                    var redirect = new URLSearchParams(window.location.search).get('redirect') || '';
                    window.location.replace(redirect || 'index.php');
                }
            }
        } catch(e) {}
    }
})();
</script>
<div class="flex min-h-screen">

    <!-- Left: Image & Branding -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-primary">
        <img src="https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=1200&q=80" alt="Travel Background"
             class="absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-80 animate-slow-zoom"/>
        <div class="absolute inset-0 bg-gradient-to-tr from-primary/70 to-transparent"></div>
        <div class="relative z-10 w-full p-12 flex flex-col justify-between">
            <a href="index" class="flex items-center gap-2">
                <img src="images/travelhub.png" alt="CSNExplore" class="h-10 brightness-0 invert"
                     onerror="this.style.display='none'; document.getElementById('login-logo-text').style.display='flex'"/>
                <span id="login-logo-text" style="display:none" class="items-center gap-1.5">
                    <span class="material-symbols-outlined text-white text-2xl">explore</span>
                    <span class="font-serif font-black text-white text-xl">CSNExplore</span>
                </span>
            </a>
            <div class="max-w-md">
                <h1 class="text-5xl font-serif font-black text-white leading-tight mb-6 tracking-tight">Your next adventure starts here.</h1>
                <p class="text-xl text-white/80 font-medium">Join thousands of travelers exploring the hidden gems of Chhatrapati Sambhajinagar and beyond.</p>
            </div>
            <div class="flex items-center gap-6 text-white/60 text-sm font-medium">
                <span class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">verified</span> 100% Secure</span>
                <span class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">support_agent</span> 24/7 Support</span>
            </div>
        </div>
    </div>

    <!-- Right: Login Form -->
    <div class="w-full lg:w-1/2 flex flex-col items-center px-6 md:px-12 pt-6 pb-12 lg:py-12 bg-slate-50 relative overflow-y-auto min-h-screen lg:min-h-0">

        <!-- Mobile Header (visible only on mobile/tablet) -->
        <div class="lg:hidden w-full mb-8">
            <header class="w-full rounded-full bg-black/90 backdrop-blur-xl border border-white/10 shadow-2xl shadow-black/40 px-4 flex items-center justify-between" style="height:52px">
                <a href="index">
                    <img src="images/travelhub.png" alt="CSNExplore" class="h-8 object-contain"/>
                </a>
                <div class="flex items-center gap-2">
                    <a href="index" class="flex items-center gap-1 text-white/70 hover:text-white text-xs font-semibold px-3 py-1.5 rounded-full hover:bg-white/10 transition-all">
                        <span class="material-symbols-outlined text-[16px]">home</span>
                        Home
                    </a>
                    
                </div>
            </header>
        </div>

        <div class="w-full max-w-md space-y-8 lg:my-auto">
            <div class="text-center lg:text-left">
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight md:text-4xl">Welcome back</h2>
                <p class="mt-3 text-slate-500 font-medium">
                    Don't have an account?
                    <a href="register" class="text-primary hover:text-orange-600 font-bold transition-all border-b-2 border-primary/20 hover:border-primary">Create one for free</a>
                </p>
            </div>

            <!-- Error Container -->
            <div id="login-error" class="hidden mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-xl">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-red-500 shrink-0">error</span>
                    <p class="text-sm text-red-700 font-medium" id="login-error-text">Something went wrong.</p>
                </div>
            </div>

            <form id="login-form" class="space-y-5">

                <div class="space-y-1">
                    <label for="email" class="text-sm font-bold text-slate-800 ml-1">Email address</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-[20px] text-slate-400 group-focus-within:text-primary transition-colors">alternate_email</span>
                        </span>
                        <input id="email" name="email" type="email" autocomplete="email" required
                               class="block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-all shadow-sm"
                               placeholder="Enter your email"/>
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="password" class="text-sm font-bold text-slate-800 ml-1">Password</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-[20px] text-slate-400 group-focus-within:text-primary transition-colors">lock</span>
                        </span>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="block w-full pl-10 pr-10 py-3 border border-slate-200 rounded-xl bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-all shadow-sm"
                               placeholder="••••••••"/>
                        <button type="button" id="toggle-password"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 transition-colors">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="h-4 w-4 text-primary focus:ring-primary border-slate-300 rounded"/>
                        <span class="text-sm font-medium text-slate-700">Stay signed in</span>
                    </label>
                    <a href="#" class="text-sm font-bold text-primary hover:text-orange-600 transition-colors">Forgot password?</a>
                </div>

                <button type="submit" id="login-btn"
                        class="w-full flex justify-center items-center gap-2 py-3.5 px-4 rounded-xl shadow-lg text-sm font-bold text-white bg-primary hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all active:scale-[0.98] hover:shadow-primary/30">
                    <span id="login-btn-text">Sign in to CSNExplore</span>
                    <span id="login-spinner" class="hidden material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                </button>
            </form>
        </div>

        <div class="mt-auto pt-8 text-center text-xs text-slate-400">
            <p>© <?php echo date('Y'); ?> CSNExplore. All rights reserved.</p>
            <div class="mt-2 flex justify-center gap-4">
                <a href="privacy" class="hover:text-primary transition-colors">Privacy Policy</a>
                <a href="terms" class="hover:text-primary transition-colors">Terms of Service</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Password toggle
    document.getElementById('toggle-password').addEventListener('click', function() {
        const pwd = document.getElementById('password');
        const icon = this.querySelector('.material-symbols-outlined');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            pwd.type = 'password';
            icon.textContent = 'visibility';
        }
    });

    // Login via fetch
    document.getElementById('login-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const errBox  = document.getElementById('login-error');
        const errText = document.getElementById('login-error-text');
        const btn     = document.getElementById('login-btn');
        const btnText = document.getElementById('login-btn-text');
        const spinner = document.getElementById('login-spinner');

        errBox.classList.add('hidden');
        btn.disabled = true;
        btnText.textContent = 'Signing in…';
        spinner.classList.remove('hidden');

        const email    = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;

        try {
            const res  = await fetch('php/api/auth.php?action=login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const data = await res.json();

            if (!res.ok) {
                errText.textContent = data.error || 'Login failed. Please try again.';
                errBox.classList.remove('hidden');
                return;
            }

            localStorage.setItem('csn_token', data.token);
            localStorage.setItem('csn_user',  JSON.stringify(data.user));

            // If admin, also set admin keys so the admin panel auth guard works
            if (data.user.role === 'admin') {
                localStorage.setItem('csn_admin_token', data.token);
                localStorage.setItem('csn_admin_user',  JSON.stringify(data.user));
            }

            // Redirect: honour ?redirect= for everyone, fall back to referrer or home
            const redirect = new URLSearchParams(window.location.search).get('redirect') || '';
            if (redirect) {
                window.location.href = redirect;
            } else if (data.user.role === 'admin') {
                window.location.href = 'admin/dashboard.php';
            } else {
                // Go back to where the user came from, or home
                const ref = document.referrer;
                if (ref && !ref.includes('login.php') && !ref.includes('register.php')) {
                    window.location.href = ref;
                } else {
                    window.location.href = 'index.php';
                }
            }
        } catch (err) {
            errText.textContent = 'Network error. Please check your connection.';
            errBox.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btnText.textContent = 'Sign in to CSNExplore';
            spinner.classList.add('hidden');
        }
    });
</script>
</body>
</html>
