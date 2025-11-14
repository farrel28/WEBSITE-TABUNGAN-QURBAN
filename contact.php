<?php include __DIR__ . '/../inc/header.php'; ?>

<div class="container py-5">
  <div class="text-center mb-5">
    <h2 class="fw-bold text-success">Hubungi Kami</h2>
    <p class="text-muted">Kami siap membantu setiap langkah kebaikanmu.</p>
  </div>

  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <h5 class="mb-3 text-success">Informasi Kontak</h5>
          <ul class="list-unstyled mb-4">
            <li><i class="bi bi-geo-alt-fill text-success me-2"></i>Jl. Kahuripan No.12, RT.07/RW.01, Klojen, Kec. Klojen, Kota Malang, Jawa Timur 65111</li>
            <li><i class="bi bi-telephone-fill text-success me-2"></i>(0341) 340327</li>
            <li><i class="bi bi-globe text-success me-2"></i>ydsfpeduli.org</li>
            <li>
              <i class="bi bi-clock-fill text-success me-2"></i>
              <button class="btn btn-sm btn-outline-success dropdown-toggle" 
                      type="button" 
                      data-bs-toggle="collapse" 
                      data-bs-target="#jamKerja" 
                      aria-expanded="false" 
                      aria-controls="jamKerja">
                Jam Kerja
              </button>
              <div class="collapse mt-2" id="jamKerja">
                <small class="text-muted">
                  Senin–Jumat: 08.00–16.30<br>
                  Sabtu: 08.00–12.00<br>
                  Minggu: Tutup
                </small>
              </div>
            </li>
          </ul>

          <h5 class="mb-3 text-success">Ikuti Kami</h5>
          <div class="social-links">
            <a href="https://www.instagram.com/ydsf_peduli/" target="_blank" class="text-dark d-block mb-2">
              <i class="bi bi-instagram fs-5 me-2 text-danger"></i> ydsf_peduli
            </a>
            <a href="https://wa.me/6289682346140" target="_blank" class="text-dark d-block mb-2">
              <i class="bi bi-whatsapp fs-5 me-2 text-success"></i> +62 896-8234-6140
            </a>
            <a href="https://www.youtube.com/@ydsfpeduli9397" target="_blank" class="text-dark d-block mb-2">
              <i class="bi bi-youtube fs-5 me-2 text-danger"></i> ydsfpeduli9397
            </a>
            <a href="https://www.tiktok.com/@ydsf_peduli" target="_blank" class="text-dark d-block">
              <i class="bi bi-tiktok fs-5 me-2 text-dark"></i> ydsf_peduli
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="text-center my-5">
  <h3 class="fw-bold text-success">Semakin Terasa Manfaatnya</h3>
</div>

<!-- Tambahkan Bootstrap JS agar tombol Jam Kerja berfungsi -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include __DIR__ . '/../inc/footer.php'; ?>
