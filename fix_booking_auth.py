#!/usr/bin/env python3
"""
fix_booking_auth.py
Patches all listing-detail HTML files to:
1. Move auth check into the form submit handler (reliable)
2. Show "Create Account to Book" button when not logged in
"""
import os, re

LISTING_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'listing-detail')

# The old click-interceptor block to remove
OLD_INTERCEPT = '''  // Intercept Book Now button to check login
  var form = document.getElementById('booking-form');
  var submitBtn = form ? form.querySelector('button[type=submit]') : null;
  if (submitBtn) {
    submitBtn.addEventListener('click', function(e) {
      var token = localStorage.getItem('csn_token');
      var u = JSON.parse(localStorage.getItem('csn_user') || 'null');
      if (!token || !u) {
        e.preventDefault();
        e.stopImmediatePropagation();
        var currentUrl = window.location.href;
        document.getElementById('lr-login-btn').href = '../login.php?redirect=' + encodeURIComponent(currentUrl);
        document.getElementById('lr-register-btn').href = '../register.php?redirect=' + encodeURIComponent(currentUrl);
        var m = document.getElementById('login-required-modal');
        m.style.display = 'flex';
      }
    }, true);
  }'''

# New: update button on load + auth check in submit handler
NEW_INIT = '''  // Update Book Now button based on login state
  var token = localStorage.getItem('csn_token');
  var user = JSON.parse(localStorage.getItem('csn_user') || 'null');
  var submitBtn = document.querySelector('#booking-form button[type=submit]');
  if (submitBtn && (!token || !user)) {
    submitBtn.textContent = 'Create Account to Book';
    submitBtn.style.background = '#64748b';
  }'''

# Old submit handler start
OLD_SUBMIT_START = 'document.getElementById("booking-form").addEventListener("submit",async function(e){{'
NEW_SUBMIT_START = '''document.getElementById("booking-form").addEventListener("submit",async function(e){{
  // Auth check first
  var token = localStorage.getItem('csn_token');
  var u = JSON.parse(localStorage.getItem('csn_user') || 'null');
  if (!token || !u) {{
    e.preventDefault();
    var currentUrl = window.location.href;
    document.getElementById('lr-login-btn').href = '../login.php?redirect=' + encodeURIComponent(currentUrl);
    document.getElementById('lr-register-btn').href = '../register.php?redirect=' + encodeURIComponent(currentUrl);
    var m = document.getElementById('login-required-modal');
    m.style.display = 'flex';
    return;
  }}'''

patched = 0
skipped = 0

for fname in os.listdir(LISTING_DIR):
    if not fname.endswith('.html'):
        continue
    fpath = os.path.join(LISTING_DIR, fname)
    with open(fpath, 'r', encoding='utf-8') as f:
        content = f.read()

    changed = False

    # Remove old click interceptor
    if OLD_INTERCEPT in content:
        content = content.replace(OLD_INTERCEPT, NEW_INIT)
        changed = True
    elif NEW_INIT not in content:
        # Already patched or different format - skip
        skipped += 1
        continue

    # Patch submit handler to include auth check
    if OLD_SUBMIT_START in content and 'Auth check first' not in content:
        content = content.replace(OLD_SUBMIT_START, NEW_SUBMIT_START)
        changed = True

    if changed:
        with open(fpath, 'w', encoding='utf-8') as f:
            f.write(content)
        patched += 1
    else:
        skipped += 1

print(f"Patched: {patched} files")
print(f"Skipped: {skipped} files")
