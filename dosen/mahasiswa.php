<?php
require_once __DIR__ . '/../config/auth.php';
require_role('dosen');

$dosen = get_current_dosen($mysqli, (int) $_SESSION['user']['id_user']);
$jadwalId = intval($_GET['jadwal'] ?? 0);

if (!$dosen || !$jadwalId) {
    header('Location: mata-kuliah.php');
    exit;
}

$stmt = $mysqli->prepare('SELECT jk.id_jadwal, mk.nama_mata_kuliah, mk.kode_mata_kuliah, ta.tahun_akademik, jk.kelas, jk.hari, jk.jam_mulai, jk.jam_selesai, jk.ruangan
                           FROM jadwal_kuliah jk
                           JOIN mata_kuliah mk ON jk.id_mata_kuliah = mk.id_mata_kuliah
                           JOIN tahun_akademik ta ON jk.id_tahun_akademik = ta.id_tahun_akademik
                           WHERE jk.id_jadwal = ? AND jk.id_dosen = ? LIMIT 1');
$stmt->bind_param('ii', $jadwalId, $dosen['id_dosen']);
$stmt->execute();
$jadwal = $stmt->get_result()->fetch_assoc();

if (!$jadwal) {
    http_response_code(403);
    echo 'Access denied.';
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>
  <div class="col-2">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>Daftar Mahasiswa</h2>
    <p class="text-muted"><?php echo e($jadwal['nama_mata_kuliah']); ?> | <?php echo e($jadwal['tahun_akademik']); ?> | Kelas <?php echo e($jadwal['kelas']); ?></p>
    <a href="nilai.php?jadwal=<?php echo $jadwal['id_jadwal']; ?>" class="btn btn-primary mb-3">Input Nilai</a>
    <table class="table table-striped table-responsive">
      <thead>
        <tr><th>NIM</th><th>Nama Mahasiswa</th><th>Program Studi</th><th>Status KRS</th></tr>
      </thead>
      <tbody>
      <?php
      $tahunAkademikId = (int) ($jadwal['id_tahun_akademik'] ?? $jadwalId);
      $stmt2 = $mysqli->prepare('SELECT m.nim, m.nama_mahasiswa, m.program_studi, k.status_krs
                                 FROM detail_krs dk
                                 JOIN krs k ON dk.id_krs = k.id_krs
                                 JOIN mahasiswa m ON k.id_mahasiswa = m.id_mahasiswa
                                 WHERE dk.id_jadwal = ? AND k.id_tahun_akademik = ? AND dk.status = "aktif"
                                 ORDER BY m.nama_mahasiswa');
      $stmt2->bind_param('ii', $jadwal['id_jadwal'], $tahunAkademikId);
      $stmt2->execute();
      $res = $stmt2->get_result();
      while ($row = $res->fetch_assoc()):
      ?>
        <tr>
          <td><?php echo e($row['nim']); ?></td>
          <td><?php echo e($row['nama_mahasiswa']); ?></td>
          <td><?php echo e($row['program_studi']); ?></td>
          <td><?php echo e($row['status_krs']); ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
