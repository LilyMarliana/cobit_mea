<?php
require_once('config.php');

// Fungsi untuk membuat tabel assessments jika belum ada
function createAssessmentsTable($pdo) {
    $sql = "
    CREATE TABLE IF NOT EXISTS assessments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    
    $pdo->exec($sql);
    echo "Tabel assessments telah dibuat atau sudah ada.\n";
}

// Fungsi untuk membuat tabel assessment_answers jika belum ada
function createAssessmentAnswersTable($pdo) {
    $sql = "
    CREATE TABLE IF NOT EXISTS assessment_answers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        assessment_id INT NOT NULL,
        question_id INT NOT NULL,
        answer VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (assessment_id) REFERENCES assessments(id),
        FOREIGN KEY (question_id) REFERENCES questions(id)
    )";
    
    $pdo->exec($sql);
    echo "Tabel assessment_answers telah dibuat atau sudah ada.\n";
}

// Fungsi untuk membuat tabel questions jika belum ada
function createQuestionsTable($pdo) {
    $sql = "
    CREATE TABLE IF NOT EXISTS questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question_text TEXT NOT NULL,
        domain VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "Tabel questions telah dibuat atau sudah ada.\n";
}

// Jalankan fungsi-fungsi pembuatan tabel
try {
    createQuestionsTable($pdo);
    createAssessmentsTable($pdo);
    createAssessmentAnswersTable($pdo);
    
    echo "\nSemua tabel yang diperlukan untuk fitur PDF telah disiapkan.\n";
    echo "Fitur PDF sekarang siap digunakan.\n";
} catch (Exception $e) {
    echo "Terjadi kesalahan saat membuat tabel: " . $e->getMessage() . "\n";
}
?>