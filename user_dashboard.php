<?php
require_once __DIR__ . '/../inc/functions.php';

// cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: /tabungan_qurban/auth/user_login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// ambil data donatur
$saver = get_saver($user_id);
$saldo = get_balance($user_id);

$target_awal = $saver['target_nominal'] ?? 0;

// hitung total setor
$stmt = $pdo->prepare("SELECT SUM(amount) as total_setor FROM transactions WHERE saver_id=? AND jenis='setor'");
$stmt->execute([$user_id]);
$total_setor = $stmt->fetchColumn() ?? 0;

// sisa target = target_awal - total setor
$sisa_target = max($target_awal - $total_setor, 0);

$progress = $target_awal > 0 ? round(($saldo / $target_awal) * 100, 2) : 0;
// cek apakah sudah lunas
$is_lunas = ($saldo >= $target_awal && $target_awal > 0);

// jika sudah lunas, hapus semua notifikasi user ini
if ($is_lunas) {
   $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE saver_id = ?");
    $stmt->execute([$user_id]);
}

// ambil tanggal_lunas dari $saver (bukan $row)
$tanggal_lunas_raw = $saver['tanggal_lunas'] ?? null;

// buat status badge secara defensif (tidak melakukan echo di sini)
$status_badge = '-';
if (!empty($tanggal_lunas_raw) && $tanggal_lunas_raw !== '0000-00-00') {
    $ts = strtotime($tanggal_lunas_raw);
    if ($ts !== false && $ts > 0) {
        $hari_ini = time();
        $status_badge = $hari_ini > $ts
            ? "<span class='badge bg-danger'>Terlambat</span>"
            : "<span class='badge bg-success'>Aktif</span>";
    }
}


include __DIR__ . '/../inc/header.php';
?>

<?php if (!empty($_SESSION['success'])): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= $_SESSION['success']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= $_SESSION['error']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<link rel="stylesheet" href="/tabungan_qurban/assets/css/user_dashboard.css">

<div class="row">
  <div class="col-md-8 mx-auto">
    <div class="card shadow-sm mb-3">
      <div class="card-body">
        <h4>Selamat datang, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋</h4>
        <p class="text-muted">Dashboard Tabungan Qurban Anda</p>
        <hr>

        <div class="row mb-3">
          <div class="col-md-6">
            <h6>Target Qurban</h6>
            <p><strong>Rp <?= number_format($target_awal, 0, ',', '.') ?></strong></p>
          </div>
          <div class="col-md-6">
            <h6>Sisa Target</h6>
            <p><strong>Rp <?= number_format($sisa_target, 0, ',', '.') ?></strong></p>
          </div>
        </div>

        <td>
  <?= $tanggal_lunas_raw && $tanggal_lunas_raw !== '0000-00-00'
        ? date('d M Y', strtotime($tanggal_lunas_raw))
        : '-' ?>
  <?= $status_badge !== '-' ? $status_badge : '' ?>
</td>


        <div class="row mb-3">
          <div class="col-md-6">
            <h6>Saldo Saat Ini</h6>
            <p><strong>Rp <?= number_format($saldo, 0, ',', '.') ?></strong></p>
          </div>
        </div>

        <h6>Progress Tabungan</h6>
        <div class="progress mb-3" style="height:25px;">
          <div class="progress-bar <?= $progress >= 100 ? 'bg-success' : 'bg-info' ?>"
               role="progressbar"
               style="width: <?= min($progress,100) ?>%">
            <?= $progress ?>%
          </div>
        </div>

        <ul class="list-group mb-3" id="notif-list">
<?php
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE saver_id=? AND is_read=0 ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$notifs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($notifs):
  foreach ($notifs as $n):
?>
  <li class="list-group-item d-flex justify-content-between align-items-start" id="notif-<?= $n['id'] ?>">
    <div>
      <strong><?= htmlspecialchars($n['message']) ?></strong><br>
      <small class="text-muted"><?= date('d M Y', strtotime($n['created_at'])) ?></small>
    </div>
    <button class="btn btn-outline-success btn-sm mark-read" data-id="<?= $n['id'] ?>">✔️</button>
  </li>
<?php endforeach; else: ?>
  <li class="list-group-item text-center text-muted">Tidak ada notifikasi baru</li>
<?php endif; ?>
</ul>


        <div class="d-flex gap-2">
          <a href="/tabungan_qurban/pages/user_transactions.php?type=setor" class="btn btn-success">+ Setor</a>
        </div>
      </div>
    </div>


    <div class="card shadow-sm">
      <div class="card-body">
        <h5>Riwayat Transaksi</h5>
        <table class="table table-striped table-sm">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Jenis</th>
              <th>Jumlah</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // ambil transaksi terbaru
            $stmt = $pdo->prepare("SELECT * FROM transactions WHERE saver_id=? ORDER BY created_at DESC");
            $stmt->execute([$user_id]);
            $trans = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($trans):
              foreach ($trans as $t):
                // tentukan kolom tanggal yang tersedia
                $when = $t['created_at'] ?? $t['tanggal'] ?? $t['tgl_transaksi'] ?? $t['waktu'] ?? null;
                $when_display = $when ? date('d-m-Y H:i', strtotime($when)) : '-';

                $amount = isset($t['amount']) ? $t['amount'] : 0;
                $jenis = isset($t['jenis']) ? $t['jenis'] : '';
            ?>
            <tr>
              <td><?= htmlspecialchars($when_display) ?></td>
              <td>
                <span class="badge <?= $jenis=='setor'?'bg-success':'bg-danger' ?>">
                  <?= $jenis ? ucfirst(htmlspecialchars($jenis)) : '-' ?>
                </span>
              </td>
              <td>Rp <?= number_format($amount, 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="3" class="text-center text-muted">Belum ada transaksi</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script>
document.querySelectorAll('.mark-read').forEach(btn => {
  btn.addEventListener('click', function() {
    const id = this.dataset.id;
    const notifEl = document.getElementById('notif-' + id);

    // kirim request sederhana ke PHP
    fetch('/tabungan_qurban/pages/mark_notification.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'notif_id=' + encodeURIComponent(id)
    })
    .then(res => res.text())
    .then(text => {
      if (text.trim() === 'OK') {
        notifEl.remove();
        alert('✅ Notifikasi berhasil dihapus!');
      } else {
        alert('❌ Gagal menghapus notifikasi!');
      }
    })
    .catch(() => alert('⚠️ Terjadi kesalahan koneksi!'));
  });
});
</script>

<?php include __DIR__ . '/../inc/footer.php'; ?>
