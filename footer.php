<?php // footer.php – shared footer for all CSNExplore pages ?>
<footer class="bg-slate-900 text-white pt-14 pb-8">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
            <!-- Brand -->
            <div data-reveal data-reveal="left">
                <img src="<?php echo BASE_PATH; ?>/images/travelhub.png" alt="CSNExplore" class="h-9 object-contain mb-4"
                     onerror="this.style.display='none'; document.getElementById('footer-logo-text').style.display='flex'"/>
                <span id="footer-logo-text" style="display:none" class="items-center gap-1.5 mb-4">
                    <span class="material-symbols-outlined text-primary text-2xl">explore</span>
                    <span class="font-serif font-black text-white text-lg tracking-tight">CSNExplore</span>
                </span>
                <p class="text-white/50 text-sm leading-relaxed mb-5">Your premium gateway to the wonders of Chhatrapati Sambhajinagar, Maharashtra.</p>
                <div class="flex gap-3 mt-6">
                    <a href="#" aria-label="Share" class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center hover:bg-primary hover:border-primary hover:scale-110 transition-all duration-300"><span class="material-symbols-outlined text-base">share</span></a>
                    <a href="mailto:supportcsnexplore@gmail.com" target="_blank" rel="noopener noreferrer" aria-label="Email Us" class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center hover:bg-primary hover:border-primary hover:scale-110 transition-all duration-300"><span class="material-symbols-outlined text-base">mail</span></a>
                    <a href="https://wa.me/918600968888" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp Us" class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center hover:bg-primary hover:border-primary hover:scale-110 transition-all duration-300">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.825c1.63.975 3.41 1.487 5.23 1.488 5.439 0 9.861-4.422 9.863-9.861.001-2.636-1.024-5.115-2.884-6.977-1.862-1.864-4.341-2.887-6.979-2.888-5.439 0-9.861-4.422-9.863 9.862 0 1.842.511 3.641 1.478 5.187l-.995 3.637 3.73-.978zm11.367-7.643c-.31-.155-1.837-.906-2.12-.108-.285.103-.55.515-.674.654-.124.14-.248.155-.558.001-.31-.155-1.31-.483-2.498-1.543-.924-.824-1.548-1.841-1.73-2.15-.181-.31-.019-.477.135-.631.14-.139.31-.36.465-.541.155-.181.206-.31.31-.515.103-.206.052-.386-.026-.541-.077-.155-.674-1.626-.924-2.228-.243-.585-.491-.504-.674-.513-.175-.008-.375-.01-.575-.01s-.525.075-.8.375c-.275.3-1.05 1.026-1.05 2.5s1.075 2.9 1.225 3.1c.15.2 2.11 3.221 5.113 4.513.714.307 1.272.49 1.706.629.718.227 1.37.195 1.886.118.575-.085 1.837-.75 2.096-1.475.258-.725.258-1.346.181-1.475-.077-.129-.283-.206-.593-.361z"/></svg>
                    </a>
                </div>
            </div>
            <!-- Quick Links -->
            <div data-reveal data-delay="2">
                <h5 class="font-bold text-sm mb-4">Quick Links</h5>
                <div class="grid grid-cols-2 gap-x-4 gap-y-2.5 text-white/50 text-sm">
                    <a href="<?php echo BASE_PATH; ?>/listing/stays"       class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Hotel Bookings</a>
                    <a href="<?php echo BASE_PATH; ?>/listing/cars"        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Car Rentals</a>
                    <a href="<?php echo BASE_PATH; ?>/listing/bikes"       class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Bike Rentals</a>
                    <a href="<?php echo BASE_PATH; ?>/listing/attractions" class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Heritage Sites</a>
                    <a href="<?php echo BASE_PATH; ?>/listing/restaurants" class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Restaurants</a>
                    <a href="<?php echo BASE_PATH; ?>/listing/buses"       class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Bus Tickets</a>
                    <a href="<?php echo BASE_PATH; ?>/blogs"                    class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Travel Guide</a>
                    <a href="<?php echo BASE_PATH; ?>/about"                    class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">About Us</a>
                    <a href="<?php echo BASE_PATH; ?>/contact"                  class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Contact Us</a>
                    <a href="<?php echo BASE_PATH; ?>/privacy"                  class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Privacy Policy</a>
                    <a href="<?php echo BASE_PATH; ?>/terms"                    class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Terms of Service</a>
                    <a href="<?php echo BASE_PATH; ?>/my-booking"               class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Track Booking</a>
                </div>
            </div>
            <!-- Contact Info -->
            <div data-reveal data-delay="3">
                <h5 class="font-bold text-sm mb-4">Contact Info</h5>
                <ul class="flex flex-col gap-4 text-white/50 text-sm">
                    <li class="flex items-start gap-3">
                        <span class="size-8 rounded-full border border-white/20 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="material-symbols-outlined text-primary text-base">location_on</span>
                        </span>
                        <a href="https://maps.google.com/?q=Behind+State+Bank+Of+India,+Plot+No.+273+Samarth+Nagar,+Central+Bus+Stand,+Chhatrapati+Sambhajinagar,+Maharashtra+431001" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors pt-1">Behind State Bank Of India, Plot No. 273 Samarth Nagar, Central Bus Stand, Chhatrapati Sambhajinagar, Maharashtra 431001</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="size-8 rounded-full border border-white/20 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-primary text-base">call</span>
                        </span>
                        <a href="tel:+918600968888" class="hover:text-primary transition-colors">+91 86009 68888</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="size-8 rounded-full border border-white/20 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-primary text-base">mail</span>
                        </span>
                        <a href="mailto:supportcsnexplore@gmail.com" class="hover:text-primary transition-colors">supportcsnexplore@gmail.com</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="size-8 rounded-full border border-white/20 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-primary text-base">schedule</span>
                        </span>
                        <span class="pt-0.5">Mon–Sat: 9am – 7pm</span>
                    </li>
                </ul>
            </div>
            <!-- Newsletter -->
            <div data-reveal data-reveal="right" data-delay="4">
                <h5 class="font-bold text-sm mb-4">Stay Updated</h5>
                <p class="text-white/50 text-sm mb-4">Get travel tips and exclusive deals in your inbox.</p>
                <form method="POST" action="subscribe.php" class="flex flex-col gap-4">
                    <input type="email" name="email" placeholder="Your email address" required
                           class="bg-white/5 border border-white/10 text-white placeholder:text-white/30 px-3 py-2.5 rounded-xl text-sm focus:outline-none focus:border-primary transition-colors"/>
                    <button type="submit" class="bg-primary text-white font-bold py-2.5 rounded-xl text-sm hover:bg-orange-600 hover:scale-[1.02] transition-all duration-200">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="border-t border-white/10 pt-6 flex flex-col md:flex-row items-center justify-between gap-3 text-white/30 text-xs">
            <p>© <?php echo date('Y'); ?> CSNExplore. All rights reserved.</p>
            <div class="flex gap-5">
                <a href="<?php echo BASE_PATH; ?>/privacy" class="hover:text-primary transition-colors">Privacy Policy</a>
                <a href="<?php echo BASE_PATH; ?>/terms"   class="hover:text-primary transition-colors">Terms of Service</a>
                <a href="<?php echo BASE_PATH; ?>/sitemap.xml" class="hover:text-primary transition-colors">Sitemap</a>
            </div>
        </div>
    </div>
</footer>

<!-- Go to Top Button -->
<button id="go-top-btn" onclick="window.scrollTo({top:0,behavior:'smooth'})"
    style="position:fixed;bottom:28px;right:24px;z-index:9999;width:48px;height:48px;border-radius:50%;background:#ec5b13;color:#fff;border:none;cursor:pointer;box-shadow:0 4px 20px rgba(236,91,19,0.5);display:flex;align-items:center;justify-content:center;opacity:0;visibility:hidden;transform:translateY(12px);transition:opacity .25s ease,visibility .25s ease,transform .25s ease;"
    aria-label="Go to top">
    <span class="material-symbols-outlined" style="font-size:22px;line-height:1;pointer-events:none;">arrow_upward</span>
</button>
<script>
(function(){
    var btn = document.getElementById('go-top-btn');
    function updateBtn() {
        if (window.scrollY > 200) {
            btn.style.opacity = '1';
            btn.style.visibility = 'visible';
            btn.style.transform = 'translateY(0)';
        } else {
            btn.style.opacity = '0';
            btn.style.visibility = 'hidden';
            btn.style.transform = 'translateY(12px)';
        }
    }
    updateBtn();
    window.addEventListener('scroll', updateBtn, {passive:true});
})();
</script>
</body>
</html>
