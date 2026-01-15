<?php
// API endpoint to calculate maturity levels for COBIT 5 MEA assessment
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$assessmentId = $input['assessment_id'] ?? 0;

if (!$assessmentId) {
    http_response_code(400);
    echo json_encode(['error' => 'Assessment ID is required']);
    exit;
}

try {
    // Get all responses for this assessment
    $stmt = $pdo->prepare("
        SELECT ar.question_id, ar.response_value, aq.process_code
        FROM assessment_responses ar
        JOIN assessment_questions aq ON ar.question_id = aq.id
        WHERE ar.assessment_id = ?
    ");
    $stmt->execute([$assessmentId]);
    $responses = $stmt->fetchAll();

    if (empty($responses)) {
        http_response_code(400);
        echo json_encode(['error' => 'No responses found for this assessment']);
        exit;
    }

    // Group responses by process code
    $processResponses = [];
    foreach ($responses as $response) {
        $processCode = $response['process_code'];
        if (!isset($processResponses[$processCode])) {
            $processResponses[$processCode] = [];
        }
        $processResponses[$processCode][] = $response['response_value'];
    }

    // Calculate maturity for each process
    $processResults = [];
    $totalMaturity = 0;
    $processCount = 0;

    foreach ($processResponses as $processCode => $responseValues) {
        $averageScore = array_sum($responseValues) / count($responseValues);
        $processResults[] = [
            'process_code' => $processCode,
            'average_score' => round($averageScore, 2),
            'total_questions' => count($responseValues),
            'total_responses' => count($responseValues)
        ];
        
        $totalMaturity += $averageScore;
        $processCount++;
    }

    // Calculate overall maturity
    $overallMaturity = $processCount > 0 ? $totalMaturity / $processCount : 0;
    $overallMaturity = round($overallMaturity, 2);

    // Determine maturity status
    $maturityStatus = getMaturityStatus($overallMaturity);

    // Generate recommendations based on maturity level
    $recommendations = generateRecommendations($overallMaturity, $processResults);

    // Start transaction
    $pdo->beginTransaction();

    // Delete existing results for this assessment (if any)
    $stmt = $pdo->prepare("DELETE FROM assessment_results WHERE assessment_id = ?");
    $stmt->execute([$assessmentId]);

    $stmt = $pdo->prepare("DELETE FROM assessment_summary WHERE assessment_id = ?");
    $stmt->execute([$assessmentId]);

    // Insert new results
    foreach ($processResults as $result) {
        $stmt = $pdo->prepare("
            INSERT INTO assessment_results (assessment_id, process_code, average_score, total_questions, total_responses)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $assessmentId,
            $result['process_code'],
            $result['average_score'],
            $result['total_questions'],
            $result['total_responses']
        ]);
    }

    // Insert summary
    $stmt = $pdo->prepare("
        INSERT INTO assessment_summary (assessment_id, overall_maturity_level, maturity_status, recommendations)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $assessmentId,
        $overallMaturity,
        $maturityStatus,
        $recommendations
    ]);

    // Update assessment status to completed
    $stmt = $pdo->prepare("UPDATE assessments SET status = 'completed', completed_at = NOW() WHERE id = ?");
    $stmt->execute([$assessmentId]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'overall_maturity' => $overallMaturity,
        'maturity_status' => $maturityStatus,
        'process_results' => $processResults,
        'recommendations' => $recommendations
    ]);

} catch (Exception $e) {
    $pdo->rollback();
    http_response_code(500);
    echo json_encode(['error' => 'Failed to calculate maturity: ' . $e->getMessage()]);
}

function getMaturityStatus($maturityLevel) {
    if ($maturityLevel >= 4.5) {
        return 'Optimized';
    } else if ($maturityLevel >= 3.5) {
        return 'Managed';
    } else if ($maturityLevel >= 2.5) {
        return 'Defined';
    } else if ($maturityLevel >= 1.5) {
        return 'Repeatable';
    } else if ($maturityLevel >= 0.5) {
        return 'Initial';
    } else {
        return 'Non-Existent';
    }
}

function generateRecommendations($overallMaturity, $processResults) {
    $recommendations = "Rekomendasi berdasarkan hasil assessment COBIT 5 MEA:\n\n";
    
    // Overall recommendation based on maturity level
    if ($overallMaturity < 1.5) {
        $recommendations .= "• Organisasi Anda berada pada tingkat kematangan yang sangat rendah. Fokus utama harus diberikan pada pembentukan dasar-dasar proses TI.\n";
        $recommendations .= "• Definisikan kebijakan dan prosedur dasar untuk praktik TI.\n";
        $recommendations .= "• Bangun struktur organisasi yang jelas untuk tata kelola TI.\n";
    } elseif ($overallMaturity < 2.5) {
        $recommendations .= "• Organisasi Anda berada pada tingkat kematangan awal. Perlu memperkuat proses-proses dasar yang telah ada.\n";
        $recommendations .= "• Dokumentasikan proses-proses penting yang saat ini hanya dilakukan secara informal.\n";
        $recommendations .= "• Terapkan pelatihan untuk memastikan konsistensi pelaksanaan proses.\n";
    } elseif ($overallMaturity < 3.5) {
        $recommendations .= "• Organisasi Anda memiliki proses yang terdokumentasi dengan baik. Fokus pada standarisasi dan integrasi proses.\n";
        $recommendations .= "• Pastikan semua unit organisasi mengikuti prosedur yang telah ditetapkan.\n";
        $recommendations .= "• Evaluasi secara berkala efektivitas proses yang ada.\n";
    } elseif ($overallMaturity < 4.5) {
        $recommendations .= "• Organisasi Anda memiliki proses yang terukur dan terkendali. Fokus pada pengoptimalan dan peningkatan kinerja.\n";
        $recommendations .= "• Terapkan metrik kinerja yang lebih canggih untuk memantau efektivitas proses.\n";
        $recommendations .= "• Gunakan teknologi untuk otomatisasi dan peningkatan efisiensi proses.\n";
    } else {
        $recommendations .= "• Organisasi Anda memiliki tingkat kematangan yang sangat tinggi. Fokus pada inovasi dan peningkatan berkelanjutan.\n";
        $recommendations .= "• Teruskan praktik terbaik dan inovasi untuk menjaga keunggulan kompetitif.\n";
        $recommendations .= "• Evaluasi dan sesuaikan proses secara berkala untuk menghadapi perubahan lingkungan bisnis.\n";
    }
    
    // Recommendations for specific processes
    $recommendations .= "\nRekomendasi untuk setiap proses MEA:\n";
    foreach ($processResults as $result) {
        $processName = '';
        switch ($result['process_code']) {
            case 'MEA01':
                $processName = 'MEA01 - Monitor, Evaluate and Assess Performance and Conformance';
                break;
            case 'MEA02':
                $processName = 'MEA02 - Monitor, Evaluate and Assess IT Governance System Performance';
                break;
            case 'MEA03':
                $processName = 'MEA03 - Monitor, Evaluate and Assess Risk';
                break;
            default:
                $processName = $result['process_code'];
        }
        
        $recommendations .= "• $processName: Maturity level " . $result['average_score'] . "/5.0\n";
        
        if ($result['average_score'] < 2.0) {
            $recommendations .= "  - Perlu perhatian serius untuk membangun dasar proses ini.\n";
        } elseif ($result['average_score'] < 3.0) {
            $recommendations .= "  - Perlu peningkatan untuk memperkuat implementasi proses.\n";
        } elseif ($result['average_score'] < 4.0) {
            $recommendations .= "  - Perlu standarisasi dan dokumentasi yang lebih baik.\n";
        } elseif ($result['average_score'] < 5.0) {
            $recommendations .= "  - Perlu pengukuran dan pengendalian yang lebih ketat.\n";
        } else {
            $recommendations .= "  - Proses ini sudah sangat baik dan dioptimalkan.\n";
        }
    }
    
    return $recommendations;
}
?>