<?php
require_once __DIR__ . '/../inc/functions.php';

// Ambil semua user dengan tanggal_lunas
$stmt = $pdo->query("SELECT id, nama, telp, tanggal_lunas FROM savers 
                     WHERE tanggal_lunas IS NOT NULL AND tanggal_lunas != '0000-00-00'");
$savers = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($savers as $saver) {
    $saver_id = $saver['id'];
    $nama = $saver['nama'];
    $telp = preg_replace('/[^0-9]/', '', $saver['telp']); // normalisasi no HP
    $tgl_lunas = strtotime($saver['tanggal_lunas']);
    $hari_ini = time();
    $selisih_hari = floor(($tgl_lunas - $hari_ini) / 86400);

    $pesan = null;
    if ($selisih_hari == 30) {
        $pesan = "Halo $nama, waktu pelunasan tabungan qurban Anda tinggal 1 bulan lagi. Yuk semangat menabung! 🐄";
    } elseif ($selisih_hari == 21) {
        $pesan = "Pengingat: 3 minggu lagi menuju pelunasan qurban Anda. Terus lanjutkan setoran terbaik Anda 💪.";
    } elseif ($selisih_hari == 14) {
        $pesan = "Tinggal 2 minggu lagi untuk melunasi tabungan qurban Anda 🕌.";
    } elseif ($selisih_hari == 7) {
        $pesan = "1 minggu lagi sebelum deadline pelunasan qurban Anda. Yuk segera lunasi sebelum terlambat.";
    } elseif ($selisih_hari == 3) {
        $pesan = "Hanya tersisa 3 hari menuju deadline pelunasan tabungan qurban Anda.";
    } elseif ($selisih_hari == 1) {
        $pesan = "Besok adalah batas akhir pelunasan qurban Anda. Jangan lupa diselesaikan, ya!";
    }

    if ($pesan) {
        // Simpan ke tabel notifikasi
        $stmt2 = $pdo->prepare("INSERT INTO notifications (saver_id, message) VALUES (?, ?)");
        $stmt2->execute([$saver_id, $pesan]);

        // Kirim ke WhatsApp melalui API Fonnte
        $token = "ISI_TOKEN_FONNTE_ANDA"; // Ganti dengan token API dari Fonnte
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
        curl_close($curl);
    }
}

