<?php
require_once __DIR__ . '/../inc/functions.php';
require_login();
include __DIR__ . '/../inc/header_admin.php';

// ambil semua donatur yang punya tanggal_lunas valid
$stmt = $pdo->query("
    SELECT id, nama, telp, tanggal_lunas, target_nominal
    FROM savers
    WHERE tanggal_lunas IS NOT NULL AND tanggal_lunas != '0000-00-00'
    ORDER BY tanggal_lunas ASC
");
$savers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Tambah SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-4">
  <h4 class="fw-bold mb-3">📢 Kirim Notifikasi Deadline Donatur</h4>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <?php if (count($savers) > 0): ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead class="table-light text-center">
            <tr>
              <th>No</th>
              <th>Nama Donatur</th>
              <th>No. Telepon</th>
              <th>Target</th>
              <th>Tanggal Lunas</th>
              <th>Sisa Hari</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($savers as $i => $s): 
              $tgl_lunas = strtotime($s['tanggal_lunas']);
              $selisih = ceil(($tgl_lunas - time()) / 86400);
              $warna = $selisih <= 7 ? 'text-danger fw-bold' : 'text-dark';
          ?>
            <tr id="row-<?= $s['id'] ?>">
              <td class="text-center"><?= $i+1 ?></td>
              <td><?= htmlspecialchars($s['nama']) ?></td>
              <td><?= htmlspecialchars($s['telp']) ?></td>
              <td>Rp <?= number_format($s['target_nominal'], 0, ',', '.') ?></td>
              <td class="text-center"><?= date('d M Y', $tgl_lunas) ?></td>
              <td class="text-center <?= $warna ?>">
                <?= $selisih > 0 ? $selisih . ' hari lagi' : 'Sudah lewat' ?>
              </td>
              <td class="text-center">
                <button class="btn btn-success btn-sm kirim-btn" 
                        data-id="<?= $s['id'] ?>" 
                        data-nama="<?= htmlspecialchars($s['nama']) ?>">
                  <i class="bi bi-send"></i> Kirim Notif
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php else: ?>
        <div class="alert alert-info text-center mb-0">
          Belum ada donatur dengan tanggal pelunasan.
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
// ketika tombol diklik
document.querySelectorAll('.kirim-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const saver_id = this.dataset.id;
    const nama = this.dataset.nama;

    Swal.fire({
      title: `Kirim notifikasi ke ${nama}?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, kirim!',
      cancelButtonText: 'Batal',
      confirmButtonColor: '#198754',
      cancelButtonColor: '#dc3545'
    }).then((result) => {
      if (result.isConfirmed) {
        fetch('send_notif_manual.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `saver_id=${saver_id}`
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Berhasil!',
              text: data.message,
              timer: 2000,
              showConfirmButton: false
            });
            // ubah tombol jadi nonaktif
            const row = document.getElementById('row-' + saver_id);
            const btn = row.querySelector('.kirim-btn');
            btn.classList.remove('btn-success');
            btn.classList.add('btn-secondary');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-check-circle"></i> Terkirim';
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Gagal!',
              text: data.message,
            });
          }
        })
        .catch(() => {
          Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
        });
      }
    });
  });
});
</script>

<?php include __DIR__ . '/../inc/footer.php'; ?>
