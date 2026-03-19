#!/usr/bin/env python3
"""
fix_booking_ui.py
Patches all listing-detail HTML files to:
- Hide the booking form when not logged in
- Show a login/register prompt + "Book Now" + "4 hours" note instead
- Show the form only when authenticated (via JS on load)
"""
import os, re

LISTING_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'listing-detail')

# The old form opening tag (always display:none now, shown by JS when logged in)
OLD_FORM_OPEN = '<form id="booking-form" class="space-y-3">'
NEW_FORM_OPEN = '<form id="booking-form" class="space-y-3" style="display:none">'

# Guest state block to insert before the form
GUEST_STATE_BLOCK = '''  <!-- Guest state: shown when not logged in -->
  <div id="booking-guest-state">
    <div class="text-center py-4 mb-4 bg-slate-50 rounded-2xl border border-slate-100">
      <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">lock</span>
      <p class="text-sm font-semibold text-slate-700 mb-1">Login or Register to Book</p>
      <p class="text-xs text-slate-400 mb-4">Create a free account to complete your booking</p>
      <div class="flex gap-2 px-2">
        <a id="guest-login-btn" href="../login.php" class="flex-1 bg-[#ec5b13] text-white font-bold py-2.5 rounded-xl text-sm hover:bg-orange-600 transition-all text-center">Login</a>
        <a id="guest-register-btn" href="../register.php" class="flex-1 border-2 border-[#ec5b13] text-[#ec5b13] font-bold py-2.5 rounded-xl text-sm hover:bg-[#ec5b13]/5 transition-all text-center">Register</a>
      </div>
    </div>
    <div class="flex items-start gap-2 bg-amber-50 border border-amber-100 rounded-xl px-3 py-2.5 mb-3">
      <span class="material-symbols-outlined text-amber-500 text-base mt-0.5 shrink-0">schedule</span>
      <p class="text-xs text-amber-700 font-medium">We process your booking request within <span class="font-bold">4 hours</span> of submission.</p>
    </div>
    <button onclick="(function(){var u=window.location.href;document.getElementById('guest-login-btn').href='../login.php?redirect='+encodeURIComponent(u);document.getElementById('guest-register-btn').href='../register.php?redirect='+encodeURIComponent(u);document.getElementById('login-required-modal').style.display='flex';})()" class="w-full bg-[#ec5b13] text-white font-bold py-3 rounded-xl hover:bg-orange-600 transition-all text-sm">Book Now</button>
  </div>

'''

# 4-hour note to insert inside the form (before the success/error divs)
FOUR_HOUR_NOTE = '''    <div class="flex items-start gap-2 bg-amber-50 border border-amber-100 rounded-xl px-3 py-2.5">
      <span class="material-symbols-outlined text-amber-500 text-base mt-0.5 shrink-0">schedule</span>
      <p class="text-xs text-amber-700 font-medium">We process your booking request within <span class="font-bold">4 hours</span> of submission.</p>
    </div>
'''

# Old init script block to replace
OLD_INIT = '''  // Update Book Now button based on login state
  var token = localStorage.getItem('csn_token');
  var user = JSON.parse(localStorage.getItem('csn_user') || 'null');
  var submitBtn = document.querySelector('#booking-form button[type=submit]');
  if (submitBtn && (!token || !user)) {
    submitBtn.textContent = 'Create Account to Book';
    submitBtn.style.background = '#64748b';
  }'''

NEW_INIT = '''  // Show/hide booking form based on login state
  var token = localStorage.getItem('csn_token');
  var user = JSON.parse(localStorage.getItem('csn_user') || 'null');
  var guestState = document.getElementById('booking-guest-state');
  var form = document.getElementById('booking-form');
  if (token && user) {
    if (guestState) guestState.style.display = 'none';
    if (form) form.style.display = '';
    if (user.name) { var n = document.getElementById('b-name'); if (n && !n.value) n.value = user.name; }
    if (user.phone) { var p = document.getElementById('b-phone'); if (p && !p.value) p.value = user.phone; }
  } else {
    var cur = encodeURIComponent(window.location.href);
    var lb = document.getElementById('guest-login-btn');
    var rb = document.getElementById('guest-register-btn');
    if (lb) lb.href = '../login.php?redirect=' + cur;
    if (rb) rb.href = '../register.php?redirect=' + cur;
  }'''

patched = 0
skipped = 0

for fname in sorted(os.listdir(LISTING_DIR)):
    if not fname.endswith('.html'):
        continue
    fpath = os.path.join(LISTING_DIR, fname)
    with open(fpath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Skip if already patched
    if 'booking-guest-state' in content:
        skipped += 1
        continue

    changed = False

    # 1. Insert guest state block before the form
    if OLD_FORM_OPEN in content:
        content = content.replace(OLD_FORM_OPEN, GUEST_STATE_BLOCK + NEW_FORM_OPEN)
        changed = True

    # 2. Insert 4-hour note inside the form before success/error divs
    OLD_SUCCESS = '    <div id="booking-success"'
    if OLD_SUCCESS in content and FOUR_HOUR_NOTE not in content:
        content = content.replace(OLD_SUCCESS, FOUR_HOUR_NOTE + OLD_SUCCESS)
        changed = True

    # 3. Replace old init script
    if OLD_INIT in content:
        content = content.replace(OLD_INIT, NEW_INIT)
        changed = True

    if changed:
        with open(fpath, 'w', encoding='utf-8') as f:
            f.write(content)
        patched += 1
    else:
        skipped += 1
        print(f"  WARN: no changes in {fname}")

print(f"\nPatched: {patched} files")
print(f"Skipped: {skipped} files")
