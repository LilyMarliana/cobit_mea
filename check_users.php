<?php
require_once 'config.php';
require_once 'includes/db_connect.php';

try {
    // Cek apakah tabel users ada
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $tableExists = $stmt->rowCount() > 0;
    
    if (!$tableExists) {
        echo "❌ Tabel 'users' tidak ditemukan di database!\n";
        echo "Nama database yang digunakan: " . DB_NAME . "\n";
        echo "Silakan jalankan setup.php terlebih dahulu\n";
        exit;
    }
    
    echo "✅ Tabel 'users' ditemukan\n";
    
    // Cek jumlah pengguna
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $count = $stmt->fetch();
    echo "Jumlah pengguna dalam database: " . $count['count'] . "\n";
    
    // Ambil semua pengguna
    $stmt = $pdo->query("SELECT id, username, email, first_name, last_name FROM users");
    $users = $stmt->fetchAll();
    
    echo "\nPengguna yang ditemukan:\n";
    foreach ($users as $user) {
        echo "- ID: {$user['id']}, Username: {$user['username']}, Email: {$user['email']}, Nama: {$user['first_name']} {$user['last_name']}\n";
    }
    
    // Cek tabel roles juga
    $stmt = $pdo->query("SHOW TABLES LIKE 'roles'");
    $rolesTableExists = $stmt->rowCount() > 0;
    
    if ($rolesTableExists) {
        echo "\n✅ Tabel 'roles' juga ditemukan\n";
        $stmt = $pdo->query("SELECT id, role_name FROM roles");
        $roles = $stmt->fetchAll();
        echo "Peran (roles) yang ditemukan:\n";
        foreach ($roles as $role) {
            echo "- ID: {$role['id']}, Role: {$role['role_name']}\n";
        }
    } else {
        echo "❌ Tabel 'roles' tidak ditemukan\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error saat mengakses database: " . $e->getMessage() . "\n";
}
?>