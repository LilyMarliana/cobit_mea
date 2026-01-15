<?php
// File untuk mengecek status database
require_once 'config.php';
require_once 'includes/db_connect.php';

echo "<h2>Database Status Check</h2>\n";

try {
    // Cek semua tabel
    $tables = [
        'users', 'roles', 'mea_processes', 'assessment_questions', 
        'assessments', 'assessment_responses', 'assessment_results', 
        'assessment_summary', 'activity_logs', 'failed_login_attempts'
    ];
    
    echo "<h3>Tabel-tabel dalam database:</h3>\n";
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        $exists = $stmt->rowCount() > 0;
        echo ($exists ? "✅" : "❌") . " $table\n";
    }
    
    // Cek isi tabel users
    echo "\n<h3>Isi tabel users:</h3>\n";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        $userCount = $result['count'];
        echo "Jumlah pengguna: $userCount\n";
        
        if ($userCount > 0) {
            $stmt = $pdo->query("SELECT id, username, email, first_name, last_name, is_active FROM users");
            $users = $stmt->fetchAll();
            echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-top: 10px;'>";
            echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Nama</th><th>Aktif</th></tr>";
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>{$user['id']}</td>";
                echo "<td>{$user['username']}</td>";
                echo "<td>{$user['email']}</td>";
                echo "<td>{$user['first_name']} {$user['last_name']}</td>";
                echo "<td>" . ($user['is_active'] ? 'Ya' : 'Tidak') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } catch (PDOException $e) {
        echo "❌ Tabel users tidak ditemukan atau bermasalah: " . $e->getMessage() . "\n";
    }
    
    // Cek isi tabel roles
    echo "\n<h3>Isi tabel roles:</h3>\n";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM roles");
        $result = $stmt->fetch();
        $roleCount = $result['count'];
        echo "Jumlah peran: $roleCount\n";
        
        if ($roleCount > 0) {
            $stmt = $pdo->query("SELECT id, role_name FROM roles");
            $roles = $stmt->fetchAll();
            echo "<ul>";
            foreach ($roles as $role) {
                echo "<li>ID: {$role['id']}, Role: {$role['role_name']}</li>";
            }
            echo "</ul>";
        }
    } catch (PDOException $e) {
        echo "❌ Tabel roles tidak ditemukan atau bermasalah: " . $e->getMessage() . "\n";
    }

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>