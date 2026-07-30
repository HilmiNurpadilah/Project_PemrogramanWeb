<?php
require_once __DIR__ . '/../../config/auth.php';
require_role('admin');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? '')) {
        $errors[] = 'Token CSRF tidak valid.';
    }
    $nim = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $program = trim($_POST['program'] ?? '');
    $angkatan = intval($_POST['angkatan'] ?? 0);
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nim === '' || $nama === '' || $program === '' || $username === '' || $password === '') {
        $errors[] = 'Semua field wajib diisi.';
    }

    if (empty($errors)) {
        // create user first
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $mysqli->prepare('INSERT INTO users (username, password, role, status) VALUES (?, ?, "mahasiswa", "aktif")');
        $stmt->bind_param('ss', $username, $hash);
        if ($stmt->execute()) {
            $id_user = $stmt->insert_id;
            $stmt2 = $mysqli->prepare('INSERT INTO mahasiswa (id_user, nim, nama_mahasiswa, program_studi, angkatan) VALUES (?, ?, ?, ?, ?)');
            $stmt2->bind_param('isssi', $id_user, $nim, $nama, $program, $angkatan);
            if ($stmt2->execute()) {
                header('Location: index.php');
                exit;
            } else {
                $errors[] = 'Gagal menyimpan data mahasiswa.';
            }
        } else {
            $errors[] = 'Gagal membuat akun pengguna (username mungkin sudah ada).';
        }
    }
}

require __DIR__ . '/../../includes/header.php';
?>
  <div class="col-2">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>Tambah Mahasiswa</h2>
    <?php if ($errors): ?>
      <div class="alert alert-danger"><?php echo e(implode('<br>', $errors)); ?></div>
    <?php endif; ?>
    <form method="post">
      <?php echo csrf_field(); ?>
      <div class="mb-3">
        <label>NIM</label>
        <input type="text" name="nim" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="nama" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Program Studi</label>
        <input type="text" name="program" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Angkatan</label>
        <input type="number" name="angkatan" class="form-control" value="2023" required>
      </div>
      <h5>Akun Login</h5>
      <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button class="btn btn-primary">Simpan</button>
    </form>
  </div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
