<?php
require_once __DIR__ . '/config/auth.php';
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['user']['role'];
if ($role === 'admin') header('Location: admin/dashboard.php');
if ($role === 'dosen') header('Location: dosen/dashboard.php');
if ($role === 'mahasiswa') header('Location: mahasiswa/dashboard.php');

?>
