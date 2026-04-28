@extends('layouts.app')

@section('title', 'Export/Import Data')
@section('subtitle', 'Kelola export dan import data sistem')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Export</h1>
                    <p class="text-green-100 text-sm sm:text-base">Kelola export data sistem desa</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Section -->
    <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-download text-blue-600 mr-3"></i>
                Export Data
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Export Penduduk -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200 hover:shadow-md transition-all duration-300">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            <h6 class="text-sm font-semibold text-gray-900">Data Penduduk</h6>
                        </div>
                        <p class="text-gray-600 text-xs mb-4">Export data penduduk ke Excel</p>
                        <a href="{{ route('export.penduduk') }}" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">
                            <i class="fas fa-download mr-2"></i>Export
                        </a>
                    </div>

                    <!-- Export Kartu Keluarga -->
                    <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-4 border border-green-200 hover:shadow-md transition-all duration-300">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-id-card text-white"></i>
                            </div>
                            <h6 class="text-sm font-semibold text-gray-900">Kartu Keluarga</h6>
                        </div>
                        <p class="text-gray-600 text-xs mb-4">Export data KK ke Excel</p>
                        <a href="{{ route('export.kartu-keluarga') }}" class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-colors">
                            <i class="fas fa-download mr-2"></i>Export
                        </a>
                    </div>

                    <!-- Export Bantuan Sosial -->
                    <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-xl p-4 border border-red-200 hover:shadow-md transition-all duration-300">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-heart text-white"></i>
                            </div>
                            <h6 class="text-sm font-semibold text-gray-900">Bantuan Sosial</h6>
                        </div>
                        <p class="text-gray-600 text-xs mb-4">Export data bantuan sosial</p>
                        <a href="{{ route('export.bantuan-sosial') }}" class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors">
                            <i class="fas fa-download mr-2"></i>Export
                        </a>
                    </div>

                    <!-- Export Penerima Bantuan -->
                    <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-xl p-4 border border-yellow-200 hover:shadow-md transition-all duration-300">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-user-heart text-white"></i>
                            </div>
                            <h6 class="text-sm font-semibold text-gray-900">Penerima Bantuan</h6>
                        </div>
                        <p class="text-gray-600 text-xs mb-4">Export data penerima bantuan</p>
                        <a href="{{ route('export.penerima-bantuan-sosial') }}" class="inline-flex items-center px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-medium rounded-lg transition-colors">
                            <i class="fas fa-download mr-2"></i>Export
                        </a>
                    </div>

                    <!-- Export Pengaduan -->
                    <div class="bg-gradient-to-r from-cyan-50 to-cyan-100 rounded-xl p-4 border border-cyan-200 hover:shadow-md transition-all duration-300">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-cyan-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-comments text-white"></i>
                            </div>
                            <h6 class="text-sm font-semibold text-gray-900">Pengaduan</h6>
                        </div>
                        <p class="text-gray-600 text-xs mb-4">Export data pengaduan warga</p>
                        <a href="{{ route('export.pengaduan') }}" class="inline-flex items-center px-3 py-2 bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-medium rounded-lg transition-colors">
                            <i class="fas fa-download mr-2"></i>Export
                        </a>
                    </div>

                    <!-- Export UMKM -->
                    <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200 hover:shadow-md transition-all duration-300">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-store text-white"></i>
                            </div>
                            <h6 class="text-sm font-semibold text-gray-900">Data UMKM</h6>
                        </div>
                        <p class="text-gray-600 text-xs mb-4">Export data UMKM desa</p>
                        <a href="{{ route('export.umkm') }}" class="inline-flex items-center px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-medium rounded-lg transition-colors">
                            <i class="fas fa-download mr-2"></i>Export
                        </a>
                    </div>

                    <!-- Export Surat Pengajuan -->
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200 hover:shadow-md transition-all duration-300">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-gray-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-file-alt text-white"></i>
                            </div>
                            <h6 class="text-sm font-semibold text-gray-900">Surat Pengajuan</h6>
                        </div>
                        <p class="text-gray-600 text-xs mb-4">Export data surat pengajuan</p>
                        <a href="{{ route('export.surat-pengajuan') }}" class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors">
                            <i class="fas fa-download mr-2"></i>Export
                        </a>
                    </div>
            </div>
        </div>
    </div>


    <!-- Instructions -->
    <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-info-circle text-gray-600 mr-3"></i>
                Petunjuk Export Data
            </h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div>
                    <h6 class="text-sm font-semibold text-gray-900 mb-3">Export Data:</h6>
                    <ul class="space-y-2">
                        <li class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Data akan diekspor dalam format Excel (.xlsx)
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            File akan otomatis terdownload
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Data dapat difilter berdasarkan kriteria tertentu
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Untuk import data, gunakan menu "Import Data" yang terpisah
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


