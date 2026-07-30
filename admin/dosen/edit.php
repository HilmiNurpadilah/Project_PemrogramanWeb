<?php
require_once __DIR__ . '/../../config/auth.php';
require_role('admin');

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$stmt = $mysqli->prepare('SELECT d.*, u.username FROM dosen d JOIN users u ON d.id_user = u.id_user WHERE id_dosen = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if (!$row) { header('Location: index.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? '')) $errors[] = 'Token CSRF tidak valid.';
    $nidn = trim($_POST['nidn'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $username = trim($_POST['username'] ?? '');

    if (empty($errors)) {
        $stmt2 = $mysqli->prepare('UPDATE dosen SET nidn=?, nama_dosen=?, email=?, no_telepon=? WHERE id_dosen=?');
        $stmt2->bind_param('ssssi', $nidn, $nama, $email, $telepon, $id);
        if ($stmt2->execute()) {
            $stmt3 = $mysqli->prepare('UPDATE users SET username=? WHERE id_user=?');
            $stmt3->bind_param('si', $username, $row['id_user']);
            $stmt3->execute();
            header('Location: index.php'); exit;
        }
        $errors[] = 'Gagal menyimpan perubahan.';
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>
  <div class="col-2">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>Edit Dosen</h2>
    <?php if ($errors): ?>
      <div class="alert alert-danger"><?php echo e(implode('<br>', $errors)); ?></div>
    <?php endif; ?>
    <form method="post">
      <?php echo csrf_field(); ?>
      <div class="mb-3"><label>NIDN</label><input type="text" name="nidn" class="form-control" value="<?php echo e($row['nidn']); ?>" required></div>
      <div class="mb-3"><label>Nama</label><input type="text" name="nama" class="form-control" value="<?php echo e($row['nama_dosen']); ?>" required></div>
      <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?php echo e($row['email']); ?>"></div>
      <div class="mb-3"><label>No Telepon</label><input type="text" name="telepon" class="form-control" value="<?php echo e($row['no_telepon']); ?>"></div>
      <h5>Akun Login</h5>
      <div class="mb-3"><label>Username</label><input type="text" name="username" class="form-control" value="<?php echo e($row['username']); ?>" required></div>
      <button class="btn btn-primary">Simpan Perubahan</button>
    </form>
  </div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
