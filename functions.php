<?php
// inc/functions.php
require_once __DIR__ . '/db.php';
session_start();

function is_logged_in() {
    return isset($_SESSION['admin_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: /tabungan_qurban/auth/login.php');
        exit;
    }
}

function login_admin($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        return true;
    }
    return false;
}

function logout() {
    session_unset();
    session_destroy();
}

function get_all_savers() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM savers ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

function get_saver($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM savers WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function create_saver($data) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO savers (nama, nik, alamat, telp, target_qurban, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([
        $data['nama'],
        $data['nik'],
        $data['alamat'],
        $data['telp'],
        $data['target_qurban'],
        $data['username'],
        password_hash($data['password'], PASSWORD_DEFAULT)
    ]);
}


function update_saver($id, $data) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE savers SET nama=?, nik=?, alamat=?, telp=?, target_qurban=? WHERE id=?");
    return $stmt->execute([$data['nama'], $data['nik'], $data['alamat'], $data['telp'], $data['target_qurban'], $id]);
}

function delete_saver($id) {
    global $pdo;

    // Hapus semua transaksi yang terkait dengan penabung ini
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE saver_id=?");
    $stmt->execute([$id]);

    // Baru hapus penabungnya
    $stmt = $pdo->prepare("DELETE FROM savers WHERE id=?");
    $stmt->execute([$id]);
}


// inc/functions.php  (ubah bagian add_transaction)
function add_transaction($saver_id, $amount, $jenis, $note = '', $bukti_transfer = null) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO transactions (saver_id, amount, jenis, note, bukti_transfer, created_at) 
                           VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$saver_id, $amount, $jenis, $note, $bukti_transfer]);
}




function get_transactions($saver_id = null) {
    global $pdo;
    if ($saver_id) {
        $stmt = $pdo->prepare("SELECT t.*, s.nama 
                               FROM transactions t 
                               JOIN savers s ON t.saver_id = s.id 
                               WHERE saver_id = ? 
                               ORDER BY t.created_at DESC");
        $stmt->execute([$saver_id]);
    } else {
        $stmt = $pdo->query("SELECT t.*, s.nama 
                             FROM transactions t 
                             JOIN savers s ON t.saver_id = s.id 
                             ORDER BY t.created_at DESC");
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function get_balance($saver_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT 
          (SELECT IFNULL(SUM(amount),0) FROM transactions WHERE saver_id = ? AND jenis='setor') -
          (SELECT IFNULL(SUM(amount),0) FROM transactions WHERE saver_id = ? AND jenis='tarik') AS balance
    ");
    $stmt->execute([$saver_id, $saver_id]);
    $row = $stmt->fetch();
    return $row ? $row['balance'] : 0;
}



function login_user($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM savers WHERE username = ?");
    $stmt->execute([$username]);
    $saver = $stmt->fetch();
    if ($saver && password_verify($password, $saver['password'])) {
        $_SESSION['user_id'] = $saver['id'];
        $_SESSION['user_name'] = $saver['nama'];
        return true;
    }
    return false;
}

function is_user_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_user_login() {
    if (!is_user_logged_in()) {
        header('Location: /tabungan_qurban/auth/login_user.php');
        exit;
    }
}

function get_daily_transactions_by_saver($saver_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT DATE(created_at) as tanggal,
               SUM(CASE WHEN jenis='setor' THEN amount ELSE 0 END) as total_setor,
               SUM(CASE WHEN jenis='tarik' THEN amount ELSE 0 END) as total_tarik
        FROM transactions
        WHERE saver_id = ?
        GROUP BY DATE(created_at)
        ORDER BY tanggal ASC
        LIMIT 30
    ");
    $stmt->execute([$saver_id]);
    return $stmt->fetchAll();
}