<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Desa Cibatu</title>
    <meta name="description" content="Admin Panel Sistem Informasi Desa Cibatu, Purwakarta - Dashboard administrasi digital">
    <meta name="keywords" content="admin panel, desa cibatu, purwakarta, sistem informasi desa, dashboard">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo-desa-cibatu.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }


        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .fade-in {
            animation: fadeIn 1s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        /* Image loading states */
        img {
            transition: opacity 0.3s ease;
        }

        img[loading="lazy"] {
            opacity: 0;
        }

        img[loading="lazy"].loaded {
            opacity: 1;
        }

        /* Loading placeholder */
        .image-placeholder {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <img src="{{ asset('logo-desa-cibatu.png') }}" alt="Logo Desa Cibatu" class="h-8 w-8 sm:h-10 sm:w-10 rounded-lg" loading="lazy" decoding="async">
                    <div>
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900">Desa Cibatu</h1>
                        <p class="text-xs sm:text-sm text-gray-600 hidden sm:block">Purwakarta, Jawa Barat</p>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#features" class="text-gray-700 hover:text-green-600 transition-colors">Fitur</a>
                    <a href="#stats" class="text-gray-700 hover:text-green-600 transition-colors">Dashboard</a>
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-green-600 transition-colors">Login</a>
                </div>
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-gray-500 hover:text-green-600 focus:outline-none focus:text-green-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t border-gray-200">
                    <a href="#features" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-md transition-colors">Fitur</a>
                    <a href="#stats" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-md transition-colors">Dashboard</a>
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-md transition-colors">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative h-screen overflow-hidden">
        <!-- Background Image with Filter -->
        <div class="absolute inset-0">
            <img src="{{ asset('foto-sawah-1.webp') }}"
                 alt="Pemandangan Desa Cibatu"
                 class="w-full h-full object-cover"
                 loading="lazy"
                 decoding="async">
            <div class="absolute inset-0 bg-gradient-to-r from-green-900/80 via-green-800/70 to-green-900/80"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 h-full flex items-center">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                    <div class="fade-in text-white text-center lg:text-left">
                        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-4 sm:mb-6 leading-tight">
                            Admin Panel
                            <span class="block text-yellow-300">Desa Cibatu</span>
                        </h1>
                        <p class="text-lg sm:text-xl text-green-100 mb-6 sm:mb-8 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                            Dashboard administrasi digital untuk mengelola data penduduk, surat, pengaduan, dan layanan desa.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 max-w-md mx-auto lg:mx-0">
                            <a href="{{ route('login') }}"
                               class="bg-white text-green-600 px-6 sm:px-8 py-3 sm:py-4 rounded-xl font-semibold text-base sm:text-lg hover:bg-green-50 transition-all duration-300 shadow-lg hover:shadow-xl text-center">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Masuk Admin
                            </a>
                            <a href="/web-desa"
                               class="border-2 border-white text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl font-semibold text-base sm:text-lg hover:bg-white hover:text-green-600 transition-all duration-300 text-center">
                                <i class="fas fa-globe mr-2"></i>
                                Web Desa
                            </a>
                        </div>
                    </div>
                    <div class="fade-in mt-8 lg:mt-0">
                        <div class="relative">
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 sm:p-8 shadow-2xl border border-white/20">
                                <div class="flex items-center space-x-3 sm:space-x-4 mb-4 sm:mb-6">
                                    <div class="bg-white/20 p-3 sm:p-4 rounded-xl">
                                        <i class="fas fa-users text-white text-xl sm:text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-white/80 text-xs sm:text-sm">Total Warga</p>
                                        <p class="text-2xl sm:text-3xl font-bold text-white" id="total-penduduk">Loading...</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3 sm:gap-4">
                                    <div class="text-center">
                                        <p class="text-white/80 text-xs sm:text-sm">Laki-laki</p>
                                        <p class="text-lg sm:text-xl font-bold text-white" id="penduduk-laki">-</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-white/80 text-xs sm:text-sm">Perempuan</p>
                                        <p class="text-lg sm:text-xl font-bold text-white" id="penduduk-perempuan">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Admin Panel Features -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Fitur Admin Panel</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Kelola seluruh aspek digital Desa Cibatu dengan mudah dan efisien
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Layanan Surat -->
                <div class="card-hover bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="bg-blue-100 w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-file-alt text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Kelola Surat Keterangan</h3>
                    <p class="text-gray-600 mb-6">Kelola pengajuan surat keterangan domisili, tidak mampu, dan surat lainnya dari warga</p>
                    <a href="{{ route('admin.surat-pengajuan.index') }}" class="text-blue-600 font-semibold hover:text-blue-700 transition-colors">
                        Kelola Surat <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>

                <!-- Pengaduan -->
                <div class="card-hover bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="bg-red-100 w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Kelola Pengaduan Warga</h3>
                    <p class="text-gray-600 mb-6">Kelola keluhan, saran, dan laporan masalah dari warga desa</p>
                    <a href="{{ route('pengaduan.index') }}" class="text-red-600 font-semibold hover:text-red-700 transition-colors">
                        Kelola Pengaduan <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>

                <!-- Informasi Desa -->
                <div class="card-hover bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="bg-green-100 w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-info-circle text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Kelola Informasi Desa</h3>
                    <p class="text-gray-600 mb-6">Kelola berita, agenda, dan informasi untuk dipublikasikan ke warga</p>
                    <a href="{{ route('berita.index') }}" class="text-green-600 font-semibold hover:text-green-700 transition-colors">
                        Kelola Berita <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>

                <!-- Transparansi -->
                <div class="card-hover bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="bg-purple-100 w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-pie text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Kelola Transparansi</h3>
                    <p class="text-gray-600 mb-6">Kelola data APBDes dan transparansi anggaran desa</p>
                    <a href="{{ route('transparansi-desa.apbdes') }}" class="text-purple-600 font-semibold hover:text-purple-700 transition-colors">
                        Kelola APBDes <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>

                <!-- Peta Desa -->
                <div class="card-hover bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="bg-yellow-100 w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-map-marked-alt text-yellow-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Kelola Fasilitas Desa</h3>
                    <p class="text-gray-600 mb-6">Kelola data fasilitas umum dan peta desa</p>
                    <a href="{{ route('fasilitas-desa.index') }}" class="text-yellow-600 font-semibold hover:text-yellow-700 transition-colors">
                        Kelola Fasilitas <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>

                <!-- Kontak Desa -->
                <div class="card-hover bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="bg-indigo-100 w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-phone text-indigo-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Kelola Pesan Kontak</h3>
                    <p class="text-gray-600 mb-6">Kelola pesan dan komunikasi dari warga desa</p>
                    <a href="{{ route('contact-messages.index') }}" class="text-indigo-600 font-semibold hover:text-indigo-700 transition-colors">
                        Kelola Pesan <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>


    <!-- Stats Section -->
    <section id="stats" class="py-20 bg-green-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-white mb-4">Dashboard Admin</h2>
                <p class="text-xl text-green-100">Statistik dan pencapaian sistem administrasi desa</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="bg-white bg-opacity-20 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-alt text-white text-3xl"></i>
                    </div>
                    <p class="text-4xl font-bold text-white mb-2" data-stat="surat">500+</p>
                    <p class="text-green-100">Surat Terselesaikan</p>
                </div>
                <div class="text-center">
                    <div class="bg-white bg-opacity-20 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-white text-3xl"></i>
                    </div>
                    <p class="text-4xl font-bold text-white mb-2" data-stat="pengaduan">95%</p>
                    <p class="text-green-100">Pengaduan Terselesaikan</p>
                </div>
                <div class="text-center">
                    <div class="bg-white bg-opacity-20 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-line text-white text-3xl"></i>
                    </div>
                    <p class="text-4xl font-bold text-white mb-2">100%</p>
                    <p class="text-green-100">Transparansi Anggaran</p>
                </div>
                <div class="text-center">
                    <div class="bg-white bg-opacity-20 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-smile text-white text-3xl"></i>
                    </div>
                    <p class="text-4xl font-bold text-white mb-2">98%</p>
                    <p class="text-green-100">Kepuasan Warga</p>
                </div>
            </div>
        </div>
    </section>


    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <img src="{{ asset('logo-desa-cibatu.png') }}" alt="Logo Desa Cibatu" class="h-12 w-12 rounded-lg" loading="lazy" decoding="async">
                        <div>
                        <h3 class="text-xl font-bold">Admin Panel</h3>
                        <p class="text-gray-400">Sistem Administrasi Desa Cibatu</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-6 max-w-md">
                        Dashboard administrasi digital untuk mengelola seluruh aspek Desa Cibatu dengan efisien dan transparan.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-whatsapp text-xl"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Fitur Admin</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('admin.surat-pengajuan.index') }}" class="text-gray-400 hover:text-white transition-colors">Kelola Surat</a></li>
                        <li><a href="{{ route('pengaduan.index') }}" class="text-gray-400 hover:text-white transition-colors">Kelola Pengaduan</a></li>
                        <li><a href="{{ route('berita.index') }}" class="text-gray-400 hover:text-white transition-colors">Kelola Berita</a></li>
                        <li><a href="{{ route('contact-messages.index') }}" class="text-gray-400 hover:text-white transition-colors">Kelola Pesan</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Akses</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition-colors">Admin Panel</a></li>
                        <li><a href="/web-desa" class="text-gray-400 hover:text-white transition-colors">Web Desa</a></li>
                        <li><a href="#kontak" class="text-gray-400 hover:text-white transition-colors">Kontak</a></li>
                        <li><a href="#tentang" class="text-gray-400 hover:text-white transition-colors">Tentang</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-gray-400">
                    © {{ date('Y') }} Desa Cibatu, Purwakarta. All rights reserved. |
                    <a href="#" class="hover:text-white transition-colors">Privacy Policy</a> |
                    <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                </p>
            </div>
        </div>
    </footer>

    <!-- Smooth Scroll Script -->
    <script nonce="{{ $csp_nonce }}">
        // Load penduduk data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadPendudukData();
            loadStatisticsData();
            initLazyLoading();
        });

        // Function to load penduduk data from API
        async function loadPendudukData() {
            try {
                const response = await fetch('/api/v1/public-statistics/penduduk', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        // Update total penduduk
                        document.getElementById('total-penduduk').textContent = data.data.total || '0';
                        document.getElementById('penduduk-laki').textContent = data.data.laki_laki || '0';
                        document.getElementById('penduduk-perempuan').textContent = data.data.perempuan || '0';
                    } else {
                        // Fallback data if API fails
                        document.getElementById('total-penduduk').textContent = '3,915';
                        document.getElementById('penduduk-laki').textContent = '1,968';
                        document.getElementById('penduduk-perempuan').textContent = '1,947';
                    }
                } else {
                    // Fallback data if API fails
                    document.getElementById('total-penduduk').textContent = '3,915';
                    document.getElementById('penduduk-laki').textContent = '1,968';
                    document.getElementById('penduduk-perempuan').textContent = '1,947';
                }
            } catch (error) {
                console.error('Error loading penduduk data:', error);
                // Fallback data if API fails
                document.getElementById('total-penduduk').textContent = '3,915';
                document.getElementById('penduduk-laki').textContent = '1,968';
                document.getElementById('penduduk-perempuan').textContent = '1,947';
            }
        }

        // Function to load statistics data from API
        async function loadStatisticsData() {
            try {
                const response = await fetch('/api/v1/public-statistics', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        // Update statistics in the stats section
                        updateStatsSection(data.data);
                    }
                }
            } catch (error) {
                console.error('Error loading statistics data:', error);
            }
        }

        // Function to update stats section with real data
        function updateStatsSection(stats) {
            // Update surat terselesaikan
            const suratElement = document.querySelector('[data-stat="surat"]');
            if (suratElement && stats.surat_selesai) {
                suratElement.textContent = stats.surat_selesai + '+';
            }

            // Update pengaduan terselesaikan
            const pengaduanElement = document.querySelector('[data-stat="pengaduan"]');
            if (pengaduanElement && stats.pengaduan_selesai && stats.pengaduan_total) {
                const percentage = Math.round((stats.pengaduan_selesai / stats.pengaduan_total) * 100);
                pengaduanElement.textContent = percentage + '%';
            }

            // Update transparansi (always 100% if data exists)
            const transparansiElement = document.querySelector('[data-stat="transparansi"]');
            if (transparansiElement) {
                transparansiElement.textContent = '100%';
            }

            // Update kepuasan warga (calculated from pengaduan resolution)
            const kepuasanElement = document.querySelector('[data-stat="kepuasan"]');
            if (kepuasanElement && stats.pengaduan_selesai && stats.pengaduan_total) {
                const percentage = Math.round((stats.pengaduan_selesai / stats.pengaduan_total) * 100);
                kepuasanElement.textContent = Math.min(percentage + 3, 100) + '%'; // Add 3% buffer
            }
        }

        // Initialize lazy loading for images
        function initLazyLoading() {
            const images = document.querySelectorAll('img[loading="lazy"]');

            // Add loaded class when image loads
            images.forEach(img => {
                img.addEventListener('load', function() {
                    this.classList.add('loaded');
                });

                // Fallback for images already loaded
                if (img.complete) {
                    img.classList.add('loaded');
                }
            });
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                }
            });
        }, observerOptions);

        // Observe all cards and sections
        document.querySelectorAll('.card-hover, section').forEach(el => {
            observer.observe(el);
        });

        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }
    </script>
</body>
</html>

