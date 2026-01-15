<?php
// File untuk memperbarui pertanyaan assessment sesuai dengan sub-process resmi COBIT 5 Domain MEA
require_once 'config.php';
require_once 'includes/db_connect.php';

echo "<h2>Memperbarui Pertanyaan Assessment COBIT 5 Domain MEA</h2>\n";

try {
    // Hapus semua pertanyaan lama
    $pdo->exec("DELETE FROM assessment_questions");
    echo "<p>✅ Semua pertanyaan lama dihapus</p>\n";
    
    // Masukkan pertanyaan baru sesuai sub-process resmi COBIT 5 Domain MEA
    $questions = [
        // MEA01 - 5 pertanyaan
        ['MEA01', 'Apakah pendekatan pemantauan telah ditetapkan untuk mengevaluasi kinerja dan kepatuhan terhadap TI?', 1],
        ['MEA01', 'Apakah target kinerja dan kepatuhan telah ditetapkan untuk mendukung pencapaian tujuan organisasi?', 2],
        ['MEA01', 'Apakah data kinerja dan kepatuhan dikumpulkan dan diproses secara sistematis?', 3],
        ['MEA01', 'Apakah analisis kinerja dilakukan dan dilaporkan kepada pemangku kepentingan yang relevan?', 4],
        ['MEA01', 'Apakah tindakan korektif diidentifikasi dan diimplementasikan untuk mengatasi isu yang ditemukan?', 5],
        
        // MEA02 - 8 pertanyaan
        ['MEA02', 'Apakah kontrol internal dipantau secara terus-menerus untuk memastikan efektivitasnya?', 6],
        ['MEA02', 'Apakah efektivitas kontrol proses bisnis ditinjau secara berkala?', 7],
        ['MEA02', 'Apakah penilaian kontrol mandiri dilakukan secara terstruktur?', 8],
        ['MEA02', 'Apakah kekurangan kontrol diidentifikasi dan dilaporkan secara tepat waktu?', 9],
        ['MEA02', 'Apakah penyedia jaminan independen dan berkualifikasi untuk mengevaluasi sistem kontrol?', 10],
        ['MEA02', 'Apakah inisiatif jaminan direncanakan secara komprehensif?', 11],
        ['MEA02', 'Apakah cakupan inisiatif jaminan ditentukan secara tepat?', 12],
        ['MEA02', 'Apakah inisiatif jaminan dieksekusi sesuai dengan rencana?', 13],
        
        // MEA03 - 4 pertanyaan
        ['MEA03', 'Apakah persyaratan kepatuhan eksternal diidentifikasi secara menyeluruh?', 14],
        ['MEA03', 'Apakah respons terhadap persyaratan eksternal dioptimalkan?', 15],
        ['MEA03', 'Apakah kepatuhan terhadap persyaratan eksternal dikonfirmasi secara berkala?', 16],
        ['MEA03', 'Apakah jaminan kepatuhan eksternal diperoleh dari pihak yang kompeten?', 17]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO assessment_questions (process_code, question_text, question_order) VALUES (?, ?, ?)");
    
    foreach ($questions as $question) {
        $stmt->execute($question);
    }
    
    echo "<p>✅ " . count($questions) . " pertanyaan baru telah dimasukkan ke database</p>\n";
    
    // Tampilkan pertanyaan yang telah dimasukkan
    echo "<h3>Daftar Pertanyaan Assessment Baru:</h3>\n";
    echo "<div style='margin-top: 20px;'>\n";
    
    $processes = ['MEA01', 'MEA02', 'MEA03'];
    foreach ($processes as $process) {
        echo "<h4>Proses: $process</h4>\n";
        $stmt = $pdo->prepare("SELECT * FROM assessment_questions WHERE process_code = ? ORDER BY question_order");
        $stmt->execute([$process]);
        $questions = $stmt->fetchAll();
        
        echo "<ol>\n";
        foreach ($questions as $q) {
            echo "<li>{$q['question_text']}</li>\n";
        }
        echo "</ol>\n";
    }
    
    echo "</div>\n";
    echo "<p>✅ Perubahan telah disimpan. Silakan coba kembali fitur assessment.</p>\n";
    
} catch (PDOException $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>\n";
}
?>