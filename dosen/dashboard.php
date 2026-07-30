<?php
require_once __DIR__ . '/../config/auth.php';
require_role('dosen');
require 'includes/header.php';
?>
<div class="container mt-4">
    <h1>Dashboard Dosen</h1>
    <p>Selamat datang, <?php echo e($_SESSION['user']['username']); ?></p>
    <a href="/logout.php" class="btn btn-danger">Logout</a>
</div>
<?php require 'includes/footer.php'; ?>
