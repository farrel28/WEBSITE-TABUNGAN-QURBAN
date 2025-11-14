<?php
require_once __DIR__ . '/../inc/functions.php';
require_login();
include __DIR__ . '/../inc/header_admin.php';

// lokasi upload relative terhadap root project
$upload_dir = __DIR__ . '/../uploads/';
$web_upload_dir = '/tabungan_qurban/uploads/';

// terima saver_id jika dikirim lewat GET
$saver_id = $_GET['saver_id'] ?? null;
$err = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $saver_id_post = $_POST['saver_id'];
    $amount = (float) $_POST['amount'];
    $jenis = $_POST['jenis'] ?? 'setor';
    $note = $_POST['note'] ?? '';

    // proses file upload (opsional)
    $receipt_filename = null;
    if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['receipt'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $err = 'Gagal meng-upload file. Kode error: ' . $file['error'];
        } else {
            $allowed = ['image/jpeg','image/png','application/pdf'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, $allowed)) {
                $err = 'Tipe file tidak diperbolehkan. Hanya JPG, PNG, PDF.';
            } elseif ($file['size'] > 5 * 1024 * 1024) {
                $err = 'Ukuran file melebihi 5 MB.';
            } else {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $safe_ext = $ext ?: ($mime === 'application/pdf' ? 'pdf' : 'jpg');
                $newname = time() . '_' . bin2hex(random_bytes(6)) . '.' . $safe_ext;

                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0775, true);
                }

                $target = $upload_dir . $newname;
                if (!move_uploaded_file($file['tmp_name'], $target)) {
                    $err = 'Gagal memindahkan file upload.';
                } else {
                    $receipt_filename = $newname;
                }
            }
        }
    }

    if (!$err) {
        add_transaction($saver_id_post, $amount, $jenis, $note, $receipt_filename);
        header('Location: /tabungan_qurban/pages/transactions.php?saver_id=' . $saver_id_post);
        exit;
    }
}

$savers = get_all_savers();
// Pagination setup
$limit = 5; // jumlah data per halaman
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// hitung total transaksi untuk pagination
$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM transactions" . ($saver_id ? " WHERE saver_id = ?" : ""));
$total_stmt->execute($saver_id ? [$saver_id] : []);
$total_rows = $total_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// ambil transaksi per halaman
if ($saver_id) {
    $stmt = $pdo->prepare("SELECT t.*, s.nama 
                           FROM transactions t 
                           JOIN savers s ON t.saver_id = s.id 
                           WHERE t.saver_id = ? 
                           ORDER BY t.created_at DESC 
                           LIMIT $limit OFFSET $offset");
    $stmt->execute([$saver_id]);
} else {
    $stmt = $pdo->query("SELECT t.*, s.nama 
                         FROM transactions t 
                         JOIN savers s ON t.saver_id = s.id 
                         ORDER BY t.created_at DESC 
                         LIMIT $limit OFFSET $offset");
}
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
$saver = $saver_id ? get_saver($saver_id) : null;
$balance = $saver_id ? get_balance($saver_id) : 0;
?>

<div class="row">
  <div class="col-md-5">
    <div class="card p-3">
      <h5>Tambah Transaksi</h5>
      <?php if ($err): ?><div class="alert alert-danger"><?=htmlspecialchars($err)?></div><?php endif; ?>
     <form method="post" enctype="multipart/form-data">
  <div class="mb-2">
    <label>Penabung</label>
    <select name="saver_id" class="form-select" required>
      <option value="">-- pilih --</option>
      <?php foreach ($savers as $s): ?>
        <option value="<?= $s['id'] ?>" <?= $s['id']==$saver_id ? 'selected':'' ?>>
          <?= htmlspecialchars($s['nama']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-2">
    <label>Jumlah (Rp)</label>
    <input name="amount" type="number" step="10000" class="form-control" required>
    <script>
 const inputJumlah = document.getElementById('jumlah');
  inputJumlah.addEventListener('keydown', function(e) {
    let step = 10000;
    let val = parseInt(inputJumlah.value) || 0;
    if (e.key === "ArrowUp") {
      e.preventDefault();
      inputJumlah.value = val + step;
    } else if (e.key === "ArrowDown") {
      e.preventDefault();
      inputJumlah.value = val - step < 0 ? 0 : val - step;
    }
  });
    </script>
    <style>
      .pagination .page-link {
  border-radius: 10px;
  margin: 0 3px;
}

    </style>
  </div>

  <div class="mb-2">
    <label>Jenis</label>
    <select name="jenis" class="form-select">
      <option value="setor">Setor</option>
    </select>
  </div>

  <div class="mb-2">
    <label>Catatan</label>
    <input name="note" class="form-control">
  </div>

  <div class="mb-2">
    <label>Bukti (foto atau PDF, max 5MB)</label>
    <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf" class="form-control">
  </div>

  <button class="btn btn-primary">Simpan</button>
</form>
    </div>
    <?php if ($saver): ?>
      <div class="card p-3 mt-3">
        <h6>Saldo <?= htmlspecialchars($saver['nama']) ?>: Rp <?= number_format($balance,0,',','.') ?></h6>
      </div>
    <?php endif; ?>
  </div>

  <div class="col-md-7">
    <div class="card p-3">
      <div class="d-flex justify-content-between align-items-center">
        <h5>Riwayat Transaksi <?= $saver ? ' - ' . htmlspecialchars($saver['nama']) : '' ?></h5>
        <div>
          <a class="btn btn-sm btn-outline-secondary" href="/tabungan_qurban/pages/export_pdf.php">Export Semua (PDF)</a>
          <?php if ($saver): ?>
            <a class="btn btn-sm btn-outline-primary" href="/tabungan_qurban/pages/export_pdf.php?saver_id=<?= $saver['id'] ?>">Export <?= htmlspecialchars($saver['nama']) ?></a>
          <?php endif; ?>
        </div>
      </div>

      <table class="table table-striped">
        <thead><tr><th>#</th><th>Nama</th><th>Jumlah</th><th>Jenis</th><th>Waktu</th><th>Catatan</th><th>Bukti</th></tr></thead>
        <tbody>
          <?php foreach ($transactions as $i => $t): ?>
            <tr>
              <td><?= $i+1 ?></td>
              <td><?= htmlspecialchars($t['nama']) ?></td>
              <td>Rp <?= number_format($t['amount'],0,',','.') ?></td>
              <td><?= $t['jenis'] ?></td>
              <td><?= $t['created_at'] ?></td>
              <td><?= htmlspecialchars($t['note']) ?></td>
             <td>
  <?php if (!empty($t['receipt'])): 
        $path = $web_upload_dir . $t['receipt'];
        $ext = strtolower(pathinfo($t['receipt'], PATHINFO_EXTENSION));
  ?>
    <?php if (in_array($ext, ['jpg','jpeg','png'])): ?>
      <a href="<?= htmlspecialchars($path) ?>" target="_blank" class="btn btn-sm btn-outline-info">Lihat</a>
    <?php else: ?>
      <a href="<?= htmlspecialchars($path) ?>" target="_blank" class="btn btn-sm btn-outline-info">Download</a>
    <?php endif; ?>
  <?php else: ?>
    <span class="text-muted">-</span>
  <?php endif; ?>
</td>

            </tr>
          <?php endforeach; ?>
          <?php if (empty($transactions)): ?>
            <tr><td colspan="7" class="text-center">Tidak ada transaksi</td></tr>
          <?php endif; ?>
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
</div>

<?php include __DIR__ . '/../inc/footer.php'; ?>
