<?php
// Setup database secara manual untuk memastikan semua tabel dibuat
require_once 'config.php';

echo "<h2>Manual Database Setup for COBIT 5 MEA Assessment System</h2>\n";

try {
    // Buat koneksi tanpa menyebut database spesifik dulu
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    // Buat database jika belum ada
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>✅ Database '" . DB_NAME . "' telah dibuat atau sudah ada.</p>\n";

    // Gunakan database tersebut
    $pdo->exec("USE " . DB_NAME);

    // Baca file skema
    $schemaFile = 'database/cobit_mea_schema.sql';
    if (file_exists($schemaFile)) {
        $sql = file_get_contents($schemaFile);

        // Bagi SQL menjadi perintah-perintah individual
        $statements = explode(';', $sql);

        $successCount = 0;
        $errorCount = 0;

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                    $successCount++;
                } catch (PDOException $e) {
                    // Beberapa perintah mungkin gagal jika sudah dieksekusi sebelumnya
                    $errorCount++;
                    // Tampilkan error hanya untuk debugging
                    // echo "<p>⚠ Statement error: " . substr($statement, 0, 50) . "... (" . $e->getMessage() . ")</p>\n";
                }
            }
        }

        echo "<p>✅ Skema database telah diimpor! $successCount statements berhasil dijalankan.</p>\n";
        if ($errorCount > 0) {
            echo "<p>ℹ $errorCount statements dilewati (kemungkinan sudah ada).</p>\n";
        }

        // Cek apakah tabel users sudah dibuat
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        if ($stmt->rowCount() > 0) {
            echo "<p>✅ Tabel 'users' telah dibuat.</p>\n";
            
            // Cek apakah pengguna default sudah ada
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
            $result = $stmt->fetch();
            $userCount = $result['count'];
            
            if ($userCount > 0) {
                echo "<p>✅ Ada $userCount pengguna dalam database.</p>\n";
                
                // Tampilkan pengguna yang ada
                $stmt = $pdo->query("SELECT id, username, email, first_name, last_name FROM users");
                $users = $stmt->fetchAll();
                echo "<h4>Pengguna yang terdaftar:</h4><ul>\n";
                foreach ($users as $user) {
                    echo "<li>ID: {$user['id']}, Username: {$user['username']}, Email: {$user['email']}, Nama: {$user['first_name']} {$user['last_name']}</li>\n";
                }
                echo "</ul>\n";
            } else {
                echo "<p>❌ Tidak ada pengguna dalam database. Mungkin ada masalah dengan INSERT statements.</p>\n";
            }
        } else {
            echo "<p>❌ Tabel 'users' tidak ditemukan. Mungkin ada masalah dengan skema.</p>\n";
        }

        echo "<h3>✅ Setup selesai! Silakan coba login kembali.</h3>\n";
        echo "<p><a href='index.php?page=login'>Ke halaman login</a></p>\n";

    } else {
        echo "<p>❌ File skema tidak ditemukan: " . $schemaFile . "</p>\n";
    }

} catch (PDOException $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>\n";
}
?>