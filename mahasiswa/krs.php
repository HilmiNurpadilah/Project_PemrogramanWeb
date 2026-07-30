<?php
require_once __DIR__ . '/../config/auth.php';
require_role('mahasiswa');

$userId = (int) $_SESSION['user']['id_user'];
$stmt = $mysqli->prepare('SELECT id_mahasiswa, nama_mahasiswa FROM mahasiswa WHERE id_user = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$mahasiswa = $stmt->get_result()->fetch_assoc();

require_once __DIR__ . '/../includes/header.php';
?>
  <div class="col-2">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>KRS Saya</h2>
    <?php if (!$mahasiswa): ?>
      <div class="alert alert-warning">Profil mahasiswa tidak ditemukan.</div>
    <?php else: ?>
      <?php
      $stmt2 = $mysqli->prepare('SELECT k.id_krs, ta.tahun_akademik, ta.semester, k.tanggal_pengisian, k.status_krs
                                 FROM krs k
                                 JOIN tahun_akademik ta ON k.id_tahun_akademik = ta.id_tahun_akademik
                                 WHERE k.id_mahasiswa = ?
                                 ORDER BY k.id_krs DESC');
      $stmt2->bind_param('i', $mahasiswa['id_mahasiswa']);
      $stmt2->execute();
      $res = $stmt2->get_result();
      while ($krs = $res->fetch_assoc()):
      ?>
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title"><?php echo e($krs['tahun_akademik'] . ' - ' . $krs['semester']); ?></h5>
            <p class="card-text mb-1">Tanggal: <?php echo e($krs['tanggal_pengisian']); ?></p>
            <p class="card-text mb-1">Status: <?php echo e($krs['status_krs']); ?></p>
            <?php
            $stmt3 = $mysqli->prepare('SELECT dk.id_jadwal, mk.nama_mata_kuliah, mk.sks, jk.hari, jk.jam_mulai, jk.jam_selesai, jk.kelas, d.nama_dosen
                                       FROM detail_krs dk
                                       JOIN jadwal_kuliah jk ON dk.id_jadwal = jk.id_jadwal
                                       JOIN mata_kuliah mk ON jk.id_mata_kuliah = mk.id_mata_kuliah
                                       JOIN dosen d ON jk.id_dosen = d.id_dosen
                                       WHERE dk.id_krs = ? AND dk.status = "aktif"');
            $stmt3->bind_param('i', $krs['id_krs']);
            $stmt3->execute();
            $detail = $stmt3->get_result();
            $totalSks = 0;
            ?>
            <table class="table table-sm table-bordered mt-3">
              <thead><tr><th>Mata Kuliah</th><th>SKS</th><th>Dosen</th><th>Jadwal</th><th>Aksi</th></tr></thead>
              <tbody>
                <?php while ($row = $detail->fetch_assoc()): $totalSks += (int) $row['sks']; ?>
                  <tr>
                    <td><?php echo e($row['nama_mata_kuliah']); ?></td>
                    <td><?php echo e($row['sks']); ?></td>
                    <td><?php echo e($row['nama_dosen']); ?></td>
                    <td><?php echo e($row['hari'] . ' ' . substr($row['jam_mulai'], 0, 5) . '-' . substr($row['jam_selesai'], 0, 5) . ' / ' . $row['kelas']); ?></td>
                    <td>
                      <?php if ($krs['status_krs'] !== 'dikunci'): ?>
                        <form action="hapus-krs.php" method="post" onsubmit="return confirm('Hapus mata kuliah ini dari KRS?');" style="display:inline-block;">
                          <?php echo csrf_field(); ?>
                          <input type="hidden" name="id_krs" value="<?php echo e($krs['id_krs']); ?>">
                          <input type="hidden" name="jadwal" value="<?php echo e($row['id_jadwal'] ?? ''); ?>">
                          <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                      <?php else: ?>
                        <span class="text-muted">Terkunci</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
            <strong>Total SKS: <?php echo e($totalSks); ?></strong>
          </div>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

