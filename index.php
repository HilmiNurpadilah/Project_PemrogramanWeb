<?php
require_once __DIR__ . '/config/auth.php';
if (!is_logged_in()) {
    header('Location: ' . app_url('login.php'));
    exit;
}

$role = $_SESSION['user']['role'];
if ($role === 'admin') header('Location: ' . app_url('admin/dashboard.php'));
if ($role === 'dosen') header('Location: ' . app_url('dosen/dashboard.php'));
if ($role === 'mahasiswa') header('Location: ' . app_url('mahasiswa/dashboard.php'));

?>
