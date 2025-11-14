<?php
require_once __DIR__ . '/../inc/functions.php';
require_login();
include __DIR__ . '/../inc/header.php';

$savers = get_all_savers();
?>
<div class="card p-3">
  <h5>Laporan Ringkas</h5>
  <table class="table table-bordered">
    <thead><tr><th>#</th><th>Nama</th><th>Target</th><th>Saldo</th><th>% Tercapai</th></tr></thead>
    <tbody>
      <?php foreach ($savers as $i => $s): 
        $saldo = get_balance($s['id']);
        $target = (float)$s['target_qurban'];
        $percent = $target>0 ? round($saldo / $target * 100,2) : 0;
      ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($s['nama']) ?></td>
          <td>Rp <?= number_format($target,0,',','.') ?></td>
          <td>Rp <?= number_format($saldo,0,',','.') ?></td>
          <td><?= $percent ?>%</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/../inc/footer.php'; ?>