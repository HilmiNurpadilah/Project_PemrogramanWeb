<?php
require_once __DIR__ . '/../config/auth.php';
require_role('dosen');

$userId = $_SESSION['user']['id_user'];
$stmt = $mysqli->prepare('SELECT id_dosen, nama_dosen FROM dosen WHERE id_user = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$dosen = $stmt->get_result()->fetch_assoc();

require_once __DIR__ . '/../includes/header.php';
?>
  <div class="col-2">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>Mata Kuliah yang Diampu</h2>
    <?php if (!$dosen): ?>
      <div class="alert alert-warning">Data dosen tidak ditemukan.</div>
    <?php else: ?>
      <table class="table table-striped table-responsive">
        <thead>
          <tr><th>Mata Kuliah</th><th>Kode</th><th>SKS</th><th>Semester</th><th>Tahun Akademik</th><th>Kelas</th><th>Hari</th><th>Jam</th><th>Ruangan</th></tr>
        </thead>
        <tbody>
        <?php
        $stmt2 = $mysqli->prepare('SELECT mk.nama_mata_kuliah, mk.kode_mata_kuliah, mk.sks, mk.semester, ta.tahun_akademik, jk.kelas, jk.hari, jk.jam_mulai, jk.jam_selesai, jk.ruangan
                                   FROM jadwal_kuliah jk
                                   JOIN mata_kuliah mk ON jk.id_mata_kuliah = mk.id_mata_kuliah
                                   JOIN tahun_akademik ta ON jk.id_tahun_akademik = ta.id_tahun_akademik
                                   WHERE jk.id_dosen = ?
                                   ORDER BY ta.id_tahun_akademik DESC, mk.nama_mata_kuliah');
        $stmt2->bind_param('i', $dosen['id_dosen']);
        $stmt2->execute();
        $res = $stmt2->get_result();
        while ($row = $res->fetch_assoc()):
        ?>
          <tr>
            <td><?php echo e($row['nama_mata_kuliah']); ?></td>
            <td><?php echo e($row['kode_mata_kuliah']); ?></td>
            <td><?php echo e($row['sks']); ?></td>
            <td><?php echo e($row['semester']); ?></td>
            <td><?php echo e($row['tahun_akademik']); ?></td>
            <td><?php echo e($row['kelas']); ?></td>
            <td><?php echo e($row['hari']); ?></td>
            <td><?php echo e(substr($row['jam_mulai'], 0, 5) . ' - ' . substr($row['jam_selesai'], 0, 5)); ?></td>
            <td><?php echo e($row['ruangan']); ?></td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
