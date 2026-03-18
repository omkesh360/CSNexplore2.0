<?php
$page_title  = "About Us | CSNExplore – Chhatrapati Sambhajinagar";
$current_page = "about.php";
$extra_styles = "
    .glass-panel { background:rgba(255,255,255,0.07); backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.12); }
";
require 'header.php';
?>

<main>
<!-- Hero -->
<section class="relative h-[480px] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img alt="Ellora Caves Heritage Site" class="w-full h-full object-cover"
             src="https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=1600&q=80"/>
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-[#0a0705]"></div>
    </div>
    <!-- Breadcrumb at very top of hero -->
    <div class="absolute top-0 left-0 right-0 z-20 pt-5">
        <div class="max-w-7xl mx-auto px-6 flex items-center gap-2 text-sm text-white/60 flex-wrap">
            <a href="index.php" class="hover:text-white transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-base">home</span>Home
            </a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-white font-semibold">About Us</span>
        </div>
    </div>
    <div class="relative z-10 text-center px-4 max-w-4xl">
        <span class="inline-block px-4 py-1.5 rounded-full bg-primary/20 text-primary font-bold text-xs uppercase tracking-widest mb-6">Established 2012</span>
        <h2 class="text-5xl md:text-6xl font-serif font-black mb-6 text-white leading-tight">
            Discover the Soul of <span class="text-primary">Maharashtra</span>
        </h2>
        <p class="text-lg text-white/70 max-w-2xl mx-auto leading-relaxed">
            Bridging the gap between ancient heritage and modern exploration in the heart of Chhatrapati Sambhajinagar.
        </p>
    </div>
</section>

<!-- Our Story -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-16 items-center">
        <div class="space-y-6">
            <h3 class="text-3xl md:text-4xl font-serif font-bold text-slate-900">Our Story</h3>
            <div class="w-20 h-1.5 bg-primary rounded-full"></div>
            <p class="text-slate-600 text-lg leading-relaxed">
                We began our journey with a simple goal: to showcase the architectural marvels of Ellora and Ajanta to the world. What started as a small local passion project has grown into the premier travel portal of the region.
            </p>
            <p class="text-slate-600 text-lg leading-relaxed">
                Our deep-rooted connection to this historic land drives us to provide authentic and immersive experiences. We don't just book tours; we curate memories that last a lifetime, connecting travelers with the spiritual and cultural pulse of Chhatrapati Sambhajinagar.
            </p>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-4">
                <img alt="Ajanta Caves" class="rounded-xl h-64 w-full object-cover shadow-2xl"
                     src="https://lh3.googleusercontent.com/aida-public/AB6AXuBF_R0V30Dh4YkFUxDOjhVDzUF271-ci2ylVPn_7zievZVdJAI8RsILb_bFfZMnWH4KlNwlTT9m65v1Q7xrINmrlW2rVXxFO8-g_CfGp81zdaPYjBK0XhOd4m-lWjJMNQbxQbwYzuhVwYQvGC3w9wZ9IWgHOilmKd97q-0jw0nOoLNCx402BN4apDGh7MkA_S0rBvad2YS6_uSe96DmuELCYRpnS_vg2tuNfZV4F9gQ8h8CU_ea6ivDt_1d-9fMhVegXOpRnG9gS3vD"/>
                <img alt="Kailasa Temple" class="rounded-xl h-48 w-full object-cover shadow-2xl"
                     src="https://lh3.googleusercontent.com/aida-public/AB6AXuBiWWm7tleVlV89m0OpAU8sE1C0LJ0Vy5o_LEMdFCHZbUZdFV7d2AKsl0pmdjpWgZm1zScw2UoxsWF3y3MUHiPhR8T31U5X7if8AVvRJvShq63hPDBpR_NmTTdEesW5ClqUPUfdrBp1gTods8Ah60teqAvNOQ5iJVaCdH_eX3up3bcKsOofnfdbLX7iA7K-_TpW1GVcjq3LkVIwLbxuK8IqQjyjLw73zvP23TCV8NNouA4CR15q6IwMk0R9avBfJ8_pMD_AYJgCdcoA"/>
            </div>
            <div class="pt-8 space-y-4">
                <img alt="Bibi Ka Maqbara" class="rounded-xl h-48 w-full object-cover shadow-2xl"
                     src="https://lh3.googleusercontent.com/aida-public/AB6AXuBs5mILVmU084j65DcidaLjrnTIKOz3DRU0L6wGZ70yfgayEfQ8xMP8d7JfuBznWIZuVwEmcd8hF5kW3OTJ6cJCVPIK6DMfk4BFnnhfF7CshPcgDO42W9TkL5IXycpQiqaNDc6VuJMMz2VAOTJlyAdeLb0A_ct7lg64X-efrTyVXpvgJr9HyGz6D30dFcTod5kFk_sg739EDjwOqwdknDwmKL5r2-Tm2hR07PPPjMANK0jtXrdGwv83JLh8PWeI0u2_KAVgdmelIwhj"/>
                <img alt="Aurangabad Gates" class="rounded-xl h-64 w-full object-cover shadow-2xl"
                     src="https://lh3.googleusercontent.com/aida-public/AB6AXuDMGkF-GU5txWzIac4pjFsFvkh0BsvYWpIGVpXn7URMLEZlQECUQPWhd8o5RZiQ4qfszSI1f5H3IaUAoPYR8MB8Hg_Kn4ltvxwcKRKbU09xgeEJ-ZvRJGetw-PsfSL3CXvd8pnMzLNzzqOfKkgRHBtbQd6VYy8Pv9Onoaty7w6V7GcbkdcdLfqBxAB53gerDL0Vm35WP9vhLBQiOkFN04jyPqXPwAwMSyPwRI2DWdYbtrubM7moA__q2c338atruVI-l53S36h5N6O6"/>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<section class="py-20 bg-primary/5">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-8">
            <div class="glass-panel p-10 rounded-3xl shadow-xl hover:-translate-y-1 transition-all">
                <div class="w-16 h-16 bg-primary/20 rounded-2xl flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-primary text-4xl">rocket_launch</span>
                </div>
                <h4 class="text-2xl font-bold mb-4 text-slate-900">Our Mission</h4>
                <p class="text-slate-600 leading-relaxed text-lg">
                    To deliver world-class tourism services that honor the sanctity and history of our heritage sites, ensuring every guest experiences the true hospitality of Maharashtra through personalized care and expert guidance.
                </p>
            </div>
            <div class="glass-panel p-10 rounded-3xl shadow-xl hover:-translate-y-1 transition-all">
                <div class="w-16 h-16 bg-primary/20 rounded-2xl flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-primary text-4xl">visibility</span>
                </div>
                <h4 class="text-2xl font-bold mb-4 text-slate-900">Our Vision</h4>
                <p class="text-slate-600 leading-relaxed text-lg">
                    To become the global gateway to Chhatrapati Sambhajinagar's wonders, setting the benchmark for sustainable and culturally-rich travel while empowering local communities and preserving our historic legacy.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h3 class="text-4xl font-serif font-bold mb-4 text-slate-900">Why Choose Us</h3>
            <p class="text-slate-500 max-w-2xl mx-auto text-lg">We pride ourselves on excellence, local expertise, and a traveler-first approach.</p>
        </div>
        <div class="grid md:grid-cols-4 gap-6">
            <?php
            $features = [
                ['icon' => 'verified_user',  'title' => 'Local Expertise',      'desc' => 'Born and raised in the city, we know the hidden gems.'],
                ['icon' => 'support_agent',  'title' => '24/7 Support',         'desc' => 'Our team is always available to ensure smooth travels.'],
                ['icon' => 'history_edu',    'title' => 'Certified Guides',     'desc' => 'History experts with official state certifications.'],
                ['icon' => 'payments',       'title' => 'Transparent Pricing',  'desc' => 'No hidden costs. Fair value for premium experiences.'],
            ];
            foreach ($features as $f): ?>
            <div class="p-8 rounded-2xl bg-white shadow-sm border border-slate-100 text-center">
                <span class="material-symbols-outlined text-5xl text-primary mb-4 block"><?php echo $f['icon']; ?></span>
                <h5 class="font-bold text-lg mb-2 text-slate-900"><?php echo $f['title']; ?></h5>
                <p class="text-sm text-slate-500"><?php echo $f['desc']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Our Team -->
<section class="py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-6 text-center">
        <h3 class="text-4xl font-serif font-bold mb-16 text-slate-900">Meet Our Team</h3>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-12">
            <?php
            $team = [
                ['name' => 'Vikram Deshmukh', 'role' => 'Founder & CEO',          'img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCsnrkajTegB7OE7ogFsxQd-yp3IHEnGJiv7KaRaBGcoPOU1c_4OPdxB1Mie6H-sXgJ2-HagwHIXL6g_XZZEszYSUvgAdOzjWC0AWa7qqFZW3mG5u-fgbri35uVscaphV83v9xdBBJRkAJhSUn21RMC4LgKsPGw7TUMQyeKpkml9bDy3rXIVrVpAVQ1_klKGjESBwHJ4ZaB_hLleYuqkzBOXfTdUFRfB6kmyQ1j3TSm4hk4wfDzGfzLnjYVMMhJnHlvpJegGwc599Tm'],
                ['name' => 'Anjali Kulkarni',  'role' => 'Head of Operations',    'img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBqyf5oqKkoQpldJRqEy4HNl9L5gL8d-ZCq-2g7y4RuzYSUncsLP2Yqdurlt-hdqs2HB796sy_GixlSCm9YJrX-Nphyf6Gor1o7NdmmVgzBhKfY99ou3dMx_ZJmDbvmkn52cqpQIHjc_CvawYGhdSSk0XKgnESohX1LN96Zo7w42nw4kTcOVGPrItMy4Y6-W_PjrfTGgE937RhSuhDC0_6Am2-d7L-MGvsjSnNuh8O7JDrhY68RavVbkIjPvEoEw3xzZKHDplhrE6mq'],
                ['name' => 'Siddharth More',   'role' => 'Chief Heritage Expert', 'img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuC1FnN4wG_3219c-_oWF1oeckh0vPjEK8NjBcqdKhRWb4upFC1M9YQMrEXNGKpQGBtR4hayr41KEnvXFxWue7RtSCxy8CX7_eSbLgBGNs1aZidpUW9HdKtf9O2hvZm0IKNVW7P8NhfUpAtUAmx5e-CBO9E-43V3mqiww5TvkjHxdIJmuC7Dgr-AEC5iQhfkZSz7EZ9kacWiKK3YlTjIFU87Pkhom-7YKDSaSL_clufRU_y5eXyjABF-iD_nLdUWp0Ij03tkEyNJAf0X'],
            ];
            foreach ($team as $member): ?>
            <div class="group">
                <div class="relative overflow-hidden rounded-2xl mb-6">
                    <img alt="<?php echo htmlspecialchars($member['name']); ?>"
                         class="w-full h-80 object-cover group-hover:scale-105 transition-transform duration-500"
                         src="<?php echo $member['img']; ?>"/>
                    <div class="absolute bottom-4 left-4 right-4 p-4 glass-panel rounded-xl text-left">
                        <h6 class="font-bold text-lg text-white"><?php echo htmlspecialchars($member['name']); ?></h6>
                        <p class="text-sm text-primary font-semibold"><?php echo htmlspecialchars($member['role']); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="rounded-[3rem] bg-primary p-12 md:p-20 text-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <img alt="Heritage Pattern" class="w-full h-full object-cover"
                 src="https://lh3.googleusercontent.com/aida-public/AB6AXuCWlz272LF0IoJnEIdtroG6WMXcqOoHkudt7yBJ19PqufEI-7ISJ_zn1G8dnCkJGSuT6mi0qxXFqNrcEONccUuLNshgvjmSshh-Jz76I3m8QJW8nzY_MGhbCfHxiOLiE9UG83G9Efw1DjgBGtP9SOV-kceOiHL4wkeDxyVEwFUGwifUJtUlQYU2eTj5HdEm8TGa_qGjUtgI1dHODPNb15eQQRY_ZE4ATENcXErfoYbMGNR8QQw1xclUhx8RkpuhbA4h7Fv6uWkq4hil"/>
        </div>
        <div class="relative z-10">
            <h3 class="text-3xl md:text-5xl font-serif font-black text-white mb-8">Ready to explore history?</h3>
            <p class="text-white/80 text-lg mb-10 max-w-xl mx-auto">Join us for a journey through time and culture. Book your customized heritage tour today.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="listing.php?type=stays" class="bg-white text-primary px-8 py-4 rounded-full font-bold text-lg shadow-xl hover:bg-slate-100 transition-colors">Start Your Journey</a>
                <a href="contact.php" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white/10 transition-colors">Contact Support</a>
            </div>
        </div>
        </div>
    </div>
</section>
</main>

<?php require 'footer.php'; ?>
