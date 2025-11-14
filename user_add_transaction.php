<?php
require_once __DIR__ . '/../inc/functions.php';
require_user_login();

$err = '';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $saver_id = $_SESSION['user_id'];
    $amount   = (float) $_POST['amount'];
    $jenis    = $_POST['jenis'];
    $note     = $_POST['note'] ?? '';

    // handle upload
    $receipt_filename = null;
    if (!empty($_FILES['receipt']) && $_FILES['receipt']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['receipt'];
        $allowed = ['image/jpeg','image/png','application/pdf'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed)) {
            $err = 'Format file salah (hanya JPG, PNG, PDF).';
        } elseif ($file['size'] > 5*1024*1024) {
            $err = 'Ukuran file max 5MB.';
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newname = time().'_'.bin2hex(random_bytes(5)).'.'.$ext;
            $upload_dir = __DIR__.'/../uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0775, true);
            if (move_uploaded_file($file['tmp_name'], $upload_dir.$newname)) {
                $receipt_filename = $newname;
            }
        }
    }

    if (!$err) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO transactions (saver_id, amount, jenis, note, receipt, status) 
                               VALUES (?, ?, ?, ?, ?, 'pending')");
        if ($stmt->execute([$saver_id, $amount, $jenis, $note, $receipt_filename])) {
            $msg = 'Transaksi berhasil diajukan dan menunggu validasi admin.';
        } else {
            $err = 'Gagal menyimpan transaksi.';
        }
    }
}

include __DIR__ . '/../inc/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h4 class="card-title mb-3">Ajukan Transaksi</h4>

        <?php if ($err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>
        <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

        <form method="post" enctype="multipart/form-data">
          <div class="mb-3">
            <label>Jumlah (Rp)</label>
            <input type="number" name="amount" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Jenis Transaksi</label>
            <select name="jenis" class="form-select" required>
              <option value="setor">Setor</option>
              <option value="tarik">Tarik</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Catatan</label>
            <input type="text" name="note" class="form-control">
          </div>
          <div class="mb-3">
            <label>Bukti Transfer (jpg/png/pdf)</label>
            <input type="file" name="receipt" class="form-control">
          </div>
          <button class="btn btn-primary">Ajukan Transaksi</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../inc/footer.php'; ?>
