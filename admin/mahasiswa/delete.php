<?php
require_once __DIR__ . '/../../config/auth.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php'); exit;
}

if (!verify_csrf($_POST['_csrf'] ?? '')) {
    die('Token CSRF tidak valid.');
}

$id = intval($_POST['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

// delete mahasiswa and corresponding user via FK cascade
$stmt = $mysqli->prepare('SELECT id_user FROM mahasiswa WHERE id_mahasiswa = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if ($row) {
    $id_user = $row['id_user'];
    $stmt2 = $mysqli->prepare('DELETE FROM users WHERE id_user = ?');
    $stmt2->bind_param('i', $id_user);
    $stmt2->execute();
}

header('Location: index.php'); exit;
