<?php
require_once __DIR__ . '/../inc/functions.php';
require_login();
include __DIR__ . '/../inc/header.php';

$mode = $_GET['mode'] ?? '';
$id   = $_GET['id'] ?? null;
$msg  = $_GET['msg'] ?? '';

$savers = get_all_savers();
$edit_data = $id ? get_saver($id) : null;
?>
<?php if (isset($_GET['status'])): ?>
  <div class="alert alert-<?= 
        $_GET['status'] === 'added' ? 'success' :
        ($_GET['status'] === 'edited' ? 'info' :
        ($_GET['status'] === 'deleted' ? 'success' :
        ($_GET['status'] === 'has_transactions' ? 'warning' : 'danger'))) ?>">
    <?php
      switch ($_GET['status']) {
        case 'added': echo "Penabung berhasil ditambahkan."; break;
        case 'edited': echo "Data penabung berhasil diperbarui."; break;
        case 'deleted': echo "Penabung berhasil dihapus."; break;
        case 'has_transactions': echo "Tidak bisa menghapus, penabung masih punya transaksi."; break;
        default: echo "Terjadi kesalahan."; break;
      }
    ?>
  </div>
<?php endif; ?>


<div class="row">
  <!-- FORM PENABUNG -->
  <div class="col-md-4">
    <div class="card p-3">
      <h5><?= $edit_data ? 'Edit Penabung' : 'Tambah Penabung' ?></h5>

      <?php if ($msg): ?>
        <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
      <?php endif; ?>

      <form method="post" action="save_saver.php">
        <input type="hidden" name="action" value="<?= $edit_data ? 'edit' : 'add' ?>">
        <?php if ($edit_data): ?>
          <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
        <?php endif; ?>

        <div class="mb-2">
          <label>Nama</label>
          <input name="nama" class="form-control" required value="<?= $edit_data['nama'] ?? '' ?>">
        </div>
        <div class="mb-2">
          <label>Alamat</label>
          <textarea name="alamat" class="form-control"><?= $edit_data['alamat'] ?? '' ?></textarea>
        </div>
        <div class="mb-2">
          <label>Telp</label>
          <input name="telp" class="form-control" value="<?= $edit_data['telp'] ?? '' ?>">
        </div>

        <div class="mb-2">
          <label>Target Qurban</label>
          <select name="target_qurban" id="target_qurban" class="form-select" required>
            <option value="">-- Pilih Hewan Qurban --</option>
            <option value="Sapi" data-nominal="20000000" <?= ($edit_data['target_qurban'] ?? '')=='Sapi'?'selected':'' ?>>Sapi - Rp 20.000.000</option>
            <option value="Domba" data-nominal="5000000" <?= ($edit_data['target_qurban'] ?? '')=='Domba'?'selected':'' ?>>Domba - Rp 5.000.000</option>
            <option value="Kambing" data-nominal="3000000" <?= ($edit_data['target_qurban'] ?? '')=='Kambing'?'selected':'' ?>>Kambing - Rp 3.000.000</option>
          </select>
          <input type="hidden" name="target_nominal" id="target_nominal" value="<?= $edit_data['target_nominal'] ?? 0 ?>">
        </div>

        <button class="btn btn-success w-100"><?= $edit_data ? 'Simpan Perubahan' : 'Tambah' ?></button>
      </form>
    </div>
  </div>

  <!-- LAPORAN PENABUNG -->
  <div class="col-md-8">
    <div class="card p-3">
      <h5>Laporan Otomatis Per Penabung</h5>

      <?php if ($msg): ?>
        <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
      <?php endif; ?>

      <table class="table table-striped table-sm">
        <thead>
          <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Target</th>
            <th>Saldo</th>
            <th>% Tercapai</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($savers as $i => $s): 
              $saldo = get_balance($s['id']);
              $target = $s['target_nominal'] ?? 0;
              $progress = $target > 0 ? round(($saldo / $target) * 100, 2) : 0;
          ?>
          <tr>
            <td><?= $i+1 ?></td>
            <td><?= htmlspecialchars($s['nama']) ?></td>
            <td>Rp <?= number_format($target,0,',','.') ?> (<?= htmlspecialchars($s['target_qurban']) ?>)</td>
            <td>Rp <?= number_format($saldo,0,',','.') ?></td>
            <td style="width:180px">
              <div class="progress">
                <div class="progress-bar <?= $progress >= 100 ? 'bg-success' : 'bg-info' ?>" 
                     role="progressbar" 
                     style="width: <?= min($progress,100) ?>%">
                  <?= $progress ?>%
                </div>
              </div>
            </td>
            <td>
              <a href="?mode=edit&id=<?= $s['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
              <a href="save_saver.php?action=del&id=<?= $s['id'] ?>&from=savers" 
                 class="btn btn-sm btn-danger" 
                 onclick="return confirm('Hapus penabung ini?')">Hapus</a>
              <a href="transactions.php?saver_id=<?= $s['id'] ?>" class="btn btn-sm btn-primary">Transaksi</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
document.getElementById('target_qurban').addEventListener('change', function() {
  let nominal = this.options[this.selectedIndex].getAttribute('data-nominal');
  document.getElementById('target_nominal').value = nominal || 0;
});
</script>

<?php include __DIR__ . '/../inc/footer.php'; ?>
