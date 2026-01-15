<?php
// File untuk menambahkan chart jaring laba ke laporan assessment
require_once 'config.php';

echo "<h2>Perubahan untuk Menambahkan Chart Jaring Laba</h2>\n";

echo "<p>Untuk menambahkan chart jaring laba (spider chart) ke laporan assessment, kita perlu memperbarui file generate-pdf.php agar menyertakan chart tersebut.</p>\n";

echo "<h3>Perubahan yang Diperlukan:</h3>\n";

echo "<ol>\n";
echo "<li>Memperbarui file generate-pdf.php untuk menyertakan chart jaring laba</li>\n";
echo "<li>Menambahkan library Chart.js ke laporan HTML</li>\n";
echo "<li>Menyusun data untuk chart jaring laba berdasarkan hasil assessment</li>\n";
echo "</ol>\n";

echo "<h3>Contoh kode untuk ditambahkan ke generate-pdf.php:</h3>\n";

$chartCode = '
        <div class="section">
            <div class="section-title">Maturity Level Visualization</div>
            <div style="width: 500px; height: 500px; margin: 0 auto;">
                <canvas id="spiderChart" width="500" height="500"></canvas>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const ctx = document.getElementById("spiderChart").getContext("2d");

                // Data dari hasil assessment
                const data = {
                    labels: [\'MEA01 - Performance & Conformance\', \'MEA02 - Governance System\', \'MEA03 - Risk\'],
                    datasets: [{
                        label: "Maturity Level",
                        data: [' . ($results[0]['average_score'] ?? 0) . ', ' . ($results[1]['average_score'] ?? 0) . ', ' . ($results[2]['average_score'] ?? 0) . '],
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
                        maintainAspectRatio: true,
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
            });
        </script>';

echo "<pre>" . htmlspecialchars($chartCode) . "</pre>\n";

echo "<p>Anda perlu menambahkan kode tersebut ke dalam fungsi generateHtmlReport di file generate-pdf.php.</p>\n";
echo "<p>Chart jaring laba akan menampilkan tingkat kematangan untuk ketiga proses MEA (MEA01, MEA02, MEA03) dalam bentuk visual yang mudah dipahami.</p>\n";

// Tambahkan tombol untuk mengakses halaman download PDF
echo "<h3>Halaman Download PDF:</h3>\n";
echo '<p>Untuk mengakses halaman download PDF, klik tombol di bawah ini:</p>';
echo '<a href="pdf_download_page.php" class="btn btn-primary">Ke Halaman Download PDF</a>';
echo '<br><br>';
echo '<a href="setup_pdf_tables.php" class="btn btn-success">Setup Tabel Database</a>';

echo '<style>
    .btn {
        display: inline-block;
        padding: 6px 12px;
        margin-bottom: 10px;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.42857143;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        cursor: pointer;
        border: 1px solid transparent;
        border-radius: 4px;
        text-decoration: none;
    }
    .btn-primary {
        color: #fff;
        background-color: #337ab7;
        border-color: #2e6da4;
    }
    .btn-primary:hover {
        color: #fff;
        background-color: #286090;
        border-color: #204d74;
    }
    .btn-success {
        color: #fff;
        background-color: #5cb85c;
        border-color: #4cae4c;
    }
    .btn-success:hover {
        color: #fff;
        background-color: #449d44;
        border-color: #398439;
    }
</style>';
?>