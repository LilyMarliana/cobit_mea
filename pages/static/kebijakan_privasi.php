<?php
// Halaman Kebijakan Privasi
?>

<div class="max-w-6xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="index.php?page=dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#3291B6]">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Kebijakan Privasi</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg p-8 mb-8">
        <div class="flex items-center justify-center mb-6">
            <div class="w-16 h-16 bg-gradient-to-br from-[#3291B6] to-[#2a7a99] rounded-2xl flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
        </div>
        <h1 class="text-4xl font-bold text-gray-800 mb-2 text-center">Kebijakan Privasi</h1>
        <p class="text-gray-600 text-center mb-8">Komitmen Perlindungan Data dan Privasi Organisasi</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-[#3291B6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Penggunaan Data</h2>
            </div>
            <p class="text-gray-700 leading-relaxed mb-4">
                Data yang dikumpulkan melalui sistem ini hanya digunakan untuk keperluan assessment dan evaluasi tata kelola teknologi informasi berdasarkan kerangka kerja COBIT 5. Informasi yang dikumpulkan digunakan untuk keperluan konsultansi dan analisis tata kelola TI dalam konteks organisasi.
            </p>
            <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-[#3291B6]">
                <p class="text-gray-700 italic">"Kami berkomitmen untuk menggunakan data Anda secara bertanggung jawab dan transparan."</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Perlindungan Data</h2>
            </div>
            <p class="text-gray-700 leading-relaxed mb-4">
                Kami menjamin bahwa seluruh data yang dikumpulkan tidak akan dibagikan kepada pihak ketiga tanpa persetujuan eksplisit dari pemilik data. Data hanya akan diakses oleh konsultan dan pihak-pihak yang berkepentingan dalam proses assessment, dengan tetap menjaga kerahasiaan dan integritas informasi yang diberikan.
            </p>
            <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                <p class="text-gray-700 italic">"Keamanan dan kerahasiaan data organisasi Anda adalah prioritas utama kami."</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Tujuan Profesional</h2>
            </div>
            <p class="text-gray-700 leading-relaxed">
                Sistem ini dikembangkan sebagai alat konsultansi profesional untuk membantu organisasi dalam mengevaluasi dan meningkatkan praktik tata kelola TI mereka. Pengumpulan dan penggunaan data sepenuhnya didedikasikan untuk keperluan konsultansi dan peningkatan kapabilitas organisasi.
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Hak Pengguna</h2>
            </div>
            <p class="text-gray-700 leading-relaxed">
                Pengguna memiliki hak untuk mengetahui bagaimana data mereka digunakan serta dapat menghubungi administrator sistem jika memiliki pertanyaan atau kekhawatiran terkait perlindungan data dan privasi dalam sistem ini.
            </p>
        </div>
    </div>

    <div class="mt-8 bg-gradient-to-r from-[#3291B6] to-[#2a7a99] rounded-2xl shadow-lg p-8 text-white">
        <div class="flex items-start">
            <svg class="w-8 h-8 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h3 class="text-xl font-bold mb-2">Komitmen Kami</h3>
                <p class="opacity-90">Kami berkomitmen untuk menjaga kerahasiaan, integritas, dan ketersediaan data organisasi Anda. Setiap informasi yang dikumpulkan akan ditangani dengan standar keamanan tertinggi dan hanya digunakan untuk tujuan peningkatan tata kelola TI sesuai dengan praktik terbaik COBIT 5.</p>
            </div>
        </div>
    </div>
</div>

    <!-- Contact/Support Link -->
    <div class="mt-10 bg-white rounded-2xl shadow-lg p-6 border-t-4 border-[#3291B6]">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="mb-4 md:mb-0">
                <h3 class="text-lg font-semibold text-gray-800">Butuh Bantuan?</h3>
                <p class="text-gray-600">Hubungi tim kami jika Anda memiliki pertanyaan lebih lanjut</p>
            </div>
            <a href="index.php?page=contact" class="inline-flex items-center px-6 py-3 bg-[#3291B6] text-white font-medium rounded-lg hover:bg-[#2a7a99] transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Hubungi Kami
            </a>
        </div>
    </div>
</div>