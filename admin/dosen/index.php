<?php
require_once __DIR__ . '/../../config/auth.php';
require_role('admin');
require_once __DIR__ . '/../../includes/header.php';
?>
  <div class="col-2">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>Daftar Dosen</h2>
    <a href="create.php" class="btn btn-primary mb-3">Tambah Dosen</a>
    <table class="table table-striped">
      <thead>
        <tr><th>NIDN</th><th>Nama</th><th>Email</th><th>Aksi</th></tr>
      </thead>
      <tbody>
      <?php
      $stmt = $mysqli->prepare('SELECT id_dosen, nidn, nama_dosen, email FROM dosen ORDER BY nama_dosen');
      $stmt->execute();
      $res = $stmt->get_result();
      while ($row = $res->fetch_assoc()):
      ?>
        <tr>
          <td><?php echo e($row['nidn']); ?></td>
          <td><?php echo e($row['nama_dosen']); ?></td>
          <td><?php echo e($row['email']); ?></td>
          <td>
            <a href="edit.php?id=<?php echo $row['id_dosen']; ?>" class="btn btn-sm btn-warning">Edit</a>
            <form action="delete.php" method="post" style="display:inline-block;" onsubmit="return confirm('Hapus dosen ini?');">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="id" value="<?php echo $row['id_dosen']; ?>">
              <button class="btn btn-sm btn-danger">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
