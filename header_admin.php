<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Admin - Tabungan Qurban</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="/tabungan_qurban/pages/dashboard.php">Admin Panel</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="/tabungan_qurban/pages/dashboard.php">🏠 Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="/tabungan_qurban/pages/master_data.php">📑 Master Data</a></li>
        <li class="nav-item"><a class="nav-link" href="/tabungan_qurban/pages/transactions.php">💰 Transaksi</a></li>
        <li class="nav-item"><a class="nav-link" href="/tabungan_qurban/pages/admin_notifications.php">🔔 Notifikasi</a></li>

        <li class="nav-item"><a class="nav-link text-warning" href="/tabungan_qurban/auth/logout.php">🚪 Logout</a></li>
        
      </ul>
    </div>
  </div>
</nav>
<div class="container my-4">