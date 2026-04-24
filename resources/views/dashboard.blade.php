@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Selamat datang di sistem informasi desa')

@section('content')
<div class="space-y-6">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8 mb-8">
            <div class="flex flex-col lg:flex-row items-center justify-between">
                <div class="flex-1 text-center lg:text-left mb-6 lg:mb-0">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-2 flex items-center justify-center lg:justify-start">
                        Selamat Datang, {{ auth()->user()->name }}!
                        <i class="fas fa-hand-wave ml-3 text-yellow-300"></i>
                    </h1>
                    <p class="text-green-100 text-lg mb-2">Sistem Informasi Desa Cibatu</p>
                    <p class="text-green-200 text-sm sm:text-base">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        <span id="current-date">{{ now()->format('l, d F Y') }}</span> - <span id="current-time">{{ now()->format('H:i') }}</span>
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg">
                        <img src="{{ asset('logo desa cibatu.png') }}" alt="Logo Desa Cibatu" class="w-12 h-12 sm:w-16 sm:h-16 object-contain">
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
            <!-- Total Penduduk Card -->
            <div class="bg-white rounded-2xl shadow-lg border-0 p-4 sm:p-6 hover:shadow-xl hover:scale-105 transition-all duration-300 group">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Total Penduduk</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ number_format($stats['total_penduduk']) }}</p>
                        <p class="text-xs text-green-600 flex items-center">
                            <i class="fas fa-arrow-up mr-1"></i>
                            Data terbaru
                        </p>
                    </div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                        <i class="fas fa-users text-white text-lg sm:text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Laki-laki Card -->
            <div class="bg-white rounded-2xl shadow-lg border-0 p-4 sm:p-6 hover:shadow-xl hover:scale-105 transition-all duration-300 group">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Laki-laki</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ number_format($stats['laki_laki']) }}</p>
                        <p class="text-xs text-blue-600 flex items-center">
                            <i class="fas fa-male mr-1"></i>
                            Penduduk laki-laki
                        </p>
                    </div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                        <i class="fas fa-male text-white text-lg sm:text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Perempuan Card -->
            <div class="bg-white rounded-2xl shadow-lg border-0 p-4 sm:p-6 hover:shadow-xl hover:scale-105 transition-all duration-300 group">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Perempuan</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ number_format($stats['perempuan']) }}</p>
                        <p class="text-xs text-pink-600 flex items-center">
                            <i class="fas fa-female mr-1"></i>
                            Penduduk perempuan
                        </p>
                    </div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                        <i class="fas fa-female text-white text-lg sm:text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Kartu Keluarga Card -->
            <div class="bg-white rounded-2xl shadow-lg border-0 p-4 sm:p-6 hover:shadow-xl hover:scale-105 transition-all duration-300 group">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Kartu Keluarga</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ number_format($stats['total_kk']) }}</p>
                        <p class="text-xs text-purple-600 flex items-center">
                            <i class="fas fa-home mr-1"></i>
                            Total keluarga
                        </p>
                    </div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                        <i class="fas fa-home text-white text-lg sm:text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-lg border-0 p-6 sm:p-8 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-bolt text-green-500 mr-3"></i>
                    Aksi Cepat
                </h3>
                <div class="hidden sm:block w-8 h-1 bg-gradient-to-r from-green-500 to-green-600 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                @can('penduduk.create')
                <a href="{{ route('penduduk.create') }}" class="group flex flex-col sm:flex-row items-center sm:items-start p-4 sm:p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl hover:from-blue-100 hover:to-blue-200 transition-all duration-300 border-0 hover:shadow-lg hover:scale-105">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-4 sm:mb-0 sm:mr-6 group-hover:scale-110 transition-transform shadow-lg">
                        <i class="fas fa-plus text-white text-lg"></i>
                    </div>
                    <div class="text-center sm:text-left">
                        <p class="font-bold text-gray-900 text-sm sm:text-base">Tambah Penduduk</p>
                        <p class="text-xs sm:text-sm text-gray-600 mt-1">Input data penduduk baru</p>
                    </div>
                </a>
                @endcan

                @can('penduduk.view')
                <a href="{{ route('penduduk.index') }}" class="group flex flex-col sm:flex-row items-center sm:items-start p-4 sm:p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl hover:from-green-100 hover:to-green-200 transition-all duration-300 border-0 hover:shadow-lg hover:scale-105">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-4 sm:mb-0 sm:mr-6 group-hover:scale-110 transition-transform shadow-lg">
                        <i class="fas fa-list text-white text-lg"></i>
                    </div>
                    <div class="text-center sm:text-left">
                        <p class="font-bold text-gray-900 text-sm sm:text-base">Data Penduduk</p>
                        <p class="text-xs sm:text-sm text-gray-600 mt-1">Kelola data penduduk</p>
                    </div>
                </a>
                @endcan

                @can('statistics.view')
                <a href="{{ route('statistics.index') }}" class="group flex flex-col sm:flex-row items-center sm:items-start p-4 sm:p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl hover:from-purple-100 hover:to-purple-200 transition-all duration-300 border-0 hover:shadow-lg hover:scale-105">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-4 sm:mb-0 sm:mr-6 group-hover:scale-110 transition-transform shadow-lg">
                        <i class="fas fa-chart-bar text-white text-lg"></i>
                    </div>
                    <div class="text-center sm:text-left">
                        <p class="font-bold text-gray-900 text-sm sm:text-base">Statistik</p>
                        <p class="text-xs sm:text-sm text-gray-600 mt-1">Lihat laporan statistik</p>
                    </div>
                </a>
                @endcan

                @can('penduduk.import')
                <a href="{{ route('import.index') }}" class="group flex flex-col sm:flex-row items-center sm:items-start p-4 sm:p-6 bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl hover:from-orange-100 hover:to-orange-200 transition-all duration-300 border-0 hover:shadow-lg hover:scale-105">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mb-4 sm:mb-0 sm:mr-6 group-hover:scale-110 transition-transform shadow-lg">
                        <i class="fas fa-file-import text-white text-lg"></i>
                    </div>
                    <div class="text-center sm:text-left">
                        <p class="font-bold text-gray-900 text-sm sm:text-base">Import Data</p>
                        <p class="text-xs sm:text-sm text-gray-600 mt-1">Import dari Excel</p>
                    </div>
                </a>
                @endcan
            </div>
        </div>

        <!-- Cache Management -->
        <div class="bg-white rounded-2xl shadow-lg border-0 p-6 sm:p-8 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-tools text-green-500 mr-3"></i>
                    Manajemen Cache
                </h3>
                <div class="hidden sm:block w-8 h-1 bg-gradient-to-r from-green-500 to-green-600 rounded-full"></div>
            </div>
            <p class="text-gray-600 mb-6">Kelola cache sistem untuk performa optimal</p>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                <!-- Clear All Cache -->
                <form method="POST" action="{{ route('clear-cache') }}" class="bg-gradient-to-br from-red-50 to-red-100 rounded-2xl p-4 sm:p-6 border-0 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                            <i class="fas fa-trash-alt text-white text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm sm:text-base">Clear All Cache</h4>
                            <p class="text-xs sm:text-sm text-gray-600">Bersihkan semua cache sistem</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">Menghapus cache: application, config, route, view</p>
                    @csrf
                    <button type="submit" class="w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium py-2 px-4 rounded-xl transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-xl">
                        <i class="fas fa-broom mr-2"></i>
                        Clear Cache
                    </button>
                </form>

                <!-- Clear Optimization Cache -->
                <form method="POST" action="{{ route('clear-optimization-cache') }}" class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-4 sm:p-6 border-0 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                            <i class="fas fa-rocket text-white text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm sm:text-base">Clear Optimization Cache</h4>
                            <p class="text-xs sm:text-sm text-gray-600">Bersihkan cache optimasi</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">Menghapus cache: statistics, API, optimization</p>
                    @csrf
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium py-2 px-4 rounded-xl transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-xl">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Clear Optimization
                    </button>
                </form>
            </div>

            <!-- Cache Status -->
            <div class="mt-6 p-4 sm:p-6 bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl">
                <h5 class="font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-green-500 mr-2"></i>
                    Status Cache
                </h5>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 text-xs sm:text-sm">
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-gray-600">Application Cache</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-gray-600">Config Cache</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-gray-600">Route Cache</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-gray-600">View Cache</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-2xl shadow-lg border-0 p-6 sm:p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-history text-green-500 mr-3"></i>
                    Aktivitas Terbaru
                </h3>
                <div class="hidden sm:block w-8 h-1 bg-gradient-to-r from-green-500 to-green-600 rounded-full"></div>
            </div>

            <div class="space-y-3 sm:space-y-4">
                <div class="flex items-center p-3 sm:p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-2xl border-0 hover:shadow-md transition-all duration-300">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3 sm:mr-4 flex-shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">Sistem berhasil diinisialisasi</p>
                        <p class="text-xs text-gray-500">{{ now()->format('d/m/Y H:i') }}</p>
                    </div>
                    <i class="fas fa-check-circle text-green-500 text-lg flex-shrink-0"></i>
                </div>

                <div class="flex items-center p-3 sm:p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-2xl border-0 hover:shadow-md transition-all duration-300">
                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-3 sm:mr-4 flex-shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">Database berhasil dibuat</p>
                        <p class="text-xs text-gray-500">{{ now()->subMinutes(5)->format('d/m/Y H:i') }}</p>
                    </div>
                    <i class="fas fa-database text-blue-500 text-lg flex-shrink-0"></i>
                </div>

                <div class="flex items-center p-3 sm:p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-2xl border-0 hover:shadow-md transition-all duration-300">
                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-3 sm:mr-4 flex-shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">Dashboard berhasil dimuat</p>
                        <p class="text-xs text-gray-500">{{ now()->subMinutes(1)->format('d/m/Y H:i') }}</p>
                    </div>
                    <i class="fas fa-tachometer-alt text-purple-500 text-lg flex-shrink-0"></i>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script nonce="{{ $csp_nonce }}">
// Real-time clock
function updateClock() {
    const now = new Date();

    // Format date in Indonesian
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                   'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    const dayName = days[now.getDay()];
    const day = now.getDate();
    const month = months[now.getMonth()];
    const year = now.getFullYear();

    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');

    const dateString = `${dayName}, ${day} ${month} ${year}`;
    const timeString = `${hours}:${minutes}`;

    document.getElementById('current-date').textContent = dateString;
    document.getElementById('current-time').textContent = timeString;
}

// Update clock every minute
updateClock();
setInterval(updateClock, 60000);

// Add some interactive effects
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to statistics cards
    const cards = document.querySelectorAll('.group');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Add loading animation to buttons
    const buttons = document.querySelectorAll('button[type="submit"]');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...';
            this.disabled = true;

            // Re-enable after 3 seconds (in case of error)
            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = false;
            }, 3000);
        });
    });
});
</script>
@endpush
