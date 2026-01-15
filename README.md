📊 Sistem Assessment Tata Kelola TI
COBIT 5 – Domain MEA (Monitor, Evaluate, and Assess)
📋 Deskripsi Proyek

Proyek ini merupakan sistem berbasis web yang dikembangkan untuk mendukung assessment tata kelola Teknologi Informasi berdasarkan framework COBIT 5, khususnya pada domain MEA (Monitor, Evaluate, and Assess).

Sistem ini dirancang sebagai media pembelajaran dan implementasi praktis untuk membantu konsultan dalam:

Melakukan penilaian (assessment) tingkat kematangan tata kelola TI

Mengelola data assessment secara terstruktur

Menyajikan hasil evaluasi dalam bentuk laporan dan visualisasi

Menghasilkan laporan resmi dalam format PDF

Proyek ini dikembangkan sebagai bagian dari tugas perkuliahan dan dapat digunakan sebagai studi kasus penerapan COBIT 5 pada lingkungan organisasi.

🎯 Tujuan Pengembangan

Mengimplementasikan konsep COBIT 5 domain MEA ke dalam sistem informasi

Memfasilitasi proses monitoring, evaluasi, dan assessment tata kelola TI

Menyediakan laporan hasil assessment yang informatif dan profesional

Mendukung proses analisis dan dokumentasi untuk kebutuhan akademik

📄 Fitur Utama

🔐 Sistem Autentikasi & Otorisasi

🗂️ Manajemen Data Assessment COBIT

📊 Visualisasi hasil assessment (grafik radar/spider chart)

📑 PDF Generator menggunakan TCPDF

🧾 Ringkasan hasil assessment dalam laporan PDF

🗄️ Integrasi database untuk penyimpanan hasil evaluasi

📄 Fitur PDF Generator

Sistem ini dilengkapi dengan fitur pembuatan laporan assessment COBIT dalam format PDF menggunakan library TCPDF, yang memungkinkan pengguna untuk:

Mengunduh laporan hasil assessment COBIT 5 (domain MEA)

Menghasilkan grafik dan ringkasan hasil evaluasi dalam PDF

Menyimpan laporan dalam format formal untuk dokumentasi atau pelaporan

Cara Menggunakan Fitur PDF

Melalui Antarmuka Web:

Akses halaman pdf_download_page.php

Klik tombol “Unduh Laporan PDF”

Alternatif melalui halaman add_spider_chart.php

Akses Langsung File:

generate_pdf.php → Laporan assessment lengkap

generate_chart_pdf.php → Laporan ringkasan dengan grafik

File Terkait:

vendor/tcpdf/ – Library TCPDF

autoload.php – Autoloader TCPDF

setup_pdf_tables.php – Setup tabel database PDF

PANDUAN_PDF.md – Dokumentasi fitur PDF

🗄️ Persiapan Database

Sebelum menggunakan fitur PDF, jalankan:

setup_pdf_tables.php


Tabel yang digunakan:

assessments – Data assessment

assessment_answers – Jawaban assessment

questions – Pertanyaan COBIT MEA

🚀 Instalasi & Konfigurasi
Prasyarat

PHP 7.4 atau lebih tinggi

MySQL / MariaDB

Web Server (Apache / Nginx / Laragon)

Langkah Instalasi

Clone repository:

git clone https://github.com/LilyMarliana/cobit_mea.git


Konfigurasi database di config.php:

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cobit_mea');


Atur BASE_URL sesuai lokasi project:

define('BASE_URL', 'http://localhost/cobit_mea/');


Jalankan aplikasi melalui browser
