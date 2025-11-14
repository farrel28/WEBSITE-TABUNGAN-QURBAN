<?php
// File: reset_admin.php
require_once __DIR__ . '/inc/db.php';

// username & password default
$username = 'admin';
$new_password = 'admin123'; // password baru (belum di-hash)
$hash = password_hash($new_password, PASSWORD_BCRYPT);

try {
    // cek apakah admin sudah ada
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin) {
        // update password
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = ?");
        $stmt->execute([$hash, $username]);
        echo "✅ Password admin berhasil direset.<br>";
    } else {
        // buat akun admin baru
        $stmt = $pdo->prepare("INSERT INTO admins (username, password, name, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$username, $hash, 'Administrator']);
        echo "✅ Akun admin baru berhasil dibuat.<br>";
    }

    echo "🔑 Login dengan:<br>";
    echo "Username: <b>{$username}</b><br>";
    echo "Password: <b>{$new_password}</b><br>";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
