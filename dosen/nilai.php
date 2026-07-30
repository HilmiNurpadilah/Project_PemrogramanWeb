<?php
require_once __DIR__ . '/../config/auth.php';
require_role('dosen');

$dosen = get_current_dosen($mysqli, (int) $_SESSION['user']['id_user']);
$jadwalId = intval($_GET['jadwal'] ?? $_POST['jadwal'] ?? 0);

if (!$dosen || !$jadwalId) {
    header('Location: mata-kuliah.php');
    exit;
}

$stmt = $mysqli->prepare('SELECT jk.id_jadwal, mk.nama_mata_kuliah, mk.kode_mata_kuliah, mk.sks, ta.tahun_akademik, jk.kelas, jk.hari
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

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? '')) {
        $errors[] = 'Token CSRF tidak valid.';
    } else {
        $ids = $_POST['detail_krs_id'] ?? [];
        $tugasList = $_POST['nilai_tugas'] ?? [];
        $utsList = $_POST['nilai_uts'] ?? [];
        $uasList = $_POST['nilai_uas'] ?? [];

        foreach ($ids as $index => $detailId) {
            $detailId = (int) $detailId;
            $tugas = (float) ($tugasList[$index] ?? 0);
            $uts = (float) ($utsList[$index] ?? 0);
            $uas = (float) ($uasList[$index] ?? 0);
            $akhir = calculate_final_score($tugas, $uts, $uas);
            [$huruf, $bobot] = score_to_letter_and_weight($akhir);

            $stmtSave = $mysqli->prepare('INSERT INTO nilai (id_detail_krs, nilai_tugas, nilai_uts, nilai_uas, nilai_akhir, nilai_huruf, bobot)
                                          VALUES (?, ?, ?, ?, ?, ?, ?)
                                          ON DUPLICATE KEY UPDATE
                                            nilai_tugas = VALUES(nilai_tugas),
                                            nilai_uts = VALUES(nilai_uts),
                                            nilai_uas = VALUES(nilai_uas),
                                            nilai_akhir = VALUES(nilai_akhir),
                                            nilai_huruf = VALUES(nilai_huruf),
                                            bobot = VALUES(bobot)');
            $stmtSave->bind_param('iddddds', $detailId, $tugas, $uts, $uas, $akhir, $bobot, $huruf);
            $stmtSave->execute();
        }
        $message = 'Nilai berhasil disimpan.';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
  <div class="col-2">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>Input Nilai</h2>
    <p class="text-muted"><?php echo e($jadwal['nama_mata_kuliah']); ?> | <?php echo e($jadwal['tahun_akademik']); ?> | Kelas <?php echo e($jadwal['kelas']); ?></p>

    <?php if ($message): ?>
      <div class="alert alert-success"><?php echo e($message); ?></div>
    <?php endif; ?>
    <?php if ($errors): ?>
      <div class="alert alert-danger"><?php echo e(implode('<br>', $errors)); ?></div>
    <?php endif; ?>

    <form method="post">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="jadwal" value="<?php echo e($jadwalId); ?>">
      <table class="table table-striped table-responsive align-middle">
        <thead>
          <tr>
            <th>NIM</th>
            <th>Nama Mahasiswa</th>
            <th>Tugas</th>
            <th>UTS</th>
            <th>UAS</th>
            <th>Nilai Akhir</th>
            <th>Huruf</th>
            <th>Bobot</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $stmt2 = $mysqli->prepare('SELECT dk.id_detail_krs, m.nim, m.nama_mahasiswa,
                                          COALESCE(n.nilai_tugas, 0) AS nilai_tugas,
                                          COALESCE(n.nilai_uts, 0) AS nilai_uts,
                                          COALESCE(n.nilai_uas, 0) AS nilai_uas,
                                          COALESCE(n.nilai_akhir, 0) AS nilai_akhir,
                                          COALESCE(n.nilai_huruf, "E") AS nilai_huruf,
                                          COALESCE(n.bobot, 0) AS bobot
                                   FROM detail_krs dk
                                   JOIN krs k ON dk.id_krs = k.id_krs
                                   JOIN mahasiswa m ON k.id_mahasiswa = m.id_mahasiswa
                                   LEFT JOIN nilai n ON dk.id_detail_krs = n.id_detail_krs
                                   WHERE dk.id_jadwal = ? AND dk.status = "aktif"
                                   ORDER BY m.nama_mahasiswa');
        $stmt2->bind_param('i', $jadwalId);
        $stmt2->execute();
        $res = $stmt2->get_result();
        $index = 0;
        while ($row = $res->fetch_assoc()):
        ?>
          <tr>
            <td><?php echo e($row['nim']); ?></td>
            <td><?php echo e($row['nama_mahasiswa']); ?></td>
            <td><input type="number" name="nilai_tugas[]" class="form-control" step="0.01" min="0" max="100" value="<?php echo e($row['nilai_tugas']); ?>"></td>
            <td><input type="number" name="nilai_uts[]" class="form-control" step="0.01" min="0" max="100" value="<?php echo e($row['nilai_uts']); ?>"></td>
            <td><input type="number" name="nilai_uas[]" class="form-control" step="0.01" min="0" max="100" value="<?php echo e($row['nilai_uas']); ?>"></td>
            <td><?php echo e($row['nilai_akhir']); ?></td>
            <td><?php echo e($row['nilai_huruf']); ?></td>
            <td><?php echo e($row['bobot']); ?></td>
            <td><input type="hidden" name="detail_krs_id[]" value="<?php echo e($row['id_detail_krs']); ?>"></td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
      <button class="btn btn-primary">Simpan Nilai</button>
    </form>
  </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
