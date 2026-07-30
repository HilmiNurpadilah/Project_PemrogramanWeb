<?php
require_once __DIR__ . '/../config/auth.php';
require_role('mahasiswa');

$userId = (int) $_SESSION['user']['id_user'];
$stmt = $mysqli->prepare('SELECT id_mahasiswa, nama_mahasiswa FROM mahasiswa WHERE id_user = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$mahasiswa = $stmt->get_result()->fetch_assoc();

$activeTa = null;
$stmt2 = $mysqli->prepare('SELECT id_tahun_akademik, tahun_akademik, semester FROM tahun_akademik WHERE status = "aktif" LIMIT 1');
$stmt2->execute();
$activeTa = $stmt2->get_result()->fetch_assoc();

if (!$mahasiswa || !$activeTa) {
    require_once __DIR__ . '/../includes/header.php';
    echo '<div class="container mt-4"><div class="alert alert-warning">Data mahasiswa atau tahun akademik aktif belum tersedia.</div></div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$message = '';
$errors = [];
$maxSks = 24;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? '')) {
        $errors[] = 'Token CSRF tidak valid.';
    } else {
        $selectedJadwal = array_map('intval', $_POST['jadwal'] ?? []);
        $uniqueSelected = array_values(array_unique(array_filter($selectedJadwal)));

        if (count($uniqueSelected) !== count($selectedJadwal)) {
            $errors[] = 'Mata kuliah yang sama tidak boleh dipilih lebih dari satu kali.';
        }

        if (empty($errors)) {
            $stmt3 = $mysqli->prepare('SELECT COALESCE(SUM(mk.sks), 0) AS total_sks
                                       FROM krs k
                                       LEFT JOIN detail_krs dk ON k.id_krs = dk.id_krs AND dk.status = "aktif"
                                       LEFT JOIN jadwal_kuliah jk ON dk.id_jadwal = jk.id_jadwal
                                       LEFT JOIN mata_kuliah mk ON jk.id_mata_kuliah = mk.id_mata_kuliah
                                       WHERE k.id_mahasiswa = ? AND k.id_tahun_akademik = ?');
            $stmt3->bind_param('ii', $mahasiswa['id_mahasiswa'], $activeTa['id_tahun_akademik']);
            $stmt3->execute();
            $currentTotal = (int) ($stmt3->get_result()->fetch_assoc()['total_sks'] ?? 0);

            $selectedSks = 0;
            if ($uniqueSelected) {
                $placeholders = implode(',', array_fill(0, count($uniqueSelected), '?'));
                $types = str_repeat('i', count($uniqueSelected));
                $sql = "SELECT COALESCE(SUM(mk.sks), 0) AS total_sks FROM jadwal_kuliah jk JOIN mata_kuliah mk ON jk.id_mata_kuliah = mk.id_mata_kuliah WHERE jk.id_jadwal IN ($placeholders) AND jk.id_tahun_akademik = ?";
                $stmt4 = $mysqli->prepare($sql);
                $bindValues = array_merge($uniqueSelected, [(int) $activeTa['id_tahun_akademik']]);
                $types .= 'i';
                $stmt4->bind_param($types, ...$bindValues);
                $stmt4->execute();
                $selectedSks = (int) ($stmt4->get_result()->fetch_assoc()['total_sks'] ?? 0);
            }

            if (($currentTotal + $selectedSks) > $maxSks) {
                $errors[] = 'Total SKS melebihi batas maksimal 24 SKS.';
            }

            if (empty($errors)) {
                $stmtKrs = $mysqli->prepare('SELECT id_krs, status_krs FROM krs WHERE id_mahasiswa = ? AND id_tahun_akademik = ? LIMIT 1');
                $stmtKrs->bind_param('ii', $mahasiswa['id_mahasiswa'], $activeTa['id_tahun_akademik']);
                $stmtKrs->execute();
                $existing = $stmtKrs->get_result()->fetch_assoc();

                if ($existing && $existing['status_krs'] === 'dikunci') {
                    $errors[] = 'KRS sudah dikunci dan tidak dapat diubah.';
                } else {
                    if (!$existing) {
                        $stmtInsertKrs = $mysqli->prepare('INSERT INTO krs (id_mahasiswa, id_tahun_akademik, tanggal_pengisian, status_krs) VALUES (?, ?, CURDATE(), "draft")');
                        $stmtInsertKrs->bind_param('ii', $mahasiswa['id_mahasiswa'], $activeTa['id_tahun_akademik']);
                        $stmtInsertKrs->execute();
                        $krsId = $stmtInsertKrs->insert_id;
                    } else {
                        $krsId = (int) $existing['id_krs'];
                    }

                    foreach ($uniqueSelected as $jadwalId) {
                        $stmtCheck = $mysqli->prepare('SELECT dk.id_detail_krs FROM detail_krs dk WHERE dk.id_krs = ? AND dk.id_jadwal = ? LIMIT 1');
                        $stmtCheck->bind_param('ii', $krsId, $jadwalId);
                        $stmtCheck->execute();
                        if (!$stmtCheck->get_result()->fetch_assoc()) {
                            $stmtDetail = $mysqli->prepare('INSERT INTO detail_krs (id_krs, id_jadwal, status) VALUES (?, ?, "aktif")');
                            $stmtDetail->bind_param('ii', $krsId, $jadwalId);
                            $stmtDetail->execute();
                        }
                    }

                    $message = 'KRS berhasil disimpan.';
                }
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
  <div class="col-2">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>Isi KRS</h2>
    <p class="text-muted">Mahasiswa: <?php echo e($mahasiswa['nama_mahasiswa']); ?> | Tahun Akademik: <?php echo e($activeTa['tahun_akademik'] . ' - ' . $activeTa['semester']); ?></p>

    <?php if ($message): ?>
      <div class="alert alert-success"><?php echo e($message); ?></div>
    <?php endif; ?>
    <?php if ($errors): ?>
      <div class="alert alert-danger"><?php echo e(implode('<br>', $errors)); ?></div>
    <?php endif; ?>

    <form method="post">
      <?php echo csrf_field(); ?>
      <table class="table table-striped table-responsive align-middle">
        <thead>
          <tr><th>Pilih</th><th>Kode</th><th>Mata Kuliah</th><th>SKS</th><th>Semester</th><th>Dosen</th><th>Jadwal</th></tr>
        </thead>
        <tbody>
        <?php
        $stmt5 = $mysqli->prepare('SELECT jk.id_jadwal, mk.kode_mata_kuliah, mk.nama_mata_kuliah, mk.sks, mk.semester, d.nama_dosen, jk.kelas, jk.hari, jk.jam_mulai, jk.jam_selesai
                                   FROM jadwal_kuliah jk
                                   JOIN mata_kuliah mk ON jk.id_mata_kuliah = mk.id_mata_kuliah
                                   JOIN dosen d ON jk.id_dosen = d.id_dosen
                                   WHERE jk.id_tahun_akademik = ?
                                   ORDER BY mk.semester, mk.nama_mata_kuliah');
        $stmt5->bind_param('i', $activeTa['id_tahun_akademik']);
        $stmt5->execute();
        $res = $stmt5->get_result();
        while ($row = $res->fetch_assoc()):
        ?>
          <tr>
            <td><input type="checkbox" name="jadwal[]" value="<?php echo e($row['id_jadwal']); ?>"></td>
            <td><?php echo e($row['kode_mata_kuliah']); ?></td>
            <td><?php echo e($row['nama_mata_kuliah']); ?></td>
            <td><?php echo e($row['sks']); ?></td>
            <td><?php echo e($row['semester']); ?></td>
            <td><?php echo e($row['nama_dosen']); ?></td>
            <td><?php echo e($row['hari'] . ' ' . substr($row['jam_mulai'], 0, 5) . '-' . substr($row['jam_selesai'], 0, 5) . ' / ' . $row['kelas']); ?></td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
      <button class="btn btn-primary">Simpan KRS</button>
    </form>
  </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
