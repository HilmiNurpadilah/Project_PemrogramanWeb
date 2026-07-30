<?php
// includes/sidebar.php
// Simple sidebar; extend per role
$role = $_SESSION['user']['role'] ?? null;
?>
<div class="bg-light border" style="width:220px; padding:10px;">
  <h5>Menu</h5>
  <ul class="nav flex-column">
    <?php if ($role === 'admin'): ?>
      <li class="nav-item"><a class="nav-link" href="/admin/dashboard.php">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="#">Mahasiswa</a></li>
      <li class="nav-item"><a class="nav-link" href="#">Dosen</a></li>
      <li class="nav-item"><a class="nav-link" href="/admin/mata-kuliah/index.php">Mata Kuliah</a></li>
      <li class="nav-item"><a class="nav-link" href="/admin/tahun-akademik/index.php">Tahun Akademik</a></li>
    <?php elseif ($role === 'dosen'): ?>
      <li class="nav-item"><a class="nav-link" href="/dosen/dashboard.php">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="#">Mata Kuliah</a></li>
    <?php elseif ($role === 'mahasiswa'): ?>
      <li class="nav-item"><a class="nav-link" href="/mahasiswa/dashboard.php">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="#">Isi KRS</a></li>
    <?php endif; ?>
  </ul>
</div>
