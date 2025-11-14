<?php
require_once __DIR__ . '/../inc/functions.php';

$err = '';
$msg = '';

// tampilkan pesan logout sukses
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    $msg = 'Anda berhasil logout.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    if (login_admin($u, $p)) {
        header('Location: /tabungan_qurban/pages/dashboard.php');
        exit;
    } else {
        $err = 'Username atau password salah.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Admin - Tabungan Qurban</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- PENTING: gunakan path absolut dari root localhost -->
  <link rel="stylesheet" href="/tabungan_qurban/assets/css/login.css?v=1">
</head>

<body class="bg-light">
<div class="page-logo">
  <img src="../assets/images/YDSF.png" alt="Logo YDSF">
</div>
<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="card-title mb-3 text-center">Login Admin</h4>

          <?php if ($msg): ?>
            <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
          <?php endif; ?>

          <?php if ($err): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
          <?php endif; ?>

          <form method="post">
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input name="username" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input name="password" type="password" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100">Login</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
