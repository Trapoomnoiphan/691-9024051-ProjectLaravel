<?php
// Automatic Database Setup for MySQL (XAMPP Standard: 127.0.0.1:3306, user 'root', empty password)
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$user = getenv('DB_USERNAME') ?: 'root';
$pass = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';
$dbName = getenv('DB_DATABASE') ?: 'phone_repair_db';

echo "===================================================\n";
echo "  Setting up MySQL Database: {$dbName}\n";
echo "  Target: {$host}:{$port} (User: '{$user}')\n";
echo "===================================================\n";

try {
    $pdo = new PDO("mysql:host={$host};port={$port}", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $sql = "CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
    $pdo->exec($sql);
    echo "[SUCCESS] Database `{$dbName}` created or already exists.\n";
    exit(0);
} catch (PDOException $e) {
    echo "[ERROR] Could not connect to MySQL server: " . $e->getMessage() . "\n";
    echo "[HINT] Please ensure MySQL is started in XAMPP Control Panel on port {$port}.\n";
    exit(1);
}
