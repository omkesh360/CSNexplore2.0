import os
import re
import glob

# The CSS block to replace everything from .glass up to </style>
NEW_CSS_BLOCK = """
        @keyframes marquee { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }
        .marquee { animation: marquee 30s linear infinite; }
        .glass { background:rgba(255,255,255,0.07); backdrop-filter:blur(16px); -webkit-backdrop-filter:blur(16px); border:1px solid rgba(255,255,255,0.12); }
        .glass-dark { background:rgba(10,7,5,0.7); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border:1px solid rgba(236,91,19,0.1); }
        .header-solid { background:#000000 !important; backdrop-filter:none !important; -webkit-backdrop-filter:none !important; }
        
        #site-header {
            transition: all 0.8s cubic-bezier(0.25, 1, 0.5, 1) !important;
            border-radius: 0px;
        }
        #site-header nav { 
            transition: all 0.8s cubic-bezier(0.25, 1, 0.5, 1) !important; 
        }
        .header-btn {
            transition: all 0.8s cubic-bezier(0.25, 1, 0.5, 1) !important;
        }
        .header-btn-text {
            max-width: 100px;
            transition: all 0.8s cubic-bezier(0.25, 1, 0.5, 1) !important;
        }
        
        .header-pill {
            top: 12px !important;
            width: calc(100% - 16px) !important;
            max-width: 1280px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            border-radius: 99px !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.5);
        }
        .header-pill nav {
            height: 58px !important;
        }
        .header-pill .header-btn {
            width: 40px !important;
            height: 40px !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            border-radius: 50% !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
        }
        .header-pill .header-btn-text {
            max-width: 0 !important;
            opacity: 0 !important;
            margin-left: 0 !important;
            pointer-events: none;
        }
        /* Page transition */
        html { background:#fff; }
        body { opacity:0; will-change:opacity; backface-visibility:hidden; -webkit-backface-visibility:hidden; }
        body.page-ready { animation: pageFadeIn 0.2s ease forwards; }
        @keyframes pageFadeIn { from { opacity:0; } to { opacity:1; } }
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; font-family:'Material Symbols Outlined'; font-style:normal; display:inline-block; line-height:1; }
        <?php if (!empty($extra_styles)) echo $extra_styles; ?>
"""

NEW_CSS_BLOCK_STATIC = """
        @keyframes marquee { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }
        .marquee { animation: marquee 30s linear infinite; }
        .glass { background:rgba(255,255,255,0.07); backdrop-filter:blur(16px); -webkit-backdrop-filter:blur(16px); border:1px solid rgba(255,255,255,0.12); }
        .glass-dark { background:rgba(10,7,5,0.7); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border:1px solid rgba(236,91,19,0.1); }
        .header-solid { background:#000000 !important; backdrop-filter:none !important; -webkit-backdrop-filter:none !important; }
        
        #site-header {
            transition: all 0.8s cubic-bezier(0.25, 1, 0.5, 1) !important;
            border-radius: 0px;
        }
        #site-header nav { 
            transition: all 0.8s cubic-bezier(0.25, 1, 0.5, 1) !important; 
        }
        .header-btn {
            transition: all 0.8s cubic-bezier(0.25, 1, 0.5, 1) !important;
        }
        .header-btn-text {
            max-width: 100px;
            transition: all 0.8s cubic-bezier(0.25, 1, 0.5, 1) !important;
        }
        
        .header-pill {
            top: 12px !important;
            width: calc(100% - 16px) !important;
            max-width: 1280px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            border-radius: 99px !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.5);
        }
        .header-pill nav {
            height: 58px !important;
        }
        .header-pill .header-btn {
            width: 40px !important;
            height: 40px !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            border-radius: 50% !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
        }
        .header-pill .header-btn-text {
            max-width: 0 !important;
            opacity: 0 !important;
            margin-left: 0 !important;
            pointer-events: none;
        }
        /* Page transition */
        html { background:#fff; }
        body { opacity:0; will-change:opacity; backface-visibility:hidden; -webkit-backface-visibility:hidden; }
        body.page-ready { animation: pageFadeIn 0.2s ease forwards; }
        @keyframes pageFadeIn { from { opacity:0; } to { opacity:1; } }
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; font-family:'Material Symbols Outlined'; font-style:normal; display:inline-block; line-height:1; }
"""

RIGHT_BTNS_STATIC = """        <div class="flex items-center gap-1.5 md:gap-2">
            <!-- Call Now -->
            <a href="tel:+919876543210" class="header-btn flex items-center justify-center bg-white/10 hover:bg-white/20 text-white rounded-full transition-all duration-[800ms] h-[40px] px-3 md:px-4">
                <span class="material-symbols-outlined text-[18px]">call</span>
                <span class="header-btn-text text-[13px] md:text-sm font-semibold whitespace-nowrap overflow-hidden ml-1.5 opacity-100">Call Now</span>
            </a>
            <!-- WhatsApp -->
            <a href="https://wa.me/919876543210" target="_blank" class="header-btn flex items-center justify-center bg-whatsapp hover:bg-[#1fad53] text-white rounded-full transition-all duration-[800ms] h-[40px] px-3 md:px-4 shadow-lg shadow-whatsapp/20 hover:shadow-whatsapp/40">
                <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                <span class="header-btn-text text-[13px] md:text-sm font-semibold whitespace-nowrap overflow-hidden ml-1.5 opacity-100">WhatsApp</span>
            </a>
            <a href="{prefix}login.php" class="hidden sm:block text-white text-sm font-semibold px-3 py-1.5 hover:bg-white/10 rounded-full transition-all">Login</a>
            <button id="mob-btn" class="md:hidden p-1.5 rounded-lg transition-colors ml-0.5">
                <span class="material-symbols-outlined text-2xl text-white">menu</span>
            </button>
        </div>"""

def replace_in_file(filepath, is_php=True, prefix=""):
    try:
        with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
    except Exception as e:
        print(f"Error reading {filepath}")
        return
    
    original = content
    
    # Update CSS
    if is_php:
        content = re.sub(
            r'\.glass\s*\{.*?\}(.*?)(?=</style>)', 
            NEW_CSS_BLOCK.strip() + '\n', 
            content, 
            flags=re.DOTALL
        )
    else:
        content = re.sub(
            r'\.glass\s*\{.*?\}(.*?)(?=</style>)', 
            NEW_CSS_BLOCK_STATIC.strip() + '\n', 
            content, 
            flags=re.DOTALL
        )
        
    # Simplify the header buttons in nav to our new structure
    btns_in_nav_pattern = r'<div class="flex items-center gap-[^"]*">(?:.|\n)*?<button id="mob-btn"(?:.|\n)*?</button>\s*</div>'
    if re.search(btns_in_nav_pattern, content):
        content = re.sub(btns_in_nav_pattern, RIGHT_BTNS_STATIC.format(prefix=prefix), content)

    if content != original:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated {filepath}")
    else:
        pass

# Update PHP files
php_files = ['header.php', 'index.php', 'login.php', 'register.php']
for f in php_files:
    if os.path.exists(f):
        replace_in_file(f, is_php=True, prefix="")

for f in glob.glob('listing-detail/*.html') + glob.glob('blogs/*.html') + glob.glob('cache/pages/*.html'):
    replace_in_file(f, is_php=False, prefix="../")

print("Done updating headers across workspaces.")
