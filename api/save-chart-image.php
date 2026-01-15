<?php
// File: api/save-chart-image.php
// Endpoint untuk menyimpan gambar chart yang dikirim dari JavaScript

require_once '../config.php';

// Periksa apakah sesi sudah aktif sebelum memulainya
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit();
}

$imageData = $input['image_data'] ?? '';
$assessmentId = $input['assessment_id'] ?? 0;
$chartId = $input['chart_id'] ?? 'chart';

// Validate inputs
if (empty($imageData) || empty($assessmentId)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

// Validate assessment belongs to user
$stmt = $pdo->prepare("SELECT id FROM assessments WHERE id = ? AND user_id = ?");
$stmt->execute([$assessmentId, $_SESSION['user_id']]);
$assessment = $stmt->fetch();

if (!$assessment) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

// Extract image data (remove data:image/png;base64, prefix)
$imageData = str_replace('data:image/png;base64,', '', $imageData);
$imageData = str_replace(' ', '+', $imageData);

// Decode base64 image data
$imageBinary = base64_decode($imageData);

if ($imageBinary === false) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Invalid image data']);
    exit();
}

// Validate image
if (!imagecreatefromstring($imageBinary)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Invalid image format']);
    exit();
}

// Create directory for chart images if it doesn't exist
$chartDir = __DIR__ . '/../assets/charts/';
if (!is_dir($chartDir)) {
    mkdir($chartDir, 0755, true);
}

// Generate unique filename
$filename = 'chart_' . $assessmentId . '_' . $chartId . '_' . time() . '.png';
$filepath = $chartDir . $filename;

// Save image to file
if (file_put_contents($filepath, $imageBinary)) {
    echo json_encode([
        'success' => true,
        'filename' => $filename,
        'filepath' => $filepath,
        'message' => 'Chart image saved successfully'
    ]);
} else {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'message' => 'Failed to save image']);
}
?>