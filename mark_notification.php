<?php
require_once __DIR__ . '/../inc/functions.php';

// mode paling simpel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notif_id'])) {
    $notif_id = (int) $_POST['notif_id'];

    // langsung hapus notifikasi dari database
    $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = ?");
    $success = $stmt->execute([$notif_id]);

    if ($success) {
        echo "OK";
    } else {
        echo "FAILED";
    }
    exit;
}

echo "INVALID";
