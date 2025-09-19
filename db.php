<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'dbxp2iul2t0rvp'); // Database name
define('DB_USER', 'u7bkx8pwvemeg'); // Database user
define('DB_PASS', 'qredcpdgqd9j');  // Database password

if (session_status() === PHP_SESSION_NONE) session_start();

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>
