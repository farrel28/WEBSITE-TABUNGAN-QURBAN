<?php
require_once __DIR__ . '/../inc/functions.php';

$err = '';
$msg = '';

// Inisialisasi supaya nilai tetap ada di form saat error
$nama = $alamat = $telp = $username = $target_qurban = '';
$target_nominal = 0;
$username_error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = $_POST['nama'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $telp   = $_POST['telp'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $target_qurban = $_POST['target_qurban'] ?? '';
    $target_nominal = $_POST['target_nominal'] ?? 0;
    $tanggal_lunas = $_POST['tanggal_lunas'] ?? '';

    if ($nama && $alamat && $telp && $username && $password && $target_qurban) {
        global $pdo;
        // cek apakah username sudah ada
        $stmt = $pdo->prepare("SELECT id FROM savers WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $err = "⚠️ Username sudah terpakai. Silakan gunakan yang lain.";
            $username_error = true;
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO savers 
                (nama, alamat, telp, username, password, target_qurban, target_nominal, tanggal_lunas) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$nama, $alamat, $telp, $username, $hash, $target_qurban, $target_nominal, $tanggal_lunas])) {
                $msg = "✅ Registrasi berhasil! Silakan login.";
                // reset form setelah sukses
                $nama = $alamat = $telp = $username = $target_qurban = '';
                $target_nominal = 0;
            } else {
                $err = "❌ Terjadi kesalahan saat menyimpan data.";
            }
        }
    } else {
        $err = "⚠️ Harap lengkapi semua field.";
    }
}

include __DIR__ . '/../inc/header.php';
?>

<link rel="stylesheet" href="/tabungan_qurban/assets/css/register.css">

<style>
.form-control.error {
  border-color: #dc3545;
  background-color: #fff5f5;
}
</style>

<div class="row justify-content-center">
  <div class="col-md-7">
    <div class="card shadow-sm">
      <div class="card-body">
        <h4 class="card-title mb-3">Registrasi Penabung</h4>

        <?php if ($err): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
        <?php endif; ?>
        <?php if ($msg): ?>
          <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="post">
          <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($nama) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" required><?= htmlspecialchars($alamat) ?></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">No. Telepon</label>
            <input type="text" name="telp" class="form-control" value="<?= htmlspecialchars($telp) ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" 
                   class="form-control <?= $username_error ? 'error' : '' ?>" 
                   value="<?= htmlspecialchars($username) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Target Tabungan Qurban</label>
            <select name="target_qurban" id="target_qurban" class="form-select" required>
              <option value="">-- Pilih Hewan Qurban --</option>
              <option value="Sapi" data-nominal="20000000" <?= $target_qurban == 'Sapi' ? 'selected' : '' ?>>Sapi - Rp 20.000.000</option>
              <option value="Domba" data-nominal="5000000" <?= $target_qurban == 'Domba' ? 'selected' : '' ?>>Domba - Rp 5.000.000</option>
              <option value="Kambing" data-nominal="3000000" <?= $target_qurban == 'Kambing' ? 'selected' : '' ?>>Kambing - Rp 3.000.000</option>
            </select>
            <input type="hidden" name="target_nominal" id="target_nominal" value="<?= htmlspecialchars($target_nominal) ?>">
          </div>

          <div class="mb-3">
  <label for="tanggal_lunas" class="form-label">Tanggal Target Pelunasan</label>
  <input type="date" name="tanggal_lunas" id="tanggal_lunas" class="form-control" required>
</div>

          <button type="submit" class="btn btn-primary">Daftar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('target_qurban').addEventListener('change', function() {
  let nominal = this.options[this.selectedIndex].getAttribute('data-nominal');
  document.getElementById('target_nominal').value = nominal || 0;
});
</script>
