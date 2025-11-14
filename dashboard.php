<?php
require_once __DIR__ . '/../inc/functions.php';
require_login();
include __DIR__ . '/../inc/header.php';
include __DIR__ . '/../cron/check_deadline_notifications.php';


// ambil semua donatur
$savers = get_all_savers();

// cek jika admin pilih donatur
$selected_saver = $_GET['saver_id'] ?? null;
$labels = $setor = $tarik = [];

if ($selected_saver) {
    $daily = get_daily_transactions_by_saver($selected_saver);
    foreach ($daily as $d) {
        $labels[] = $d['tanggal'];
        $setor[] = $d['total_setor'];
    }
    $saver   = get_saver($selected_saver);
    $saldo   = get_balance($selected_saver);
    $target  = $saver['target_nominal'] ?? 0;
    $progress = $target > 0 ? round(($saldo / $target) * 100, 2) : 0;
}


// hitung total semua donatur
$total_savers  = count($savers);
$total_balance = 0;
foreach ($savers as $s) {
    $total_balance += get_balance($s['id']);
}
?>

<div class="row">
  <div class="col-md-12">
    <div class="card p-3 mb-3">
      <h4>Grafik Transaksi Harian per Donatur</h4>
      <form method="get" class="row g-2">
        <div class="col-auto">
          <select name="saver_id" class="form-select" onchange="this.form.submit()">
            <option value=""> Pilih Donatur </option>
            <?php foreach ($savers as $s): ?>
              <option value="<?= $s['id'] ?>" <?= $selected_saver == $s['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['nama']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </form>
    </div>
  </div>
</div>

<?php if ($selected_saver): ?>
<div class="row mb-3">
  <div class="col-md-12">
    <div class="card p-3">
      <h5>Grafik Transaksi Harian: <?= htmlspecialchars($saver['nama']) ?></h5>
      <canvas id="dailyChart" height="100"></canvas>
      <p class="mt-3"><b>Saldo:</b> Rp <?= number_format($saldo,0,',','.') ?> /
         <b>Target:</b> Rp <?= number_format($target,0,',','.') ?> (<?= $progress ?>%)
      </p>
      <div class="progress">
        <div class="progress-bar <?= $progress >= 100 ? 'bg-success' : 'bg-info' ?>"
             role="progressbar" style="width: <?= min($progress,100) ?>%">
          <?= $progress ?>%
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('dailyChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [
            {
                label: 'Setoran',
                data: <?= json_encode($setor) ?>,
                borderColor: 'green',
                backgroundColor: 'rgba(0,128,0,0.2)',
                fill: true,
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            title: { display: true, text: 'Setoran & Tarikan Harian (30 Hari Terakhir)' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
<?php endif; ?>

<div class="row text-center my-4">
  <div class="col-md-4">
    <div class="card p-3">
      <h6>Total Penabung</h6>
      <h3><?= $total_savers ?></h3>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3">
      <h6>Total Dana Tersimpan</h6>
      <h3>Rp <?= number_format($total_balance,0,',','.') ?></h3>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3">
      <h6>Admin</h6>
      <h4><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></h4>
    </div>
  </div>
</div>

<div class="mt-4">
  <h5>Daftar Penabung Terbaru</h5>
  <table class="table table-striped">
    <thead><tr><th>#</th><th>Nama</th><th>Telp</th><th>Saldo</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php foreach ($savers as $i => $s): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($s['nama']) ?></td>
          <td><?= htmlspecialchars($s['telp']) ?></td>
          <td>Rp <?= number_format(get_balance($s['id']),0,',','.') ?></td>
          <td>
            <a href="/tabungan_qurban/pages/transactions.php?saver_id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary">Transaksi</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/../inc/footer.php'; ?>
