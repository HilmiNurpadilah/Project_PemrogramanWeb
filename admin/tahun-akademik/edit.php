<?php
require_once __DIR__ . '/../../config/auth.php';
require_role('admin');

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $mysqli->prepare('SELECT * FROM tahun_akademik WHERE id_tahun_akademik = ? LIMIT 1');
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

    $tahun = trim($_POST['tahun_akademik'] ?? '');
    $semester = $_POST['semester'] ?? '';
    $status = $_POST['status'] ?? 'nonaktif';

    if ($tahun === '' || !in_array($semester, ['ganjil', 'genap'], true) || !in_array($status, ['aktif', 'nonaktif'], true)) {
        $errors[] = 'Data tahun akademik tidak valid.';
    }

    if (empty($errors)) {
        $stmt2 = $mysqli->prepare('UPDATE tahun_akademik SET tahun_akademik = ?, semester = ?, status = ? WHERE id_tahun_akademik = ?');
        $stmt2->bind_param('sssi', $tahun, $semester, $status, $id);
        if ($stmt2->execute()) {
            header('Location: index.php');
            exit;
        }
        $errors[] = 'Gagal menyimpan perubahan. Data mungkin duplikat.';
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>
  <div class="col-2">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>Edit Tahun Akademik</h2>
    <?php if ($errors): ?>
      <div class="alert alert-danger"><?php echo e(implode('<br>', $errors)); ?></div>
    <?php endif; ?>
    <form method="post">
      <?php echo csrf_field(); ?>
      <div class="mb-3"><label>Tahun Akademik</label><input type="text" name="tahun_akademik" class="form-control" value="<?php echo e($row['tahun_akademik']); ?>" required></div>
      <div class="mb-3">
        <label>Semester</label>
        <select name="semester" class="form-select" required>
          <option value="ganjil" <?php echo $row['semester'] === 'ganjil' ? 'selected' : ''; ?>>Ganjil</option>
          <option value="genap" <?php echo $row['semester'] === 'genap' ? 'selected' : ''; ?>>Genap</option>
        </select>
      </div>
      <div class="mb-3">
        <label>Status</label>
        <select name="status" class="form-select" required>
          <option value="aktif" <?php echo $row['status'] === 'aktif' ? 'selected' : ''; ?>>Aktif</option>
          <option value="nonaktif" <?php echo $row['status'] === 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
        </select>
      </div>
      <button class="btn btn-primary">Simpan Perubahan</button>
    </form>
  </div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
