@extends('layouts.app')

@section('title', 'Data Mutasi')
@section('subtitle', 'Kelola data mutasi penduduk')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Data Mutasi</h1>
                    <p class="text-green-100 text-sm sm:text-base">Kelola data mutasi penduduk desa Cibatu</p>
                    <p class="text-green-200 text-sm font-medium">Total: {{ $mutasis->total() }} mutasi</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        @can('mutasi.create')
        <a href="{{ route('mutasi.data.create') }}" class="group flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
            <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-plus text-white text-sm"></i>
            </div>
            <div class="text-left">
                <p class="font-semibold text-sm sm:text-base">Tambah Mutasi</p>
                <p class="text-blue-100 text-sm">Input data mutasi baru</p>
            </div>
        </a>
        @endcan
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl shadow-lg border-0 p-6 sm:p-8 mb-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-filter text-green-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Filter & Pencarian</h3>
                    <p class="text-sm text-gray-600">Saring data mutasi berdasarkan kriteria</p>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('mutasi.data.index') }}" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian Data</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari berdasarkan nama penduduk atau alasan..."
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors">
                    </div>
                </div>

                <!-- Jenis Mutasi Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Mutasi</label>
                    <select name="jenis_mutasi" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors">
                        <option value="">Semua Jenis</option>
                        <option value="kelahiran" {{ request('jenis_mutasi') == 'kelahiran' ? 'selected' : '' }}>Kelahiran</option>
                        <option value="kematian" {{ request('jenis_mutasi') == 'kematian' ? 'selected' : '' }}>Kematian</option>
                        <option value="pindah_masuk" {{ request('jenis_mutasi') == 'pindah_masuk' ? 'selected' : '' }}>Pindah Masuk</option>
                        <option value="pindah_keluar" {{ request('jenis_mutasi') == 'pindah_keluar' ? 'selected' : '' }}>Pindah Keluar</option>
                        <option value="pindah_rt_rw" {{ request('jenis_mutasi') == 'pindah_rt_rw' ? 'selected' : '' }}>Pindah RT/RW</option>
                        <option value="pisah_kk" {{ request('jenis_mutasi') == 'pisah_kk' ? 'selected' : '' }}>Pisah KK</option>
                    </select>
                </div>

                <!-- Kategori Mutasi Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="kategori_mutasi" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors">
                        <option value="">Semua Kategori</option>
                        <option value="dalam_desa" {{ request('kategori_mutasi') == 'dalam_desa' ? 'selected' : '' }}>Dalam Desa</option>
                        <option value="dalam_kota" {{ request('kategori_mutasi') == 'dalam_kota' ? 'selected' : '' }}>Dalam Kota</option>
                        <option value="luar_kota" {{ request('kategori_mutasi') == 'luar_kota' ? 'selected' : '' }}>Luar Kota</option>
                        <option value="luar_negeri" {{ request('kategori_mutasi') == 'luar_negeri' ? 'selected' : '' }}>Luar Negeri</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="group flex items-center px-4 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                    <i class="fas fa-search mr-2"></i>
                    Terapkan Filter
                </button>
                <a href="{{ route('mutasi.data.index') }}" class="group flex items-center px-4 py-2.5 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                    <i class="fas fa-refresh mr-2"></i>
                    Reset Filter
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
        <div class="px-6 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-list text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Mutasi</h3>
                        <p class="text-sm text-gray-600">Total {{ $mutasis->total() }} data mutasi</p>
                    </div>
                </div>
            </div>
        </div>

        @if($mutasis->count() > 0)
            <!-- Mobile Card View -->
        <div class="lg:hidden px-1 sm:px-2 py-4 space-y-3">
                    @foreach($mutasis as $mutasi)
            <div class="bg-white rounded-2xl shadow-lg border-0 p-4 sm:p-6 hover:shadow-xl transition-all duration-300 w-full">
                <!-- Header with Nama and Status -->
                        <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-exchange-alt text-white text-sm sm:text-xl"></i>
                                </div>
                        <div class="flex-1 min-w-0 overflow-hidden">
                            <h3 class="text-base sm:text-lg font-bold text-gray-900 truncate">
                                @if($mutasi->penduduk)
                                    {{ $mutasi->penduduk->nama }}
                                @else
                                    <span class="text-red-600">Data penduduk tidak ditemukan</span>
                                @endif
                            </h3>
                            <div class="flex items-center space-x-2 mt-1">
                                <p class="text-xs font-mono text-gray-600">{{ $mutasi->penduduk->nik ?? '-' }}</p>
                                @if($mutasi->penduduk && $mutasi->penduduk->trashed())
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-trash mr-1"></i>
                                        Terhapus
                                    </span>
                                @endif
                            </div>
                        </div>
                            </div>
                        </div>

                <!-- Information Grid - Mobile Optimized -->
                <div class="space-y-3 mb-4">
                    <!-- Jenis Mutasi -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-3">
                        <div class="flex items-center mb-2">
                                @php
                                    $jenisColors = [
                                    'kelahiran' => 'text-green-500',
                                    'kematian' => 'text-red-500',
                                    'pindah_masuk' => 'text-blue-500',
                                    'pindah_keluar' => 'text-yellow-500',
                                    'pindah_rt_rw' => 'text-purple-500',
                                    'pisah_kk' => 'text-indigo-500'
                                    ];
                                    $jenisIcons = [
                                        'kelahiran' => 'fa-baby',
                                    'kematian' => 'fa-user-times',
                                        'pindah_masuk' => 'fa-arrow-right',
                                    'pindah_keluar' => 'fa-arrow-left',
                                    'pindah_rt_rw' => 'fa-exchange-alt',
                                    'pisah_kk' => 'fa-users'
                                    ];
                                @endphp
                            <i class="fas {{ $jenisIcons[$mutasi->jenis_mutasi] ?? 'fa-question' }} {{ $jenisColors[$mutasi->jenis_mutasi] ?? 'text-gray-500' }} mr-2 text-sm"></i>
                            <span class="text-xs sm:text-sm font-medium text-gray-700">Jenis Mutasi</span>
                        </div>
                        <p class="text-sm text-gray-900 font-medium">{{ ucfirst(str_replace('_', ' ', $mutasi->jenis_mutasi)) }}</p>
                            </div>

                    <!-- Kategori dan Tanggal -->
                    <div class="grid grid-cols-2 gap-2">
                        <!-- Kategori -->
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-2.5">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-tag text-gray-500 mr-1 text-xs"></i>
                                <span class="text-xs font-medium text-gray-700">Kategori</span>
                            </div>
                            <p class="text-xs text-gray-900">{{ ucfirst(str_replace('_', ' ', $mutasi->kategori_mutasi)) }}</p>
                            </div>

                        <!-- Tanggal -->
                        <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-2.5">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-calendar text-purple-500 mr-1 text-xs"></i>
                                <span class="text-xs font-medium text-gray-700">Tanggal</span>
                            </div>
                            <p class="text-xs text-gray-900">{{ $mutasi->tanggal_mutasi->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <!-- Detail Tambahan -->
                    @if($mutasi->jenis_mutasi == 'kematian' && $mutasi->data_kematian)
                    <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-xl p-3">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-info-circle text-red-500 mr-2 text-sm"></i>
                            <span class="text-xs sm:text-sm font-medium text-gray-700">Detail Kematian</span>
                        </div>
                        <div class="space-y-1 text-xs text-gray-600">
                            @if($mutasi->data_kematian['hari'] ?? false)
                            <div>Hari: {{ $mutasi->data_kematian['hari'] }}</div>
                            @endif
                            @if($mutasi->data_kematian['jam'] ?? false)
                            <div>Jam: {{ $mutasi->data_kematian['jam'] }}</div>
                            @endif
                            @if($mutasi->data_kematian['bertempat_di'] ?? false)
                            <div>Bertempat di: {{ Str::limit($mutasi->data_kematian['bertempat_di'], 30) }}</div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($mutasi->jenis_mutasi == 'pindah_rt_rw' && strpos($mutasi->asal_tujuan ?? '', ' → ') !== false)
                        @php
                            $parts = explode(' → ', $mutasi->asal_tujuan);
                            $asal = $parts[0] ?? '';
                            $tujuan = $parts[1] ?? '';
                        @endphp
                    <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-xl p-3">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-route text-yellow-500 mr-2 text-sm"></i>
                            <span class="text-xs sm:text-sm font-medium text-gray-700">Rute Pindah</span>
                        </div>
                        <div class="space-y-1 text-xs text-gray-600">
                            <div>Asal: {{ Str::limit($asal, 25) }}</div>
                            <div>Tujuan: {{ Str::limit($tujuan, 25) }}</div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Action Buttons - Mobile Optimized -->
                <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:gap-2">
                    <!-- Detail & Edit Row -->
                    <div class="flex gap-2">
                        <a href="{{ route('mutasi.data.show', $mutasi) }}" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                            <i class="fas fa-eye mr-2 text-sm"></i>
                            <span class="text-sm font-medium">Detail</span>
                        </a>
                        @can('mutasi.edit')
                        <a href="{{ route('mutasi.data.edit', $mutasi) }}" class="flex-1 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                            <i class="fas fa-edit mr-2 text-sm"></i>
                            <span class="text-sm font-medium">Edit</span>
                        </a>
                        @endcan
                    </div>

                    <!-- Action Button Row -->
                    @can('mutasi.delete')
                        @if($mutasi->isSoftDeleteType())
                            <button type="button" data-id="{{ $mutasi->id }}" data-name="{{ $mutasi->penduduk->nama ?? 'Data' }}" class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md js-confirm-undo">
                                <i class="fas fa-undo mr-2 text-sm"></i>
                                <span class="text-sm font-medium">Undo</span>
                            </button>
                        @else
                            <button type="button" data-id="{{ $mutasi->id }}" data-name="{{ $mutasi->penduduk->nama ?? 'Data' }}" class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md js-confirm-cancel">
                                <i class="fas fa-times mr-2 text-sm"></i>
                                <span class="text-sm font-medium">Cancel</span>
                            </button>
                        @endif
                    @endcan
                </div>
            </div>
            @endforeach
        </div>

        @else
        <!-- Empty State -->
        <div class="bg-white rounded-2xl shadow-lg border-0 p-8 text-center">
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-exchange-alt text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data Mutasi</h3>
                <p class="text-gray-500 mb-6">Mulai tambah data mutasi pertama</p>
                @can('mutasi.create')
                <a href="{{ route('mutasi.data.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Mutasi Pertama
                </a>
                @endcan
            </div>
        </div>
        @endif

        <!-- Pagination -->
        @if($mutasis->hasPages())
        <div class="px-4 py-4">
            {{ $mutasis->appends(request()->query())->links() }}
        </div>
        @endif

        <!-- Desktop Table View -->
        <div class="hidden lg:block bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        @if(request('jenis_mutasi') == 'kematian')
                            <!-- Header untuk Kematian -->
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64">
                                    Penduduk yang Meninggal
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Tanggal Meninggal
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                    Hari
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                    Jam
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                                    Bertempat di
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                                    Penyebab
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Aksi
                                </th>
                            </tr>
                        @elseif(request('jenis_mutasi') == 'kelahiran')
                            <!-- Header untuk Kelahiran -->
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                                    Nama Bayi
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                    Jenis Kelamin
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Tanggal Lahir
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                                    Orang Tua
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    No KK
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Tanggal Mutasi
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Aksi
                                </th>
                            </tr>
                        @elseif(request('jenis_mutasi') == 'pindah_masuk')
                            <!-- Header untuk Pindah Masuk -->
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                                    Nama Lengkap
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    NIK
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                    Jenis Kelamin
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                    Agama
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                                    Alamat
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Tanggal Masuk
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Aksi
                                </th>
                            </tr>
                        @elseif(request('jenis_mutasi') == 'pindah_rt_rw')
                            <!-- Header untuk Pindah RT/RW -->
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                                    No KK
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    RT Lama
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    RW Lama
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    RT Baru
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    RW Baru
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Tanggal Pindah
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Aksi
                                </th>
                            </tr>
                        @elseif(request('jenis_mutasi') == 'pisah_kk')
                            <!-- Header untuk Pisah KK -->
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                                    Penduduk
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    No KK Lama
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    No KK Baru
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Kategori
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                                    Alamat Tujuan
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Tanggal Pisah
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Aksi
                                </th>
                            </tr>
                        @else
                            <!-- Header Default (Semua Jenis) -->
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64">
                                Penduduk
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                Jenis Mutasi
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                Kategori
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                Tanggal
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                                Alasan
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                Aksi
                            </th>
                        </tr>
                        @endif
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($mutasis as $mutasi)
                        <tr class="hover:bg-gray-50 cursor-pointer transition-colors group js-row-link" data-href="{{ route('mutasi.data.show', $mutasi) }}">
                            @if(request('jenis_mutasi') == 'kematian')
                                <!-- Row untuk Kematian -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center mr-3 group-hover:bg-red-200 transition-colors">
                                            <i class="fas fa-user-times text-red-600"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-medium text-gray-900 group-hover:text-red-900 transition-colors truncate">
                                                @if($mutasi->penduduk)
                                                    {{ $mutasi->penduduk->nama }}
                                                    @if($mutasi->penduduk->trashed())
                                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <i class="fas fa-trash mr-1"></i>
                                                            Terhapus
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-red-600">Data penduduk tidak ditemukan</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 font-mono">{{ $mutasi->penduduk->nik ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $mutasi->tanggal_mutasi->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $mutasi->data_kematian['hari'] ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $mutasi->data_kematian['jam'] ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ Str::limit($mutasi->data_kematian['bertempat_di'] ?? '-', 30) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ Str::limit($mutasi->alasan, 40) }}
                                </td>
                            @elseif(request('jenis_mutasi') == 'kelahiran')
                                <!-- Row untuk Kelahiran -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mr-3 group-hover:bg-green-200 transition-colors">
                                            <i class="fas fa-baby text-green-600"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-medium text-gray-900 group-hover:text-green-900 transition-colors truncate">
                                                {{ $mutasi->penduduk->nama ?? '-' }}
                                            </div>
                                            <div class="text-xs text-gray-500">Bayi baru</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $mutasi->penduduk->jenis_kelamin ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $mutasi->penduduk->tanggal_lahir ? \Carbon\Carbon::parse($mutasi->penduduk->tanggal_lahir)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div class="text-xs">
                                        <div><strong>Ayah:</strong> {{ Str::limit($mutasi->penduduk->nama_ayah ?? '-', 15) }}</div>
                                        <div><strong>Ibu:</strong> {{ Str::limit($mutasi->penduduk->nama_ibu ?? '-', 15) }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 font-mono">
                                    {{ $mutasi->penduduk->nkk ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $mutasi->tanggal_mutasi->format('d/m/Y') }}
                                </td>
                            @elseif(request('jenis_mutasi') == 'pindah_masuk')
                                <!-- Row untuk Pindah Masuk -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mr-3 group-hover:bg-blue-200 transition-colors">
                                            <i class="fas fa-arrow-right text-blue-600"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-medium text-gray-900 group-hover:text-blue-900 transition-colors truncate">
                                                {{ $mutasi->penduduk->nama ?? '-' }}
                                            </div>
                                            <div class="text-xs text-gray-500">Pindah masuk</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 font-mono">
                                    {{ $mutasi->penduduk->nik ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $mutasi->penduduk->jenis_kelamin ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $mutasi->penduduk->agama ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ Str::limit($mutasi->penduduk->alamat ?? '-', 30) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $mutasi->tanggal_mutasi->format('d/m/Y') }}
                                </td>
                            @elseif(request('jenis_mutasi') == 'pindah_rt_rw')
                                <!-- Row untuk Pindah RT/RW -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center mr-3 group-hover:bg-purple-200 transition-colors">
                                            <i class="fas fa-exchange-alt text-purple-600"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-medium text-gray-900 group-hover:text-purple-900 transition-colors truncate">
                                                {{ $mutasi->penduduk->nkk ?? '-' }}
                                            </div>
                                            <div class="text-xs text-gray-500">Pindah RT/RW</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @if(strpos($mutasi->asal_tujuan ?? '', ' → ') !== false)
                                        @php
                                            $parts = explode(' → ', $mutasi->asal_tujuan);
                                            $asal = $parts[0] ?? '';

                                            // Extract RT from asal (RT 005/RW 002 (Dusun 2))
                                            preg_match('/RT (\d+)/', $asal, $rtAsal);
                                            $rtLama = $rtAsal[1] ?? '-';
                                        @endphp
                                        {{ $rtLama }}
                                    @else
                                        {{ $mutasi->penduduk->rt ?? '-' }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @if(strpos($mutasi->asal_tujuan ?? '', ' → ') !== false)
                                        @php
                                            $parts = explode(' → ', $mutasi->asal_tujuan);
                                            $asal = $parts[0] ?? '';

                                            // Extract RW from asal (RT 005/RW 002 (Dusun 2))
                                            preg_match('/RW (\d+)/', $asal, $rwAsal);
                                            $rwLama = $rwAsal[1] ?? '-';
                                        @endphp
                                        {{ $rwLama }}
                                    @else
                                        {{ $mutasi->penduduk->rw ?? '-' }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @if(strpos($mutasi->asal_tujuan ?? '', ' → ') !== false)
                                        @php
                                            $parts = explode(' → ', $mutasi->asal_tujuan);
                                            $tujuan = $parts[1] ?? '';

                                            // Extract RT from tujuan (RT 008/RW 003 (Dusun 1))
                                            preg_match('/RT (\d+)/', $tujuan, $rtTujuan);
                                            $rtBaru = $rtTujuan[1] ?? '-';
                                        @endphp
                                        {{ $rtBaru }}
                                    @else
                                        {{ $mutasi->penduduk->rt ?? '-' }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @if(strpos($mutasi->asal_tujuan ?? '', ' → ') !== false)
                                        @php
                                            $parts = explode(' → ', $mutasi->asal_tujuan);
                                            $tujuan = $parts[1] ?? '';

                                            // Extract RW from tujuan (RT 008/RW 003 (Dusun 1))
                                            preg_match('/RW (\d+)/', $tujuan, $rwTujuan);
                                            $rwBaru = $rwTujuan[1] ?? '-';
                                        @endphp
                                        {{ $rwBaru }}
                                    @else
                                        {{ $mutasi->penduduk->rw ?? '-' }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $mutasi->tanggal_mutasi->format('d/m/Y') }}
                                </td>
                            @elseif(request('jenis_mutasi') == 'pisah_kk')
                                <!-- Row untuk Pisah KK -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center mr-3 group-hover:bg-indigo-200 transition-colors">
                                            <i class="fas fa-users text-indigo-600"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-medium text-gray-900 group-hover:text-indigo-900 transition-colors truncate">
                                                @if($mutasi->penduduk)
                                                    {{ $mutasi->penduduk->nama }}
                                                    @if($mutasi->penduduk->trashed())
                                                        <span class="text-red-500">(Soft Deleted)</span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500">Pisah KK</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 font-mono">
                                    {{ $mutasi->penduduk->nkk ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 font-mono">
                                    {{ $mutasi->penduduk->nkk ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ ucfirst(str_replace('_', ' ', $mutasi->kategori_mutasi)) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ Str::limit($mutasi->asal_tujuan ?? '-', 30) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $mutasi->tanggal_mutasi->format('d/m/Y') }}
                                </td>
                            @else
                                <!-- Row Default (Semua Jenis) -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mr-3 group-hover:bg-blue-200 transition-colors">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-sm font-medium text-gray-900 group-hover:text-blue-900 transition-colors truncate">
                                            @if($mutasi->penduduk)
                                                {{ $mutasi->penduduk->nama }}
                                                @if($mutasi->penduduk->trashed())
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <i class="fas fa-trash mr-1"></i>
                                                        Terhapus
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-red-600">Data penduduk tidak ditemukan</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 font-mono">{{ $mutasi->penduduk->nik ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $jenisColors = [
                                        'kelahiran' => 'bg-green-100 text-green-800',
                                        'kematian' => 'bg-red-100 text-red-800',
                                        'pindah_masuk' => 'bg-blue-100 text-blue-800',
                                        'pindah_keluar' => 'bg-yellow-100 text-yellow-800',
                                        'pindah_rt_rw' => 'bg-purple-100 text-purple-800',
                                        'pisah_kk' => 'bg-indigo-100 text-indigo-800'
                                    ];
                                    $jenisIcons = [
                                        'kelahiran' => 'fa-baby',
                                            'kematian' => 'fa-user-times',
                                        'pindah_masuk' => 'fa-arrow-right',
                                        'pindah_keluar' => 'fa-arrow-left',
                                        'pindah_rt_rw' => 'fa-exchange-alt',
                                        'pisah_kk' => 'fa-users'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $jenisColors[$mutasi->jenis_mutasi] ?? 'bg-gray-100 text-gray-800' }}">
                                    <i class="fas {{ $jenisIcons[$mutasi->jenis_mutasi] ?? 'fa-question' }} mr-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $mutasi->jenis_mutasi)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $mutasi->kategori_mutasi)) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                    <div>
                                {{ $mutasi->tanggal_mutasi->format('d/m/Y') }}
                                        @if($mutasi->jenis_mutasi == 'kematian' && ($mutasi->data_kematian['hari'] ?? false))
                                            <br><span class="text-xs text-gray-500">{{ $mutasi->data_kematian['hari'] }}</span>
                                        @endif
                                        @if($mutasi->jenis_mutasi == 'kematian' && ($mutasi->data_kematian['jam'] ?? false))
                                            <br><span class="text-xs text-gray-500">{{ $mutasi->data_kematian['jam'] }}</span>
                                        @endif
                                    </div>
                            </td>
                            <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">
                                        @if($mutasi->jenis_mutasi == 'kematian' && ($mutasi->data_kematian['bertempat_di'] ?? false))
                                            <div class="text-xs text-gray-500 mb-1">{{ Str::limit($mutasi->data_kematian['bertempat_di'], 30) }}</div>
                                        @endif
                                        {{ Str::limit($mutasi->alasan, 40) }}
                                    </div>
                            </td>
                            @endif
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-1">
                                    @if($mutasi->jenis_mutasi == 'kematian')
                                    <a href="{{ route('mutasi.print-kematian', $mutasi) }}" target="_blank"
                                       class="flex items-center px-2 py-1 bg-red-50 hover:bg-red-100 text-red-700 rounded-md transition-colors text-xs font-medium js-stop-row"
                                       title="Cetak Surat Kematian">
                                        <i class="fas fa-print mr-1"></i>
                                        Surat
                                    </a>
                                    @endif
                                    <a href="{{ route('mutasi.data.show', $mutasi) }}"
                                       class="flex items-center px-2 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-md transition-colors text-xs font-medium js-stop-row"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye mr-1"></i>
                                        Detail
                                    </a>
                                    @can('mutasi.edit')
                                    <a href="{{ route('mutasi.data.edit', $mutasi) }}"
                                       class="flex items-center px-2 py-1 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-md transition-colors text-xs font-medium js-stop-row"
                                       title="Edit Data">
                                        <i class="fas fa-edit mr-1"></i>
                                        Edit
                                    </a>
                                    @endcan
                                    @can('mutasi.delete')
                                        @if($mutasi->isSoftDeleteType())
                                            <button type="button" data-id="{{ $mutasi->id }}" data-name="{{ $mutasi->penduduk->nama ?? 'Data' }}"
                                                    class="flex items-center px-2 py-1 bg-green-50 hover:bg-green-100 text-green-700 rounded-md transition-colors text-xs font-medium js-confirm-undo js-stop-row"
                                                    title="Kembalikan Data">
                                                <i class="fas fa-undo mr-1"></i>
                                                Undo
                                            </button>
                                        @else
                                            <button type="button" data-id="{{ $mutasi->id }}" data-name="{{ $mutasi->penduduk->nama ?? 'Data' }}"
                                            class="flex items-center px-2 py-1 bg-red-50 hover:bg-red-100 text-red-700 rounded-md transition-colors text-xs font-medium js-confirm-cancel js-stop-row"
                                                    title="Batalkan Mutasi">
                                                <i class="fas fa-times mr-1"></i>
                                                Cancel
                                    </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-6 border-t border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
                {{ $mutasis->appends(request()->query())->links('vendor.pagination.tailwind') }}
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-row-link').forEach((row) => {
        row.addEventListener('click', function () {
            const href = row.dataset.href;
            if (href) window.location.href = href;
        });
    });

    document.querySelectorAll('.js-stop-row').forEach((el) => {
        el.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    });

    document.querySelectorAll('.js-confirm-undo').forEach((btn) => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            confirmUndo(btn.dataset.id, btn.dataset.name || 'Data');
        });
    });

    document.querySelectorAll('.js-confirm-cancel').forEach((btn) => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            confirmCancel(btn.dataset.id, btn.dataset.name || 'Data');
        });
    });
});

// SweetAlert untuk konfirmasi cancel
function confirmCancel(id, name) {
    Swal.fire({
        title: 'Konfirmasi Cancel',
        text: `Apakah Anda yakin ingin membatalkan mutasi ${name}? Log mutasi akan dihapus.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Cancel!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Membatalkan...',
                text: 'Sedang membatalkan mutasi',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Submit cancel request
            fetch(`/mutasi/cancel/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                throw new Error('Network response was not ok');
            })
            .then(data => {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Mutasi berhasil dibatalkan.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Reload page to show updated data
                    window.location.reload();
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat membatalkan mutasi.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

// SweetAlert untuk konfirmasi undo
function confirmUndo(id, name) {
    Swal.fire({
        title: 'Konfirmasi Undo',
        text: `Apakah Anda yakin ingin mengembalikan data mutasi ${name}? Data penduduk akan dikembalikan ke database.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Undo!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form undo
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/mutasi/undo/${id}`;

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';

            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection


