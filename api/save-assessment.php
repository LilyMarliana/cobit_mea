<?php
// API endpoint to save assessment data
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$title = $input['title'] ?? '';
$description = $input['description'] ?? '';
$responses = $input['responses'] ?? [];
$userId = $_SESSION['user_id'] ?? 0;

if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

if (empty($title)) {
    http_response_code(400);
    echo json_encode(['error' => 'Title is required']);
    exit;
}

if (empty($responses)) {
    http_response_code(400);
    echo json_encode(['error' => 'Responses are required']);
    exit;
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Create new assessment
    $stmt = $pdo->prepare("
        INSERT INTO assessments (user_id, title, description, status)
        VALUES (?, ?, ?, 'in_progress')
    ");
    $stmt->execute([$userId, $title, $description]);
    $assessmentId = $pdo->lastInsertId();

    // Save responses
    foreach ($responses as $questionId => $responseValue) {
        $stmt = $pdo->prepare("
            INSERT INTO assessment_responses (assessment_id, question_id, response_value)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$assessmentId, $questionId, (int)$responseValue]);
    }

    $pdo->commit();

    // Calculate maturity levels via API call
    $calculateUrl = BASE_URL . 'api/calculate-maturity.php';
    $postData = json_encode(['assessment_id' => $assessmentId]);

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $postData
        ]
    ]);

    $result = file_get_contents($calculateUrl, false, $context);

    if ($result === FALSE) {
        // If API call fails, return error
        http_response_code(500);
        echo json_encode(['error' => 'Failed to calculate maturity levels']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'assessment_id' => $assessmentId,
        'message' => 'Assessment saved and calculated successfully'
    ]);

} catch (Exception $e) {
    $pdo->rollback();
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save assessment: ' . $e->getMessage()]);
}
?>