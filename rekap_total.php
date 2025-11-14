<?php
include __DIR__ . '/../inc/header.php';
include '../inc/db.php'; // koneksi PDO

// =========================
// Total Donatur & Total Tabungan (dari savers)
// =========================
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total_donatur FROM savers");
    $total_donatur = $stmt->fetch(PDO::FETCH_ASSOC)['total_donatur'] ?? 0;

    $stmt2 = $pdo->query("SELECT SUM(target_qurban) as total_target FROM savers");
    $total_target = $stmt2->fetch(PDO::FETCH_ASSOC)['total_target'] ?? 0;
} catch (Exception $e) {
    // kalau terjadi error, set default agar halaman tidak break
    $total_donatur = 0;
    $total_target = 0;
}

// =========================
// Rekap Per Hewan Qurban (menggunakan transactions agar otomatis terupdate)
// =========================
$sql = "
    SELECT a.id,
           a.nama_hewan AS hewan,
           a.harga AS harga_hewan,
           COUNT(DISTINCT s.id) AS jumlah_donatur,
           COALESCE(SUM(s.target_qurban), 0) AS total_target_from_savers,
           COALESCE(SUM(t.amount), 0) AS total_tabungan
    FROM animals a
    LEFT JOIN savers s ON s.animal_id = a.id
    LEFT JOIN transactions t ON t.saver_id = s.id
    GROUP BY a.id, a.nama_hewan, a.harga
    ORDER BY a.nama_hewan
";

try {
    $stmt3 = $pdo->query($sql);
    $data_db = $stmt3->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // jika query gagal, jangan biarkan $data_db null
    $data_db = [];
    // optional: error_log($e->getMessage());
}

// Default hewan (wajib ada meskipun belum ada di table animals)
$defaultHewan = [
    'Sapi'    => ['hewan' => 'Sapi',    'harga_hewan' => 0, 'jumlah_donatur' => 0, 'total_target_from_savers' => 0, 'total_tabungan' => 0],
    'Kambing' => ['hewan' => 'Kambing', 'harga_hewan' => 0, 'jumlah_donatur' => 0, 'total_target_from_savers' => 0, 'total_tabungan' => 0],
    'Domba'   => ['hewan' => 'Domba',   'harga_hewan' => 0, 'jumlah_donatur' => 0, 'total_target_from_savers' => 0, 'total_tabungan' => 0],
];

// Gabungkan default + hasil DB (DB akan menimpa default jika nama sama)
foreach ($data_db as $row) {
    $name = $row['hewan'] ?? ('animal_' . ($row['id'] ?? uniqid()));
    $defaultHewan[$name] = $row;
}

// Pastikan kita punya array numerik untuk foreach
$data_campaign = array_values($defaultHewan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Total Donatur Tabungan Qurban</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; }
        .box { background: #fff; padding: 20px; margin: 20px auto; max-width: 1100px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
        h2, h3 { margin-top: 0; color: #333; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; background: #fff; }
        th, td { border: 1px solid #e0e0e0; padding: 10px; text-align: center; }
        th { background: #fafafa; }
        .progress { background: #eee; border-radius: 6px; overflow: hidden; height: 20px; }
        .progress-bar { height: 100%; text-align: center; color: #fff; font-weight: 600; font-size: 12px; line-height: 20px; }
        .bg-info { background: linear-gradient(90deg,#2196F3,#64B5F6); }
        .bg-success { background: linear-gradient(90deg,#4CAF50,#81C784); }
    </style>
</head>
<body>

<div class="box">
    <h2>📊 Rekap Total Tabungan Qurban</h2>
    <p><strong>Total Donatur:</strong> <?= htmlspecialchars($total_donatur) ?> orang</p>
    <p><strong>Total Target (dari savers):</strong> Rp <?= number_format($total_target ?: 0, 0, ',', '.') ?></p>
</div>

<div class="box">
    <h3>🔹 Rekap Per Jenis Hewan Qurban</h3>
    <table>
        <thead>
            <tr>
                <th>Hewan</th>
                <th>Harga</th>
                <th>Donatur </th>
                <th>Total Target </th>
                <th>Terkumpul </th>
                <th>Progress</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($data_campaign as $row): 
            $hewan = $row['hewan'] ?? 'Belum dipilih';
            $harga_hewan = (float) ($row['harga_hewan'] ?? 0);
            $jumlah_donatur = (int) ($row['jumlah_donatur'] ?? 0);
            $total_target_from_savers = (float) ($row['total_target_from_savers'] ?? 0);
            $total_tabungan = (float) ($row['total_tabungan'] ?? 0);

            // logika progress:
            // gunakan total_target_from_savers jika > 0, jika tidak pakai harga_hewan (jika ada)
            if ($total_target_from_savers > 0) {
                $progress = ($total_tabungan / $total_target_from_savers) * 100;
            } elseif ($harga_hewan > 0) {
                $progress = ($total_tabungan / $harga_hewan) * 100;
            } else {
                $progress = 0;
            }
            $progress = $progress > 100 ? 100 : round($progress, 2);
            $barClass = $progress >= 100 ? 'bg-success' : 'bg-info';
        ?>
            <tr>
                <td><?= htmlspecialchars($hewan) ?></td>
                <td>Rp <?= number_format($harga_hewan, 0, ',', '.') ?></td>
                <td><?= $jumlah_donatur ?></td>
                <td>Rp <?= number_format($total_target_from_savers, 0, ',', '.') ?></td>
                <td>Rp <?= number_format($total_tabungan, 0, ',', '.') ?></td>
                <td style="min-width: 180px;">
                    <div class="progress" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar <?= $barClass ?>" style="width: <?= $progress ?>%;"><?= $progress ?>%</div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
