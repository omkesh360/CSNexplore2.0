<?php
require_once 'c:/xampp/htdocs/CSNexplore2.0/php/config.php';
$pdo = getDB()->getConnection();
$stmt = $pdo->query("SELECT * FROM vendors");
$vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($vendors);
