<?php
// Direct test of vendor auth API
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Direct API Test</title>
    <style>
        body { font-family: monospace; padding: 20px; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h1>Direct Vendor Auth API Test</h1>
    
    <h2>Test 1: Direct PHP Include</h2>
    <?php
    echo "<p>Testing direct include of vendor-auth.php...</p>";
    
    // Simulate POST request
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_GET['action'] = 'login';
    $_POST['username'] = 'testvendor';
    $_POST['password'] = 'test123';
    
    // Capture output
    ob_start();
    
    try {
        // Set up JSON input
        $GLOBALS['_test_json_input'] = json_encode([
            'username' => 'testvendor',
            'password' => 'test123'
        ]);
        
        // Temporarily override getJsonInput
        if (!function_exists('getJsonInput_original')) {
            function getJsonInput_original() {
                return json_decode($GLOBALS['_test_json_input'] ?? '{}', true) ?? [];
            }
        }
        
        include 'php/api/vendor-auth.php';
        
        $output = ob_get_clean();
        
        echo "<p class='success'>✅ No fatal errors</p>";
        echo "<h3>Output:</h3>";
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
        
        // Try to parse as JSON
        $json = json_decode($output, true);
        if ($json) {
            echo "<p class='success'>✅ Valid JSON response</p>";
            echo "<pre>" . print_r($json, true) . "</pre>";
        } else {
            echo "<p class='error'>❌ Not valid JSON</p>";
            echo "<p>JSON Error: " . json_last_error_msg() . "</p>";
        }
        
    } catch (Exception $e) {
        $output = ob_get_clean();
        echo "<p class='error'>❌ Exception: " . $e->getMessage() . "</p>";
        echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
        echo "<h3>Output before error:</h3>";
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
    }
    ?>
    
    <hr>
    
    <h2>Test 2: Check File Existence</h2>
    <?php
    $files = [
        'php/config.php',
        'php/jwt.php',
        'php/database.php',
        'php/api/vendor-auth.php'
    ];
    
    foreach ($files as $file) {
        if (file_exists($file)) {
            echo "<p class='success'>✅ $file exists</p>";
            
            // Check for BOM
            $content = file_get_contents($file);
            if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
                echo "<p class='error'>⚠️ $file has BOM (Byte Order Mark)</p>";
            }
            
            // Check for whitespace before <?php
            if (preg_match('/^\s+<\?php/', $content)) {
                echo "<p class='error'>⚠️ $file has whitespace before &lt;?php</p>";
            }
        } else {
            echo "<p class='error'>❌ $file NOT FOUND</p>";
        }
    }
    ?>
    
    <hr>
    
    <h2>Test 3: Check Database</h2>
    <?php
    try {
        require_once 'php/config.php';
        $db = getDB();
        
        $vendors = $db->fetchAll("SELECT id, name, username, status FROM vendors");
        
        echo "<p class='success'>✅ Database connection OK</p>";
        echo "<p>Found " . count($vendors) . " vendor(s)</p>";
        
        if (count($vendors) > 0) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Name</th><th>Username</th><th>Status</th></tr>";
            foreach ($vendors as $v) {
                echo "<tr>";
                echo "<td>{$v['id']}</td>";
                echo "<td>{$v['name']}</td>";
                echo "<td>{$v['username']}</td>";
                echo "<td>{$v['status']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='error'>⚠️ No vendors found. Create one in admin panel.</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Database error: " . $e->getMessage() . "</p>";
    }
    ?>
    
    <hr>
    
    <h2>Test 4: Check PHP Configuration</h2>
    <?php
    echo "<p>PHP Version: " . PHP_VERSION . "</p>";
    echo "<p>display_errors: " . ini_get('display_errors') . "</p>";
    echo "<p>error_reporting: " . error_reporting() . "</p>";
    echo "<p>output_buffering: " . ini_get('output_buffering') . "</p>";
    ?>
    
    <hr>
    
    <p><a href="test-vendor-api.html">← Back to Interactive Test</a></p>
    <p><small>Delete this file after testing: test-api-direct.php</small></p>
</body>
</html>
