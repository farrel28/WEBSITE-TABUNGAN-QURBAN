<?php
session_start();
session_unset();
session_destroy();
header('Location: /tabungan_qurban/auth/login_user.php');
exit;
