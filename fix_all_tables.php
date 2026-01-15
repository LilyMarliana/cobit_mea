<?php
// File untuk memperbaiki semua struktur tabel yang mungkin bermasalah
require_once 'config.php';
require_once 'includes/db_connect.php';

echo "<h2>Perbaikan Struktur Tabel Lengkap</h2>\n";

try {
    // Periksa dan tambahkan kolom yang mungkin hilang di assessment_results
    $stmt = $pdo->prepare("SHOW COLUMNS FROM assessment_results LIKE 'overall_maturity_level'");
    $stmt->execute();
    $columnExists = $stmt->rowCount() > 0;
    
    if ($columnExists) {
        echo "<p>ℹ Kolom 'overall_maturity_level' ditemukan di tabel 'assessment_results' (ini mungkin salah)</p>\n";
    } else {
        echo "<p>✅ Kolom 'overall_maturity_level' tidak ditemukan di tabel 'assessment_results' (ini benar)</p>\n";
    }
    
    // Periksa struktur assessment_summary
    $stmt = $pdo->prepare("SHOW COLUMNS FROM assessment_summary LIKE 'overall_maturity_level'");
    $stmt->execute();
    $columnExists = $stmt->rowCount() > 0;
    
    if (!$columnExists) {
        $pdo->exec("ALTER TABLE assessment_summary ADD COLUMN overall_maturity_level DECIMAL(3,2) NOT NULL DEFAULT 0.00");
        echo "<p>✅ Kolom 'overall_maturity_level' ditambahkan ke tabel 'assessment_summary'</p>\n";
    } else {
        echo "<p>ℹ Kolom 'overall_maturity_level' sudah ada di tabel 'assessment_summary'</p>\n";
    }
    
    // Periksa struktur assessments
    $stmt = $pdo->prepare("SHOW COLUMNS FROM assessments LIKE 'last_updated'");
    $stmt->execute();
    $columnExists = $stmt->rowCount() > 0;
    
    if (!$columnExists) {
        $pdo->exec("ALTER TABLE assessments ADD COLUMN last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        echo "<p>✅ Kolom tambahan diperiksa/ditambahkan ke tabel 'assessments'</p>\n";
    } else {
        echo "<p>ℹ Kolom tambahan sudah ada di tabel 'assessments'</p>\n";
    }
    
    echo "<p>✅ Perbaikan struktur tabel selesai!</p>\n";
    
} catch (PDOException $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>\n";
}
?>