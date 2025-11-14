<?php
require_once __DIR__ . '/../inc/functions.php';

$id = $_GET['id'] ?? null;

// ambil data user
$stmt = $pdo->prepare("SELECT * FROM savers WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ambil daftar hewan qurban
$animals = $pdo->query("SELECT * FROM animals")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $animal_id = $_POST['animal_id'] ?: null;

    $stmt = $pdo->prepare("UPDATE savers SET nama = ?, email = ?, animal_id = ? WHERE id = ?");
    $stmt->execute([$nama, $email, $animal_id, $id]);

    header("Location: admin_users.php?success=1");
    exit;
}
?>

<h3>Edit Donatur</h3>
<form method="post">
    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Target Hewan Qurban</label>
        <select name="animal_id" class="form-control">
            <option value="">-- Pilih Hewan --</option>
            <?php foreach ($animals as $animal): ?>
                <option value="<?= $animal['id'] ?>" <?= $user['animal_id'] == $animal['id'] ? 'selected' : '' ?>>
                    <?= $animal['nama_hewan'] ?> - Rp <?= number_format($animal['harga'], 0, ',', '.') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
</form>
