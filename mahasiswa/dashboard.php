<?php
require_once __DIR__ . '/../config/auth.php';
require_role('mahasiswa');

$userId = (int) $_SESSION['user']['id_user'];
$stmt = $mysqli->prepare('SELECT id_mahasiswa, nim, nama_mahasiswa, program_studi, angkatan FROM mahasiswa WHERE id_user = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$mahasiswa = $stmt->get_result()->fetch_assoc();

$activeTa = null;
$stmt2 = $mysqli->prepare('SELECT id_tahun_akademik, tahun_akademik, semester FROM tahun_akademik WHERE status = "aktif" LIMIT 1');
$stmt2->execute();
$activeTa = $stmt2->get_result()->fetch_assoc();

$totalKrs = 0;
$totalSks = 0;
if ($mahasiswa) {
    $stmt3 = $mysqli->prepare('SELECT COUNT(*) AS total_krs, COALESCE(SUM(mk.sks), 0) AS total_sks
                               FROM krs k
                               LEFT JOIN detail_krs dk ON k.id_krs = dk.id_krs AND dk.status = "aktif"
                               LEFT JOIN jadwal_kuliah jk ON dk.id_jadwal = jk.id_jadwal
                               LEFT JOIN mata_kuliah mk ON jk.id_mata_kuliah = mk.id_mata_kuliah
                               WHERE k.id_mahasiswa = ?');
    $stmt3->bind_param('i', $mahasiswa['id_mahasiswa']);
    $stmt3->execute();
    $stats = $stmt3->get_result()->fetch_assoc();
    $totalKrs = (int) ($stats['total_krs'] ?? 0);
    $totalSks = (int) ($stats['total_sks'] ?? 0);
}

require_once __DIR__ . '/../includes/header.php';
?>
  <div class="col-2">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h1>Dashboard Mahasiswa</h1>
    <p>Selamat datang, <?php echo e($_SESSION['user']['username']); ?></p>

    <?php if ($mahasiswa): ?>
      <div class="row mt-4">
        <div class="col-md-4 mb-3">
          <div class="card text-bg-primary">
            <div class="card-body">
              <h5 class="card-title">Profil</h5>
              <p class="card-text mb-0"><?php echo e($mahasiswa['nama_mahasiswa']); ?></p>
              <small><?php echo e($mahasiswa['nim']); ?></small>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="card text-bg-success">
            <div class="card-body">
              <h5 class="card-title">Total KRS</h5>
              <p class="card-text fs-3"><?php echo e($totalKrs); ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="card text-bg-warning">
            <div class="card-body">
              <h5 class="card-title">Total SKS</h5>
              <p class="card-text fs-3"><?php echo e($totalSks); ?></p>
            </div>
          </div>
        </div>
      </div>
      <?php if ($activeTa): ?>
        <div class="alert alert-info">Tahun akademik aktif: <?php echo e($activeTa['tahun_akademik'] . ' - ' . $activeTa['semester']); ?></div>
      <?php endif; ?>
    <?php else: ?>
      <div class="alert alert-warning">Profil mahasiswa belum ditemukan.</div>
    <?php endif; ?>

    <a href="<?php echo e(app_url('mahasiswa/isi-krs.php')); ?>" class="btn btn-outline-primary">Isi KRS</a>
    <a href="<?php echo e(app_url('mahasiswa/krs.php')); ?>" class="btn btn-outline-success ms-2">Lihat KRS Saya</a>
    <a href="<?php echo e(app_url('logout.php')); ?>" class="btn btn-danger ms-2">Logout</a>
  </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<?php
require_once __DIR__ . '/../config/auth.php';
require_role('mahasiswa');
require 'includes/header.php';
?>
    <div class="col-2">
        <?php require 'includes/sidebar.php'; ?>
    </div>
    <div class="col-10 mt-4">
        <h1>Dashboard Mahasiswa</h1>
        <p>Selamat datang, <?php echo e($_SESSION['user']['username']); ?></p>
        <a href="<?php echo e(app_url('logout.php')); ?>" class="btn btn-danger">Logout</a>
    </div>
<?php require 'includes/footer.php'; ?>
