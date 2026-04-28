@extends('layouts.app')

@section('title', 'Laporan')
@section('subtitle', 'Laporan dan statistik data kependudukan')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-3 lg:space-y-0 px-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Laporan</h1>
            <p class="text-gray-600 mt-1">Laporan dan statistik data kependudukan</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 px-6 sm:px-0">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Penduduk</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalPenduduk) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-home text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total KK</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalKK) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exchange-alt text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Mutasi</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalMutasi) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-friends text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pisah KK</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalPisahKK) }}</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Report Types -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-6 sm:px-0">
        <!-- Laporan Penduduk -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Laporan Penduduk</h3>
                    <p class="text-sm text-gray-600">Data penduduk dengan filter lengkap</p>
                </div>
            </div>
            <div class="space-y-3">
                <a href="{{ route('laporan.penduduk') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center transition-colors">
                    <i class="fas fa-eye mr-2"></i>
                    Lihat Laporan
                </a>
                <a href="{{ route('laporan.penduduk.export.excel') }}" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center transition-colors">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export Excel
                </a>
            </div>
        </div>

        <!-- Laporan Mutasi -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-exchange-alt text-yellow-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Laporan Mutasi</h3>
                    <p class="text-sm text-gray-600">Data mutasi penduduk</p>
                </div>
            </div>
            <div class="space-y-3">
                <a href="{{ route('laporan.mutasi') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center transition-colors">
                    <i class="fas fa-eye mr-2"></i>
                    Lihat Laporan
                </a>
                <a href="{{ route('laporan.mutasi.export.excel') }}" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center transition-colors">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export Excel
                </a>
            </div>
        </div>

    </div>

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 px-6 sm:px-0">
        <!-- Recent Mutasi -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Mutasi Terbaru</h3>
            </div>
            <div class="p-6">
                @if($recentMutasi->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentMutasi as $mutasi)
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-exchange-alt text-yellow-600 text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $mutasi->penduduk->nama ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">{{ $mutasi->jenis_mutasi }} - {{ $mutasi->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Tidak ada data mutasi terbaru</p>
                @endif
            </div>
        </div>

    </div>
</div>

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


