<?php
require_once('config.php');
require_once('vendor/tcpdf/tcpdf.php');

// Periksa apakah sesi sudah aktif sebelum memulainya
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Periksa apakah pengguna login
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Fungsi untuk mendapatkan data assessment dari database
function getAssessmentData($pdo, $userId) {
    $stmt = $pdo->prepare("
        SELECT a.*, u.username
        FROM assessments a
        JOIN users u ON a.user_id = u.id
        WHERE a.user_id = ?
        ORDER BY a.created_at DESC
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fungsi untuk mendapatkan jawaban assessment
function getAssessmentAnswers($pdo, $assessmentId) {
    // Cek apakah tabel assessment_answers ada
    $tableExists = $pdo->query("SHOW TABLES LIKE 'assessment_answers'");
    if ($tableExists->rowCount() == 0) {
        return []; // Kembalikan array kosong jika tabel tidak ada
    }

    $stmt = $pdo->prepare("
        SELECT aa.question_id, aa.answer, q.question_text, q.domain
        FROM assessment_answers aa
        JOIN questions q ON aa.question_id = q.id
        WHERE aa.assessment_id = ?
        ORDER BY q.domain, q.id
    ");
    $stmt->execute([$assessmentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Ambil data assessment
$assessment = getAssessmentData($pdo, $_SESSION['user_id']);
if (!$assessment) {
    die("Tidak ada data assessment ditemukan.");
}

$answers = getAssessmentAnswers($pdo, $assessment['id']);

// Buat instance TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set informasi dokumen
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Laporan Assessment COBIT - ' . $assessment['username']);
$pdf->SetSubject('Laporan Hasil Assessment COBIT');
$pdf->SetKeywords('COBIT, assessment, PDF, report');

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set font
$pdf->SetFont('helvetica', '', 12);

// Add a page
$pdf->AddPage();

// Header
$html = '
<style>
    h1 { color: #333; font-size: 22px; margin-bottom: 10px; }
    h2 { color: #555; font-size: 16px; margin-top: 20px; margin-bottom: 10px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
    .info-box { background-color: #f9f9f9; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
    .question { margin-bottom: 15px; }
    .domain-header { background-color: #e9ecef; padding: 8px; font-weight: bold; margin-top: 15px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>
<div style="text-align: center;">
    <h1>LAPORAN ASSESSMENT COBIT</h1>
    <div class="info-box">
        <strong>Nama Pengguna:</strong> ' . htmlspecialchars($assessment['username']) . '<br>
        <strong>Tanggal Assessment:</strong> ' . date('d M Y H:i:s', strtotime($assessment['created_at'])) . '<br>
        <strong>ID Assessment:</strong> ' . $assessment['id'] . '
    </div>
</div>
';

$pdf->writeHTML($html, true, false, true, false, '');

// Group answers by domain
$groupedAnswers = [];
if (!empty($answers)) {
    foreach ($answers as $answer) {
        $domain = $answer['domain'];
        if (!isset($groupedAnswers[$domain])) {
            $groupedAnswers[$domain] = [];
        }
        $groupedAnswers[$domain][] = $answer;
    }

    // Add answers by domain
    foreach ($groupedAnswers as $domain => $domainAnswers) {
        $html = '<h2 class="domain-header">Domain: ' . htmlspecialchars($domain) . '</h2>';
        $html .= '<table><thead><tr><th>No</th><th>Pertanyaan</th><th>Jawaban</th></tr></thead><tbody>';

        $counter = 1;
        foreach ($domainAnswers as $answer) {
            $html .= '<tr>';
            $html .= '<td>' . $counter . '</td>';
            $html .= '<td>' . htmlspecialchars($answer['question_text']) . '</td>';
            $html .= '<td>' . htmlspecialchars($answer['answer']) . '</td>';
            $html .= '</tr>';
            $counter++;
        }

        $html .= '</tbody></table>';

        $pdf->writeHTML($html, true, false, true, false, '');
    }
} else {
    $html = '<p><em>Tidak ada data jawaban assessment yang ditemukan.</em></p>';
    $pdf->writeHTML($html, true, false, true, false, '');
}

// Add summary section
$html = '<h2>Ringkasan Assessment</h2>';
if (!empty($answers)) {
    $html .= '<p>Assessment ini mencakup ' . count($answers) . ' pertanyaan dari berbagai domain COBIT. ';
    $html .= 'Hasil assessment ini dapat digunakan sebagai dasar untuk evaluasi dan perbaikan proses IT Governance.</p>';
} else {
    $html .= '<p>Tidak ada data jawaban assessment yang ditemukan dalam database.</p>';
}

$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF
$filename = 'COBIT_Assessment_Report_' . $assessment['username'] . '_' . date('Y-m-d') . '.pdf';
$pdf->Output($filename, 'D'); // 'D' forces download
?>