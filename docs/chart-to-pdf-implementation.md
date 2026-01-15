# Implementasi Chart ke PDF dalam Sistem Assessment COBIT 5 MEA

## Penjelasan Umum

Dokumen ini menjelaskan implementasi sistem untuk menangani chart interaktif (Chart.js) di web dan menyertakan versi gambar statis dalam laporan PDF. TCPDF/DOMPDF tidak mendukung JavaScript, sehingga kita perlu mengkonversi chart ke gambar statis sebelum menyertakannya dalam PDF.

## Arsitektur Solusi

### 1. Konversi Canvas Chart ke Gambar PNG
- Menggunakan metode `toDataURL()` pada elemen canvas untuk mendapatkan data gambar
- Format yang digunakan adalah PNG untuk kualitas terbaik
- Data dikirim dalam format base64

### 2. Pengiriman Data ke Server
- Menggunakan AJAX untuk mengirim data gambar ke server
- Server menyimpan gambar sementara di direktori khusus
- Gambar kemudian digunakan dalam proses pembuatan PDF

### 3. Penyisipan Gambar ke PDF
- Menggunakan metode `Image()` dari TCPDF untuk menyisipkan gambar
- Gambar disisipkan dalam layout A4 yang rapi dan profesional

## File-file Implementasi

### 1. assets/js/chart-to-image.js
File JavaScript yang menangani konversi chart ke gambar dan pengiriman ke server:

```javascript
// Mengkonversi canvas chart ke data URL
function convertChartToImage(chartId, callback) {
    const canvas = document.getElementById(chartId);
    const dataUrl = canvas.toDataURL('image/png');
    if (callback && typeof callback === 'function') {
        callback(dataUrl);
    }
    return dataUrl;
}

// Mengirim data gambar ke server
function saveChartImage(chartId, assessmentId, callback) {
    const dataUrl = convertChartToImage(chartId);

    fetch(BASE_URL + 'api/save-chart-image.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            image_data: dataUrl,
            assessment_id: assessmentId,
            chart_id: chartId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (callback && typeof callback === 'function') {
                callback(data.filename);
            }
        }
    })
    .catch(error => {
        console.error('Error saat menyimpan gambar chart:', error);
    });
}
```

### 2. api/save-chart-image.php
Endpoint PHP untuk menerima dan menyimpan gambar chart:

```php
// Extract image data (remove data:image/png;base64, prefix)
$imageData = str_replace('data:image/png;base64,', '', $imageData);
$imageData = str_replace(' ', '+', $imageData);

// Decode base64 image data
$imageBinary = base64_decode($imageData);

// Validate image
if (!imagecreatefromstring($imageBinary)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Invalid image format']);
    exit();
}

// Save image to file
if (file_put_contents($filepath, $imageBinary)) {
    echo json_encode([
        'success' => true,
        'filename' => $filename,
        'filepath' => $filepath,
        'message' => 'Chart image saved successfully'
    ]);
}
```

### 3. api/generate-pdf-with-chart.php
File utama yang menghasilkan PDF dengan menyertakan gambar chart:

```php
// Get chart images if provided
$chartImages = [];
if (isset($_GET['chart_images']) && !empty($_GET['chart_images'])) {
    $chartImageFiles = explode(',', $_GET['chart_images']);
    foreach ($chartImageFiles as $imageFile) {
        $imagePath = __DIR__ . '/../assets/charts/' . trim($imageFile);
        if (file_exists($imagePath)) {
            $chartImages[] = $imagePath;
        }
    }
}

// Dalam fungsi HTML generation
if (!empty($chartImages)) {
    foreach ($chartImages as $chartImage) {
        $html .= '
        <div class="chart-container">
            <img src="' . $chartImage . '" class="chart-image" alt="Visualisasi Maturity Level" />
        </div>';
    }
}
```

## Alur Implementasi

1. Pengguna menekan tombol "Generate PDF with Charts"
2. JavaScript mengambil semua canvas chart di halaman
3. Setiap chart dikonversi ke data URL (base64)
4. Data dikirim ke server melalui AJAX
5. Server menyimpan gambar dan mengembalikan nama file
6. Setelah semua chart diproses, browser diarahkan ke endpoint PDF
7. Endpoint PDF mengambil gambar yang telah disimpan dan menyisipkannya ke dalam dokumen
8. PDF dihasilkan dan diunduh oleh pengguna

## Keunggulan Solusi

- **Kompatibilitas**: Chart interaktif tetap berfungsi di web, gambar statis digunakan di PDF
- **Kualitas**: Gambar disimpan dalam format PNG untuk kualitas terbaik
- **Keamanan**: Validasi input dan otorisasi pengguna diterapkan
- **Efisiensi**: Gambar hanya disimpan sementara dan dapat dihapus setelah PDF dihasilkan
- **Profesional**: Layout PDF tetap rapi dan sesuai standar akademik

## Penggunaan

Untuk menggunakan sistem ini, cukup panggil fungsi `generatePdfWithChart(assessmentId)` dari halaman yang menampilkan chart. Fungsi ini akan:

1. Mengkonversi semua chart di halaman ke gambar
2. Mengirim gambar ke server
3. Menghasilkan PDF dengan menyertakan gambar-gambar tersebut

Hasil akhir adalah PDF profesional yang layak untuk laporan perkuliahan dan mudah dipresentasikan.