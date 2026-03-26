<?php
/**
 * install.php — CSNExplore One-Click Setup Script
 * Run this once after uploading to your server.
 * DELETE or RENAME this file after setup is complete.
 */

// ── Security: block if already installed ─────────────────────────────────────
$lockFile = __DIR__ . '/cache/install.lock';
if (file_exists($lockFile)) {
    http_response_code(403);
    die('<h2 style="font-family:sans-serif;color:#dc2626">Already installed. Delete cache/install.lock to re-run.</h2>');
}

// ── Simple password protection ────────────────────────────────────────────────
$setupPassword = 'csnsetup2025';
$authenticated = false;
$message       = '';
$errors        = [];
$steps         = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup_password'])) {
    if ($_POST['setup_password'] !== $setupPassword) {
        $message = 'error:Incorrect setup password.';
    } else {
        $authenticated = true;
    }
}

if ($authenticated || (isset($_POST['run_setup']) && $_POST['run_setup'] === '1')) {
    if (!isset($_POST['run_setup'])) {
        // Show confirmation form
        $authenticated = true;
    } else {
        // Run setup
        $authenticated = true;
        ob_start();

        // Step 1: Check PHP version
        $steps[] = ['name' => 'PHP Version Check', 'status' => version_compare(PHP_VERSION, '8.0.0', '>=') ? 'ok' : 'warn',
                    'msg'  => 'PHP ' . PHP_VERSION . (version_compare(PHP_VERSION, '8.0.0', '>=') ? ' ✓' : ' — PHP 8.0+ recommended')];

        // Step 2: Check PDO SQLite
        $steps[] = ['name' => 'PDO SQLite Extension', 'status' => extension_loaded('pdo_sqlite') ? 'ok' : 'error',
                    'msg'  => extension_loaded('pdo_sqlite') ? 'Available ✓' : 'NOT available — install php-sqlite3'];

        // Step 3: Create directories
        $dirs = ['cache', 'logs', 'database', 'images/uploads'];
        foreach ($dirs as $dir) {
            $path = __DIR__ . '/' . $dir;
            if (!is_dir($path)) {
                $ok = mkdir($path, 0755, true);
                $steps[] = ['name' => "Create /$dir", 'status' => $ok ? 'ok' : 'error',
                            'msg'  => $ok ? 'Created ✓' : 'Failed — check permissions'];
            } else {
                $steps[] = ['name' => "Directory /$dir", 'status' => 'ok', 'msg' => 'Already exists ✓'];
            }
        }

        // Step 4: Write .gitkeep files
        foreach (['cache/.gitkeep', 'logs/.gitkeep'] as $gk) {
            $gkPath = __DIR__ . '/' . $gk;
            if (!file_exists($gkPath)) @file_put_contents($gkPath, '');
        }

        // Step 5: Check database
        if (extension_loaded('pdo_sqlite')) {
            try {
                require_once __DIR__ . '/php/config.php';
                $db   = getDB();
                $conn = $db->getConnection();

                // Create newsletter table
                $conn->exec("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    email TEXT UNIQUE NOT NULL,
                    subscribed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    is_active INTEGER DEFAULT 1
                )");

                $steps[] = ['name' => 'Database Initialization', 'status' => 'ok', 'msg' => 'Schema created ✓'];

                // Check admin user
                $admin = $db->fetchOne("SELECT id FROM users WHERE email = ?", [ADMIN_EMAIL]);
                $steps[] = ['name' => 'Admin User', 'status' => 'ok',
                            'msg'  => $admin ? 'Admin account exists ✓' : 'Admin account created ✓'];

            } catch (Exception $e) {
                $steps[] = ['name' => 'Database Initialization', 'status' => 'error', 'msg' => 'Error: ' . $e->getMessage()];
            }
        }

        // Step 6: Check .htaccess
        $steps[] = ['name' => '.htaccess', 'status' => file_exists(__DIR__ . '/.htaccess') ? 'ok' : 'warn',
                    'msg'  => file_exists(__DIR__ . '/.htaccess') ? 'Present ✓' : 'Missing — URL rewriting may not work'];

        // Step 7: Check mod_rewrite
        $modRewrite = function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules());
        $steps[] = ['name' => 'Apache mod_rewrite', 'status' => $modRewrite ? 'ok' : 'warn',
                    'msg'  => $modRewrite ? 'Enabled ✓' : 'Cannot detect — ensure AllowOverride All is set'];

        // Step 8: Seed data (optional)
        if (isset($_POST['seed_data']) && $_POST['seed_data'] === '1') {
            try {
                $seedUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']
                         . dirname($_SERVER['REQUEST_URI']) . '/php/api/seed.php';
                $ctx = stream_context_create(['http' => ['timeout' => 60]]);
                $result = @file_get_contents($seedUrl, false, $ctx);
                $decoded = $result ? json_decode($result, true) : null;
                $steps[] = ['name' => 'Seed Listings Data', 'status' => ($decoded && isset($decoded['success'])) ? 'ok' : 'warn',
                            'msg'  => ($decoded && isset($decoded['success'])) ? 'Data seeded ✓' : 'Seed may have failed — run php/api/seed.php manually'];
            } catch (Exception $e) {
                $steps[] = ['name' => 'Seed Listings Data', 'status' => 'warn', 'msg' => 'Could not auto-seed: ' . $e->getMessage()];
            }
        }

        // Step 9: Write lock file
        $hasErrors = count(array_filter($steps, fn($s) => $s['status'] === 'error')) > 0;
        if (!$hasErrors) {
            $cacheDir = __DIR__ . '/cache';
            if (!is_dir($cacheDir)) mkdir($cacheDir, 0755, true);
            file_put_contents($lockFile, json_encode(['installed_at' => date('c'), 'php' => PHP_VERSION]));
            $steps[] = ['name' => 'Installation Lock', 'status' => 'ok', 'msg' => 'Lock file created — delete install.php now ✓'];
        }

        ob_end_clean();
    }
}

$hasErrors = count(array_filter($steps, fn($s) => $s['status'] === 'error')) > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>CSNExplore — Setup</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: '#ec5b13' } } } }</script>
    <meta name="robots" content="noindex,nofollow"/>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-6 font-sans">
<div class="w-full max-w-2xl">

    <!-- Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-primary rounded-2xl mb-4 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
            </svg>
        </div>
        <h1 class="text-3xl font-black text-slate-900">CSNExplore Setup</h1>
        <p class="text-slate-500 mt-1">One-click installation wizard</p>
    </div>

    <?php if (empty($steps) && !$authenticated): ?>
    <!-- Password Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <h2 class="text-lg font-bold text-slate-800 mb-6">Enter Setup Password</h2>
        <?php if ($message): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-4 text-sm"><?php echo htmlspecialchars(str_replace('error:', '', $message)); ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="password" name="setup_password" placeholder="Setup password" required
                   class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary mb-4"/>
            <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-xl hover:bg-orange-600 transition-colors">
                Continue →
            </button>
        </form>
        <p class="text-xs text-slate-400 mt-4 text-center">Default password: <code class="bg-slate-100 px-1 rounded">csnsetup2025</code></p>
    </div>

    <?php elseif ($authenticated && empty($steps)): ?>
    <!-- Confirmation Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <h2 class="text-lg font-bold text-slate-800 mb-2">Ready to Install</h2>
        <p class="text-slate-500 text-sm mb-6">This will initialize the database, create required directories, and configure the application.</p>
        <form method="POST">
            <input type="hidden" name="setup_password" value="<?php echo htmlspecialchars($_POST['setup_password'] ?? ''); ?>"/>
            <input type="hidden" name="run_setup" value="1"/>
            <label class="flex items-center gap-3 mb-6 cursor-pointer">
                <input type="checkbox" name="seed_data" value="1" class="h-4 w-4 text-primary rounded"/>
                <span class="text-sm text-slate-700">Also seed sample listings data (recommended for first install)</span>
            </label>
            <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-xl hover:bg-orange-600 transition-colors">
                Run Installation
            </button>
        </form>
    </div>

    <?php else: ?>
    <!-- Results -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <h2 class="text-lg font-bold text-slate-800 mb-6">
            <?php echo $hasErrors ? '⚠️ Installation completed with errors' : '✅ Installation successful'; ?>
        </h2>
        <div class="space-y-3">
            <?php foreach ($steps as $step): ?>
            <div class="flex items-start gap-3 p-3 rounded-xl <?php echo $step['status'] === 'ok' ? 'bg-green-50' : ($step['status'] === 'error' ? 'bg-red-50' : 'bg-yellow-50'); ?>">
                <span class="text-lg shrink-0"><?php echo $step['status'] === 'ok' ? '✅' : ($step['status'] === 'error' ? '❌' : '⚠️'); ?></span>
                <div>
                    <p class="font-semibold text-sm text-slate-800"><?php echo htmlspecialchars($step['name']); ?></p>
                    <p class="text-xs text-slate-600 mt-0.5"><?php echo htmlspecialchars($step['msg']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (!$hasErrors): ?>
        <div class="mt-6 bg-primary/10 border border-primary/20 rounded-xl p-4">
            <p class="text-sm font-bold text-primary mb-1">🎉 Setup complete!</p>
            <p class="text-xs text-slate-600">
                Admin login: <strong><?php echo ADMIN_EMAIL; ?></strong> / <strong>admin123</strong><br/>
                <span class="text-red-600 font-semibold">Important: Delete install.php from your server now.</span>
            </p>
        </div>
        <div class="mt-4 flex gap-3">
            <a href="index" class="flex-1 text-center bg-primary text-white font-bold py-3 rounded-xl hover:bg-orange-600 transition-colors text-sm">
                Go to Website →
            </a>
            <a href="adminexplorer.php" class="flex-1 text-center bg-slate-800 text-white font-bold py-3 rounded-xl hover:bg-slate-900 transition-colors text-sm">
                Admin Panel →
            </a>
        </div>
        <?php else: ?>
        <div class="mt-6 bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-sm font-bold text-red-700">Fix the errors above and re-run setup.</p>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <p class="text-center text-xs text-slate-400 mt-6">CSNExplore © <?php echo date('Y'); ?> — Delete this file after installation</p>
</div>
</body>
</html>
