@extends('layouts.app')

@section('title', 'Statistik Kependudukan')
@section('subtitle', 'Analisis data kependudukan desa Cibatu')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Statistik Kependudukan</h1>
            <p class="text-gray-600 mt-1">Analisis data kependudukan desa Cibatu</p>
        </div>
    </div>

    <!-- Error Message -->
    <div class="bg-red-50 border border-red-200 rounded-xl p-6">
        <div class="flex items-center">
            <div class="bg-red-100 p-3 rounded-lg mr-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-red-900">Terjadi Kesalahan</h3>
                <p class="text-red-700 mt-1">Tidak dapat memuat data statistik. Silakan coba lagi nanti atau hubungi administrator.</p>
            </div>
        </div>
    </div>

    <!-- Basic Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-blue-50 p-6 rounded-xl shadow-sm border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-600 text-sm font-medium">Total Penduduk</p>
                    <p class="text-4xl font-bold text-blue-900">{{ number_format($totalPenduduk) }}</p>
                </div>
                <div class="bg-blue-100 p-4 rounded-xl">
                    <i class="fas fa-users text-blue-600 text-3xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-emerald-50 p-6 rounded-xl shadow-sm border border-emerald-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-emerald-600 text-sm font-medium">Kartu Keluarga</p>
                    <p class="text-4xl font-bold text-emerald-900">{{ number_format($totalKK) }}</p>
                </div>
                <div class="bg-emerald-100 p-4 rounded-xl">
                    <i class="fas fa-home text-emerald-600 text-3xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 p-6 rounded-xl shadow-sm border border-purple-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-600 text-sm font-medium">Total Mutasi</p>
                    <p class="text-4xl font-bold text-purple-900">{{ number_format($totalMutasi) }}</p>
                </div>
                <div class="bg-purple-100 p-4 rounded-xl">
                    <i class="fas fa-exchange-alt text-purple-600 text-3xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-orange-50 p-6 rounded-xl shadow-sm border border-orange-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-600 text-sm font-medium">Rata-rata per KK</p>
                    <p class="text-4xl font-bold text-orange-900">{{ $totalKK > 0 ? number_format($totalPenduduk / $totalKK, 1) : '0' }}</p>
                </div>
                <div class="bg-orange-100 p-4 rounded-xl">
                    <i class="fas fa-chart-line text-orange-600 text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Retry Button -->
    <div class="text-center">
        <button onclick="window.location.reload()" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg flex items-center mx-auto transition-colors shadow-md">
            <i class="fas fa-refresh mr-2"></i>
            Coba Lagi
        </button>
    </div>
</div>
@endsection

