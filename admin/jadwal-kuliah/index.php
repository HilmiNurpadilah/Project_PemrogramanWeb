<?php
require_once __DIR__ . '/../../config/auth.php';
require_role('admin');
require_once __DIR__ . '/../../includes/header.php';
?>
  <div class="col-2">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
  </div>
  <div class="col-10 mt-4">
    <h2>Daftar Jadwal Kuliah</h2>
    <a href="create.php" class="btn btn-primary mb-3">Tambah Jadwal</a>
    <table class="table table-striped table-responsive">
      <thead>
        <tr><th>Mata Kuliah</th><th>Dosen</th><th>Tahun Akademik</th><th>Kelas</th><th>Hari</th><th>Jam</th><th>Ruangan</th><th>Kuota</th><th>Aksi</th></tr>
      </thead>
      <tbody>
      <?php
      $sql = 'SELECT jk.id_jadwal, mk.nama_mata_kuliah, d.nama_dosen, ta.tahun_akademik, jk.kelas, jk.hari, jk.jam_mulai, jk.jam_selesai, jk.ruangan, jk.kuota
              FROM jadwal_kuliah jk
              JOIN mata_kuliah mk ON jk.id_mata_kuliah = mk.id_mata_kuliah
              JOIN dosen d ON jk.id_dosen = d.id_dosen
              JOIN tahun_akademik ta ON jk.id_tahun_akademik = ta.id_tahun_akademik
              ORDER BY ta.id_tahun_akademik DESC, jk.hari, jk.jam_mulai';
      $stmt = $mysqli->prepare($sql);
      $stmt->execute();
      $res = $stmt->get_result();
      while ($row = $res->fetch_assoc()):
      ?>
        <tr>
          <td><?php echo e($row['nama_mata_kuliah']); ?></td>
          <td><?php echo e($row['nama_dosen']); ?></td>
          <td><?php echo e($row['tahun_akademik']); ?></td>
          <td><?php echo e($row['kelas']); ?></td>
          <td><?php echo e($row['hari']); ?></td>
          <td><?php echo e(substr($row['jam_mulai'], 0, 5) . ' - ' . substr($row['jam_selesai'], 0, 5)); ?></td>
          <td><?php echo e($row['ruangan']); ?></td>
          <td><?php echo e($row['kuota']); ?></td>
          <td>
            <a href="edit.php?id=<?php echo $row['id_jadwal']; ?>" class="btn btn-sm btn-warning">Edit</a>
            <form action="delete.php" method="post" style="display:inline-block;" onsubmit="return confirm('Hapus jadwal ini?');">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="id" value="<?php echo $row['id_jadwal']; ?>">
              <button class="btn btn-sm btn-danger">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
