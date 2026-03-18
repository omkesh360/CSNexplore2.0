#!/usr/bin/env python3
"""Fix homepage mobile responsiveness and footer quick links"""

with open('index.php', 'r', encoding='utf-8') as f:
    idx = f.read()

# ── Fix 1: search-row on mobile — allow wrapping properly ────────────────────
# The search-row CSS has flex-wrap:nowrap which breaks on small screens
old_sr = "        .search-row { display:flex; gap:10px; align-items:center; flex-wrap:nowrap; width:100%; }"
new_sr = "        .search-row { display:flex; gap:10px; align-items:center; flex-wrap:wrap; width:100%; }"
idx = idx.replace(old_sr, new_sr)

# ── Fix 2: search-field and date-field min-width on mobile ───────────────────
old_mob = """        @media(max-width:640px){
          .search-box { padding:14px 14px; }
          .search-row { flex-wrap:wrap; gap:8px; }
          .search-field, .date-field { height:46px; min-width:calc(50% - 4px); }
          .search-btn { width:100%; justify-content:center; height:46px; font-size:14px; padding:0 16px; }
          .tab-btn { padding:6px 10px; font-size:12px; }
          .tab-btn .material-symbols-outlined { font-size:15px; }
        }
        @media(max-width:400px){
          .search-field, .date-field { min-width:100%; }
        }"""
new_mob = """        @media(max-width:768px){
          .search-box { padding:14px; }
          .search-row { flex-wrap:wrap; gap:8px; }
          .search-field { flex:1 1 100%; height:46px; }
          .date-field { flex:1 1 calc(50% - 4px); height:46px; min-width:0; }
          .search-btn { width:100%; justify-content:center; height:46px; font-size:14px; padding:0 16px; }
          .tab-btn { padding:6px 10px; font-size:12px; }
          .tab-btn .material-symbols-outlined { font-size:15px; }
        }
        @media(max-width:400px){
          .date-field { flex:1 1 100%; }
        }"""
idx = idx.replace(old_mob, new_mob)

# ── Fix 3: hero section — better mobile padding ───────────────────────────────
# Already done in fix_index.py, but ensure search-box max-w is responsive
idx = idx.replace(
    'class="search-box max-w-4xl mx-auto"',
    'class="search-box max-w-4xl mx-auto w-full"'
)

# ── Fix 4: footer quick links — 2-column grid (same as footer.php) ───────────
# The homepage was using require footer.php now, so this is already fixed.
# But let's verify the footer.php quick links are 2-col
with open('footer.php', 'r', encoding='utf-8') as f:
    ft = f.read()

# Confirm footer.php has grid-cols-2 for quick links
if 'grid grid-cols-2' in ft:
    print("footer.php quick links already 2-col ✓")
else:
    print("WARNING: footer.php quick links not 2-col")

with open('index.php', 'w', encoding='utf-8') as f:
    f.write(idx)
print("index.php mobile fixes saved ✓")
