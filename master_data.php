<?php
require_once __DIR__ . '/../inc/functions.php';
require_login();
include __DIR__ . '/../inc/header.php';

$mode = $_GET['mode'] ?? '';
$id   = $_GET['id'] ?? null;

$savers = get_all_savers();
$edit_data = $id ? get_saver($id) : null;
?>

<div class="row">
  <!-- FORM PENABUNG -->
  <div class="col-md-4">
    <div class="card p-3">
      <h5><?= $edit_data ? 'Edit Penabung' : 'Tambah Penabung' ?></h5>
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
        <button class="btn btn-success"><?= $edit_data ? 'Simpan Perubahan' : 'Tambah' ?></button>
      </form>
    </div>
  </div>

<div class="col-md-8">
  <div class="card p-3 shadow-sm">
    <h5 class="mb-3">📊 Laporan Otomatis Per Penabung</h5>

    <table class="table table-striped table-hover table-sm align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Nama</th>
          <th>Target</th>
          <th>Saldo</th>
          <th>% Tercapai</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $per_page = 5; // jumlah baris per halaman
        $total_data = count($savers);
        $total_pages = ceil($total_data / $per_page);
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $start = ($page - 1) * $per_page;

        $page_savers = array_slice($savers, $start, $per_page);

        foreach ($page_savers as $i => $s):
            $saldo = get_balance($s['id']);
            $target = $s['target_nominal'] ?? 0;
            $progress = $target > 0 ? round(($saldo / $target) * 100, 2) : 0;
        ?>
        <tr>
          <td><?= $start + $i + 1 ?></td>
          <td><?= htmlspecialchars($s['nama']) ?></td>
          <td>Rp <?= number_format($target, 0, ',', '.') ?> <br><small>(<?= htmlspecialchars($s['target_qurban']) ?>)</small></td>
          <td>Rp <?= number_format($saldo, 0, ',', '.') ?></td>
          <td style="width:180px">
            <div class="progress" style="height: 18px;">
              <div class="progress-bar <?= $progress >= 100 ? 'bg-success' : 'bg-info' ?>" 
                   role="progressbar" 
                   style="width: <?= min($progress,100) ?>%">
                <?= $progress ?>%
              </div>
            </div>
          </td>
          <td>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
              <a href="?mode=edit&id=<?= $s['id'] ?>" class="btn btn-sm btn-warning">
                <i class="bi bi-pencil-square"></i> Edit
              </a>
              <a href="save_saver.php?action=del&id=<?= $s['id'] ?>" 
                 class="btn btn-sm btn-danger"
                 onclick="return confirm('Hapus penabung ini?')">
                <i class="bi bi-trash"></i> Hapus
              </a>
              <a href="transactions.php?saver_id=<?= $s['id'] ?>" class="btn btn-sm btn-primary">
                <i class="bi bi-receipt"></i> Transaksi
              </a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Pagination -->
    <nav aria-label="Page navigation" class="mt-3">
      <ul class="pagination justify-content-center">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
          <a class="page-link" href="?page=<?= $page - 1 ?>">‹</a>
        </li>
        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
          <li class="page-item <?= $page == $p ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
          <a class="page-link" href="?page=<?= $page + 1 ?>">›</a>
        </li>
      </ul>
    </nav>

  </div>
</div>


<script>
document.getElementById('target_qurban').addEventListener('change', function() {
  let nominal = this.options[this.selectedIndex].getAttribute('data-nominal');
  document.getElementById('target_nominal').value = nominal || 0;
});
</script>

<?php include __DIR__ . '/../inc/footer.php'; ?>
