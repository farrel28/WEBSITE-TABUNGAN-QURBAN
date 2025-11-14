<?php
require_once __DIR__ . '/../inc/functions.php';

// cek login user
if (!isset($_SESSION['user_id'])) {
    header('Location: /tabungan_qurban/auth/login_user.php');
    exit;
}

// --- Tambahan untuk logika target ---
$campaign_id = $_GET['campaign_id'] ?? null;

if ($campaign_id) {
    $stmt = $pdo->prepare("SELECT target, 
        (SELECT IFNULL(SUM(amount),0) FROM transactions WHERE campaign_id = ?) AS total 
        FROM campaigns WHERE id = ?");
    $stmt->execute([$campaign_id, $campaign_id]);
    $data = $stmt->fetch();
    $target = $data['target'] ?? 0;
    $total_sekarang = $data['total'] ?? 0;
    $sisa = max($target - $total_sekarang, 0);
}

$type = $_GET['type'] ?? 'setor';
$user_id = $_SESSION['user_id'];

// lokasi upload
$upload_dir = __DIR__ . '/../uploads/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0775, true);

// proses transaksi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jumlah = (int) $_POST['jumlah'];
    $bukti = null;

    if (!empty($_FILES['bukti']['name']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'application/pdf'];
        $mime = mime_content_type($_FILES['bukti']['tmp_name']);
        if (in_array($mime, $allowed) && $_FILES['bukti']['size'] <= 5 * 1024 * 1024) {
            $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
            $newName = time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['bukti']['tmp_name'], $upload_dir . $newName)) $bukti = $newName;
        } else {
            $_SESSION['error'] = "Upload gagal! Hanya JPG, PNG, atau PDF (max 5MB).";
            header('Location: /tabungan_qurban/pages/user_dashboard.php');
            exit;
        }
    }

    if ($jumlah > 0) {
        if (isset($sisa) && $jumlah > $sisa) {
            $_SESSION['warning'] = "Target sudah terpenuhi! Kelebihan sebesar Rp " .
                number_format($jumlah - $sisa, 0, ',', '.') . " akan dicatat sebagai donasi tambahan.";
        }

        $note = trim($_POST['note'] ?? '');
        $words = preg_split('/\s+/', $note);
        if (count($words) > 100) $note = implode(' ', array_slice($words, 0, 100));

        $stmt = $pdo->prepare("INSERT INTO transactions (saver_id, jenis, amount, note, created_at, receipt)
                               VALUES (?, ?, ?, ?, NOW(), ?)");
        $stmt->execute([$user_id, $type, $jumlah, $note, $bukti]);

        $_SESSION['success'] = "Transaksi berhasil disimpan, bukti telah dikirim ke admin.";
        header('Location: /tabungan_qurban/pages/user_dashboard.php');
        exit;
    }
}

include __DIR__ . '/../inc/header.php';
?>

<?php if (!empty($_SESSION['success'])): ?>
<div class="alert alert-success alert-dismissible fade show">
  <?= $_SESSION['success']; ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['success']); endif; ?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5><?= ucfirst($type) ?> Tabungan</h5>

        <form method="post" enctype="multipart/form-data">
          <!-- Pilih nominal -->
          <div class="mb-3 text-center">
            <label class="form-label fw-semibold">Pilih Jumlah Setoran</label>
            <div class="d-flex flex-wrap justify-content-center gap-2">
              <button type="button" class="btn btn-outline-primary nominal-btn" data-value="50000">Rp 50.000</button>
              <button type="button" class="btn btn-outline-primary nominal-btn" data-value="100000">Rp 100.000</button>
              <button type="button" class="btn btn-outline-primary nominal-btn" data-value="200000">Rp 200.000</button>
              <button type="button" class="btn btn-outline-primary nominal-btn" data-value="300000">Rp 300.000</button>
              <button type="button" class="btn btn-outline-primary nominal-btn" data-value="500000">Rp 500.000</button>
              <button type="button" class="btn btn-outline-primary nominal-btn" data-value="1000000">Rp 1.000.000</button>
              <button type="button" class="btn btn-outline-secondary" id="btnLainnya">Nominal Lainnya</button>
            </div>
            <input type="hidden" name="jumlah" id="jumlah" value="0">
            <div class="mt-3">
              <span id="selectedNominal" class="fw-bold text-success fs-5">Belum ada nominal dipilih</span>
            </div>
          </div>

          <!-- Modal nominal lain -->
          <div class="modal fade" id="modalNominalLain" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                  <h5 class="modal-title">Masukkan Nominal Lain</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                  <input type="number" id="inputNominalLain" class="form-control text-center" placeholder="Masukkan nominal (Rp)">
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-primary" id="simpanNominalLain" data-bs-dismiss="modal">Simpan</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Pilih metode pembayaran -->
          <div class="text-center mt-4">
            <button type="button" class="btn btn-success w-100 py-3" data-bs-toggle="modal" data-bs-target="#paymentModal">
              Pilih Metode Pembayaran
            </button>
          </div>

          <!-- tampilkan metode yang dipilih -->
          <div id="selectedMethod" style="display:none;" class="text-center mt-3">
            <img id="selectedLogo" src="" width="70" class="mb-2">
            <div id="selectedName" class="fw-bold"></div>
          </div>
          <input type="hidden" name="payment_method" id="payment_method">

          <!-- Modal metode -->
          <div class="modal fade" id="paymentModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
              <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header text-white" style="background:linear-gradient(90deg,#009272,#00b894)">
                  <h5 class="modal-title">Pilih Metode Pembayaran</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <div id="step-kategori">
                    <div class="list-group">
                      <a href="#" class="list-group-item list-group-item-action" onclick="showList('bank')">🏦 Transfer Bank / M-Banking</a>
                      <a href="#" class="list-group-item list-group-item-action" onclick="showList('ewallet')">💸 E-Wallet (GoPay, OVO, Dana, ShopeePay)</a>
                      <a href="#" class="list-group-item list-group-item-action" onclick="showQRIS()">🔳 QRIS</a>
                    </div>
                  </div>

                  <div id="step-list" style="display:none;">
                    <button type="button" class="btn btn-sm btn-outline-secondary mb-3" onclick="backToKategori()">⬅ Kembali</button>
                    <h6 id="listTitle" class="fw-bold mb-3"></h6>
                    <div class="list-group" id="listContainer"></div>
                  </div>

                  <div id="step-detail" style="display:none;">
                   <button type="button" class="btn btn-sm btn-outline-secondary mb-3" onclick="backToList()">⬅ Kembali</button>
                    <div class="text-center">
                      <img id="detailImg" src="" width="60" class="mb-2">
                      <h5 id="detailName"></h5>
                      <p class="mt-3">Nomor Rekening / E-Wallet:</p>
                      <h5 class="fw-bold" id="detailNumber"></h5>
                      <p>Atas Nama: <strong id="detailOwner">YDSF</strong></p>
                      <div class="mt-3 d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-success" onclick="copyRekening()">Salin Nomor</button>
                        <button type="button" class="btn btn-primary" onclick="selesaiPilih()">Oke</button>
                      </div>
                    </div>
                  </div>

                  <div id="step-qris" style="display:none;">
                   <button type="button" class="btn btn-sm btn-outline-secondary mb-3" onclick="backToKategori()">⬅ Kembali</button>
                    <div class="text-center">
                      <h5>Scan QRIS Berikut</h5>

                      <!-- Foto Qris -->
                      <img src="/tabungan_qurban/assets/images/payments/qris.jpg" width="250" class="rounded shadow-sm my-3">

                      <p>Atas Nama: <strong>YDSF</strong></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Upload bukti -->
          <div class="mb-3 mt-4">
            <label>Upload Bukti (JPG/PNG/PDF, max 5MB)</label>
            <input type="file" name="bukti" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
          </div>

          <!-- Catatan -->
          <div class="mb-3">
            <label>Pesan / Catatan</label>
            <textarea name="note" id="note" class="form-control" rows="2" placeholder="Tulis pesan untuk admin (maks 100 huruf)"></textarea>
            <small id="noteHelp" class="form-text text-muted">0 / 100 huruf</small>
          </div>

          <button class="btn btn-primary">Simpan</button>
          <a href="user_dashboard.php" class="btn btn-secondary">Batal</a>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const bankData = {
  'BCA': { img: 'bca.jpg', no: '1234567890' },
  'BNI': { img: 'bni.jpg', no: '9876543210' },
  'CimbNiaga': { img: 'cimb.jpg', no: '7001234567' },
  'BTN Syariah': { img: 'btn.jpg', no: '1029384756' },
  'BSI': { img: 'bsi.jpg', no: '5678901234' },
   'Muamalat': { img: 'muamalat.jpg', no: '5678901234' },
};
const ewalletData = {
  'GoPay': { img: 'gopay.jpg', no: '081234567890' },
  'OVO': { img: 'ovo.jpg', no: '081234567891' },
  'Dana': { img: 'dana.jpg', no: '081234567892' },
  'ShopeePay': { img: 'shope.jpg', no: '081234567893' },
  'LinkAja': { img: 'link.jpg', no: '081234567894' },
};

let currentCategory = '', selectedNumber = '', selectedName = '', selectedImg = '';

function showList(type) {
  currentCategory = type;
  document.getElementById('step-kategori').style.display = 'none';
  document.getElementById('step-list').style.display = 'block';
  document.getElementById('listTitle').textContent = type === 'bank' ? 'Pilih Bank Transfer' : 'Pilih E-Wallet';

  const data = type === 'bank' ? bankData : ewalletData;
  document.getElementById('listContainer').innerHTML = Object.entries(data).map(([name, info]) => `
    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center" onclick="showDetail('${name}')">
      <img src="/tabungan_qurban/assets/images/payments/${info.img}" width="40" class="me-3">${name}
    </a>
  `).join('');
}

function showDetail(name) {
  const data = currentCategory === 'bank' ? bankData[name] : ewalletData[name];
  document.getElementById('detailImg').src = '/tabungan_qurban/assets/images/payments/' + data.img;
  document.getElementById('detailName').textContent = name;
  document.getElementById('detailNumber').textContent = data.no;
  selectedNumber = data.no;
  selectedName = name;
  selectedImg = '/tabungan_qurban/assets/images/payments/' + data.img;

  document.getElementById('step-list').style.display = 'none';
  document.getElementById('step-detail').style.display = 'block';
}

function showQRIS() {
  document.getElementById('step-kategori').style.display = 'none';
  document.getElementById('step-qris').style.display = 'block';
}

function backToKategori() {
  document.getElementById('step-list').style.display = 'none';
  document.getElementById('step-detail').style.display = 'none';
  document.getElementById('step-qris').style.display = 'none';
  document.getElementById('step-kategori').style.display = 'block';
}

function backToList() {
  document.getElementById('step-detail').style.display = 'none';
  document.getElementById('step-list').style.display = 'block';
}

function copyRekening() {
  navigator.clipboard.writeText(selectedNumber);
  alert('Nomor rekening berhasil disalin!');
}

function selesaiPilih() {
  const modalEl = document.getElementById('paymentModal');
  const modal = bootstrap.Modal.getInstance(modalEl);
  if (modal) modal.hide();

  // tampilkan metode yang dipilih di halaman utama
  const logoEl = document.getElementById('selectedLogo');
  const nameEl = document.getElementById('selectedName');
  const sectionEl = document.getElementById('selectedMethod');
  const methodInput = document.getElementById('payment_method');

  if (logoEl && nameEl && sectionEl && methodInput) {
    logoEl.src = selectedImg;
    nameEl.textContent = selectedName;
    sectionEl.style.display = 'block';
    methodInput.value = selectedName;
  }
}

// === Pilihan Nominal ===
const nominalBtns = document.querySelectorAll('.nominal-btn');
const inputJumlah = document.getElementById('jumlah');
const selectedNominal = document.getElementById('selectedNominal');
const modalLain = new bootstrap.Modal(document.getElementById('modalNominalLain'));
document.getElementById('btnLainnya').onclick = () => modalLain.show();

nominalBtns.forEach(btn => {
  btn.onclick = () => {
    if (btn.id === 'btnLainnya') return;
    nominalBtns.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const val = btn.dataset.value;
    inputJumlah.value = val;
    selectedNominal.textContent = "Nominal dipilih: Rp " + parseInt(val).toLocaleString('id-ID');
  };
});

document.getElementById('simpanNominalLain').onclick = () => {
  const val = parseInt(document.getElementById('inputNominalLain').value) || 0;
  if (val > 0) {
    inputJumlah.value = val;
    selectedNominal.textContent = "Nominal dipilih: Rp " + val.toLocaleString('id-ID');
  }
};
</script>

<?php include __DIR__ . '/../inc/footer.php'; ?>
