<?php
// inc/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tabungan Qurban</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/tabungan_qurban/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="/tabungan_qurban/pages/dashboard.php">Tabungan Qurban</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">             

        <?php if (isset($_SESSION['admin_name'])): ?>
          <!-- MENU ADMIN -->
          <li class="nav-item"><a class="nav-link" href="/tabungan_qurban/pages/dashboard.php">🏠 Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="/tabungan_qurban/pages/master_data.php">📑 Master Data</a></li>
          <li class="nav-item"><a class="nav-link" href="/tabungan_qurban/pages/transactions.php">💰 Transaksi</a></li>
          <li class="nav-item">
  <a class="nav-link" href="/tabungan_qurban/pages/admin_notifications.php">
    <i class="bi bi-bell-fill"></i> Notifikasi
  </a>
</li>

          <li class="nav-item"><a class="nav-link text-warning" href="/tabungan_qurban/auth/logout.php">🚪 Logout</a></li>

        <?php elseif (isset($_SESSION['user_id'])): ?>
          <!-- MENU USER/DONATUR -->
          <li class="nav-item"><a class="nav-link" href="/tabungan_qurban/pages/user_dashboard.php">🏠 Dashboard Saya</a></li>
          <li class="nav-item"><a class="nav-link text-warning" href="/tabungan_qurban/auth/user_logout.php">🚪 Logout</a></li>

        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/tabungan_qurban/auth/login_user.php">Login Donatur</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container my-4">
