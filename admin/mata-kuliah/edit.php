<?php
require_once __DIR__ . '/../../config/auth.php';
require_role('admin');

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $mysqli->prepare('SELECT * FROM mata_kuliah WHERE id_mata_kuliah = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if (!$row) {
    header('Location: index.php');
    exit;
}

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
        $stmt2 = $mysqli->prepare('UPDATE mata_kuliah SET kode_mata_kuliah = ?, nama_mata_kuliah = ?, sks = ?, semester = ? WHERE id_mata_kuliah = ?');
        $stmt2->bind_param('ssiii', $kode, $nama, $sks, $semester, $id);
        if ($stmt2->execute()) {
            header('Location: index.php');
            exit;
        }
        $errors[] = 'Gagal menyimpan perubahan. Kode mungkin sudah dipakai.';
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>
  <div class="col-2">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>Edit Mata Kuliah</h2>
    <?php if ($errors): ?>
      <div class="alert alert-danger"><?php echo e(implode('<br>', $errors)); ?></div>
    <?php endif; ?>
    <form method="post">
      <?php echo csrf_field(); ?>
      <div class="mb-3"><label>Kode Mata Kuliah</label><input type="text" name="kode" class="form-control" value="<?php echo e($row['kode_mata_kuliah']); ?>" required></div>
      <div class="mb-3"><label>Nama Mata Kuliah</label><input type="text" name="nama" class="form-control" value="<?php echo e($row['nama_mata_kuliah']); ?>" required></div>
      <div class="mb-3"><label>SKS</label><input type="number" name="sks" class="form-control" min="1" max="10" value="<?php echo e($row['sks']); ?>" required></div>
      <div class="mb-3"><label>Semester</label><input type="number" name="semester" class="form-control" min="1" max="14" value="<?php echo e($row['semester']); ?>" required></div>
      <button class="btn btn-primary">Simpan Perubahan</button>
    </form>
  </div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
