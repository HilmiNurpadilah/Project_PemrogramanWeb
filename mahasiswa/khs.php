<?php
require_once __DIR__ . '/../config/auth.php';
require_role('mahasiswa');

$mahasiswa = get_current_mahasiswa($mysqli, (int) $_SESSION['user']['id_user']);

require_once __DIR__ . '/../includes/header.php';
?>
  <div class="col-2">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>KHS, IPS, dan IPK</h2>
    <?php if (!$mahasiswa): ?>
      <div class="alert alert-warning">Profil mahasiswa tidak ditemukan.</div>
    <?php else: ?>
      <p class="text-muted"><?php echo e($mahasiswa['nama_mahasiswa']); ?> | <?php echo e($mahasiswa['nim']); ?> | <?php echo e($mahasiswa['program_studi']); ?></p>
      <?php
      $stmt = $mysqli->prepare('SELECT k.id_krs, ta.tahun_akademik, ta.semester, k.tanggal_pengisian, k.status_krs
                                 FROM krs k
                                 JOIN tahun_akademik ta ON k.id_tahun_akademik = ta.id_tahun_akademik
                                 WHERE k.id_mahasiswa = ?
                                 ORDER BY ta.id_tahun_akademik DESC, k.id_krs DESC');
      $stmt->bind_param('i', $mahasiswa['id_mahasiswa']);
      $stmt->execute();
      $krsList = $stmt->get_result();

      $totalMutuAll = 0;
      $totalSksAll = 0;
      while ($krs = $krsList->fetch_assoc()):
        $stmt2 = $mysqli->prepare('SELECT mk.nama_mata_kuliah, mk.sks, COALESCE(n.nilai_akhir, 0) AS nilai_akhir, COALESCE(n.nilai_huruf, "E") AS nilai_huruf, COALESCE(n.bobot, 0) AS bobot
                                   FROM detail_krs dk
                                   JOIN jadwal_kuliah jk ON dk.id_jadwal = jk.id_jadwal
                                   JOIN mata_kuliah mk ON jk.id_mata_kuliah = mk.id_mata_kuliah
                                   LEFT JOIN nilai n ON dk.id_detail_krs = n.id_detail_krs
                                   WHERE dk.id_krs = ? AND dk.status = "aktif"
                                   ORDER BY mk.nama_mata_kuliah');
        $stmt2->bind_param('i', $krs['id_krs']);
        $stmt2->execute();
        $detail = $stmt2->get_result();

        $totalSksSemester = 0;
        $totalMutuSemester = 0;
      ?>
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title"><?php echo e($krs['tahun_akademik'] . ' - ' . $krs['semester']); ?></h5>
            <p class="card-text mb-1">Tanggal KRS: <?php echo e($krs['tanggal_pengisian']); ?> | Status: <?php echo e($krs['status_krs']); ?></p>
            <div class="table-responsive">
              <table class="table table-bordered mt-3 align-middle">
                <thead>
                  <tr>
                    <th>Mata Kuliah</th>
                    <th>SKS</th>
                    <th>Nilai Akhir</th>
                    <th>Huruf</th>
                    <th>Bobot</th>
                    <th>Mutu</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = $detail->fetch_assoc()):
                    $mutu = (float) $row['bobot'] * (int) $row['sks'];
                    $totalSksSemester += (int) $row['sks'];
                    $totalMutuSemester += $mutu;
                  ?>
                    <tr>
                      <td><?php echo e($row['nama_mata_kuliah']); ?></td>
                      <td><?php echo e($row['sks']); ?></td>
                      <td><?php echo e($row['nilai_akhir']); ?></td>
                      <td><?php echo e($row['nilai_huruf']); ?></td>
                      <td><?php echo e($row['bobot']); ?></td>
                      <td><?php echo e(number_format($mutu, 2)); ?></td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>

            <?php
            $ips = calculate_ips($totalMutuSemester, $totalSksSemester);
            $totalMutuAll += $totalMutuSemester;
            $totalSksAll += $totalSksSemester;
            ?>
            <div class="row">
              <div class="col-md-4"><strong>Total SKS Semester:</strong> <?php echo e($totalSksSemester); ?></div>
              <div class="col-md-4"><strong>Total Mutu Semester:</strong> <?php echo e(number_format($totalMutuSemester, 2)); ?></div>
              <div class="col-md-4"><strong>IPS:</strong> <?php echo e(number_format($ips, 2)); ?></div>
            </div>
          </div>
        </div>
      <?php endwhile; ?>

      <?php $ipk = calculate_ipk($totalMutuAll, $totalSksAll); ?>
      <div class="card border-primary">
        <div class="card-body">
          <h5 class="card-title">Rekap Kumulatif</h5>
          <p class="mb-1"><strong>Total SKS Ditempuh:</strong> <?php echo e($totalSksAll); ?></p>
          <p class="mb-1"><strong>Total Mutu:</strong> <?php echo e(number_format($totalMutuAll, 2)); ?></p>
          <p class="mb-0"><strong>IPK:</strong> <?php echo e(number_format($ipk, 2)); ?></p>
        </div>
      </div>
    <?php endif; ?>
  </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
