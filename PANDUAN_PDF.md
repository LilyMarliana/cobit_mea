# Panduan Penggunaan Fitur PDF Generator

Fitur ini memungkinkan pengguna untuk mengunduh laporan assessment COBIT dalam format PDF.

## File-file yang Telah Ditambahkan

1. `vendor/tcpdf/` - Library TCPDF untuk membuat PDF
2. `autoload.php` - File autoloader untuk TCPDF
3. `generate_pdf.php` - File untuk menghasilkan laporan PDF lengkap
4. `generate_chart_pdf.php` - File untuk menghasilkan laporan PDF dengan grafik
5. `pdf_download_page.php` - Halaman antarmuka untuk download PDF
6. Pembaruan pada `add_spider_chart.php` - Menambahkan tombol akses ke halaman PDF

## Cara Menggunakan

### 1. Generate Laporan PDF Lengkap
Akses file `generate_pdf.php` untuk menghasilkan laporan PDF lengkap dengan semua jawaban assessment.

### 2. Generate Laporan PDF dengan Grafik
Akses file `generate_chart_pdf.php` untuk menghasilkan laporan PDF dengan grafik ringkasan per domain.

### 3. Melalui Antarmuka Web
Kunjungi `pdf_download_page.php` untuk antarmuka yang lebih ramah pengguna dengan tombol download.

## Struktur Data yang Ditampilkan

- Informasi pengguna (nama, tanggal assessment)
- Semua jawaban assessment berdasarkan domain COBIT
- Ringkasan dan analisis hasil
- Grafik perbandingan nilai per domain (untuk versi chart)

## Catatan Teknis

- TCPDF digunakan sebagai engine pembuat PDF
- File PDF akan otomatis diunduh saat diakses
- Format PDF siap cetak dan profesional
- Data diambil langsung dari database assessment

## Kustomisasi

Anda dapat mengubah tampilan PDF dengan mengedit file-file PHP yang menghasilkan PDF:
- Gaya CSS dalam tag `<style>`
- Struktur HTML dalam variabel `$html`
- Parameter TCPDF seperti margin, font, ukuran kertas

## Persiapan Database

Sebelum menggunakan fitur PDF, pastikan tabel-tabel berikut telah dibuat di database:

1. Jalankan file `setup_pdf_tables.php` untuk membuat tabel-tabel yang diperlukan:
   - `assessments` - Menyimpan data assessment pengguna
   - `assessment_answers` - Menyimpan jawaban dari assessment
   - `questions` - Menyimpan pertanyaan-pertanyaan assessment

2. Alternatifnya, Anda bisa menjalankan perintah SQL berikut secara manual di database Anda:

```sql
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT NOT NULL,
    domain VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS assessments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS assessment_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assessment_id INT NOT NULL,
    question_id INT NOT NULL,
    answer VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id),
    FOREIGN KEY (question_id) REFERENCES questions(id)
);
```