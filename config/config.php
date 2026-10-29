<?php
declare(strict_types=1);

/*
 * AksiBersama - konfigurasi aplikasi
 * Sesuaikan nilai database dengan konfigurasi MySQL/XAMPP Anda.
 */
define('APP_NAME', 'AksiBersama');
define('BASE_URL', '/AksiBersama'); // Sesuaikan dengan nama folder di htdocs; kosongkan jika dipasang di document root

$dbHost = '127.0.0.1';
$dbName = 'aksibersama';
$dbUser = 'root';
$dbPass = '';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        $options
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('Koneksi database gagal. Pastikan MySQL aktif dan pengaturan config/config.php sudah benar.');
}
