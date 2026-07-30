<?php
require_once __DIR__ . '/../../config/auth.php';
require_role('admin');

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php'); exit;
}

$stmt = $mysqli->prepare('SELECT m.*, u.username FROM mahasiswa m JOIN users u ON m.id_user = u.id_user WHERE id_mahasiswa = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if (!$row) { header('Location: index.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? '')) $errors[] = 'Token CSRF tidak valid.';
    $nim = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $program = trim($_POST['program'] ?? '');
    $angkatan = intval($_POST['angkatan'] ?? 0);
    $username = trim($_POST['username'] ?? '');

    if (empty($errors)) {
        $stmt2 = $mysqli->prepare('UPDATE mahasiswa SET nim=?, nama_mahasiswa=?, program_studi=?, angkatan=? WHERE id_mahasiswa=?');
        $stmt2->bind_param('sssii', $nim, $nama, $program, $angkatan, $id);
        if ($stmt2->execute()) {
            $stmt3 = $mysqli->prepare('UPDATE users SET username=? WHERE id_user=?');
            $stmt3->bind_param('si', $username, $row['id_user']);
            $stmt3->execute();
            header('Location: index.php'); exit;
        } else {
            $errors[] = 'Gagal menyimpan perubahan.';
        }
    }
}

require __DIR__ . '/../../includes/header.php';
?>
  <div class="col-2">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>Edit Mahasiswa</h2>
    <?php if ($errors): ?>
      <div class="alert alert-danger"><?php echo e(implode('<br>', $errors)); ?></div>
    <?php endif; ?>
    <form method="post">
      <?php echo csrf_field(); ?>
      <div class="mb-3">
        <label>NIM</label>
        <input type="text" name="nim" class="form-control" value="<?php echo e($row['nim']); ?>" required>
      </div>
      <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="nama" class="form-control" value="<?php echo e($row['nama_mahasiswa']); ?>" required>
      </div>
      <div class="mb-3">
        <label>Program Studi</label>
        <input type="text" name="program" class="form-control" value="<?php echo e($row['program_studi']); ?>" required>
      </div>
      <div class="mb-3">
        <label>Angkatan</label>
        <input type="number" name="angkatan" class="form-control" value="<?php echo e($row['angkatan']); ?>" required>
      </div>
      <h5>Akun Login</h5>
      <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" value="<?php echo e($row['username']); ?>" required>
      </div>
      <button class="btn btn-primary">Simpan Perubahan</button>
    </form>
  </div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
