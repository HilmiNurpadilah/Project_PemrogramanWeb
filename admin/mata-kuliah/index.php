<?php
require_once __DIR__ . '/../../config/auth.php';
require_role('admin');
require_once __DIR__ . '/../../includes/header.php';
?>
  <div class="col-2">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>Daftar Mata Kuliah</h2>
    <a href="create.php" class="btn btn-primary mb-3">Tambah Mata Kuliah</a>
    <table class="table table-striped">
      <thead>
        <tr><th>Kode</th><th>Nama</th><th>SKS</th><th>Semester</th><th>Aksi</th></tr>
      </thead>
      <tbody>
      <?php
      $stmt = $mysqli->prepare('SELECT id_mata_kuliah, kode_mata_kuliah, nama_mata_kuliah, sks, semester FROM mata_kuliah ORDER BY semester, nama_mata_kuliah');
      $stmt->execute();
      $res = $stmt->get_result();
      while ($row = $res->fetch_assoc()):
      ?>
        <tr>
          <td><?php echo e($row['kode_mata_kuliah']); ?></td>
          <td><?php echo e($row['nama_mata_kuliah']); ?></td>
          <td><?php echo e($row['sks']); ?></td>
          <td><?php echo e($row['semester']); ?></td>
          <td>
            <a href="edit.php?id=<?php echo $row['id_mata_kuliah']; ?>" class="btn btn-sm btn-warning">Edit</a>
            <form action="delete.php" method="post" style="display:inline-block;" onsubmit="return confirm('Hapus mata kuliah ini?');">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="id" value="<?php echo $row['id_mata_kuliah']; ?>">
              <button class="btn btn-sm btn-danger">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
