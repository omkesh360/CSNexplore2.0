<?php
$page_title = "Create Account | CSNExplore";
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
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
    <style>
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; font-family:'Material Symbols Outlined'; font-style:normal; display:inline-block; line-height:1; }
        @keyframes slow-zoom { 0%{transform:scale(1)} 100%{transform:scale(1.1)} }
        .animate-slow-zoom { animation: slow-zoom 20s linear infinite alternate; }
    </style>
</head>
<body class="bg-white font-display text-slate-900 antialiased min-h-screen overflow-hidden">
<div class="flex min-h-screen">

    <!-- Left: Register Form -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center items-center px-6 md:px-12 py-12 bg-slate-50 relative overflow-y-auto">

        <!-- Mobile Logo -->
        <div class="lg:hidden absolute top-8 left-8">
            <a href="index.php" class="flex items-center gap-2">
                <img src="images/travelhub.png" alt="CSNExplore" class="h-8"
                     onerror="this.style.display='none'"/>
                <span class="font-serif font-black text-primary text-lg">CSNExplore</span>
            </a>
        </div>

        <div class="w-full max-w-md space-y-8">
            <div class="text-center lg:text-left">
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight md:text-4xl">Start your journey</h2>
                <p class="mt-3 text-slate-500 font-medium">
                    Already have an account?
                    <a href="login.php" class="text-primary hover:text-orange-600 font-bold transition-all border-b-2 border-primary/20 hover:border-primary">Sign in here</a>
                </p>
            </div>

            <!-- Error Container -->
            <div id="reg-error" class="hidden bg-red-50 border-l-4 border-red-500 p-4 rounded-xl">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-red-500 shrink-0">error</span>
                    <p class="text-sm text-red-700 font-medium" id="reg-error-text">Something went wrong.</p>
                </div>
            </div>

            <form id="registration-form" method="POST" action="auth-register.php" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(bin2hex(random_bytes(16))); ?>"/>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="first-name" class="text-xs font-bold text-slate-700 ml-1 uppercase tracking-wider">First Name</label>
                        <input id="first-name" name="first_name" type="text" required
                               class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-all shadow-sm"
                               placeholder="John"/>
                    </div>
                    <div class="space-y-1">
                        <label for="last-name" class="text-xs font-bold text-slate-700 ml-1 uppercase tracking-wider">Last Name</label>
                        <input id="last-name" name="last_name" type="text" required
                               class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-all shadow-sm"
                               placeholder="Doe"/>
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="email" class="text-xs font-bold text-slate-700 ml-1 uppercase tracking-wider">Email Address</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-[20px] text-slate-400 group-focus-within:text-primary transition-colors">alternate_email</span>
                        </span>
                        <input id="email" name="email" type="email" autocomplete="email" required
                               class="block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-all shadow-sm"
                               placeholder="john@example.com"/>
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="phone" class="text-xs font-bold text-slate-700 ml-1 uppercase tracking-wider">Phone Number</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-[20px] text-slate-400 group-focus-within:text-primary transition-colors">phone</span>
                        </span>
                        <input id="phone" name="phone" type="tel" autocomplete="tel"
                               class="block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-all shadow-sm"
                               placeholder="+91 XXXXX XXXXX"/>
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="password" class="text-xs font-bold text-slate-700 ml-1 uppercase tracking-wider">Create Password</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-[20px] text-slate-400 group-focus-within:text-primary transition-colors">lock</span>
                        </span>
                        <input id="password" name="password" type="password" required minlength="8"
                               class="block w-full pl-10 pr-10 py-3 border border-slate-200 rounded-xl bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-all shadow-sm"
                               placeholder="Min. 8 characters"/>
                        <button type="button" id="toggle-password"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 transition-colors">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                </div>

                <div class="flex items-start mt-2">
                    <input id="terms" name="terms" type="checkbox" required
                           class="h-4 w-4 mt-0.5 text-primary focus:ring-primary border-slate-300 rounded"/>
                    <label for="terms" class="ml-3 text-sm text-slate-500">
                        I agree to the <a href="terms.php" class="text-primary font-bold hover:underline">Terms of Service</a> and <a href="privacy.php" class="text-primary font-bold hover:underline">Privacy Policy</a>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full flex justify-center py-3.5 px-4 rounded-xl shadow-lg text-sm font-bold text-white bg-primary hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all active:scale-[0.98] hover:shadow-primary/30">
                        Create Account
                    </button>
                </div>
            </form>

            <div class="text-center text-xs text-slate-400 pt-4">
                <p>© <?php echo date('Y'); ?> CSNExplore. All rights reserved.</p>
            </div>
        </div>
    </div>

    <!-- Right: Image & Branding -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-primary">
        <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&q=80" alt="Travel Background"
             class="absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-80 animate-slow-zoom"/>
        <div class="absolute inset-0 bg-gradient-to-tl from-primary/70 to-transparent"></div>
        <div class="relative z-10 w-full p-12 flex flex-col justify-between items-end text-right">
            <a href="index.php" class="flex items-center gap-2">
                <img src="images/travelhub.png" alt="CSNExplore" class="h-10 brightness-0 invert"
                     onerror="this.style.display='none'; document.getElementById('reg-logo-text').style.display='flex'"/>
                <span id="reg-logo-text" style="display:none" class="items-center gap-1.5">
                    <span class="material-symbols-outlined text-white text-2xl">explore</span>
                    <span class="font-serif font-black text-white text-xl">CSNExplore</span>
                </span>
            </a>
            <div class="max-w-md">
                <h1 class="text-5xl font-serif font-black text-white leading-tight mb-6 tracking-tight">Expand your horizons.</h1>
                <p class="text-xl text-white/80 font-medium">Unlock exclusive deals, personalized itineraries, and 24/7 travel support when you join our community.</p>
            </div>
            <div class="flex items-center gap-6 text-white/60 text-sm font-medium">
                <span class="flex items-center gap-2">Join 10,000+ Travelers <span class="material-symbols-outlined text-[18px]">public</span></span>
            </div>
        </div>
    </div>
</div>

<script>
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
</script>
</body>
</html>
