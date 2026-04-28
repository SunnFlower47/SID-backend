@extends('layouts.app')

@section('title', 'Perbandingan Data')
@section('subtitle', 'Bulan ini vs Bulan lalu')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Perbandingan Data Kependudukan</h2>
            <p class="text-gray-600 mt-2">Perbandingan data {{ $currentMonth->format('F Y') }} vs {{ $lastMonth->format('F Y') }}</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Penduduk -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-blue-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Penduduk</p>
                            <div class="flex items-center">
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($currentData['total_penduduk']) }}</p>
                                @if($changes['total_penduduk']['trend'] === 'up')
                                    <span class="ml-2 text-green-600 text-sm">
                                        <i class="fas fa-arrow-up"></i> +{{ $changes['total_penduduk']['difference'] }}
                                    </span>
                                @elseif($changes['total_penduduk']['trend'] === 'down')
                                    <span class="ml-2 text-red-600 text-sm">
                                        <i class="fas fa-arrow-down"></i> {{ $changes['total_penduduk']['difference'] }}
                                    </span>
                                @else
                                    <span class="ml-2 text-gray-600 text-sm">
                                        <i class="fas fa-minus"></i> 0
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">
                                {{ $lastMonth->format('M Y') }}: {{ number_format($lastData['total_penduduk']) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total KK -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-id-card text-green-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total KK</p>
                            <div class="flex items-center">
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($currentData['total_kk']) }}</p>
                                @if($changes['total_kk']['trend'] === 'up')
                                    <span class="ml-2 text-green-600 text-sm">
                                        <i class="fas fa-arrow-up"></i> +{{ $changes['total_kk']['difference'] }}
                                    </span>
                                @elseif($changes['total_kk']['trend'] === 'down')
                                    <span class="ml-2 text-red-600 text-sm">
                                        <i class="fas fa-arrow-down"></i> {{ $changes['total_kk']['difference'] }}
                                    </span>
                                @else
                                    <span class="ml-2 text-gray-600 text-sm">
                                        <i class="fas fa-minus"></i> 0
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">
                                {{ $lastMonth->format('M Y') }}: {{ number_format($lastData['total_kk']) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Mutasi -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exchange-alt text-yellow-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Mutasi</p>
                            <div class="flex items-center">
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($currentData['total_mutasi']) }}</p>
                                @if($changes['total_mutasi']['trend'] === 'up')
                                    <span class="ml-2 text-green-600 text-sm">
                                        <i class="fas fa-arrow-up"></i> +{{ $changes['total_mutasi']['difference'] }}
                                    </span>
                                @elseif($changes['total_mutasi']['trend'] === 'down')
                                    <span class="ml-2 text-red-600 text-sm">
                                        <i class="fas fa-arrow-down"></i> {{ $changes['total_mutasi']['difference'] }}
                                    </span>
                                @else
                                    <span class="ml-2 text-gray-600 text-sm">
                                        <i class="fas fa-minus"></i> 0
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">
                                {{ $lastMonth->format('M Y') }}: {{ number_format($lastData['total_mutasi']) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Penduduk Aktif -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-check text-purple-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Penduduk Aktif</p>
                            <div class="flex items-center">
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($currentData['penduduk_aktif']) }}</p>
                                @if($changes['penduduk_aktif']['trend'] === 'up')
                                    <span class="ml-2 text-green-600 text-sm">
                                        <i class="fas fa-arrow-up"></i> +{{ $changes['penduduk_aktif']['difference'] }}
                                    </span>
                                @elseif($changes['penduduk_aktif']['trend'] === 'down')
                                    <span class="ml-2 text-red-600 text-sm">
                                        <i class="fas fa-arrow-down"></i> {{ $changes['penduduk_aktif']['difference'] }}
                                    </span>
                                @else
                                    <span class="ml-2 text-gray-600 text-sm">
                                        <i class="fas fa-minus"></i> 0
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">
                                {{ $lastMonth->format('M Y') }}: {{ number_format($lastData['penduduk_aktif']) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Comparison -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Mutasi Breakdown -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Detail Mutasi</h3>

                    <div class="space-y-4">
                        <!-- Kematian -->
                        <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-skull text-red-600 mr-3"></i>
                                <span class="font-medium text-gray-900">Kematian</span>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-semibold text-gray-900">{{ $currentData['mutasi_kematian'] }}</p>
                                <p class="text-sm text-gray-500">{{ $lastData['mutasi_kematian'] }} bulan lalu</p>
                            </div>
                        </div>

                        <!-- Pindah Keluar -->
                        <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-walking text-yellow-600 mr-3"></i>
                                <span class="font-medium text-gray-900">Pindah Keluar</span>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-semibold text-gray-900">{{ $currentData['mutasi_pindah'] }}</p>
                                <p class="text-sm text-gray-500">{{ $lastData['mutasi_pindah'] }} bulan lalu</p>
                            </div>
                        </div>

                        <!-- Pindah RT/RW -->
                        <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-home text-blue-600 mr-3"></i>
                                <span class="font-medium text-gray-900">Pindah RT/RW</span>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-semibold text-gray-900">{{ $currentData['mutasi_pindah_rt'] }}</p>
                                <p class="text-sm text-gray-500">{{ $lastData['mutasi_pindah_rt'] }} bulan lalu</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Trends Chart -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Trend 12 Bulan Terakhir</h3>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Penduduk</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <span class="text-sm text-gray-900">{{ number_format($monthlyTrends[11]['penduduk']) }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Kartu Keluarga</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <span class="text-sm text-gray-900">{{ number_format($monthlyTrends[11]['kk']) }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Mutasi</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                <span class="text-sm text-gray-900">{{ number_format($monthlyTrends[11]['mutasi']) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Simple trend visualization -->
                    <div class="mt-6">
                        <canvas id="trendChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex justify-end space-x-4">
            <button onclick="exportComparison()" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-download mr-2"></i>
                Export Data
            </button>
            <button onclick="refreshData()" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-sync-alt mr-2"></i>
                Refresh
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@noncescript
    // Trend Chart
    const ctx = document.getElementById('trendChart').getContext('2d');
    const trendData = @json($monthlyTrends);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: trendData.map(item => item.month),
            datasets: [{
                label: 'Penduduk',
                data: trendData.map(item => item.penduduk),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1
            }, {
                label: 'KK',
                data: trendData.map(item => item.kk),
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.1
            }, {
                label: 'Mutasi',
                data: trendData.map(item => item.mutasi),
                borderColor: 'rgb(234, 179, 8)',
                backgroundColor: 'rgba(234, 179, 8, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function exportComparison() {
        // Export functionality
        Swal.fire({
            title: 'Info!',
            text: 'Export feature coming soon!',
            icon: 'info',
            confirmButtonText: 'OK'
        });
    }

    function refreshData() {
        location.reload();
    }
@endnoncescript

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@noncescript
// SweetAlert untuk notifikasi sukses
@if(session('success'))
    Swal.fire({
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        icon: 'success',
        confirmButtonText: 'OK'
    });
@endif

// SweetAlert untuk notifikasi error
@if(session('error'))
    Swal.fire({
        title: 'Error!',
        text: '{{ session('error') }}',
        icon: 'error',
        confirmButtonText: 'OK'
    });
@endif

// SweetAlert untuk notifikasi warning
@if(session('warning'))
    Swal.fire({
        title: 'Peringatan!',
        text: '{{ session('warning') }}',
        icon: 'warning',
        confirmButtonText: 'OK'
    });
@endif

// SweetAlert untuk notifikasi info
@if(session('info'))
    Swal.fire({
        title: 'Informasi!',
        text: '{{ session('info') }}',
        icon: 'info',
        confirmButtonText: 'OK'
    });
@endif
@endnoncescript
@endsection


