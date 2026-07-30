<?php
// config/database.php
// Database connection using MySQLi with basic error handling

$dbHost = '127.0.0.1';
$dbUser = 'root';
$dbPass = '';
$dbName = 'siakad_php_native';

$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($mysqli->connect_errno) {
    error_log('DB connect error: ' . $mysqli->connect_error);
    die('Database connection failed.');
}

// set charset
$mysqli->set_charset('utf8mb4');

?>
