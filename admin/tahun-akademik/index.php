<?php
require_once __DIR__ . '/../../config/auth.php';
require_role('admin');
require_once __DIR__ . '/../../includes/header.php';
?>
  <div class="col-2">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>Daftar Tahun Akademik</h2>
    <a href="create.php" class="btn btn-primary mb-3">Tambah Tahun Akademik</a>
    <table class="table table-striped">
      <thead>
        <tr><th>Tahun Akademik</th><th>Semester</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
      <?php
      $stmt = $mysqli->prepare('SELECT id_tahun_akademik, tahun_akademik, semester, status FROM tahun_akademik ORDER BY id_tahun_akademik DESC');
      $stmt->execute();
      $res = $stmt->get_result();
      while ($row = $res->fetch_assoc()):
      ?>
        <tr>
          <td><?php echo e($row['tahun_akademik']); ?></td>
          <td><?php echo e($row['semester']); ?></td>
          <td><?php echo e($row['status']); ?></td>
          <td>
            <a href="edit.php?id=<?php echo $row['id_tahun_akademik']; ?>" class="btn btn-sm btn-warning">Edit</a>
            <form action="delete.php" method="post" style="display:inline-block;" onsubmit="return confirm('Hapus tahun akademik ini?');">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="id" value="<?php echo $row['id_tahun_akademik']; ?>">
              <button class="btn btn-sm btn-danger">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
