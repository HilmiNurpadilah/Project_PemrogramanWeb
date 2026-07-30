<?php
require_once __DIR__ . '/../../config/auth.php';
require_role('admin');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? '')) {
        $errors[] = 'Token CSRF tidak valid.';
    }

    $kode = trim($_POST['kode'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $sks = intval($_POST['sks'] ?? 0);
    $semester = intval($_POST['semester'] ?? 0);

    if ($kode === '' || $nama === '' || $sks <= 0 || $semester <= 0) {
        $errors[] = 'Semua field wajib diisi dan harus valid.';
    }

    if (empty($errors)) {
        $stmt = $mysqli->prepare('INSERT INTO mata_kuliah (kode_mata_kuliah, nama_mata_kuliah, sks, semester) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssii', $kode, $nama, $sks, $semester);
        if ($stmt->execute()) {
            header('Location: index.php');
            exit;
        }
        $errors[] = 'Gagal menyimpan data mata kuliah. Kode mungkin sudah dipakai.';
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>
  <div class="col-2">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>Tambah Mata Kuliah</h2>
    <?php if ($errors): ?>
      <div class="alert alert-danger"><?php echo e(implode('<br>', $errors)); ?></div>
    <?php endif; ?>
    <form method="post">
      <?php echo csrf_field(); ?>
      <div class="mb-3"><label>Kode Mata Kuliah</label><input type="text" name="kode" class="form-control" required></div>
      <div class="mb-3"><label>Nama Mata Kuliah</label><input type="text" name="nama" class="form-control" required></div>
      <div class="mb-3"><label>SKS</label><input type="number" name="sks" class="form-control" min="1" max="10" required></div>
      <div class="mb-3"><label>Semester</label><input type="number" name="semester" class="form-control" min="1" max="14" required></div>
      <button class="btn btn-primary">Simpan</button>
    </form>
  </div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
