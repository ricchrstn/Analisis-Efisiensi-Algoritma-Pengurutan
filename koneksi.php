<?php
/**
 * koneksi.php - MySQL connection using mysqli
 * 
 * File ini menangani koneksi ke database MySQL menggunakan mysqli extension.
 * Konfigurasi database dapat diubah sesuai dengan environment.
 * 
 * @var string $db_host Database host (default: 127.0.0.1)
 * @var string $db_user Database username (default: root)
 * @var string $db_pass Database password (default: empty)
 * @var string $db_name Database name (default: algoritma_pengurutan)
 * @var mysqli $conn MySQLi connection object
 */

$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'algoritma_pengurutan';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_errno) {
    die('Database connection error: ' . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset('utf8mb4');
?>
