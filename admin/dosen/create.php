<?php
require_once __DIR__ . '/../../config/auth.php';
require_role('admin');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? '')) {
        $errors[] = 'Token CSRF tidak valid.';
    }
    $nidn = trim($_POST['nidn'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nidn === '' || $nama === '' || $username === '' || $password === '') {
        $errors[] = 'Field wajib belum lengkap.';
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $mysqli->prepare('INSERT INTO users (username, password, role, status) VALUES (?, ?, "dosen", "aktif")');
        $stmt->bind_param('ss', $username, $hash);
        if ($stmt->execute()) {
            $id_user = $stmt->insert_id;
            $stmt2 = $mysqli->prepare('INSERT INTO dosen (id_user, nidn, nama_dosen, email, no_telepon) VALUES (?, ?, ?, ?, ?)');
            $stmt2->bind_param('issss', $id_user, $nidn, $nama, $email, $telepon);
            if ($stmt2->execute()) {
                header('Location: index.php'); exit;
            }
            $errors[] = 'Gagal menyimpan data dosen.';
        } else {
            $errors[] = 'Gagal membuat akun dosen.';
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>
  <div class="col-2">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>Tambah Dosen</h2>
    <?php if ($errors): ?>
      <div class="alert alert-danger"><?php echo e(implode('<br>', $errors)); ?></div>
    <?php endif; ?>
    <form method="post">
      <?php echo csrf_field(); ?>
      <div class="mb-3"><label>NIDN</label><input type="text" name="nidn" class="form-control" required></div>
      <div class="mb-3"><label>Nama</label><input type="text" name="nama" class="form-control" required></div>
      <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control"></div>
      <div class="mb-3"><label>No Telepon</label><input type="text" name="telepon" class="form-control"></div>
      <h5>Akun Login</h5>
      <div class="mb-3"><label>Username</label><input type="text" name="username" class="form-control" required></div>
      <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
      <button class="btn btn-primary">Simpan</button>
    </form>
  </div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
