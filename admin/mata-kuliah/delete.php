<?php
require_once __DIR__ . '/../../config/auth.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (!verify_csrf($_POST['_csrf'] ?? '')) {
    die('Token CSRF tidak valid.');
}

$id = intval($_POST['id'] ?? 0);
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $mysqli->prepare('DELETE FROM mata_kuliah WHERE id_mata_kuliah = ?');
$stmt->bind_param('i', $id);
$stmt->execute();

header('Location: index.php');
exit;
