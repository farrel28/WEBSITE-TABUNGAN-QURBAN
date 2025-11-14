<?php
require_once __DIR__ . '/../inc/functions.php';
require_login();

$action = $_POST['action'] ?? $_GET['action'] ?? null;

try {
    if ($action === 'add') {
        // Tambah penabung
        $stmt = $pdo->prepare("INSERT INTO savers (nama, alamat, telp, target_qurban, target_nominal) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['nama'],
            $_POST['alamat'],
            $_POST['telp'],
            $_POST['target_qurban'],
            $_POST['target_nominal']
        ]);
        header("Location: savers.php?status=added");
        exit;

    } elseif ($action === 'edit') {
        // Edit penabung
        $stmt = $pdo->prepare("UPDATE savers SET nama=?, alamat=?, telp=?, target_qurban=?, target_nominal=? WHERE id=?");
        $stmt->execute([
            $_POST['nama'],
            $_POST['alamat'],
            $_POST['telp'],
            $_POST['target_qurban'],
            $_POST['target_nominal'],
            $_POST['id']
        ]);
        header("Location: savers.php?status=edited");
        exit;

    } elseif ($action === 'del') {
        // Hapus penabung
        $id = $_GET['id'] ?? null;
        if ($id) {
            // cek apakah masih ada transaksi
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE saver_id = ?");
            $stmt->execute([$id]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                // tidak boleh hapus kalau masih ada transaksi
                header("Location: savers.php?status=has_transactions");
                exit;
            }

            // hapus saver
            $stmt = $pdo->prepare("DELETE FROM savers WHERE id=?");
            $stmt->execute([$id]);
            header("Location: savers.php?status=deleted");
            exit;
        }
    }
} catch (Exception $e) {
    // Jika ada error
    header("Location: savers.php?status=error");
    exit;
}
