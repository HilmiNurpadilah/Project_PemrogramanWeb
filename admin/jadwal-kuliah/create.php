<?php
require_once __DIR__ . '/../../config/auth.php';
require_role('admin');

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
        $stmt = $mysqli->prepare('INSERT INTO jadwal_kuliah (id_mata_kuliah, id_dosen, id_tahun_akademik, kelas, hari, jam_mulai, jam_selesai, ruangan, kuota) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('iiisssssi', $id_mk, $id_dosen, $id_ta, $kelas, $hari, $jam_mulai, $jam_selesai, $ruangan, $kuota);
        if ($stmt->execute()) {
            header('Location: index.php');
            exit;
        }
        $errors[] = 'Gagal menyimpan jadwal. Periksa apakah kombinasi jadwal sudah pernah dipakai.';
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>
  <div class="col-2">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>Tambah Jadwal Kuliah</h2>
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
            <option value="<?php echo $mk['id_mata_kuliah']; ?>"><?php echo e($mk['nama_mata_kuliah']); ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="mb-3">
        <label>Dosen</label>
        <select name="id_dosen" class="form-select" required>
          <option value="">Pilih Dosen</option>
          <?php while ($d = $dosenList->fetch_assoc()): ?>
            <option value="<?php echo $d['id_dosen']; ?>"><?php echo e($d['nama_dosen']); ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="mb-3">
        <label>Tahun Akademik</label>
        <select name="id_tahun_akademik" class="form-select" required>
          <option value="">Pilih Tahun Akademik</option>
          <?php while ($ta = $tahunList->fetch_assoc()): ?>
            <option value="<?php echo $ta['id_tahun_akademik']; ?>"><?php echo e($ta['tahun_akademik'] . ' - ' . $ta['semester'] . ' - ' . $ta['status']); ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="mb-3"><label>Kelas</label><input type="text" name="kelas" class="form-control" required></div>
      <div class="mb-3"><label>Hari</label><input type="text" name="hari" class="form-control" placeholder="Senin" required></div>
      <div class="mb-3"><label>Jam Mulai</label><input type="time" name="jam_mulai" class="form-control" required></div>
      <div class="mb-3"><label>Jam Selesai</label><input type="time" name="jam_selesai" class="form-control" required></div>
      <div class="mb-3"><label>Ruangan</label><input type="text" name="ruangan" class="form-control" required></div>
      <div class="mb-3"><label>Kuota</label><input type="number" name="kuota" class="form-control" min="1" value="30" required></div>
      <button class="btn btn-primary">Simpan</button>
    </form>
  </div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
