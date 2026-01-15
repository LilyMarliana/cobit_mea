<?php
// Assessment page for COBIT 5 MEA
// Path has been corrected to work with the main index.php structure

// Get all assessment questions grouped by process
$stmt = $pdo->prepare("
    SELECT q.*, p.process_name, p.description
    FROM assessment_questions q
    JOIN mea_processes p ON q.process_code = p.process_code
    ORDER BY p.process_order, q.question_order
");
$stmt->execute();
$questions = $stmt->fetchAll();

// Group questions by process
$groupedQuestions = [];
foreach ($questions as $question) {
    $processCode = $question['process_code'];
    if (!isset($groupedQuestions[$processCode])) {
        $groupedQuestions[$processCode] = [
            'process_name' => $question['process_name'],
            'description' => $question['description'],
            'questions' => []
        ];
    }
    $groupedQuestions[$processCode]['questions'][] = $question;
}

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = cleanInput($_POST['title']);
    $description = cleanInput($_POST['description']);
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Create new assessment
        $stmt = $pdo->prepare("
            INSERT INTO assessments (user_id, title, description, status)
            VALUES (?, ?, ?, 'in_progress')
        ");
        $stmt->execute([$_SESSION['user_id'], $title, $description]);
        $assessmentId = $pdo->lastInsertId();
        
        // Save responses
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'question_') === 0) {
                $questionId = str_replace('question_', '', $key);
                $responseValue = (int)$value;
                
                $stmt = $pdo->prepare("
                    INSERT INTO assessment_responses (assessment_id, question_id, response_value)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$assessmentId, $questionId, $responseValue]);
            }
        }
        
        $pdo->commit();
        
        // Now calculate maturity levels using the API
        $url = BASE_URL . 'api/calculate-maturity.php';
        $data = json_encode(['assessment_id' => $assessmentId]);
        
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => $data
            ]
        ]);
        
        $result = file_get_contents($url, false, $context);
        
        if ($result === FALSE) {
            // If API call fails, redirect anyway
            redirect('index.php?page=results&id=' . $assessmentId);
        } else {
            redirect('index.php?page=results&id=' . $assessmentId);
        }
        
    } catch (Exception $e) {
        $pdo->rollback();
        setAlert('error', 'Gagal menyimpan assessment: ' . $e->getMessage());
    }
}
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Assessment COBIT 5 MEA</h1>
    <p class="text-gray-600">Isi kuesioner untuk mengevaluasi tingkat kematangan proses TI Anda</p>
</div>

<form method="POST" action="">
    <!-- Assessment Info -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Informasi Assessment</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul Assessment</label>
                <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3291B6] focus:border-[#3291B6]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi (Opsional)</label>
                <input type="text" name="description" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3291B6] focus:border-[#3291B6]">
            </div>
        </div>
    </div>

    <!-- Maturity Level Guide -->
    <div class="bg-[#3291B6]/10 border border-[#3291B6]/20 rounded-2xl p-6 mb-6">
        <h3 class="text-lg font-bold text-[#3291B6] mb-3">Skala Penilaian Maturity Level</h3>
        <div class="grid grid-cols-1 md:grid-cols-6 gap-2">
            <div class="text-center p-3 bg-white rounded-lg border">
                <div class="font-bold text-red-600">0</div>
                <div class="text-xs">Non-Existent</div>
            </div>
            <div class="text-center p-3 bg-white rounded-lg border">
                <div class="font-bold text-orange-600">1</div>
                <div class="text-xs">Initial</div>
            </div>
            <div class="text-center p-3 bg-white rounded-lg border">
                <div class="font-bold text-yellow-600">2</div>
                <div class="text-xs">Repeatable</div>
            </div>
            <div class="text-center p-3 bg-white rounded-lg border">
                <div class="font-bold text-green-600">3</div>
                <div class="text-xs">Defined</div>
            </div>
            <div class="text-center p-3 bg-white rounded-lg border">
                <div class="font-bold text-blue-600">4</div>
                <div class="text-xs">Managed</div>
            </div>
            <div class="text-center p-3 bg-white rounded-lg border">
                <div class="font-bold text-purple-600">5</div>
                <div class="text-xs">Optimized</div>
            </div>
        </div>
    </div>

    <?php foreach ($groupedQuestions as $processCode => $processData): ?>
    <!-- Process Section -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6">
        <div class="flex items-start mb-4">
            <div class="w-12 h-12 bg-gradient-to-br from-[#3291B6] to-[#2a7a99] rounded-xl flex items-center justify-center mr-4">
                <span class="text-white font-bold"><?php echo $processCode; ?></span>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($processData['process_name']); ?></h2>
                <p class="text-gray-600"><?php echo htmlspecialchars($processData['description']); ?></p>
            </div>
        </div>

        <div class="space-y-4">
            <?php foreach ($processData['questions'] as $question): ?>
            <div class="border border-gray-200 rounded-xl p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 bg-[#3291B6]/10 rounded-full flex items-center justify-center text-[#3291B6] font-bold mr-3 mt-1">
                        <?php echo $question['question_order']; ?>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-medium text-gray-800 mb-3"><?php echo htmlspecialchars($question['question_text']); ?></h3>
                        
                        <div class="grid grid-cols-6 gap-2">
                            <?php for ($i = 0; $i <= 5; $i++): ?>
                            <label class="flex flex-col items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" 
                                       name="question_<?php echo $question['id']; ?>" 
                                       value="<?php echo $i; ?>" 
                                       required
                                       class="mb-1">
                                <span class="font-medium <?php echo $i == 0 ? 'text-red-600' : ($i == 1 ? 'text-orange-600' : ($i == 2 ? 'text-yellow-600' : ($i == 3 ? 'text-green-600' : ($i == 4 ? 'text-[#3291B6]' : 'text-purple-600')))); ?>">
                                    <?php echo $i; ?>
                                </span>
                                <?php if ($i == 0): ?>
                                    <span class="text-xs text-gray-500">Non</span>
                                <?php elseif ($i == 1): ?>
                                    <span class="text-xs text-gray-500">Init</span>
                                <?php elseif ($i == 2): ?>
                                    <span class="text-xs text-gray-500">Rep</span>
                                <?php elseif ($i == 3): ?>
                                    <span class="text-xs text-gray-500">Def</span>
                                <?php elseif ($i == 4): ?>
                                    <span class="text-xs text-gray-500">Mng</span>
                                <?php else: ?>
                                    <span class="text-xs text-gray-500">Opt</span>
                                <?php endif; ?>
                            </label>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Submit Button -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex justify-end">
            <button type="submit" class="bg-[#3291B6] hover:bg-[#2a7a99] text-white font-semibold px-6 py-3 rounded-xl transition-colors">
                Simpan & Hitung Maturity Level
            </button>
        </div>
    </div>
</form>
</div>
</main>
</div>
</div>
</div>
</main>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>