<?php
require_once __DIR__ . '/../inc/functions.php';
require_login();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saver_id'])) {
    $id = (int)$_POST['saver_id'];

    // ambil data donatur
    $stmt = $pdo->prepare("SELECT * FROM savers WHERE id = ?");
    $stmt->execute([$id]);
    $saver = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$saver) {
        echo json_encode(['success' => false, 'message' => 'Donatur tidak ditemukan.']);
        exit;
    }

    $nama = $saver['nama'];
    $telp = preg_replace('/[^0-9]/', '', $saver['telp']);
    $tanggal = date('d M Y', strtotime($saver['tanggal_lunas']));
    $pesan = "Halo $nama, ini pengingat dari Admin Tabungan Qurban 🙏
Tanggal pelunasan tabungan Anda adalah *$tanggal*.
Yuk segera lunasi tabungan qurban Anda sebelum tenggat waktunya 🐄.";

    // simpan ke tabel notifikasi
    $stmt2 = $pdo->prepare("INSERT INTO notifications (saver_id, message) VALUES (?, ?)");
    $stmt2->execute([$id, $pesan]);

    // cek apakah dijalankan di localhost
    $is_localhost = in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']);

    if ($is_localhost) {
        // MODE SIMULASI - tidak kirim ke Fonnte
        echo json_encode([
            'success' => true,
            'message' => "Simulasi sukses (localhost): pesan untuk $nama akan dikirim ke $telp"
        ]);
        exit;
    }

    // kalau bukan localhost, kirim via Fonnte
    $token = "ISI_TOKEN_FONNTE_ANDA"; // Ganti token kamu
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.fonnte.com/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'target' => $telp,
            'message' => $pesan,
        ],
        CURLOPT_HTTPHEADER => [
            "Authorization: $token"
        ],
    ]);
    $response = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);

    // periksa hasilnya
    if ($error) {
        echo json_encode(['success' => false, 'message' => "Gagal mengirim ke $nama ($telp). Error: $error"]);
    } elseif (strpos($response, '"status":true') !== false || strpos($response, '"status":"success"') !== false) {
        echo json_encode(['success' => true, 'message' => "Notifikasi berhasil dikirim ke $nama ($telp)."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Respon gagal: $response"]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid.']);
