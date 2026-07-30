<?php
require_once __DIR__ . '/../../config/auth.php';
require_role('admin');

require '..'.'/../includes/header.php';
?>
  <div class="col-2">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>Daftar Mahasiswa</h2>
    <a href="create.php" class="btn btn-primary mb-3">Tambah Mahasiswa</a>
    <table class="table table-striped">
      <thead>
        <tr><th>NIM</th><th>Nama</th><th>Program Studi</th><th>Aksi</th></tr>
      </thead>
      <tbody>
      <?php
      $stmt = $mysqli->prepare('SELECT id_mahasiswa, nim, nama_mahasiswa, program_studi FROM mahasiswa ORDER BY nama_mahasiswa');
      $stmt->execute();
      $res = $stmt->get_result();
      while ($row = $res->fetch_assoc()):
      ?>
        <tr>
          <td><?php echo e($row['nim']); ?></td>
          <td><?php echo e($row['nama_mahasiswa']); ?></td>
          <td><?php echo e($row['program_studi']); ?></td>
          <td>
            <a href="edit.php?id=<?php echo $row['id_mahasiswa']; ?>" class="btn btn-sm btn-warning">Edit</a>
            <form action="delete.php" method="post" style="display:inline-block;" onsubmit="return confirm('Hapus mahasiswa ini?');">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="id" value="<?php echo $row['id_mahasiswa']; ?>">
              <button class="btn btn-sm btn-danger">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
