<?php
require_once __DIR__ . '/../inc/functions.php';
require_login();

if (isset($_GET['approve'])) {
    $id = (int) $_GET['approve'];
    $pdo->prepare("UPDATE transactions SET status='approved' WHERE id=?")->execute([$id]);
}
if (isset($_GET['reject'])) {
    $id = (int) $_GET['reject'];
    $pdo->prepare("UPDATE transactions SET status='rejected' WHERE id=?")->execute([$id]);
}

$transactions = $pdo->query("SELECT t.*, s.nama 
    FROM transactions t JOIN savers s ON t.saver_id=s.id 
    ORDER BY t.created_at DESC")->fetchAll();

include __DIR__ . '/../inc/header.php';
?>
<div class="card p-3 shadow-sm">
  <h4>Validasi Transaksi</h4>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>#</th><th>Nama</th><th>Jumlah</th><th>Jenis</th>
        <th>Waktu</th><th>Status</th><th>Bukti</th><th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($transactions as $i => $t): ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td><?= htmlspecialchars($t['nama']) ?></td>
        <td>Rp <?= number_format($t['amount'],0,',','.') ?></td>
        <td><?= $t['jenis'] ?></td>
        <td><?= $t['created_at'] ?></td>
        <td>
          <?php if ($t['status']=='pending'): ?>
            <span class="badge bg-warning">Pending</span>
          <?php elseif ($t['status']=='approved'): ?>
            <span class="badge bg-success">Approved</span>
          <?php else: ?>
            <span class="badge bg-danger">Rejected</span>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($t['receipt']): ?>
            <a href="/tabungan_qurban/uploads/<?= $t['receipt'] ?>" target="_blank">Lihat</a>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($t['status']=='pending'): ?>
            <a href="?approve=<?= $t['id'] ?>" class="btn btn-sm btn-success">Approve</a>
            <a href="?reject=<?= $t['id'] ?>" class="btn btn-sm btn-danger">Reject</a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/../inc/footer.php'; ?>
