@extends('layouts.app')

@section('title', 'Transparansi Desa')
@section('subtitle', 'Informasi keuangan dan proyek desa untuk transparansi publik')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 via-purple-700 to-purple-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Transparansi Desa</h1>
                    <p class="text-purple-100 mt-1">Informasi keuangan dan proyek desa untuk transparansi publik</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('transparansi-desa.apbdes') }}" class="group flex items-center px-4 py-2.5 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Lihat APBDes
                </a>
                <a href="{{ route('transparansi-desa.proyek') }}" class="group flex items-center px-4 py-2.5 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                    <i class="fas fa-project-diagram mr-2"></i>
                    Lihat Proyek
                </a>
            </div>
        </div>
    </div>

    <!-- APBDes Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <i class="fas fa-chart-pie text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Anggaran {{ $currentYear ?? date('Y') }}</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['total_anggaran'] ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100">
                    <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pendapatan</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['pendapatan'] ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100">
                    <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Belanja</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['belanja'] ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100">
                    <i class="fas fa-project-diagram text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Proyek</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_proyek'] ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- APBDes Card -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-blue-500">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-lg bg-blue-100">
                    <i class="fas fa-chart-pie text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">APBDes</h3>
                    <p class="text-sm text-gray-600">Anggaran Pendapatan dan Belanja Desa</p>
                </div>
            </div>
            <p class="text-sm text-gray-700 mb-4">Lihat detail anggaran desa, sumber pendapatan, dan alokasi belanja untuk transparansi keuangan.</p>
            <a href="{{ route('transparansi-desa.apbdes') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all duration-200">
                <i class="fas fa-eye mr-2"></i>
                Lihat APBDes
            </a>
        </div>

        <!-- Proyek Card -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-green-500">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-lg bg-green-100">
                    <i class="fas fa-project-diagram text-green-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Proyek Desa</h3>
                    <p class="text-sm text-gray-600">Daftar proyek pembangunan desa</p>
                </div>
            </div>
            <p class="text-sm text-gray-700 mb-4">Pantau progress proyek pembangunan desa, anggaran yang digunakan, dan status penyelesaian.</p>
            <a href="{{ route('transparansi-desa.proyek') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all duration-200">
                <i class="fas fa-eye mr-2"></i>
                Lihat Proyek
            </a>
        </div>

        <!-- Laporan Keuangan Card -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-purple-500">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-lg bg-purple-100">
                    <i class="fas fa-file-invoice-dollar text-purple-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Laporan Keuangan</h3>
                    <p class="text-sm text-gray-600">Laporan keuangan bulanan dan tahunan</p>
                </div>
            </div>
            <p class="text-sm text-gray-700 mb-4">Akses laporan keuangan desa secara detail untuk memastikan transparansi dan akuntabilitas.</p>
            <a href="#" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all duration-200">
                <i class="fas fa-download mr-2"></i>
                Download Laporan
            </a>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
        <div class="px-6 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-center p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-pie text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-blue-900">APBDes {{ date('Y') }} Telah Disahkan</h4>
                        <p class="text-sm text-blue-700">Anggaran Pendapatan dan Belanja Desa tahun {{ date('Y') }} telah disahkan dan dapat diakses publik.</p>
                        <p class="text-xs text-blue-600 mt-1">{{ date('d F Y') }}</p>
                    </div>
                </div>

                <div class="flex items-center p-4 bg-green-50 rounded-lg border border-green-200">
                    <div class="flex-shrink-0">
                        <i class="fas fa-project-diagram text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-green-900">Proyek Jalan Desa Selesai</h4>
                        <p class="text-sm text-green-700">Pembangunan jalan desa sepanjang 2 km telah selesai dan dapat digunakan masyarakat.</p>
                        <p class="text-xs text-green-600 mt-1">{{ date('d F Y', strtotime('-3 days')) }}</p>
                    </div>
                </div>

                <div class="flex items-center p-4 bg-purple-50 rounded-lg border border-purple-200">
                    <div class="flex-shrink-0">
                        <i class="fas fa-file-invoice-dollar text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-purple-900">Laporan Keuangan Bulanan</h4>
                        <p class="text-sm text-purple-700">Laporan keuangan bulan {{ date('F Y') }} telah tersedia untuk diunduh.</p>
                        <p class="text-xs text-purple-600 mt-1">{{ date('d F Y', strtotime('-1 week')) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Box -->
    <div class="bg-white rounded-2xl shadow-lg border-0 p-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-yellow-50 to-yellow-100 -mx-6 -mt-6 mb-6">
            <h3 class="text-lg font-semibold text-yellow-900">Informasi Penting</h3>
        </div>
        <div class="space-y-3">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <div class="text-sm text-gray-700">
                        <p>• Semua data keuangan dan proyek desa dapat diakses secara transparan oleh masyarakat</p>
                        <p>• Laporan keuangan diperbarui setiap bulan dan dapat diunduh dalam format PDF</p>
                        <p>• Progress proyek pembangunan dapat dipantau secara real-time</p>
                        <p>• Jika ada pertanyaan atau keluhan, silakan hubungi kantor desa</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
// Session messages handled by global component
</script>
@endsection

