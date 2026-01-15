// File: assets/js/chart-to-image.js
// Fungsi untuk mengkonversi chart canvas menjadi gambar dan menyimpannya

function convertChartToImage(chartId, callback) {
    // Ambil elemen canvas dari chart
    const canvas = document.getElementById(chartId);
    if (!canvas) {
        console.error('Canvas dengan ID ' + chartId + ' tidak ditemukan');
        return;
    }

    // Konversi canvas ke data URL (format base64)
    const dataUrl = canvas.toDataURL('image/png');

    // Panggil callback dengan data gambar
    if (callback && typeof callback === 'function') {
        callback(dataUrl);
    }

    return dataUrl;
}

function saveChartImage(chartId, assessmentId, callback) {
    const dataUrl = convertChartToImage(chartId);

    // Kirim data gambar ke server
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
            console.log('Gambar chart berhasil disimpan:', data.filename);
            if (callback && typeof callback === 'function') {
                callback(data.filename);
            }
        } else {
            console.error('Gagal menyimpan gambar chart:', data.message);
        }
    })
    .catch(error => {
        console.error('Error saat menyimpan gambar chart:', error);
    });
}

// Fungsi untuk menghasilkan PDF dengan chart
function generatePdfWithChart(assessmentId) {
    // Dapatkan semua chart di halaman
    const charts = document.querySelectorAll('canvas');
    
    // Array untuk menyimpan nama file gambar
    const imageFiles = [];
    
    // Konversi setiap chart ke gambar
    charts.forEach((chart, index) => {
        const chartId = chart.id || 'chart-' + index;
        
        // Simpan gambar chart
        saveChartImage(chartId, assessmentId, function(filename) {
            imageFiles.push(filename);
            
            // Jika semua chart telah diproses, buat PDF
            if (imageFiles.length === charts.length) {
                // Redirect ke endpoint PDF dengan parameter gambar
                window.location.href = BASE_URL + 'api/generate-pdf-with-chart.php?id=' + assessmentId + '&chart_images=' + imageFiles.join(',');
            }
        });
    });
}

// Fungsi untuk menampilkan chart di PDF (hanya untuk referensi)
function displayChartInPdf(pdf, chartImage, x, y, width, height) {
    // Dalam implementasi sebenarnya, Anda akan menggunakan:
    // $pdf->Image($chartImage, $x, $y, $width, $height);
    // Fungsi ini hanya untuk dokumentasi
    console.log('Menyisipkan gambar chart ke PDF pada posisi:', x, y, 'dengan ukuran:', width, height);
}