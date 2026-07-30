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
$jadwalId = intval($_POST['jadwal'] ?? 0);

if (!$mahasiswa || !$idKrs || !$jadwalId) {
    header('Location: krs.php');
    exit;
}

$stmt2 = $mysqli->prepare('SELECT status_krs FROM krs WHERE id_krs = ? AND id_mahasiswa = ? LIMIT 1');
$stmt2->bind_param('ii', $idKrs, $mahasiswa['id_mahasiswa']);
$stmt2->execute();
$krs = $stmt2->get_result()->fetch_assoc();

if (!$krs || $krs['status_krs'] === 'dikunci') {
    die('KRS sudah dikunci atau tidak ditemukan.');
}

$stmt3 = $mysqli->prepare('DELETE FROM detail_krs WHERE id_krs = ? AND id_jadwal = ?');
$stmt3->bind_param('ii', $idKrs, $jadwalId);
$stmt3->execute();

header('Location: krs.php');
exit;
