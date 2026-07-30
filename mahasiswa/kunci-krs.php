<?php
require_once __DIR__ . '/../config/auth.php';
require_role('mahasiswa');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: krs.php');
    exit;
}

if (!verify_csrf($_POST['_csrf'] ?? '')) {
    die('Token CSRF tidak valid.');
}

$userId = (int) $_SESSION['user']['id_user'];
$stmt = $mysqli->prepare('SELECT id_mahasiswa FROM mahasiswa WHERE id_user = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$mahasiswa = $stmt->get_result()->fetch_assoc();

$idKrs = intval($_POST['id_krs'] ?? 0);
if (!$mahasiswa || !$idKrs) {
    header('Location: krs.php');
    exit;
}

$stmt2 = $mysqli->prepare('SELECT status_krs FROM krs WHERE id_krs = ? AND id_mahasiswa = ? LIMIT 1');
$stmt2->bind_param('ii', $idKrs, $mahasiswa['id_mahasiswa']);
$stmt2->execute();
$krs = $stmt2->get_result()->fetch_assoc();

if (!$krs) {
    die('KRS tidak ditemukan.');
}

if ($krs['status_krs'] === 'dikunci') {
    header('Location: krs.php');
    exit;
}

$stmt3 = $mysqli->prepare('UPDATE krs SET status_krs = "dikunci" WHERE id_krs = ? AND id_mahasiswa = ?');
$stmt3->bind_param('ii', $idKrs, $mahasiswa['id_mahasiswa']);
$stmt3->execute();

header('Location: krs.php');
exit;
