# Panduan Implementasi Chart dalam PDF untuk Sistem Assessment COBIT 5 MEA

## Ringkasan

Dokumen ini menjelaskan berbagai pendekatan yang telah diimplementasikan untuk menangani chart interaktif (Chart.js) di web dan menyertakan versi visual dalam laporan PDF. TCPDF/DOMPDF tidak mendukung JavaScript, sehingga kita perlu pendekatan khusus untuk menyertakan visualisasi data dalam PDF.

## Pendekatan yang Telah Diimplementasikan

### 1. Pendekatan Dasar (generate-pdf.php)
File ini menyertakan representasi visual dari chart dalam bentuk tabel dengan progress bar yang dibuat menggunakan HTML/CSS yang kompatibel dengan TCPDF.

**Keunggulan:**
- Mudah diimplementasikan
- Tidak memerlukan proses tambahan
- Kompatibel penuh dengan TCPDF

**Kekurangan:**
- Tidak seinteraktif chart asli
- Tampilan lebih sederhana

### 2. Pendekatan JavaScript ke Gambar (generate-pdf-with-chart.php)
File ini mengimplementasikan sistem di mana chart dari halaman web dikonversi ke gambar PNG menggunakan JavaScript dan dikirim ke server untuk disisipkan ke PDF.

**Keunggulan:**
- Menyertakan chart asli dari halaman web
- Kualitas gambar tinggi
- Cocok untuk presentasi

**Kekurangan:**
- Memerlukan proses tambahan (JavaScript + AJAX)
- Bergantung pada pengiriman file ke server
- Lebih kompleks dalam implementasi

### 3. Pendekatan TCPDF Murni (generate-pdf-with-tcpdf-chart.php)
File ini menggunakan fungsi-fungsi drawing bawaan TCPDF untuk membuat chart langsung dalam proses pembuatan PDF.

**Keunggulan:**
- Tidak memerlukan JavaScript
- Chart dibuat langsung dalam PDF
- Tampilan profesional dan konsisten
- Tidak perlu menyimpan file sementara

**Kekurangan:**
- Memerlukan pengetahuan fungsi drawing TCPDF
- Kurang fleksibel dibandingkan Chart.js

## File-file yang Telah Dibuat

### 1. api/generate-pdf.php
- Versi standar dengan representasi tabel dari chart
- Menggunakan HTML/CSS yang kompatibel TCPDF
- Mudah digunakan dan diintegrasikan

### 2. api/generate-pdf-with-chart.php
- Versi dengan sistem konversi chart ke gambar
- Menggunakan JavaScript untuk mengkonversi canvas
- Mengirim gambar ke server untuk disisipkan ke PDF

### 3. api/generate-pdf-with-tcpdf-chart.php
- Versi dengan chart dibuat langsung menggunakan fungsi TCPDF
- Menggunakan Rect, Line, dan fungsi drawing lainnya
- Tampilan profesional dan konsisten

### 4. assets/js/chart-to-image.js
- Fungsi JavaScript untuk mengkonversi chart ke gambar
- Mengirim data gambar ke server
- Dapat digunakan untuk pendekatan JavaScript ke gambar

### 5. api/save-chart-image.php
- Endpoint untuk menyimpan gambar chart dari JavaScript
- Melakukan validasi dan keamanan
- Mengembalikan nama file untuk digunakan dalam PDF

## Rekomendasi Penggunaan

### Untuk Kebutuhan Dasar
Gunakan `api/generate-pdf.php` - ini adalah pendekatan paling sederhana dan paling stabil.

### Untuk Kualitas Visual Tinggi
Gunakan `api/generate-pdf-with-tcpdf-chart.php` - ini memberikan tampilan paling profesional dan konsisten.

### Untuk Integrasi dengan Chart Interaktif
Gunakan `api/generate-pdf-with-chart.php` bersama dengan `assets/js/chart-to-image.js` - ini memungkinkan Anda menyertakan chart asli dari halaman web.

## Integrasi ke Sistem

Untuk mengintegrasikan ke sistem Anda, cukup ganti tautan download PDF di halaman assessment Anda ke salah satu dari endpoint yang telah dibuat:

```html
<!-- Untuk pendekatan dasar -->
<a href="api/generate-pdf.php?id=123">Download PDF (Dasar)</a>

<!-- Untuk pendekatan JavaScript ke gambar -->
<a href="javascript:generatePdfWithChart(123)">Download PDF (Chart)</a>

<!-- Untuk pendekatan TCPDF murni -->
<a href="api/generate-pdf-with-tcpdf-chart.php?id=123">Download PDF (Chart TCPDF)</a>
```

## Kesimpulan

Ketiga pendekatan yang telah diimplementasikan memberikan solusi lengkap untuk menangani chart dalam PDF. Pilihan pendekatan tergantung pada kebutuhan spesifik Anda:
- Jika Anda menginginkan solusi cepat dan sederhana: gunakan pendekatan dasar
- Jika Anda menginginkan kualitas visual tertinggi: gunakan pendekatan TCPDF murni
- Jika Anda ingin menyertakan chart asli dari halaman web: gunakan pendekatan JavaScript ke gambar