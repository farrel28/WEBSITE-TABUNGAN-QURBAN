<?php
require_once __DIR__ . '/../inc/functions.php';

// hapus semua session
session_unset();
session_destroy();

// arahkan ke login dengan pesan logout
header("Location: /tabungan_qurban/auth/login.php?logout=1");
exit;
