<?php
// includes/sidebar.php
// Simple sidebar; extend per role
$role = $_SESSION['user']['role'] ?? null;
?>
<div class="bg-light border" style="width:220px; padding:10px;">
  <h5>Menu</h5>
  <ul class="nav flex-column">
    <?php if ($role === 'admin'): ?>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(app_url('admin/dashboard.php')); ?>">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="#">Mahasiswa</a></li>
      <li class="nav-item"><a class="nav-link" href="#">Dosen</a></li>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(app_url('admin/mata-kuliah/index.php')); ?>">Mata Kuliah</a></li>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(app_url('admin/tahun-akademik/index.php')); ?>">Tahun Akademik</a></li>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(app_url('admin/jadwal-kuliah/index.php')); ?>">Jadwal Kuliah</a></li>
    <?php elseif ($role === 'dosen'): ?>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(app_url('dosen/dashboard.php')); ?>">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(app_url('dosen/mata-kuliah.php')); ?>">Mata Kuliah Diampu</a></li>
    <?php elseif ($role === 'mahasiswa'): ?>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(app_url('mahasiswa/dashboard.php')); ?>">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(app_url('mahasiswa/isi-krs.php')); ?>">Isi KRS</a></li>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(app_url('mahasiswa/krs.php')); ?>">KRS Saya</a></li>
      <li class="nav-item"><a class="nav-link" href="<?php echo e(app_url('mahasiswa/khs.php')); ?>">KHS / IPK</a></li>
    <?php endif; ?>
  </ul>
</div>
