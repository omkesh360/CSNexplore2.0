<?php
// Test script to debug vendor login
require_once 'php/config.php';
require_once 'php/jwt.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Vendor Login Test</h1>";

$db = getDB();

// Check if vendors table exists
try {
    $vendors = $db->fetchAll("SELECT id, name, username, status FROM vendors");
    echo "<h2>✅ Vendors Table Exists</h2>";
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
        echo "<p style='color: orange;'>⚠️ No vendors found. Create one in admin panel first.</p>";
    }
} catch (Exception $e) {
    echo "<h2>❌ Error: Vendors table doesn't exist</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Run the database migration first!</p>";
}

// Test JWT functions
echo "<h2>JWT Test</h2>";
try {
    $testPayload = ['vendor_id' => 1, 'username' => 'test', 'type' => 'vendor'];
    $token = createJWT($testPayload, JWT_SECRET);
    echo "<p>✅ JWT Creation: OK</p>";
    echo "<p>Token: <code style='font-size: 10px;'>" . substr($token, 0, 50) . "...</code></p>";
    
    $verified = verifyJWT($token, JWT_SECRET);
    if ($verified) {
        echo "<p>✅ JWT Verification: OK</p>";
    } else {
        echo "<p>❌ JWT Verification: FAILED</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ JWT Error: " . $e->getMessage() . "</p>";
}

// Test vendor auth API
echo "<h2>Vendor Auth API Test</h2>";
echo "<p>API URL: <code>" . BASE_PATH . "/php/api/vendor-auth.php</code></p>";

// Check if vendor-auth.php exists
if (file_exists('php/api/vendor-auth.php')) {
    echo "<p>✅ vendor-auth.php exists</p>";
} else {
    echo "<p>❌ vendor-auth.php NOT FOUND</p>";
}

echo "<hr>";
echo "<h2>Quick Actions</h2>";
echo "<ul>";
echo "<li><a href='admin/vendors.php'>Go to Admin Vendors Page</a> (create a vendor)</li>";
echo "<li><a href='vendor/vendorlogin.php'>Go to Vendor Login Page</a></li>";
echo "<li><a href='adminexplorer.php'>Go to Admin Login</a></li>";
echo "</ul>";

echo "<hr>";
echo "<p><small>Delete this file after testing: test-vendor-login.php</small></p>";
?>
