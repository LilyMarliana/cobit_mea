<?php
// Dashboard page for COBIT 5 MEA Assessment System
// Path has been corrected to work with the main index.php structure

// Get user's recent assessments
$stmt = $pdo->prepare("
    SELECT a.*, asum.overall_maturity_level, asum.maturity_status
    FROM assessments a
    LEFT JOIN assessment_summary asum ON a.id = asum.assessment_id
    WHERE a.user_id = ?
    ORDER BY a.created_at DESC
    LIMIT 5
");
$stmt->execute([$_SESSION['user_id']]);
$recentAssessments = $stmt->fetchAll();
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Dashboard COBIT 5 MEA</h1>
    <p class="text-gray-600">Sistem Assessment Maturity Level COBIT 5 Domain Monitor, Evaluate, Assess</p>
</div>

<!-- Welcome Card -->
<div class="bg-gradient-to-r from-[#3291B6] to-[#2a7a99] rounded-2xl p-6 text-white mb-8">
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold mb-2">Selamat Datang, <?php echo htmlspecialchars($currentUser['first_name']); ?>!</h2>
            <p class="text-[#3291B6]/20 mb-4">Sistem ini dirancang untuk mengevaluasi tingkat kematangan praktik IT Anda berdasarkan framework COBIT 5 Domain MEA (Monitor, Evaluate, Assess).</p>
           <a href="index.php?page=assessment"
   class="inline-block bg-white text-[#3291B6] font-semibold px-6 py-3 rounded-xl
          hover:bg-[#3291B6]/10 transition-colors
          relative z-20">
   Mulai Assessment
</a>
        </div>
        <div class="text-5xl opacity-30">📊</div>
    </div>
</div>

<!-- COBIT 5 Overview -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 card-hover transition-all">
        <div class="w-12 h-12 bg-[#3291B6]/10 rounded-xl flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-[#3291B6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <h3 class="font-bold text-lg text-gray-800 mb-2">COBIT 5 Framework</h3>
        <p class="text-gray-600 text-sm">COBIT 5 (Control Objectives for Information and Related Technologies) adalah framework tata kelola dan manajemen TI yang membantu organisasi mencapai tujuan bisnisnya.</p>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 card-hover transition-all">
        <div class="w-12 h-12 bg-[#3291B6]/10 rounded-xl flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-[#3291B6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <h3 class="font-bold text-lg text-gray-800 mb-2">Domain MEA</h3>
        <p class="text-gray-600 text-sm">MEA (Monitor, Evaluate, Assess) adalah domain COBIT 5 yang fokus pada pemantauan, evaluasi, dan penilaian kinerja dan kepatuhan TI terhadap kriteria yang relevan.</p>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 card-hover transition-all">
        <div class="w-12 h-12 bg-[#3291B6]/10 rounded-xl flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-[#3291B6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <h3 class="font-bold text-lg text-gray-800 mb-2">Maturity Level</h3>
        <p class="text-gray-600 text-sm">Tingkat kematangan (0-5) menunjukkan sejauh mana proses TI telah didefinisikan, dikelola, dan dioptimalkan dalam organisasi Anda.</p>
    </div>
</div>

<!-- MEA Processes Overview -->
<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-8">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Proses COBIT 5 Domain MEA</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="border border-gray-200 rounded-xl p-4">
            <h3 class="font-bold text-gray-800 mb-2">MEA01 - Monitor, Evaluate and Assess Performance and Conformance</h3>
            <p class="text-gray-600 text-sm mb-3">Menyediakan jaminan bahwa tujuan, objektif, dan aktivitas terkait TI dipantau, dievaluasi, dan dinilai terhadap kriteria yang relevan.</p>
            <span class="inline-block bg-[#3291B6]/10 text-[#3291B6] text-xs px-2 py-1 rounded">Performance & Conformance</span>
        </div>
        <div class="border border-gray-200 rounded-xl p-4">
            <h3 class="font-bold text-gray-800 mb-2">MEA02 - Monitor, Evaluate and Assess IT Governance System Performance</h3>
            <p class="text-gray-600 text-sm mb-3">Menyediakan jaminan bahwa sistem tata kelola TI berkinerja sesuai yang diperlukan untuk mendukung pencapaian tujuan organisasi.</p>
            <span class="inline-block bg-[#3291B6]/10 text-[#3291B6] text-xs px-2 py-1 rounded">Governance System</span>
        </div>
        <div class="border border-gray-200 rounded-xl p-4">
            <h3 class="font-bold text-gray-800 mb-2">MEA03 - Monitor, Evaluate and Assess Risk</h3>
            <p class="text-gray-600 text-sm mb-3">Menyediakan jaminan bahwa risiko terkait TI dipantau, dievaluasi, dan dinilai untuk mendukung keputusan manajemen risiko dan nafsu risiko.</p>
            <span class="inline-block bg-[#3291B6]/10 text-[#3291B6] text-xs px-2 py-1 rounded">Risk Management</span>
        </div>
    </div>
</div>

<!-- Recent Assessments -->
<?php if (!empty($recentAssessments)): ?>
<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Assessment Terbaru</h2>
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
                <?php foreach ($recentAssessments as $assessment): ?>
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="py-3 px-4"><?php echo htmlspecialchars($assessment['title']); ?></td>
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
                        <a href="index.php?page=results&id=<?php echo $assessment['id']; ?>" class="text-[#3291B6] hover:text-[#2a7a99] text-sm font-medium">Lihat Hasil</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-4 text-right">
        <a href="index.php?page=history" class="text-[#3291B6] hover:text-[#2a7a99] text-sm font-medium">Lihat Semua Assessment →</a>
    </div>
</div>
<?php endif; ?>
</div>
</main>

<script>
    // Toggle sidebar on mobile
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        
        sidebar.classList.toggle('translate-x-0');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
    
    // Toggle profile menu
    function toggleProfileMenu() {
        const menu = document.getElementById('profileMenu');
        menu.classList.toggle('hidden');
    }
    
    // Close profile menu when clicking outside
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('profileMenu');
        const button = document.querySelector('[onclick="toggleProfileMenu()"]');
        
        if (!button.contains(event.target) && !menu.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });
</script>
</body>
</html>
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