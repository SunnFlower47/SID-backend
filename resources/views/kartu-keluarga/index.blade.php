@extends('layouts.app')

@section('title', 'Kartu Keluarga')
@section('subtitle', 'Kelola data Kartu Keluarga Desa Cibatu')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-id-card text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Kartu Keluarga</h1>
                    <p class="text-green-100 text-sm sm:text-base">Kelola data Kartu Keluarga dan deteksi KK yang bermasalah</p>
                    <p class="text-green-200 text-sm font-medium">Total: {{ $kartuKeluarga->total() ?? 0 }} Kartu Keluarga</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        @can('kartu-keluarga.create')
        <a href="{{ route('kartu-keluarga.create') }}" class="group flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
            <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-plus text-white text-sm"></i>
            </div>
            <div class="text-left">
                <p class="font-semibold text-sm sm:text-base">Tambah KK</p>
                <p class="text-blue-100 text-sm">Input data KK baru</p>
            </div>
        </a>
        @endcan

        @can('kartu_keluarga.export')
        <button onclick="exportData()" class="group flex items-center justify-center px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
            <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-file-excel text-white text-sm"></i>
            </div>
            <div class="text-left">
                <p class="font-semibold text-sm sm:text-base">Export Excel</p>
                <p class="text-green-100 text-sm">Download data KK</p>
            </div>
        </button>
        @endcan

        @can('kartu-keluarga.edit')
        <form method="POST" action="{{ route('kartu-keluarga.sync-summary') }}" onsubmit="return confirm('Proses ini akan mensinkronkan data KK sekaligus memindai KK historis yang tidak memiliki Kepala Keluarga aktif. Lanjutkan?')">
            @csrf
            <button type="submit" class="group flex items-center justify-center px-4 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform group-hover:rotate-180 duration-500">
                    <i class="fas fa-rotate text-white text-sm"></i>
                </div>
                <div class="text-left">
                    <p class="font-semibold text-sm sm:text-base">Sinkronkan KK</p>
                    <p class="text-indigo-100 text-sm">Perbarui & Cek KK Bermasalah</p>
                </div>
            </button>
        </form>
        @endcan

        @php $kkBermasalahCount = $stats['bermasalah'] ?? 0; @endphp
        <a href="{{ route('kk.bermasalah.index') }}"
           class="group relative flex items-center justify-center px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
            <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-exclamation-triangle text-white text-sm {{ $kkBermasalahCount > 0 ? 'animate-pulse' : '' }}"></i>
            </div>
            <div class="text-left">
                <p class="font-semibold text-sm sm:text-base">KK Bermasalah</p>
                <p class="text-red-100 text-sm">Pantau & audit resolusi KK</p>
            </div>
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-lg border-0 p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-home text-blue-600 text-lg"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total KK</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border-0 p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-lg"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">KK Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['aktif']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border-0 p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">KK Kosong</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['kosong']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border-0 p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-exclamation-circle text-yellow-600 text-lg"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">KK Bermasalah</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['bermasalah']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="bg-white rounded-2xl shadow-lg border-0 p-6 sm:p-8 mb-8">
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-filter text-green-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Filter & Pencarian</h3>
                <p class="text-sm text-gray-600">Saring data Kartu Keluarga berdasarkan kriteria</p>
            </div>
        </div>
        <form method="GET" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian Data</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="search" value="{{ $search }}"
                               placeholder="Cari berdasarkan NKK atau nama kepala keluarga..."
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors">
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status KK</label>
                    <select name="status" id="status"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="aktif" {{ $status === 'aktif' ? 'selected' : '' }}>KK Aktif</option>
                        <option value="kosong" {{ $status === 'kosong' ? 'selected' : '' }}>KK Kosong</option>
                        <option value="bermasalah" {{ $status === 'bermasalah' ? 'selected' : '' }}>KK Bermasalah</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-end space-x-2">
                    <button type="submit"
                            class="group flex items-center px-4 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                        <i class="fas fa-search mr-2"></i>
                        Cari
                    </button>
                    <a href="{{ route('kartu-keluarga.index') }}"
                       class="group flex items-center px-4 py-2.5 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                        <i class="fas fa-sync mr-2"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Desktop Table View -->
    <div class="hidden lg:block bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NKK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kepala Keluarga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Anggota</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($kartuKeluarga as $kk)
                    @if($kk)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $kk->nkk }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $kk->nama_kepala_keluarga ?: 'Tidak ada' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="text-sm text-gray-900">{{ $kk->jumlah_anggota }}</span>
                                @if($kk->anggota_aktif > 0)
                                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $kk->anggota_aktif }} aktif
                                </span>
                                @endif
                                @if($kk->anggota_meninggal > 0)
                                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $kk->anggota_meninggal }} meninggal
                                </span>
                                @endif
                                @if($kk->anggota_pindah > 0)
                                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ $kk->anggota_pindah }} pindah
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusKk = $kk->status_kk ?? 'normal';
                            @endphp
                            @if($kk->anggota_aktif == 0)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-ban mr-1"></i> Kosong
                            </span>
                            @elseif($statusKk === 'bermasalah')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Bermasalah
                            </span>
                            @elseif($statusKk === 'bermasalah_sementara')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                <i class="fas fa-clock mr-1"></i> Sementara
                            </span>
                            @elseif($statusKk === 'resolved')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                <i class="fas fa-archive mr-1"></i> Diarsip
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Aktif
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="text-xs">
                                <div>Dibuat: {{ \Carbon\Carbon::parse($kk->tanggal_dibuat)->format('d/m/Y') }}</div>
                                <div>Update: {{ \Carbon\Carbon::parse($kk->tanggal_update)->format('d/m/Y') }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                @can('kartu-keluarga.view')
                                <a href="{{ route('kartu-keluarga.show', $kk->nkk) }}"
                                   class="text-blue-600 hover:text-blue-900"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                @if(in_array($kk->status_kk ?? 'normal', ['bermasalah','bermasalah_sementara']))
                                @can('kartu-keluarga.edit')
                                <a href="{{ route('kk.bermasalah', $kk->nkk) }}"
                                   class="text-orange-600 hover:text-orange-900"
                                   title="Selesaikan KK Bermasalah">
                                    <i class="fas fa-tools"></i>
                                </a>
                                @endcan
                                @endif
                                @can('kartu-keluarga.view')
                                <a href="{{ route('kartu-keluarga.download-pdf', $kk->nkk) }}"
                                   class="text-green-600 hover:text-green-900"
                                   title="Download PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                @endcan
                                @can('kartu-keluarga.edit')
                                <a href="{{ route('kartu-keluarga.edit', $kk->nkk) }}"
                                   class="text-gray-600 hover:text-gray-900"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('kartu-keluarga.delete')
                                <button onclick="deleteKK('{{ $kk->nkk }}')"
                                        class="text-red-600 hover:text-red-900"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-home text-4xl mb-4"></i>
                            <p class="text-lg">Tidak ada data Kartu Keluarga</p>
                            <p class="text-sm">Mulai dengan menambahkan Kartu Keluarga baru</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($kartuKeluarga->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $kartuKeluarga->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Mobile Card View -->
    <div class="lg:hidden px-1 sm:px-2 py-4 space-y-3">
            @foreach($kartuKeluarga as $kk)
            @if($kk)
            <div class="bg-white rounded-2xl shadow-lg border-0 p-4 sm:p-6 hover:shadow-xl transition-all duration-300 w-full">
                <!-- Header with NKK and Status -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-home text-white text-sm sm:text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0 overflow-hidden">
                            <h3 class="text-base sm:text-lg font-bold text-gray-900 truncate" title="{{ $kk->nkk }}">{{ $kk->nkk }}</h3>
                            <div class="flex items-center space-x-2 mt-1">
                                @php $statusKk = $kk->status_kk ?? 'normal'; @endphp
                                @if($kk->anggota_aktif == 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-ban mr-1"></i> Kosong
                                </span>
                                @elseif($statusKk === 'bermasalah')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Bermasalah
                                </span>
                                @elseif($statusKk === 'bermasalah_sementara')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                    <i class="fas fa-clock mr-1"></i> Sementara
                                </span>
                                @elseif($statusKk === 'resolved')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    <i class="fas fa-archive mr-1"></i> Diarsip
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Aktif
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Information Grid - Mobile Optimized -->
                <div class="space-y-3 mb-4">
                    <!-- Kepala Keluarga -->
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-3">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-user-tie text-gray-500 mr-2 text-sm"></i>
                            <span class="text-xs sm:text-sm font-medium text-gray-700">Kepala Keluarga</span>
                        </div>
                        <p class="text-sm text-gray-900 font-medium">{{ $kk->nama_kepala_keluarga ?: 'Tidak ada' }}</p>
                    </div>

                    <!-- Jumlah Anggota dengan Detail -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-3">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-users text-blue-500 mr-2 text-sm"></i>
                            <span class="text-xs sm:text-sm font-medium text-gray-700">Jumlah Anggota</span>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm text-gray-900 font-medium">{{ $kk->jumlah_anggota }} total</p>
                            <div class="flex flex-wrap gap-1">
                                @if($kk->anggota_aktif > 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $kk->anggota_aktif }} aktif
                                </span>
                                @endif
                                @if($kk->anggota_meninggal > 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $kk->anggota_meninggal }} meninggal
                                </span>
                                @endif
                                @if($kk->anggota_pindah > 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ $kk->anggota_pindah }} pindah
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tanggal Info -->
                    <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-3">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-calendar text-purple-500 mr-2 text-sm"></i>
                            <span class="text-xs sm:text-sm font-medium text-gray-700">Tanggal</span>
                        </div>
                        <div class="text-xs text-gray-600 space-y-1">
                            <div>Dibuat: {{ \Carbon\Carbon::parse($kk->tanggal_dibuat)->format('d/m/Y') }}</div>
                            <div>Update: {{ \Carbon\Carbon::parse($kk->tanggal_update)->format('d/m/Y') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons - Mobile Optimized -->
                <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:gap-2">
                    <!-- Detail & Download Row -->
                    <div class="flex gap-2">
                        @can('kartu-keluarga.view')
                        <a href="{{ route('kartu-keluarga.show', $kk->nkk) }}" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                            <i class="fas fa-eye mr-2 text-sm"></i>
                            <span class="text-sm font-medium">Detail</span>
                        </a>
                        @endcan
                        @if(in_array($kk->status_kk ?? 'normal', ['bermasalah','bermasalah_sementara']))
                        @can('kartu-keluarga.edit')
                        <a href="{{ route('kk.bermasalah', $kk->nkk) }}" class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                            <i class="fas fa-tools mr-2 text-sm"></i>
                            <span class="text-sm font-medium">Selesaikan</span>
                        </a>
                        @endcan
                        @else
                        @can('kartu-keluarga.view')
                        <a href="{{ route('kartu-keluarga.download-pdf', $kk->nkk) }}" class="flex-1 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                            <i class="fas fa-file-pdf mr-2 text-sm"></i>
                            <span class="text-sm font-medium">PDF</span>
                        </a>
                        @endcan
                        @endif
                    </div>

                    <!-- Edit & Hapus Row -->
                    <div class="flex gap-2">
                        @can('kartu-keluarga.edit')
                        <a href="{{ route('kartu-keluarga.edit', $kk->nkk) }}" class="flex-1 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                            <i class="fas fa-edit mr-2 text-sm"></i>
                            <span class="text-sm font-medium">Edit</span>
                        </a>
                        @endcan
                        @can('kartu-keluarga.delete')
                        <button onclick="deleteKK('{{ $kk->nkk }}')" class="flex-1 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                            <i class="fas fa-trash mr-2 text-sm"></i>
                            <span class="text-sm font-medium">Hapus</span>
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
            @endif
            @endforeach

        <!-- Mobile Pagination -->
        @if($kartuKeluarga->hasPages())
        <div class="px-4 py-4">
            {{ $kartuKeluarga->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script nonce="{{ $csp_nonce }}">
function deleteKK(nkk) {
    console.log('DEBUG: Attempting to delete NKK:', nkk);
    
    if (!nkk || nkk.trim() === '') {
        console.error('DEBUG: NKK is empty or invalid!');
        Swal.fire({
            title: 'Error!',
            text: 'NKK tidak valid atau kosong. Silakan refresh halaman atau sinkronkan data.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }

    Swal.fire({
        title: '⚠️ Peringatan!',
        html: `
            <div class="text-left">
                <p class="font-semibold mb-3">Apakah Anda yakin ingin menghapus Kartu Keluarga <strong>${nkk}</strong>?</p>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-3">
                    <p class="text-red-800 font-medium mb-2">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        PERHATIAN:
                    </p>
                    <ul class="text-red-700 text-sm space-y-1 list-disc list-inside">
                        <li><strong>SEMUA anggota keluarga</strong> dengan NKK ini akan dihapus (soft delete)</li>
                        <li>Data masih bisa dikembalikan melalui menu <strong>Mutasi > Undo</strong></li>
                        <li>Tindakan ini akan membuat log mutasi otomatis</li>
                    </ul>
                </div>
                <p class="text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    Jika Anda hanya ingin menghapus 1 anggota keluarga, gunakan menu <strong>Penduduk</strong> atau <strong>Mutasi</strong>.
                </p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-trash mr-2"></i>Ya, Hapus Semua!',
        cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal',
        width: '600px',
        customClass: {
            popup: 'text-left'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Menghapus...',
                html: 'Sedang menghapus Kartu Keluarga dan semua anggotanya...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const deleteUrl = `${window.location.origin}/kartu-keluarga/${nkk}`;
            console.log('DEBUG: Sending DELETE request to:', deleteUrl);

            fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 405) {
                        throw new Error('Metode tidak diizinkan (405). Pastikan rute DELETE sudah benar.');
                    }
                    throw new Error('Gagal menghapus data (Status: ' + response.status + ')');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Kartu Keluarga dan semua anggotanya berhasil dihapus',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Gagal menghapus Kartu Keluarga',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Terjadi kesalahan saat menghapus Kartu Keluarga',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

function exportData() {
    // Show loading
    Swal.fire({
        title: 'Memproses...',
        html: 'Sedang mengexport data Kartu Keluarga ke Excel...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Build URL with current filters
    const urlParams = new URLSearchParams(window.location.search);
    const exportUrl = '{{ route("kartu-keluarga.export.excel") }}?' + urlParams.toString();

    // Fetch the file as blob
    fetch(exportUrl, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        // Get filename from response headers or use default
        const contentDisposition = response.headers.get('content-disposition');
        let filename = 'data_kartu_keluarga_' + new Date().toISOString().slice(0,19).replace(/:/g, '-') + '.xlsx';
        if (contentDisposition) {
            const filenameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
            if (filenameMatch && filenameMatch[1]) {
                filename = filenameMatch[1].replace(/['"]/g, '');
            }
        }

        return response.blob().then(blob => ({ blob, filename }));
    })
    .then(({ blob, filename }) => {
        // Create download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = filename;

        document.body.appendChild(a);
        a.click();

        // Cleanup
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        // Show success message
        Swal.fire({
            title: 'Berhasil!',
            text: 'File Excel berhasil diunduh!',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    })
    .catch(error => {
        console.error('Export error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Gagal mengexport data: ' + error.message,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
}

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
</script>
@endpush
@endsection
