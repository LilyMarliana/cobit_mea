# Struktur-AI - Admin Panel & Game Management System

## 📋 Deskripsi Proyek

Template repository untuk sistem admin panel dengan manajemen game. Proyek ini menyediakan struktur dasar untuk sistem manajemen pengguna dan game dengan otentikasi serta sistem otorisasi.

## 📄 Fitur PDF Generator

Proyek ini dilengkapi dengan fitur PDF generator menggunakan library TCPDF yang memungkinkan pengguna untuk:
- Mengunduh laporan assessment COBIT dalam format PDF
- Menghasilkan grafik dan ringkasan hasil assessment dalam bentuk PDF
- Mencetak dan menyimpan laporan dalam format profesional

### Cara Menggunakan Fitur PDF

1. **Melalui Antarmuka Web**:
   - Kunjungi halaman `pdf_download_page.php` untuk akses antarmuka yang ramah pengguna
   - Klik tombol "Unduh Laporan PDF" untuk mendapatkan laporan lengkap
   - Klik tombol "Ke Halaman Download PDF" dari halaman `add_spider_chart.php`

2. **Langsung Mengakses File**:
   - Akses `generate_pdf.php` untuk laporan PDF lengkap
   - Akses `generate_chart_pdf.php` untuk laporan dengan grafik ringkasan

3. **File-file Terkait**:
   - `vendor/tcpdf/` - Library TCPDF
   - `autoload.php` - Autoloader untuk TCPDF
   - `generate_pdf.php` - Generator laporan PDF lengkap
   - `generate_chart_pdf.php` - Generator laporan dengan grafik
   - `pdf_download_page.php` - Antarmuka download PDF
   - `setup_pdf_tables.php` - Script untuk membuat tabel-tabel database yang diperlukan
   - `PANDUAN_PDF.md` - Dokumentasi lengkap fitur PDF

### Persiapan Database

Sebelum menggunakan fitur PDF, pastikan untuk menjalankan file `setup_pdf_tables.php` untuk membuat tabel-tabel yang diperlukan dalam database. Fitur ini memerlukan tabel-tabel berikut:
- `assessments` - Menyimpan data assessment pengguna
- `assessment_answers` - Menyimpan jawaban dari assessment
- `questions` - Menyimpan pertanyaan-pertanyaan assessment

## 🚀 Instalasi

### Prasyarat
- PHP 7.4 atau lebih tinggi
- MySQL/MariaDB
- Web server (Apache/Nginx)

### Langkah-langkah Instalasi

1. **Clone repository atau gunakan sebagai template**
   ```
   git clone https://github.com/username/struktur-ai.git
   # Atau gunakan tombol "Use this template" di GitHub
   ```

2. **Konfigurasi Database**
   - Buat database baru di MySQL/MariaDB
   - Import file `database/schema.sql` untuk skema utama
   - Import file `database/game_schema.sql` untuk skema game
   - **Catatan**: File skema mungkin sudah termasuk default admin user:

     -- Insert default admin user
     -- Username: admin
     -- Password: admin123

3. **Konfigurasi Aplikasi**
   - Buka file `config.php`
   - Sesuaikan konfigurasi database:
     ```php
     define('DB_HOST', 'localhost');        // Host database
     define('DB_USER', 'root');             // Username database
     define('DB_PASS', '');                 // Password database
     define('DB_NAME', 'admin_panel_db');   // Nama database
     ```
   - Atur BASE_URL sesuai dengan lokasi proyek Anda:
     ```php
     define('BASE_URL', 'http://localhost/latihan/struktur-ai/');
     ```

4. **Struktur Folder**
   - Buat folder `assets/uploads/avatars/` dan pastikan web server memiliki izin untuk menulis di folder ini

5. **Akses Aplikasi**
   - Buka browser dan akses URL proyek
   - Gunakan kredensial default berikut untuk login pertama:
     - Username: `admin`
     - Password: `admin123`
   - Sebaiknya ubah password default setelah login pertama untuk alasan keamanan

## 📁 Struktur Folder

```
struktur-ai/
│
├── index.php                      # Entry point aplikasi
├── config.php                     # Konfigurasi database & konstanta
├── README.md                      # Dokumentasi proyek
│
├── includes/
│   ├── header.php                 # Header HTML & Navigation
│   ├── footer.php                 # Footer HTML
│   ├── sidebar.php                # Sidebar navigation
│   ├── functions.php              # Fungsi-fungsi helper
│   ├── db_connect.php             # Koneksi database
│   └── auth_check.php             # Cek authentication & authorization
│
├── pages/
│   ├── dashboard/
│   │   └── index.php              # Dashboard
│   │
│   ├── profile/
│   │   └── index.php              # Profile
│   │
│   ├── users/
│   │   ├── index.php              # List users
│   │   ├── create.php             # Create user
│   │   ├── edit.php               # Edit user
│   │   └── delete.php             # Delete user
│   │
│   ├── settings/
│   │   └── index.php              # Settings
│   │
│   ├── auth/
│   │   ├── login.php              # Login
│   │   └── logout.php             # Logout
│   │
│   └── errors/
│       └── 403.php                # Access Denied
│
├── assets/
│   ├── css/
│   ├── js/
│   ├── images/
│   └── uploads/
│       └── avatars/
│
└── database/
    ├── schema.sql                 # Skema database utama
    └── game_schema.sql            # Skema database untuk game
```