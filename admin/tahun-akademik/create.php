<?php
require_once __DIR__ . '/../../config/auth.php';
require_role('admin');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? '')) {
        $errors[] = 'Token CSRF tidak valid.';
    }

    $tahun = trim($_POST['tahun_akademik'] ?? '');
    $semester = $_POST['semester'] ?? '';
    $status = $_POST['status'] ?? 'nonaktif';

    if ($tahun === '' || !in_array($semester, ['ganjil', 'genap'], true) || !in_array($status, ['aktif', 'nonaktif'], true)) {
        $errors[] = 'Data tahun akademik tidak valid.';
    }

    if (empty($errors)) {
        $stmt = $mysqli->prepare('INSERT INTO tahun_akademik (tahun_akademik, semester, status) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $tahun, $semester, $status);
        if ($stmt->execute()) {
            header('Location: index.php');
            exit;
        }
        $errors[] = 'Gagal menyimpan data tahun akademik. Data mungkin duplikat.';
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>
  <div class="col-2">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>Tambah Tahun Akademik</h2>
    <?php if ($errors): ?>
      <div class="alert alert-danger"><?php echo e(implode('<br>', $errors)); ?></div>
    <?php endif; ?>
    <form method="post">
      <?php echo csrf_field(); ?>
      <div class="mb-3"><label>Tahun Akademik</label><input type="text" name="tahun_akademik" class="form-control" placeholder="2024/2025" required></div>
      <div class="mb-3">
        <label>Semester</label>
        <select name="semester" class="form-select" required>
          <option value="">Pilih Semester</option>
          <option value="ganjil">Ganjil</option>
          <option value="genap">Genap</option>
        </select>
      </div>
      <div class="mb-3">
        <label>Status</label>
        <select name="status" class="form-select" required>
          <option value="nonaktif">Nonaktif</option>
          <option value="aktif">Aktif</option>
        </select>
      </div>
      <button class="btn btn-primary">Simpan</button>
    </form>
  </div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
