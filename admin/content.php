<?php
$admin_page  = 'content';
$admin_title = 'Page Content | CSNExplore Admin';
require 'admin-header.php';
?>
<div class="space-y-5">
<!-- Tabs -->
<div class="flex gap-1 bg-white border border-slate-200 p-1 rounded-xl w-fit">
    <button onclick="switchSection('about')" data-section="about"
            class="content-tab px-4 py-1.5 rounded-lg text-xs font-semibold text-slate-500 hover:text-slate-900 transition-all">
        About Us
    </button>
    <button onclick="switchSection('contact')" data-section="contact"
            class="content-tab px-4 py-1.5 rounded-lg text-xs font-semibold text-slate-500 hover:text-slate-900 transition-all">
        Contact Info
    </button>
    <button onclick="switchSection('homepage')" data-section="homepage"
            class="content-tab px-4 py-1.5 rounded-lg text-xs font-semibold text-slate-500 hover:text-slate-900 transition-all">
        Homepage
    </button>
    <button onclick="switchSection('messages')" data-section="messages"
            class="content-tab px-4 py-1.5 rounded-lg text-xs font-semibold text-slate-500 hover:text-slate-900 transition-all">
        Messages
    </button>
</div>

<!-- About section -->
<div id="section-about" class="space-y-4">
    <div class="bg-white rounded-2xl border border-slate-100 p-6 space-y-4">
        <h2 class="text-base font-bold">About Us Content</h2>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Mission Statement</label>
            <textarea id="about-mission" rows="3" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary resize-none"></textarea>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Vision</label>
            <textarea id="about-vision" rows="2" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary resize-none"></textarea>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">About Description</label>
            <textarea id="about-description" rows="4" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary resize-none"></textarea>
        </div>
        <div id="about-success" class="hidden text-sm text-green-700 bg-green-50 px-4 py-2 rounded-xl">Saved successfully!</div>
        <button onclick="saveAbout()" class="bg-primary text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-orange-600 transition-all">Save About Content</button>
    </div>
</div>

<!-- Contact section -->
<div id="section-contact" class="hidden space-y-4">
    <div class="bg-white rounded-2xl border border-slate-100 p-6 space-y-4">
        <h2 class="text-base font-bold">Contact Information</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Phone</label>
                <input id="contact-phone" type="text" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Email</label>
                <input id="contact-email" type="email" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">WhatsApp</label>
                <input id="contact-whatsapp" type="text" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Hours</label>
                <input id="contact-hours" type="text" placeholder="Mon–Sat: 9am – 7pm" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Address</label>
                <textarea id="contact-address" rows="2" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary resize-none"></textarea>
            </div>
        </div>
        <div id="contact-success" class="hidden text-sm text-green-700 bg-green-50 px-4 py-2 rounded-xl">Saved successfully!</div>
        <button onclick="saveContact()" class="bg-primary text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-orange-600 transition-all">Save Contact Info</button>
    </div>
</div>

<!-- Messages section -->
<div id="section-messages" class="hidden space-y-4">
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-bold">Contact Form Messages</h2>
            <span id="msg-count" class="text-xs text-slate-400"></span>
        </div>
        <div id="messages-list" class="divide-y divide-slate-50">
            <div class="text-center py-12 text-slate-400">Loading...</div>
        </div>
    </div>
</div>
</div>
    <div class="bg-white rounded-2xl border border-slate-100 p-6 space-y-5">
        <h2 class="text-base font-bold">Homepage Settings</h2>

        <!-- Marquee Items -->
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-2">Marquee Bar Items <span class="text-slate-400 font-normal">(one per line)</span></label>
            <textarea id="hp-marquee" rows="4" placeholder="★ 20% OFF on first heritage tour booking&#10;★ Verified guides for Ajanta &amp; Ellora" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary resize-none font-mono"></textarea>
        </div>

        <!-- Section Visibility -->
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-3">Section Visibility</label>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <?php foreach (['attractions'=>'Ancient Marvels','bikes'=>'Quick Bike Rentals','restaurants'=>'Taste the City','buses'=>'Travel Your Way','blogs'=>'Travel Insights'] as $key => $label): ?>
                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-xl border border-slate-100 hover:border-primary/30 transition-all">
                    <input type="checkbox" id="hp-show-<?php echo $key; ?>" class="w-4 h-4 accent-primary" checked/>
                    <span class="text-sm font-medium text-slate-700"><?php echo $label; ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Section Titles -->
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-3">Section Titles <span class="text-slate-400 font-normal">(leave blank to use default)</span></label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] text-slate-500 mb-1">Attractions Title</label>
                    <input id="hp-title-attractions" type="text" placeholder="Ancient Marvels" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-[11px] text-slate-500 mb-1">Bikes Title</label>
                    <input id="hp-title-bikes" type="text" placeholder="Quick Bike Rentals" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-[11px] text-slate-500 mb-1">Restaurants Title</label>
                    <input id="hp-title-restaurants" type="text" placeholder="Taste the City" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-[11px] text-slate-500 mb-1">Buses Title</label>
                    <input id="hp-title-buses" type="text" placeholder="Travel Your Way" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-[11px] text-slate-500 mb-1">Blogs Title</label>
                    <input id="hp-title-blogs" type="text" placeholder="Travel Insights" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
            </div>
        </div>

        <div id="homepage-success" class="hidden text-sm text-green-700 bg-green-50 px-4 py-2 rounded-xl">Saved successfully!</div>
        <button onclick="saveHomepage()" class="bg-primary text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-orange-600 transition-all">Save Homepage Settings</button>
    </div>
</div>

<!-- Messages section -->
<div id="section-messages" class="hidden space-y-4">
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-bold">Contact Form Messages</h2>
            <span id="msg-count" class="text-xs text-slate-400"></span>
        </div>
        <div id="messages-list" class="divide-y divide-slate-50">
            <div class="text-center py-12 text-slate-400">Loading...</div>
        </div>
    </div>
</div>
</div>

<?php
$extra_js = <<<'JS'
<script>
function switchSection(s) {
    document.querySelectorAll('.content-tab').forEach(function(b){
        var active = b.dataset.section === s;
        b.classList.toggle('bg-primary', active);
        b.classList.toggle('text-white', active);
        b.classList.toggle('text-slate-500', !active);
    });
    document.getElementById('section-about').classList.toggle('hidden', s !== 'about');
    document.getElementById('section-contact').classList.toggle('hidden', s !== 'contact');
    document.getElementById('section-homepage').classList.toggle('hidden', s !== 'homepage');
    document.getElementById('section-messages').classList.toggle('hidden', s !== 'messages');
    if (s === 'messages') loadMessages();
}

async function loadAbout() {
    var data = await api('../php/api/about_contact.php?section=about');
    if (!data) return;
    document.getElementById('about-mission').value     = data.mission || '';
    document.getElementById('about-vision').value      = data.vision || '';
    document.getElementById('about-description').value = data.description || '';
}

async function loadContact() {
    var data = await api('../php/api/about_contact.php?section=contact');
    if (!data) return;
    document.getElementById('contact-phone').value    = data.phone || '';
    document.getElementById('contact-email').value    = data.email || '';
    document.getElementById('contact-whatsapp').value = data.whatsapp || '';
    document.getElementById('contact-hours').value    = data.hours || '';
    document.getElementById('contact-address').value  = data.address || '';
}

async function loadHomepage() {
    var data = await api('../php/api/about_contact.php?section=homepage');
    if (!data) return;
    if (data.marquee) document.getElementById('hp-marquee').value = data.marquee;
    var sections = ['attractions','bikes','restaurants','buses','blogs'];
    sections.forEach(function(k){
        var cb = document.getElementById('hp-show-' + k);
        if (cb) cb.checked = data['show_' + k] !== false;
        var ti = document.getElementById('hp-title-' + k);
        if (ti) ti.value = data['title_' + k] || '';
    });
}

async function saveAbout() {
    var data = {
        mission:     document.getElementById('about-mission').value,
        vision:      document.getElementById('about-vision').value,
        description: document.getElementById('about-description').value,
    };
    await api('../php/api/about_contact.php', {
        method: 'PUT', body: JSON.stringify({ section: 'about', data: data })
    });
    var s = document.getElementById('about-success');
    s.classList.remove('hidden');
    setTimeout(function(){ s.classList.add('hidden'); }, 3000);
}

async function saveContact() {
    var data = {
        phone:    document.getElementById('contact-phone').value,
        email:    document.getElementById('contact-email').value,
        whatsapp: document.getElementById('contact-whatsapp').value,
        hours:    document.getElementById('contact-hours').value,
        address:  document.getElementById('contact-address').value,
    };
    await api('../php/api/about_contact.php', {
        method: 'PUT', body: JSON.stringify({ section: 'contact', data: data })
    });
    var s = document.getElementById('contact-success');
    s.classList.remove('hidden');
    setTimeout(function(){ s.classList.add('hidden'); }, 3000);
}

async function saveHomepage() {
    var sections = ['attractions','bikes','restaurants','buses','blogs'];
    var data = { marquee: document.getElementById('hp-marquee').value };
    sections.forEach(function(k){
        var cb = document.getElementById('hp-show-' + k);
        data['show_' + k] = cb ? cb.checked : true;
        var ti = document.getElementById('hp-title-' + k);
        data['title_' + k] = ti ? ti.value : '';
    });
    await api('../php/api/about_contact.php', {
        method: 'PUT', body: JSON.stringify({ section: 'homepage', data: data })
    });
    var s = document.getElementById('homepage-success');
    s.classList.remove('hidden');
    setTimeout(function(){ s.classList.add('hidden'); }, 3000);
}

switchSection('about');
loadAbout();
loadContact();
loadHomepage();
</script>
JS;
require 'admin-footer.php';
?>
