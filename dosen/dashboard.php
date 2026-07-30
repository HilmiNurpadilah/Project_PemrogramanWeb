<?php
require_once __DIR__ . '/../config/auth.php';
require_role('dosen');

$userId = $_SESSION['user']['id_user'];
$stmt = $mysqli->prepare('SELECT id_dosen, nama_dosen FROM dosen WHERE id_user = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$dosen = $stmt->get_result()->fetch_assoc();

$totalMatkul = 0;
$totalKelas = 0;
if ($dosen) {
    $stmt2 = $mysqli->prepare('SELECT COUNT(*) AS total_matkul, COUNT(DISTINCT id_tahun_akademik) AS total_kelas FROM jadwal_kuliah WHERE id_dosen = ?');
    $stmt2->bind_param('i', $dosen['id_dosen']);
    $stmt2->execute();
    $counts = $stmt2->get_result()->fetch_assoc();
    $totalMatkul = (int) ($counts['total_matkul'] ?? 0);
    $totalKelas = (int) ($counts['total_kelas'] ?? 0);
}

require_once __DIR__ . '/../includes/header.php';
?>
    <div class="col-2">
        <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    </div>
    <div class="col-10 mt-4">
        <h1>Dashboard Dosen</h1>
        <p>Selamat datang, <?php echo e($_SESSION['user']['username']); ?></p>
        <div class="row mt-4">
            <div class="col-md-4 mb-3">
                <div class="card text-bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Mata Kuliah Diampu</h5>
                        <p class="card-text fs-3"><?php echo e($totalMatkul); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Kelas/Ajaran</h5>
                        <p class="card-text fs-3"><?php echo e($totalKelas); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <a href="<?php echo e(app_url('dosen/mata-kuliah.php')); ?>" class="btn btn-outline-primary">Lihat Mata Kuliah Diampu</a>
        <a href="<?php echo e(app_url('logout.php')); ?>" class="btn btn-danger ms-2">Logout</a>
    </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

