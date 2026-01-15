<?php
// History page for COBIT 5 MEA Assessments
// Path has been corrected to work with the main index.php structure

// Get all user's assessments
$stmt = $pdo->prepare("
    SELECT a.*, asum.overall_maturity_level, asum.maturity_status
    FROM assessments a
    LEFT JOIN assessment_summary asum ON a.id = asum.assessment_id
    WHERE a.user_id = ?
    ORDER BY a.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$assessments = $stmt->fetchAll();
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Riwayat Assessment</h1>
    <p class="text-gray-600">Daftar semua assessment COBIT 5 MEA yang telah Anda lakukan</p>
</div>

<?php if (empty($assessments)): ?>
<div class="bg-white rounded-2xl p-12 shadow-sm border border-gray-100 text-center">
    <div class="text-5xl text-gray-300 mb-4">📊</div>
    <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Assessment</h3>
    <p class="text-gray-600 mb-6">Anda belum melakukan assessment COBIT 5 MEA. Mulai assessment pertama Anda sekarang.</p>
    <a href="index.php?page=assessment" class="bg-[#3291B6] hover:bg-[#2a7a99] text-white font-semibold px-6 py-3 rounded-xl transition-colors">
        Mulai Assessment
    </a>
</div>
<?php else: ?>
<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Judul</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Tanggal</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Maturity Level</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assessments as $assessment): ?>
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="py-3 px-4">
                        <div class="font-medium text-gray-800"><?php echo htmlspecialchars($assessment['title']); ?></div>
                        <div class="text-sm text-gray-600"><?php echo htmlspecialchars($assessment['description']); ?></div>
                    </td>
                    <td class="py-3 px-4"><?php echo formatDate($assessment['created_at']); ?></td>
                    <td class="py-3 px-4">
                        <span class="inline-block bg-<?php echo $assessment['status'] === 'completed' ? 'green' : 'yellow'; ?>-100 text-<?php echo $assessment['status'] === 'completed' ? 'green' : 'yellow'; ?>-800 text-xs px-2 py-1 rounded">
                            <?php echo $assessment['status'] === 'completed' ? 'Selesai' : 'Dalam Proses'; ?>
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <?php if ($assessment['overall_maturity_level']): ?>
                            <span class="font-semibold"><?php echo number_format($assessment['overall_maturity_level'], 2); ?>/5.0</span>
                            <div class="text-xs text-gray-500"><?php echo $assessment['maturity_status']; ?></div>
                        <?php else: ?>
                            <span class="text-gray-400">Belum dihitung</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex space-x-2">
                            <a href="index.php?page=results&id=<?php echo $assessment['id']; ?>" class="text-[#3291B6] hover:text-[#2a7a99] text-sm font-medium">Lihat Hasil</a>
                            <a href="api/generate-pdf.php?id=<?php echo $assessment['id']; ?>" target="_blank" class="text-green-600 hover:text-green-800 text-sm font-medium">PDF</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
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