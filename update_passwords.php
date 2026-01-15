<?php
// File untuk memperbarui password pengguna yang ada
require_once 'config.php';
require_once 'includes/db_connect.php';

echo "<h2>Update User Passwords</h2>\n";

try {
    // Update password untuk semua pengguna default ke 'admin123'
    $newPassword = 'admin123';
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
    $stmt->execute([$hashedPassword]);
    echo "<p>✅ Password untuk user 'admin' telah diperbarui</p>\n";
    
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'user'");
    $stmt->execute([$hashedPassword]);
    echo "<p>✅ Password untuk user 'user' telah diperbarui</p>\n";
    
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'lily'");
    $stmt->execute([$hashedPassword]);
    echo "<p>✅ Password untuk user 'lily' telah diperbarui</p>\n";
    
    echo "<p>✅ Semua password telah diperbarui ke: <strong>$newPassword</strong></p>\n";
    echo "<p>Sekarang Anda bisa login dengan username: <strong>admin</strong>, <strong>user</strong>, atau <strong>lily</strong> dan password: <strong>$newPassword</strong></p>\n";
    echo "<a href='index.php?page=login'>Ke halaman login</a>\n";
    
} catch (PDOException $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>\n";
}
?>