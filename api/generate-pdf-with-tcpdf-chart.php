<?php
// File: api/generate-pdf-with-tcpdf-chart.php
// API endpoint to generate PDF report for assessment with TCPDF-generated chart

require_once '../config.php';
require_once '../vendor/tcpdf/tcpdf.php';

// Check if assessment ID is provided
$assessmentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$assessmentId) {
    die('Assessment ID is required');
}

// Periksa apakah sesi sudah aktif sebelum memulainya
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Get assessment details
$stmt = $pdo->prepare("
    SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) as user_name, u.email as user_email
    FROM assessments a
    JOIN users u ON a.user_id = u.id
    WHERE a.id = ? AND a.user_id = ?
");
$stmt->execute([$assessmentId, $_SESSION['user_id']]);
$assessment = $stmt->fetch();

if (!$assessment) {
    die('Assessment not found or unauthorized');
}

// Get assessment results
$stmt = $pdo->prepare("
    SELECT *
    FROM assessment_results
    WHERE assessment_id = ?
    ORDER BY process_code
");
$stmt->execute([$assessmentId]);
$results = $stmt->fetchAll();

// Get assessment summary
$stmt = $pdo->prepare("
    SELECT *
    FROM assessment_summary
    WHERE assessment_id = ?
");
$stmt->execute([$assessmentId]);
$summary = $stmt->fetch();

// Get individual responses for detailed view
$stmt = $pdo->prepare("
    SELECT ar.response_value, aq.question_text, aq.process_code, mp.process_name
    FROM assessment_responses ar
    JOIN assessment_questions aq ON ar.question_id = aq.id
    JOIN mea_processes mp ON aq.process_code = mp.process_code
    WHERE ar.assessment_id = ?
    ORDER BY mp.process_order, aq.question_order
");
$stmt->execute([$assessmentId]);
$responses = $stmt->fetchAll();

// Group responses by process
$groupedResponses = [];
foreach ($responses as $response) {
    $processCode = $response['process_code'];
    if (!isset($groupedResponses[$processCode])) {
        $groupedResponses[$processCode] = [
            'process_name' => $response['process_name'],
            'responses' => []
        ];
    }
    $groupedResponses[$processCode]['responses'][] = $response;
}

// Create TCPDF instance
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('COBIT 5 MEA Assessment Report - ' . $assessment['title']);
$pdf->SetSubject('COBIT 5 MEA Assessment Report');
$pdf->SetKeywords('COBIT, assessment, PDF, report');

// Set default header data
$pdf->SetHeaderData('', 0, 'COBIT 5 MEA Assessment Report', SITE_NAME);

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Set font to DejaVu Sans for better Unicode support
$pdf->SetFont('dejavusans', '', 10);

// Add a page
$pdf->AddPage();

// Add header
$pdf->SetFont('dejavusans', 'B', 18);
$pdf->Cell(0, 15, 'LAPORAN ASSESSMENT COBIT 5 MEA', 0, 1, 'C');
$pdf->SetFont('dejavusans', '', 14);
$pdf->Cell(0, 8, 'Monitor, Evaluate, Assess Domain', 0, 1, 'C');
$pdf->SetFont('dejavusans', '', 12);
$pdf->Cell(0, 8, 'Program Studi Teknologi Informasi - Universitas Sari Mulia', 0, 1, 'C');
$pdf->Ln(10);

// Add assessment information
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, '1. Informasi Assessment', 0, 1, 'L');
$pdf->Ln(2);

$pdf->SetFont('dejavusans', '', 10);
$pdf->Cell(50, 8, 'Judul Assessment:', 0, 0);
$pdf->MultiCell(0, 8, $assessment['title'], 0, 'L');
$pdf->Cell(50, 8, 'Deskripsi Assessment:', 0, 0);
$pdf->MultiCell(0, 8, $assessment['description'], 0, 'L');
$pdf->Cell(50, 8, 'Nama Pengguna:', 0, 0);
$pdf->MultiCell(0, 8, $assessment['user_name'], 0, 'L');
$pdf->Cell(50, 8, 'Tanggal Assessment:', 0, 0);
$pdf->MultiCell(0, 8, date('d M Y H:i:s', strtotime($assessment['created_at'])), 0, 'L');
$pdf->Ln(5);

// Add summary section
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, '2. Ringkasan Maturity Level', 0, 1, 'L');
$pdf->Ln(2);

if (!empty($results)) {
    // Calculate max score for scaling
    $maxScore = 0;
    foreach ($results as $result) {
        if ($result['average_score'] > $maxScore) {
            $maxScore = $result['average_score'];
        }
    }
    $maxScore = max($maxScore, 5); // Ensure max is at least 5
    
    // Draw chart using TCPDF drawing functions
    $pdf->SetFont('dejavusans', 'B', 10);
    $pdf->Cell(0, 10, 'Visualisasi Maturity Level per Proses', 0, 1, 'L');
    
    // Chart dimensions
    $chartX = 25;
    $chartY = $pdf->GetY();
    $chartWidth = 160;
    $chartHeight = 60;
    
    // Draw chart area
    $pdf->Rect($chartX, $chartY, $chartWidth, $chartHeight, 'D');
    
    // Draw Y-axis labels and grid
    for ($i = 0; $i <= 5; $i++) {
        $yPos = $chartY + $chartHeight - ($i * $chartHeight / 5);
        $pdf->Line($chartX, $yPos, $chartX + $chartWidth, $yPos, array('color' => array(200, 200, 200)));
        $pdf->SetXY($chartX - 8, $yPos - 3);
        $pdf->Cell(8, 6, $i, 0, 0, 'R');
    }
    
    // Draw Y-axis label
    $pdf->SetFont('dejavusans', '', 8);
    $pdf->StartTransform();
    $pdf->Rotate(-90, $chartX - 5, $chartY + $chartHeight/2);
    $pdf->SetXY($chartX - 5, $chartY + $chartHeight/2 - 5);
    $pdf->Cell(0, 0, 'Tingkat Kematangan', 0, 0, 'C');
    $pdf->StopTransform();
    
    // Draw bars
    $numBars = count($results);
    $barWidth = ($chartWidth - 40) / $numBars; // Leave space for labels
    $barSpacing = ($chartWidth - 40 - ($numBars * $barWidth)) / ($numBars - 1);
    
    $colors = [
        [59, 130, 246],  // Blue
        [16, 185, 129],  // Green
        [245, 158, 11]   // Yellow
    ];
    
    $index = 0;
    foreach ($results as $result) {
        $xPos = $chartX + 20 + $index * ($barWidth + $barSpacing);
        $barHeight = ($result['average_score'] / $maxScore) * ($chartHeight - 10);
        $yPos = $chartY + $chartHeight - $barHeight - 5;
        
        // Draw bar
        $colorIndex = $index % count($colors);
        $pdf->SetFillColor($colors[$colorIndex][0], $colors[$colorIndex][1], $colors[$colorIndex][2]);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->Rect($xPos, $yPos, $barWidth, $barHeight, 'DF');
        
        // Draw value on top of bar
        $pdf->SetFont('dejavusans', '', 8);
        $pdf->SetXY($xPos, $yPos - 6);
        $pdf->Cell($barWidth, 6, number_format($result['average_score'], 1), 0, 0, 'C');
        
        // Draw process label below bar
        $processCode = $result['process_code'];
        $pdf->SetXY($xPos, $chartY + $chartHeight - 5);
        $pdf->Cell($barWidth, 6, $processCode, 0, 0, 'C');
        
        $index++;
    }
    
    $pdf->Ln($chartHeight + 15);
} else {
    $pdf->Cell(0, 10, 'Tidak ada data hasil assessment untuk ditampilkan.', 0, 1, 'L');
    $pdf->Ln(5);
}

// Add detailed results section
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, '3. Detail Hasil Assessment', 0, 1, 'L');
$pdf->Ln(2);

if (!empty($groupedResponses)) {
    foreach ($groupedResponses as $processCode => $processData) {
        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(0, 8, $processData['process_name'] . ' (' . $processCode . ')', 0, 1, 'L', true);
        $pdf->Ln(1);
        
        $pdf->SetFont('dejavusans', 'B', 9);
        $pdf->Cell(10, 8, 'No', 1, 0, 'C');
        $pdf->Cell(120, 8, 'Pertanyaan', 1, 0, 'C');
        $pdf->Cell(25, 8, 'Respons', 1, 0, 'C');
        $pdf->Cell(35, 8, 'Status', 1, 1, 'C');
        
        $pdf->SetFont('dejavusans', '', 9);
        $index = 1;
        foreach ($processData['responses'] as $response) {
            $status = '';
            switch ($response['response_value']) {
                case 5: $status = 'Sangat Baik'; break;
                case 4: $status = 'Baik'; break;
                case 3: $status = 'Cukup'; break;
                case 2: $status = 'Kurang'; break;
                case 1: $status = 'Buruk'; break;
                case 0: $status = 'Tidak Ada'; break;
                default: $status = 'Tidak Valid'; break;
            }
            
            $pdf->Cell(10, 8, $index, 1, 0, 'C');
            $pdf->MultiCell(120, 8, $response['question_text'], 1, 'L', false, 0, '', '', true, 0, true);
            $pdf->Cell(25, 8, $response['response_value'].'/5', 1, 0, 'C');
            $pdf->Cell(35, 8, $status, 1, 1, 'C');
            
            $index++;
        }
        $pdf->Ln(3);
    }
} else {
    $pdf->Cell(0, 10, 'Tidak ada data respons terperinci untuk ditampilkan.', 0, 1, 'L');
    $pdf->Ln(5);
}

// Add recommendations section
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, '4. Rekomendasi', 0, 1, 'L');
$pdf->Ln(2);

if ($summary && !empty($summary['recommendations'])) {
    $pdf->SetFont('dejavusans', '', 10);
    $pdf->MultiCell(0, 8, $summary['recommendations'], 0, 'L');
} else {
    $pdf->SetFont('dejavusans', '', 10);
    $pdf->Cell(0, 10, 'Belum ada rekomendasi yang tersedia untuk assessment ini.', 0, 1, 'L');
}

// Close and output PDF
$filename = 'COBIT_5_MEA_Assessment_Report_' . $assessmentId . '_' . date('Y-m-d') . '.pdf';
$pdf->Output($filename, 'D'); // 'D' forces download
?>