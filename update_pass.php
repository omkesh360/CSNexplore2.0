<?php
require 'php/config.php';
$pdo = getDB()->getConnection();
$h = password_hash('omkeshAa.1@', PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE vendors SET password_hash=? WHERE username='rupesh123'");
$stmt->execute([$h]);
echo "Password updated\n";
