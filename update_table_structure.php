<?php
// File untuk memperbaiki struktur tabel jika tabel sudah ada
require_once 'config.php';
require_once 'includes/db_connect.php';

echo "<h2>Update Table Structure</h2>\n";

try {
    // Tambahkan kolom last_login jika belum ada
    $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'last_login'");
    $stmt->execute();
    $columnExists = $stmt->rowCount() > 0;
    
    if (!$columnExists) {
        $pdo->exec("ALTER TABLE users ADD COLUMN last_login TIMESTAMP NULL");
        echo "<p>✅ Kolom 'last_login' telah ditambahkan ke tabel 'users'</p>\n";
    } else {
        echo "<p>ℹ Kolom 'last_login' sudah ada di tabel 'users'</p>\n";
    }
    
    // Tambahkan kolom avatar jika belum ada (kemungkinan lain yang dibutuhkan oleh aplikasi)
    $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'avatar'");
    $stmt->execute();
    $columnExists = $stmt->rowCount() > 0;
    
    if (!$columnExists) {
        $pdo->exec("ALTER TABLE users ADD COLUMN avatar VARCHAR(255) DEFAULT 'default-avatar.png'");
        echo "<p>✅ Kolom 'avatar' telah ditambahkan ke tabel 'users'</p>\n";
    } else {
        echo "<p>ℹ Kolom 'avatar' sudah ada di tabel 'users'</p>\n";
    }
    
    // Tambahkan kolom phone jika belum ada
    $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'phone'");
    $stmt->execute();
    $columnExists = $stmt->rowCount() > 0;
    
    if (!$columnExists) {
        $pdo->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20)");
        echo "<p>✅ Kolom 'phone' telah ditambahkan ke tabel 'users'</p>\n";
    } else {
        echo "<p>ℹ Kolom 'phone' sudah ada di tabel 'users'</p>\n";
    }
    
    echo "<p>✅ Perbaikan struktur tabel selesai!</p>\n";
    echo "<a href='update_passwords.php'>Lanjutkan ke update password</a>\n";
    
} catch (PDOException $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>\n";
}
?>