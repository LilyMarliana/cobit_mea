<?php
// Results page for COBIT 5 MEA Assessment
// Path has been corrected to work with the main index.php structure

$assessmentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$assessmentId) {
    setAlert('error', 'ID assessment tidak valid');
    redirect('index.php?page=dashboard');
    exit;
}

// Get assessment details
$stmt = $pdo->prepare("
    SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) as user_name
    FROM assessments a
    JOIN users u ON a.user_id = u.id
    WHERE a.id = ? AND a.user_id = ?
");
$stmt->execute([$assessmentId, $_SESSION['user_id']]);
$assessment = $stmt->fetch();

if (!$assessment) {
    setAlert('error', 'Assessment tidak ditemukan');
    redirect('index.php?page=dashboard');
    exit;
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
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Hasil Assessment COBIT 5 MEA</h1>
    <p class="text-gray-600">Detail hasil penilaian maturity level untuk assessment "<?php echo htmlspecialchars($assessment['title']); ?>"</p>
</div>

<!-- Assessment Summary -->
<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="text-center p-4 bg-[#3291B6]/10 rounded-xl">
            <div class="text-3xl font-bold text-[#3291B6]"><?php echo $summary ? number_format($summary['overall_maturity_level'], 2) : '0.00'; ?></div>
            <div class="text-sm text-gray-600">Overall Maturity</div>
        </div>
        <div class="text-center p-4 bg-green-50 rounded-xl">
            <div class="text-xl font-bold text-green-600"><?php echo $summary ? $summary['maturity_status'] : 'Belum dihitung'; ?></div>
            <div class="text-sm text-gray-600">Status</div>
        </div>
        <div class="text-center p-4 bg-purple-50 rounded-xl">
            <div class="text-xl font-bold text-purple-600"><?php echo formatDate($assessment['created_at']); ?></div>
            <div class="text-sm text-gray-600">Tanggal</div>
        </div>
        <div class="text-center p-4 bg-yellow-50 rounded-xl">
            <div class="text-xl font-bold text-yellow-600"><?php echo $assessment['title']; ?></div>
            <div class="text-sm text-gray-600">Judul</div>
        </div>
    </div>
</div>

<!-- Maturity Level Chart -->
<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Tingkat Maturity per Proses</h2>
    <div class="space-y-6">
        <?php foreach ($results as $result): ?>
        <div>
            <div class="flex justify-between mb-2">
                <span class="font-medium text-gray-700"><?php echo htmlspecialchars($result['process_code']); ?> - <?php echo htmlspecialchars($result['process_code'] == 'MEA01' ? 'Monitor, Evaluate and Assess Performance and Conformance' : ($result['process_code'] == 'MEA02' ? 'Monitor, Evaluate and Assess IT Governance System Performance' : 'Monitor, Evaluate and Assess Risk')); ?></span>
                <span class="font-bold text-gray-800"><?php echo number_format($result['average_score'], 2); ?>/5.0</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="bg-gradient-to-r from-[#3291B6] to-[#2a7a99] h-4 rounded-full" style="width: <?php echo ($result['average_score'] / 5) * 100; ?>%"></div>
            </div>
            <div class="mt-1 text-sm text-gray-600">
                <?php
                if ($result['average_score'] >= 4.5) {
                    echo 'Optimized - Proses dioptimalkan secara berkelanjutan';
                } elseif ($result['average_score'] >= 3.5) {
                    echo 'Managed - Proses diukur dan dikendalikan';
                } elseif ($result['average_score'] >= 2.5) {
                    echo 'Defined - Proses didokumentasikan dan distandarisasi';
                } elseif ($result['average_score'] >= 1.5) {
                    echo 'Repeatable - Proses direncanakan dan dikelola';
                } elseif ($result['average_score'] >= 0.5) {
                    echo 'Initial - Proses bersifat ad hoc dan kacau';
                } else {
                    echo 'Non-Existent - Proses tidak didefinisikan atau dilakukan';
                }
                ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Spider Chart Visualization -->
<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Visualisasi Tingkat Maturity (Spider Chart)</h2>
    <div class="flex justify-center">
        <div style="width: 500px; height: 500px; max-width: 100%;">
            <canvas id="spiderChart" width="500" height="500"></canvas>
        </div>
    </div>
</div>

<!-- Overall Maturity Status -->
<div class="bg-gradient-to-r from-[#3291B6] to-[#2a7a99] rounded-2xl p-6 text-white mb-6">
    <div class="flex items-center">
        <div class="text-5xl mr-4">📊</div>
        <div>
            <h2 class="text-2xl font-bold mb-2">Status Maturity Keseluruhan: <?php echo $summary ? $summary['maturity_status'] : 'Belum dihitung'; ?></h2>
            <p class="text-[#3291B6]/20">
                <?php
                if ($summary) {
                    switch ($summary['maturity_status']) {
                        case 'Optimized':
                            echo 'Organisasi Anda memiliki praktik TI yang sangat matang, dengan proses yang dioptimalkan secara berkelanjutan dan memberikan nilai tambah strategis.';
                            break;
                        case 'Managed':
                            echo 'Organisasi Anda memiliki praktik TI yang terukur dan terkendali, dengan metrik kinerja yang digunakan untuk pengambilan keputusan.';
                            break;
                        case 'Defined':
                            echo 'Organisasi Anda memiliki praktik TI yang terdokumentasi dan distandarisasi, dengan proses yang konsisten di seluruh organisasi.';
                            break;
                        case 'Repeatable':
                            echo 'Organisasi Anda memiliki praktik TI yang direncanakan dan dikelola, dengan proses dasar yang dapat diulang.';
                            break;
                        case 'Initial':
                            echo 'Organisasi Anda memiliki praktik TI yang bersifat ad hoc dan kacau, dengan kurangnya struktur dan dokumentasi.';
                            break;
                        default:
                            echo 'Tingkat kematangan organisasi Anda dalam praktik TI berdasarkan domain MEA.';
                    }
                }
                ?>
            </p>
        </div>
    </div>
</div>

<!-- Detailed Results by Process -->
<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Detail Hasil per Proses</h2>
    <?php foreach ($groupedResponses as $processCode => $processData): ?>
    <div class="mb-8">
        <h3 class="text-lg font-bold text-gray-800 mb-4"><?php echo htmlspecialchars($processData['process_name']); ?></h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="py-3 px-4 text-left">No</th>
                        <th class="py-3 px-4 text-left">Pertanyaan</th>
                        <th class="py-3 px-4 text-left">Jawaban</th>
                        <th class="py-3 px-4 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($processData['responses'] as $index => $response): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4"><?php echo $index + 1; ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($response['question_text']); ?></td>
                        <td class="py-3 px-4"><?php echo $response['response_value']; ?>/5</td>
                        <td class="py-3 px-4">
                            <?php
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
                            echo $status;
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Recommendations -->
<?php if ($summary && !empty($summary['recommendations'])): ?>
<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Rekomendasi</h2>
    <div class="prose max-w-none">
        <?php echo nl2br(htmlspecialchars($summary['recommendations'])); ?>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Tunggu hingga DOM sepenuhnya dimuat
    document.addEventListener("DOMContentLoaded", function() {
        // Pastikan elemen canvas sudah ada sebelum membuat chart
        const spiderChartCanvas = document.getElementById("spiderChart");
        if (spiderChartCanvas) {
            const ctx = spiderChartCanvas.getContext("2d");

            // Data dari hasil assessment
            const processScores = {};
            <?php foreach ($results as $result): ?>
            processScores['<?php echo $result['process_code']; ?>'] = <?php echo $result['average_score']; ?>;
            <?php endforeach; ?>

            const scores = {
                MEA01: processScores['MEA01'] || 0,
                MEA02: processScores['MEA02'] || 0,
                MEA03: processScores['MEA03'] || 0
            };

            // Pastikan kita memiliki data sebelum membuat chart
            const hasData = Object.keys(processScores).length > 0;

            if (hasData) {
                const data = {
                    labels: [
                        "MEA01 - Performance & Conformance",
                        "MEA02 - Governance System",
                        "MEA03 - Risk"
                    ],
                    datasets: [{
                        label: "Maturity Level",
                        data: [scores.MEA01, scores.MEA02, scores.MEA03],
                        backgroundColor: "rgba(59, 130, 246, 0.2)",
                        borderColor: "rgba(59, 130, 246, 1)",
                        pointBackgroundColor: "rgba(59, 130, 246, 1)",
                        pointBorderColor: "#fff",
                        pointHoverBackgroundColor: "#fff",
                        pointHoverBorderColor: "rgba(59, 130, 246, 1)",
                        fill: true
                    }]
                };

                // Konfigurasi chart
                const config = {
                    type: "radar",
                    data: data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                beginAtZero: true,
                                max: 5,
                                ticks: {
                                    stepSize: 1,
                                    callback: function(value) {
                                        if (value === 0 || value === 1 || value === 2 || value === 3 || value === 4 || value === 5) {
                                            return value;
                                        }
                                        return null;
                                    }
                                },
                                pointLabels: {
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: "top",
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.raw + "/5.0";
                                    }
                                }
                            }
                        }
                    }
                };

                // Buat chart
                const spiderChart = new Chart(ctx, config);
            } else {
                console.log("Tidak ada data untuk membuat chart");
                spiderChartCanvas.parentNode.innerHTML = "<p class='text-center text-gray-500'>Tidak ada data untuk ditampilkan dalam chart.</p>";
            }
        } else {
            console.log("Elemen canvas dengan ID 'spiderChart' tidak ditemukan");
        }
    });

    // Sebagai fallback, coba inisialisasi chart setelah window sepenuhnya dimuat
    window.addEventListener("load", function() {
        if (!window.spiderChart && document.getElementById("spiderChart")) {
            // Jika chart belum dibuat, buat sekarang
            const ctx = document.getElementById("spiderChart").getContext("2d");
            // Kode untuk membuat chart akan dijalankan jika belum dibuat sebelumnya
        }
    });
</script>

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
</div>