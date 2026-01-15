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

// Fungsi untuk menghitung rata-rata jawaban per domain
function calculateDomainAverages($answers) {
    if (empty($answers)) {
        return []; // Kembalikan array kosong jika tidak ada jawaban
    }

    $domainTotals = [];
    $domainCounts = [];

    foreach ($answers as $answer) {
        $domain = $answer['domain'];
        $answerValue = (int)$answer['answer']; // Asumsikan jawaban berupa angka

        if (!isset($domainTotals[$domain])) {
            $domainTotals[$domain] = 0;
            $domainCounts[$domain] = 0;
        }

        $domainTotals[$domain] += $answerValue;
        $domainCounts[$domain]++;
    }

    $averages = [];
    foreach ($domainTotals as $domain => $total) {
        $averages[$domain] = $domainCounts[$domain] > 0 ? $total / $domainCounts[$domain] : 0;
    }

    return $averages;
}

// Ambil data assessment
$assessment = getAssessmentData($pdo, $_SESSION['user_id']);
if (!$assessment) {
    // Buat PDF dengan pesan bahwa tidak ada data
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('Laporan Grafik Assessment COBIT - Tidak Ada Data');
    $pdf->SetSubject('Laporan Grafik Hasil Assessment COBIT');
    $pdf->SetKeywords('COBIT, assessment, PDF, chart, graph');

    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->AddPage();

    $html = '
    <style>
        h1 { color: #333; font-size: 22px; margin-bottom: 10px; text-align: center; }
        .info-box { background-color: #f9f9f9; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
    </style>
    <div style="text-align: center;">
        <h1>LAPORAN GRAFIK ASSESSMENT COBIT</h1>
        <div class="info-box">
            <strong>Nama Pengguna:</strong> ' . ($_SESSION['username'] ?? 'User') . '<br>
            <strong>Status:</strong> Tidak ada data assessment ditemukan
        </div>
        <p><em>Belum ada data assessment yang tersedia untuk dibuatkan grafik.</em></p>
    </div>';

    $pdf->writeHTML($html, true, false, true, false, '');

    $filename = 'COBIT_Chart_Report_' . ($_SESSION['username'] ?? 'user') . '_' . date('Y-m-d') . '.pdf';
    $pdf->Output($filename, 'D');
    exit;
}

$answers = getAssessmentAnswers($pdo, $assessment['id']);
$domainAverages = calculateDomainAverages($answers);

// Buat instance TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set informasi dokumen
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Laporan Grafik Assessment COBIT - ' . $assessment['username']);
$pdf->SetSubject('Laporan Grafik Hasil Assessment COBIT');
$pdf->SetKeywords('COBIT, assessment, PDF, chart, graph');

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
    h1 { color: #333; font-size: 22px; margin-bottom: 10px; text-align: center; }
    h2 { color: #555; font-size: 16px; margin-top: 20px; margin-bottom: 10px; }
    .info-box { background-color: #f9f9f9; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
    .chart-container { text-align: center; margin: 20px 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
    th { background-color: #f2f2f2; }
</style>
<div style="text-align: center;">
    <h1>LAPORAN GRAFIK ASSESSMENT COBIT</h1>
    <div class="info-box">
        <strong>Nama Pengguna:</strong> ' . htmlspecialchars($assessment['username']) . '<br>
        <strong>Tanggal Assessment:</strong> ' . date('d M Y H:i:s', strtotime($assessment['created_at'])) . '<br>
        <strong>ID Assessment:</strong> ' . $assessment['id'] . '
    </div>
</div>
';

$pdf->writeHTML($html, true, false, true, false, '');

// Create a simple bar chart representation using TCPDF
$pdf->SetFont('helvetica', '', 10);

// Chart title
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Grafik Rata-rata Jawaban per Domain', 0, 1, 'C');
$pdf->Ln(5);

// Check if we have domain averages to display
if (!empty($domainAverages)) {
    // Prepare chart data
    $domains = array_keys($domainAverages);
    $values = array_values($domainAverages);

    // Find max value for scaling
    $maxValue = !empty($values) ? max($values) : 1;
    if ($maxValue == 0) $maxValue = 1; // Prevent division by zero

    // Chart dimensions
    $chartX = 30;
    $chartY = $pdf->GetY();
    $chartWidth = 150;
    $chartHeight = 80;

    // Draw chart axes
    $pdf->Line($chartX, $chartY, $chartX, $chartY + $chartHeight); // Y-axis
    $pdf->Line($chartX, $chartY + $chartHeight, $chartX + $chartWidth, $chartY + $chartHeight); // X-axis

    // Draw scale on Y-axis
    for ($i = 0; $i <= 5; $i++) {
        $yPos = $chartY + $chartHeight - ($i * $chartHeight / 5);
        $pdf->Line($chartX - 3, $yPos, $chartX, $yPos); // Tick marks
        $pdf->SetXY($chartX - 10, $yPos - 3);
        $pdf->Cell(8, 6, sprintf("%.1f", $i * $maxValue / 5), 0, 0, 'R');
    }

    // Draw bars
    $barWidth = $chartWidth / count($domains) * 0.8;
    $barSpacing = $chartWidth / count($domains) * 0.2;

    for ($i = 0; $i < count($domains); $i++) {
        $xPos = $chartX + ($i * ($barWidth + $barSpacing)) + ($barSpacing / 2);
        $barHeight = ($values[$i] / $maxValue) * $chartHeight;
        $yPos = $chartY + $chartHeight - $barHeight;

        // Draw bar
        $pdf->SetFillColor(66, 139, 202); // Blue color
        $pdf->Rect($xPos, $yPos, $barWidth, $barHeight, 'F');

        // Draw bar outline
        $pdf->SetDrawColor(0);
        $pdf->Rect($xPos, $yPos, $barWidth, $barHeight, 'D');

        // Draw domain name below bar
        $pdf->SetXY($xPos, $chartY + $chartHeight + 5);
        $pdf->Cell($barWidth, 6, substr($domains[$i], 0, 10) . (strlen($domains[$i]) > 10 ? '...' : ''), 0, 0, 'C', false, '', 1, true, 'T', 'C');

        // Draw value on top of bar
        $pdf->SetXY($xPos, $yPos - 6);
        $pdf->Cell($barWidth, 6, sprintf("%.2f", $values[$i]), 0, 0, 'C', false, '', 1, true, 'T', 'C');
    }

    $pdf->Ln(100); // Add space after chart
} else {
    // Display message when no chart data is available
    $pdf->SetFont('helvetica', 'I', 12);
    $pdf->Cell(0, 10, '(Grafik tidak dapat ditampilkan: tidak ada data assessment)', 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', '', 12);
}

// Table with detailed values
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Rincian Nilai per Domain', 0, 1);
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 10);
if (!empty($domainAverages)) {
    $html = '<table>
    <thead>
    <tr><th>Domain</th><th>Rata-rata Nilai</th></tr>
    </thead>
    <tbody>';

    foreach ($domainAverages as $domain => $average) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($domain) . '</td>';
        $html .= '<td>' . sprintf("%.2f", $average) . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';
} else {
    $html = '<p><em>Tidak ada data nilai per domain untuk ditampilkan.</em></p>';
}

$pdf->writeHTML($html, true, false, true, false, '');

// Add summary
$pdf->Ln(10);
$pdf->SetFont('helvetica', '', 12);
$html = '<h2>Analisis Hasil</h2>';
if (!empty($domainAverages)) {
    $html .= '<p>Grafik di atas menunjukkan rata-rata nilai jawaban Anda pada masing-masing domain COBIT. ';
    $html .= 'Nilai yang lebih tinggi menunjukkan kemampuan atau kematangan yang lebih baik dalam domain tersebut. ';
    $html .= 'Gunakan informasi ini untuk mengidentifikasi area yang perlu ditingkatkan dalam tata kelola TI organisasi Anda.</p>';
} else {
    $html .= '<p>Belum ada data assessment yang tersedia untuk dianalisis. Silakan lengkapi assessment terlebih dahulu.</p>';
}

$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF
$filename = 'COBIT_Chart_Report_' . $assessment['username'] . '_' . date('Y-m-d') . '.pdf';
$pdf->Output($filename, 'D'); // 'D' forces download
?>