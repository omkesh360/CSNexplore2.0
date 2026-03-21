<?php
// Migration: Add checkin_date and checkout_date columns to bookings table
require_once __DIR__ . '/../config.php';

try {
    $db = getDB();
    
    // Check if columns already exist
    $columns = $db->fetchAll("PRAGMA table_info(bookings)");
    $columnNames = array_column($columns, 'name');
    
    if (!in_array('checkin_date', $columnNames)) {
        $db->exec("ALTER TABLE bookings ADD COLUMN checkin_date TEXT");
        echo "✓ Added checkin_date column\n";
    } else {
        echo "- checkin_date column already exists\n";
    }
    
    if (!in_array('checkout_date', $columnNames)) {
        $db->exec("ALTER TABLE bookings ADD COLUMN checkout_date TEXT");
        echo "✓ Added checkout_date column\n";
    } else {
        echo "- checkout_date column already exists\n";
    }
    
    echo "\n✓ Migration completed successfully!\n";
    
} catch (Exception $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
