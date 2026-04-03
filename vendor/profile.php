<?php
$vendor_page  = 'profile';
$vendor_title = 'My Profile | Vendor Portal';
require 'vendor-header.php';
?>

<div class="mb-5">
    <h2 class="text-xl font-black text-slate-900">My Profile</h2>
    <p class="text-xs text-slate-500 mt-0.5">Manage your account details and security</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    <!-- Left: profile info card -->
    <div class="vendor-card p-5 text-center">
        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-primary to-orange-400 flex items-center justify-center text-white font-black text-3xl mx-auto mb-3" id="profile-avatar">V</div>
        <p id="profile-name" class="font-black text-slate-900 text-base">—</p>
        <p id="profile-username" class="text-[11px] text-slate-400 font-bold mb-1">@—</p>
        <p id="profile-business" class="text-[11px] text-primary font-bold mb-3"></p>
        <div class="flex items-center justify-center gap-1.5 text-[10px] font-bold text-green-700 bg-green-50 border border-green-100 px-3 py-1 rounded-full mb-4">
            <span class="w-2 h-2 bg-green-500 rounded-full"></span> Active Vendor
        </div>
        <div class="text-left space-y-2 border-t border-slate-100 pt-4">
            <div class="flex items-center gap-2 text-xs text-slate-600">
                <span class="material-symbols-outlined text-slate-400 text-base">email</span>
                <span id="profile-email">—</span>
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-600">
                <span class="material-symbols-outlined text-slate-400 text-base">phone</span>
                <span id="profile-phone">—</span>
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-600">
                <span class="material-symbols-outlined text-slate-400 text-base">calendar_today</span>
                <span id="profile-joined">Member since —</span>
            </div>
        </div>
    </div>

    <!-- Right: edit form + password -->
    <div class="lg:col-span-2 space-y-5">

        <!-- Edit profile -->
        <div class="vendor-card">
            <div class="p-4 border-b border-slate-100">
                <p class="text-sm font-bold text-slate-700">Edit Profile</p>
            </div>
            <form id="profile-form" onsubmit="saveProfile(event)" class="p-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Full Name</label>
                        <input id="p-name" required class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Business Name</label>
                        <input id="p-business" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email</label>
                        <input id="p-email" type="email" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Phone</label>
                        <input id="p-phone" type="tel" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                    </div>
                </div>
                <div class="pt-1">
                    <button type="submit" id="profile-save-btn" class="bg-primary text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-orange-600 transition-all">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Change password -->
        <div class="vendor-card">
            <div class="p-4 border-b border-slate-100">
                <p class="text-sm font-bold text-slate-700">Change Password</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Minimum 8 characters</p>
            </div>
            <form id="password-form" onsubmit="changePassword(event)" class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Current Password</label>
                    <div class="relative">
                        <input id="pw-current" type="password" required class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary pr-10"/>
                        <button type="button" onclick="togglePw('pw-current')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">New Password</label>
                        <div class="relative">
                            <input id="pw-new" type="password" required minlength="8" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary pr-10"/>
                            <button type="button" onclick="togglePw('pw-new')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <span class="material-symbols-outlined text-lg">visibility</span>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Confirm New Password</label>
                        <input id="pw-confirm" type="password" required minlength="8" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                    </div>
                </div>
                <div class="pt-1">
                    <button type="submit" id="pw-save-btn" class="bg-slate-800 text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-700 transition-all">
                        Update Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Danger zone -->
        <div class="vendor-card border-red-100 border">
            <div class="p-4 border-b border-red-50">
                <p class="text-sm font-bold text-red-600">Sign Out</p>
            </div>
            <div class="p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-700">Sign out of vendor portal</p>
                    <p class="text-[11px] text-slate-400">You'll need to log in again to access your dashboard</p>
                </div>
                <button onclick="vendorLogout()" class="flex items-center gap-1.5 bg-red-50 border border-red-100 text-red-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-red-100 transition-all">
                    <span class="material-symbols-outlined text-base">logout</span> Sign Out
                </button>
            </div>
        </div>

    </div>
</div>

<script>
const BASE = '<?php echo VENDOR_API_BASE; ?>';

async function loadProfile() {
    const d = await vendorApi(`${BASE}/php/api/vendor-profile.php?action=get`);
    if (!d?.vendor) return;
    const v = d.vendor;
    const name = v.name||'Vendor';
    document.getElementById('profile-avatar').textContent = name.charAt(0).toUpperCase();
    document.getElementById('profile-name').textContent     = name;
    document.getElementById('profile-username').textContent = '@'+(v.username||'');
    document.getElementById('profile-business').textContent = v.business_name||'';
    document.getElementById('profile-email').textContent    = v.email||'Not set';
    document.getElementById('profile-phone').textContent    = v.phone||'Not set';
    document.getElementById('profile-joined').textContent   = 'Member since ' + (v.created_at||'').substring(0,10);
    // Fill form
    document.getElementById('p-name').value     = v.name||'';
    document.getElementById('p-business').value = v.business_name||'';
    document.getElementById('p-email').value    = v.email||'';
    document.getElementById('p-phone').value    = v.phone||'';
}

async function saveProfile(e) {
    e.preventDefault();
    const btn = document.getElementById('profile-save-btn');
    btn.disabled = true; btn.textContent = 'Saving…';
    const d = await vendorApi(`${BASE}/php/api/vendor-profile.php?action=update`, {
        method:'POST',
        body: JSON.stringify({
            name:          document.getElementById('p-name').value.trim(),
            business_name: document.getElementById('p-business').value.trim(),
            email:         document.getElementById('p-email').value.trim(),
            phone:         document.getElementById('p-phone').value.trim(),
        })
    });
    btn.disabled = false; btn.textContent = 'Save Changes';
    if (d?.success) {
        showVendorToast('Profile updated successfully');
        // Update localStorage user
        var u = JSON.parse(localStorage.getItem('csn_vendor_user')||'{}');
        u.name = document.getElementById('p-name').value.trim();
        localStorage.setItem('csn_vendor_user', JSON.stringify(u));
        loadProfile();
    } else {
        showVendorToast(d?.error||'Failed to update', 'error');
    }
}

async function changePassword(e) {
    e.preventDefault();
    const nw = document.getElementById('pw-new').value;
    const co = document.getElementById('pw-confirm').value;
    if (nw !== co) { showVendorToast('New passwords do not match', 'error'); return; }
    const btn = document.getElementById('pw-save-btn');
    btn.disabled = true; btn.textContent = 'Updating…';
    const d = await vendorApi(`${BASE}/php/api/vendor-profile.php?action=change_password`, {
        method:'POST',
        body: JSON.stringify({
            current_password: document.getElementById('pw-current').value,
            new_password: nw
        })
    });
    btn.disabled = false; btn.textContent = 'Update Password';
    if (d?.success) {
        showVendorToast('Password updated! Please log in again.');
        document.getElementById('password-form').reset();
        setTimeout(vendorLogout, 2000);
    } else {
        showVendorToast(d?.error||'Failed', 'error');
    }
}

function togglePw(id) {
    var el = document.getElementById(id);
    el.type = el.type==='password' ? 'text' : 'password';
}

loadProfile();
</script>

<?php require 'vendor-footer.php'; ?>
