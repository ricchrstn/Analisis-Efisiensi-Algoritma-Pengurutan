<?php
// koneksi.php - MySQL connection using mysqli
$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'algoritma_pengurutan';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_errno) {
    die('Database connection error: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>
