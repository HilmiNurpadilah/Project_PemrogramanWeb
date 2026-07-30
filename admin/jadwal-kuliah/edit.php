<?php
require_once __DIR__ . '/../../config/auth.php';
require_role('admin');

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $mysqli->prepare('SELECT * FROM jadwal_kuliah WHERE id_jadwal = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if (!$row) {
    header('Location: index.php');
    exit;
}

$errors = [];
$mataKuliah = $mysqli->query('SELECT id_mata_kuliah, nama_mata_kuliah FROM mata_kuliah ORDER BY nama_mata_kuliah');
$dosenList = $mysqli->query('SELECT id_dosen, nama_dosen FROM dosen ORDER BY nama_dosen');
$tahunList = $mysqli->query('SELECT id_tahun_akademik, tahun_akademik, semester, status FROM tahun_akademik ORDER BY id_tahun_akademik DESC');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? '')) {
        $errors[] = 'Token CSRF tidak valid.';
    }

    $id_mk = intval($_POST['id_mata_kuliah'] ?? 0);
    $id_dosen = intval($_POST['id_dosen'] ?? 0);
    $id_ta = intval($_POST['id_tahun_akademik'] ?? 0);
    $kelas = trim($_POST['kelas'] ?? '');
    $hari = trim($_POST['hari'] ?? '');
    $jam_mulai = $_POST['jam_mulai'] ?? '';
    $jam_selesai = $_POST['jam_selesai'] ?? '';
    $ruangan = trim($_POST['ruangan'] ?? '');
    $kuota = intval($_POST['kuota'] ?? 0);

    if ($id_mk <= 0 || $id_dosen <= 0 || $id_ta <= 0 || $kelas === '' || $hari === '' || $jam_mulai === '' || $jam_selesai === '' || $ruangan === '' || $kuota <= 0) {
        $errors[] = 'Semua field wajib diisi dan harus valid.';
    }

    if (empty($errors)) {
        $stmt2 = $mysqli->prepare('UPDATE jadwal_kuliah SET id_mata_kuliah = ?, id_dosen = ?, id_tahun_akademik = ?, kelas = ?, hari = ?, jam_mulai = ?, jam_selesai = ?, ruangan = ?, kuota = ? WHERE id_jadwal = ?');
        $stmt2->bind_param('iiisssssii', $id_mk, $id_dosen, $id_ta, $kelas, $hari, $jam_mulai, $jam_selesai, $ruangan, $kuota, $id);
        if ($stmt2->execute()) {
            header('Location: index.php');
            exit;
        }
        $errors[] = 'Gagal menyimpan perubahan jadwal.';
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>
  <div class="col-2">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>Edit Jadwal Kuliah</h2>
    <?php if ($errors): ?>
      <div class="alert alert-danger"><?php echo e(implode('<br>', $errors)); ?></div>
    <?php endif; ?>
    <form method="post">
      <?php echo csrf_field(); ?>
      <div class="mb-3">
        <label>Mata Kuliah</label>
        <select name="id_mata_kuliah" class="form-select" required>
          <option value="">Pilih Mata Kuliah</option>
          <?php while ($mk = $mataKuliah->fetch_assoc()): ?>
            <option value="<?php echo $mk['id_mata_kuliah']; ?>" <?php echo $row['id_mata_kuliah'] == $mk['id_mata_kuliah'] ? 'selected' : ''; ?>><?php echo e($mk['nama_mata_kuliah']); ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="mb-3">
        <label>Dosen</label>
        <select name="id_dosen" class="form-select" required>
          <option value="">Pilih Dosen</option>
          <?php while ($d = $dosenList->fetch_assoc()): ?>
            <option value="<?php echo $d['id_dosen']; ?>" <?php echo $row['id_dosen'] == $d['id_dosen'] ? 'selected' : ''; ?>><?php echo e($d['nama_dosen']); ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="mb-3">
        <label>Tahun Akademik</label>
        <select name="id_tahun_akademik" class="form-select" required>
          <option value="">Pilih Tahun Akademik</option>
          <?php while ($ta = $tahunList->fetch_assoc()): ?>
            <option value="<?php echo $ta['id_tahun_akademik']; ?>" <?php echo $row['id_tahun_akademik'] == $ta['id_tahun_akademik'] ? 'selected' : ''; ?>><?php echo e($ta['tahun_akademik'] . ' - ' . $ta['semester'] . ' - ' . $ta['status']); ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="mb-3"><label>Kelas</label><input type="text" name="kelas" class="form-control" value="<?php echo e($row['kelas']); ?>" required></div>
      <div class="mb-3"><label>Hari</label><input type="text" name="hari" class="form-control" value="<?php echo e($row['hari']); ?>" required></div>
      <div class="mb-3"><label>Jam Mulai</label><input type="time" name="jam_mulai" class="form-control" value="<?php echo e(substr($row['jam_mulai'], 0, 5)); ?>" required></div>
      <div class="mb-3"><label>Jam Selesai</label><input type="time" name="jam_selesai" class="form-control" value="<?php echo e(substr($row['jam_selesai'], 0, 5)); ?>" required></div>
      <div class="mb-3"><label>Ruangan</label><input type="text" name="ruangan" class="form-control" value="<?php echo e($row['ruangan']); ?>" required></div>
      <div class="mb-3"><label>Kuota</label><input type="number" name="kuota" class="form-control" min="1" value="<?php echo e($row['kuota']); ?>" required></div>
      <button class="btn btn-primary">Simpan Perubahan</button>
    </form>
  </div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
