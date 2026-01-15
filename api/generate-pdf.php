<?php
// API endpoint to generate PDF report for assessment
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

// Generate HTML content for the PDF
$html = generateAcademicReport($assessment, $results, $summary, $groupedResponses);

// Output the HTML content to PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF
$filename = 'COBIT_5_MEA_Assessment_Report_' . $assessmentId . '_' . date('Y-m-d') . '.pdf';
$pdf->Output($filename, 'D'); // 'D' forces download

function generateAcademicReport($assessment, $results, $summary, $groupedResponses) {
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body {
                font-family: "DejaVu Sans", sans-serif;
                margin: 20px;
                line-height: 1.4;
            }
            .header {
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 2px solid #333;
                padding-bottom: 10px;
            }
            .title {
                font-size: 18px;
                font-weight: bold;
                color: #333;
                margin-bottom: 5px;
            }
            .subtitle {
                font-size: 14px;
                color: #666;
                margin-bottom: 10px;
            }
            .institution {
                font-size: 12px;
                color: #888;
            }
            .section {
                margin-bottom: 25px;
            }
            .section-title {
                font-size: 14px;
                font-weight: bold;
                color: #333;
                border-bottom: 1px solid #ccc;
                padding-bottom: 5px;
                margin-bottom: 15px;
            }
            .info-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 15px;
            }
            .info-table th, .info-table td {
                border: 1px solid #999;
                padding: 8px;
                text-align: left;
            }
            .info-table th {
                background-color: #f0f0f0;
                width: 30%;
            }
            .summary-grid {
                width: 100%;
                display: table;
                table-layout: fixed;
                margin-bottom: 15px;
            }
            .summary-cell {
                display: table-cell;
                width: 33.33%;
                padding: 5px;
                vertical-align: top;
            }
            .summary-card {
                background-color: #f9f9f9;
                border: 1px solid #ddd;
                border-radius: 4px;
                padding: 10px;
                height: 150px;
            }
            .summary-value {
                font-size: 18px;
                font-weight: bold;
                color: #1e40af;
                text-align: center;
            }
            .summary-label {
                font-size: 12px;
                color: #666;
                text-align: center;
            }
            .results-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 15px;
            }
            .results-table th, .results-table td {
                border: 1px solid #999;
                padding: 6px;
                text-align: left;
            }
            .results-table th {
                background-color: #f0f0f0;
                font-weight: bold;
                text-align: center;
            }
            .results-table .process-name {
                background-color: #f5f5f5;
                font-weight: bold;
            }
            .results-table .no {
                width: 5%;
                text-align: center;
            }
            .results-table .question {
                width: 50%;
            }
            .results-table .response {
                width: 15%;
                text-align: center;
            }
            .results-table .status {
                width: 15%;
                text-align: center;
            }
            .results-table .process-code {
                width: 15%;
                text-align: center;
            }
            .recommendations {
                white-space: pre-wrap;
            }
            .progress-bar-container {
                width: 100%;
                background-color: #e0e0e0;
                border-radius: 5px;
                height: 15px;
                margin: 5px 0;
            }
            .progress-bar-fill {
                height: 100%;
                border-radius: 5px;
                background: linear-gradient(to right, #3b82f6, #1d4ed8);
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="title">LAPORAN ASSESSMENT COBIT 5 MEA</div>
            <div class="subtitle">Monitor, Evaluate, Assess Domain</div>
            <div class="institution">Program Studi Teknologi Informasi - Universitas Sari Mulia</div>
        </div>

        <!-- Section 1: Informasi Assessment -->
        <div class="section">
            <div class="section-title">1. Informasi Assessment</div>
            <table class="info-table">
                <tr>
                    <th>Judul Assessment</th>
                    <td>' . htmlspecialchars($assessment['title']) . '</td>
                </tr>
                <tr>
                    <th>Deskripsi Assessment</th>
                    <td>' . htmlspecialchars($assessment['description']) . '</td>
                </tr>
                <tr>
                    <th>Nama Pengguna</th>
                    <td>' . htmlspecialchars($assessment['user_name']) . '</td>
                </tr>
                <tr>
                    <th>Tanggal Assessment</th>
                    <td>' . date('d M Y H:i:s', strtotime($assessment['created_at'])) . '</td>
                </tr>
            </table>
        </div>

        <!-- Section 2: Ringkasan Maturity Level -->
        <div class="section">
            <div class="section-title">2. Ringkasan Maturity Level</div>';
            
            if (!empty($results)) {
                $html .= '<div class="summary-grid">';
                
                foreach ($results as $result) {
                    $processName = '';
                    switch ($result['process_code']) {
                        case 'MEA01':
                            $processName = 'MEA01 - Performance & Conformance';
                            break;
                        case 'MEA02':
                            $processName = 'MEA02 - Governance System';
                            break;
                        case 'MEA03':
                            $processName = 'MEA03 - Risk';
                            break;
                        default:
                            $processName = $result['process_code'];
                    }
                    
                    $percentage = ($result['average_score'] / 5) * 100;
                    
                    $html .= '
                    <div class="summary-cell">
                        <div class="summary-card">
                            <div class="summary-value">' . number_format($result['average_score'], 2) . '</div>
                            <div class="summary-label">Skor Rata-rata</div>
                            <div style="margin-top: 10px; font-size: 12px; text-align: center;">' . htmlspecialchars($processName) . '</div>
                            <div class="progress-bar-container">
                                <div class="progress-bar-fill" style="width: ' . min($percentage, 100) . '%;"></div>
                            </div>
                            <div style="text-align: center; font-size: 10px; margin-top: 5px;">' . number_format($percentage, 1) . '% dari maksimum</div>
                        </div>
                    </div>';
                }
                
                $html .= '</div>';
                
                // Overall summary if available
                if ($summary) {
                    $html .= '
                    <div style="margin-top: 20px;">
                        <table class="info-table">
                            <tr>
                                <th>Tingkat Kematangan Keseluruhan</th>
                                <td>' . ($summary['overall_maturity_level'] ? number_format($summary['overall_maturity_level'], 2) : 'N/A') . '</td>
                            </tr>
                            <tr>
                                <th>Status Kematangan</th>
                                <td>' . ($summary['maturity_status'] ? htmlspecialchars($summary['maturity_status']) : 'N/A') . '</td>
                            </tr>
                        </table>
                    </div>';
                }
            } else {
                $html .= '<p>Tidak ada data hasil assessment untuk ditampilkan.</p>';
            }
            
            $html .= '
        </div>

        <!-- Section 3: Visualisasi Maturity Level -->
        <div class="section">
            <div class="section-title">3. Visualisasi Maturity Level</div>
            <p>Grafik berikut menunjukkan tingkat kematangan untuk setiap proses MEA:</p>
            
            <!-- Representasi teks dari grafik -->
            <table class="info-table" style="width: 80%; margin: 10px auto;">
                <thead>
                    <tr style="background-color: #e0e0e0;">
                        <th style="width: 30%; text-align: center;">Proses</th>
                        <th style="width: 40%; text-align: center;">Tingkat Kematangan</th>
                        <th style="width: 30%; text-align: center;">Skor</th>
                    </tr>
                </thead>
                <tbody>';
                
                if (!empty($results)) {
                    foreach ($results as $result) {
                        $processName = '';
                        switch ($result['process_code']) {
                            case 'MEA01':
                                $processName = 'MEA01';
                                break;
                            case 'MEA02':
                                $processName = 'MEA02';
                                break;
                            case 'MEA03':
                                $processName = 'MEA03';
                                break;
                            default:
                                $processName = $result['process_code'];
                        }
                        
                        $percentage = ($result['average_score'] / 5) * 100;
                        
                        $html .= '
                        <tr>
                            <td style="text-align: center;">' . htmlspecialchars($processName) . '</td>
                            <td>
                                <div style="width: 100%; background-color: #e0e0e0; border-radius: 5px; height: 20px;">
                                    <div style="width: ' . min($percentage, 100) . '%; background: linear-gradient(to right, #3b82f6, #1d4ed8); height: 100%; border-radius: 5px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 10px;">
                                        ' . number_format($percentage, 1) . '%
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: center;">' . number_format($result['average_score'], 2) . '/5.0</td>
                        </tr>';
                    }
                } else {
                    $html .= '
                    <tr>
                        <td colspan="3" style="text-align: center;">Tidak ada data</td>
                    </tr>';
                }
                
                $html .= '
                </tbody>
            </table>
        </div>

        <!-- Section 4: Detail Hasil Assessment -->
        <div class="section">
            <div class="section-title">4. Detail Hasil Assessment</div>';
            
            if (!empty($groupedResponses)) {
                foreach ($groupedResponses as $processCode => $processData) {
                    $html .= '
                    <div style="margin-bottom: 20px;">
                        <div class="process-name" style="padding: 8px; background-color: #f5f5f5; font-weight: bold; margin-bottom: 10px;">
                            ' . htmlspecialchars($processData['process_name']) . ' (' . $processCode . ')
                        </div>
                        <table class="results-table">
                            <thead>
                                <tr>
                                    <th class="no">No</th>
                                    <th class="question">Pertanyaan</th>
                                    <th class="response">Respons</th>
                                    <th class="status">Status</th>
                                </tr>
                            </thead>
                            <tbody>';
                            
                            foreach ($processData['responses'] as $index => $response) {
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
                                
                                $html .= '
                                <tr>
                                    <td class="no">' . ($index + 1) . '</td>
                                    <td class="question">' . htmlspecialchars($response['question_text']) . '</td>
                                    <td class="response">' . $response['response_value'] . '/5</td>
                                    <td class="status">' . $status . '</td>
                                </tr>';
                            }
                            
                            $html .= '
                            </tbody>
                        </table>
                    </div>';
                }
            } else {
                $html .= '<p>Tidak ada data respons terperinci untuk ditampilkan.</p>';
            }
            
            $html .= '
        </div>';

        // Section 5: Rekomendasi
        $html .= '
        <div class="section">
            <div class="section-title">5. Rekomendasi</div>';
            
        if ($summary && !empty($summary['recommendations'])) {
            $html .= '
            <div class="recommendations">' . nl2br(htmlspecialchars($summary['recommendations'])) . '</div>';
        } else {
            $html .= '<p>Belum ada rekomendasi yang tersedia untuk assessment ini.</p>';
        }
        
        $html .= '
        </div>
    </body>
    </html>';

    return $html;
}
?>