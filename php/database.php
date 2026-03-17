<?php
class Database {
    private static $instance = null;
    private $db;

    private function __construct() {
        $dbPath = __DIR__ . '/../database/csnexplore.db';
        $dbDir  = dirname($dbPath);
        if (!is_dir($dbDir)) mkdir($dbDir, 0755, true);

        $this->db = new PDO('sqlite:' . $dbPath);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->db->exec('PRAGMA foreign_keys = ON');
        $this->initSchema();
    }

    public static function getInstance() {
        if (!self::$instance) self::$instance = new self();
        return self::$instance;
    }

    public function getConnection() { return $this->db; }

    private function initSchema() {
        $this->db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL,
            name TEXT NOT NULL,
            phone TEXT,
            role TEXT NOT NULL DEFAULT 'user' CHECK(role IN ('user','admin','vendor')),
            is_verified INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS stays (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL, type TEXT, location TEXT NOT NULL,
            description TEXT, price_per_night REAL NOT NULL DEFAULT 0,
            rating REAL DEFAULT 0, reviews INTEGER DEFAULT 0,
            badge TEXT, image TEXT, gallery TEXT, amenities TEXT,
            room_type TEXT, max_guests INTEGER DEFAULT 2,
            is_active INTEGER DEFAULT 1, display_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS cars (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL, type TEXT, location TEXT NOT NULL,
            description TEXT, price_per_day REAL NOT NULL DEFAULT 0,
            rating REAL DEFAULT 0, reviews INTEGER DEFAULT 0,
            badge TEXT, image TEXT, gallery TEXT, features TEXT,
            fuel_type TEXT, transmission TEXT, seats INTEGER DEFAULT 5,
            is_active INTEGER DEFAULT 1, display_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS bikes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL, type TEXT, location TEXT NOT NULL,
            description TEXT, price_per_day REAL NOT NULL DEFAULT 0,
            rating REAL DEFAULT 0, reviews INTEGER DEFAULT 0,
            badge TEXT, image TEXT, gallery TEXT, features TEXT,
            fuel_type TEXT, cc TEXT,
            is_active INTEGER DEFAULT 1, display_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS restaurants (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL, type TEXT, cuisine TEXT, location TEXT NOT NULL,
            description TEXT, price_per_person REAL DEFAULT 0,
            rating REAL DEFAULT 0, reviews INTEGER DEFAULT 0,
            badge TEXT, image TEXT, gallery TEXT, menu_highlights TEXT,
            is_active INTEGER DEFAULT 1, display_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS attractions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL, type TEXT, location TEXT NOT NULL,
            description TEXT, entry_fee REAL DEFAULT 0,
            rating REAL DEFAULT 0, reviews INTEGER DEFAULT 0,
            badge TEXT, image TEXT, gallery TEXT,
            opening_hours TEXT, best_time TEXT,
            is_active INTEGER DEFAULT 1, display_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS buses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            operator TEXT NOT NULL, bus_type TEXT, from_location TEXT NOT NULL,
            to_location TEXT NOT NULL, departure_time TEXT, arrival_time TEXT,
            duration TEXT, price REAL DEFAULT 0,
            rating REAL DEFAULT 0, reviews INTEGER DEFAULT 0,
            badge TEXT, image TEXT, amenities TEXT, seats_available INTEGER DEFAULT 40,
            is_active INTEGER DEFAULT 1, display_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            full_name TEXT NOT NULL, phone TEXT NOT NULL, email TEXT,
            booking_date TEXT, number_of_people INTEGER DEFAULT 1,
            service_type TEXT, listing_id INTEGER, listing_name TEXT,
            status TEXT DEFAULT 'pending' CHECK(status IN ('pending','completed','cancelled')),
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS blogs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL, content TEXT NOT NULL,
            author TEXT DEFAULT 'Admin', image TEXT,
            status TEXT DEFAULT 'published' CHECK(status IN ('published','draft')),
            category TEXT DEFAULT 'General',
            read_time TEXT, tags TEXT, meta_description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS about_contact (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            section TEXT UNIQUE NOT NULL,
            content TEXT NOT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        ");

        // Seed admin user if not exists
        $admin = $this->fetchOne("SELECT id FROM users WHERE email = ?", [ADMIN_EMAIL]);
        if (!$admin) {
            $hash = password_hash('admin123', PASSWORD_DEFAULT);
            $this->insert('users', [
                'email' => ADMIN_EMAIL,
                'password_hash' => $hash,
                'name' => 'CSNExplore Admin',
                'role' => 'admin',
                'is_verified' => 1
            ]);
        }
    }

    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchOne($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    public function insert($table, $data) {
        $cols = array_keys($data);
        $ph   = array_map(fn($c) => ":$c", $cols);
        $sql  = "INSERT INTO $table (" . implode(',', $cols) . ") VALUES (" . implode(',', $ph) . ")";
        $params = [];
        foreach ($data as $k => $v) $params[":$k"] = $v;
        $this->query($sql, $params);
        return $this->db->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = []) {
        $sets = array_map(fn($c) => "$c = :$c", array_keys($data));
        $sql  = "UPDATE $table SET " . implode(', ', $sets) . " WHERE $where";
        $params = [];
        foreach ($data as $k => $v) $params[":$k"] = $v;
        $stmt = $this->query($sql, array_merge($params, $whereParams));
        return $stmt->rowCount();
    }

    public function delete($table, $where, $params = []) {
        $stmt = $this->query("DELETE FROM $table WHERE $where", $params);
        return $stmt->rowCount();
    }

    public function lastInsertId() { return $this->db->lastInsertId(); }
    public function beginTransaction() { return $this->db->beginTransaction(); }
    public function commit() { return $this->db->commit(); }
    public function rollback() { return $this->db->rollBack(); }
}
