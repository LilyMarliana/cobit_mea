<?php
require_once('config.php');

// Mulai sesi
session_start();

// Periksa apakah pengguna login
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Laporan PDF - COBIT Assessment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Download Laporan Assessment COBIT</h4>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Anda dapat mengunduh laporan hasil assessment COBIT dalam format PDF dengan menekan tombol di bawah ini:</p>
                        
                        <div class="d-grid gap-2">
                            <a href="generate_pdf.php" class="btn btn-success btn-lg">
                                <i class="fas fa-file-pdf"></i> Unduh Laporan PDF
                            </a>
                            
                            <a href="javascript:history.back()" class="btn btn-secondary">
                                Kembali ke Halaman Sebelumnya
                            </a>
                        </div>
                        
                        <hr>

                        <h5>Fitur Laporan PDF:</h5>
                        <ul>
                            <li>Data assessment lengkap dari pengguna</li>
                            <li>Hasil jawaban pertanyaan berdasarkan domain COBIT</li>
                            <li>Format profesional siap cetak</li>
                            <li>File PDF dapat disimpan dan dibagikan</li>
                        </ul>

                        <div class="alert alert-info">
                            <strong>Catatan:</strong> Pastikan tabel-tabel database telah dibuat terlebih dahulu.
                            Jika belum, jalankan <code>setup_pdf_tables.php</code> untuk membuat tabel-tabel yang diperlukan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</body>
</html>