@extends('layouts.app')

@section('title', 'History Pembuatan Surat')
@section('subtitle', 'Daftar surat yang telah dibuat')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl border-0 p-6 sm:p-8 mb-8">
        <div class="flex flex-col lg:flex-row items-center justify-between">
            <div class="flex-1 text-center lg:text-left mb-6 lg:mb-0">
                <div class="flex items-center justify-center lg:justify-start mb-4">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mr-4">
                        <i class="fas fa-history text-yellow-300 text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-2">
                            History Pembuatan Surat
                        </h1>
                        <p class="text-green-100 text-sm sm:text-base">Daftar surat yang telah dibuat dan disimpan</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-3 justify-center lg:justify-start">
            <a href="{{ route('admin.surat-pengajuan.create') }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                <i class="fas fa-plus mr-2"></i>
                Buat Surat Baru
            </a>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-2xl shadow-lg border-0 p-6 sm:p-8 mb-8">
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-filter text-green-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Filter & Pencarian</h3>
                <p class="text-sm text-gray-600">Saring data surat berdasarkan kriteria</p>
            </div>
        </div>
                <form method="GET" action="{{ route('admin.surat-pengajuan.history') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="Cari nama penduduk, nomor surat..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="jenis_surat" class="block text-sm font-medium text-gray-700 mb-2">Jenis Surat</label>
                        <select name="jenis_surat" id="jenis_surat"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Jenis</option>
                            <option value="sku" {{ request('jenis_surat') == 'sku' ? 'selected' : '' }}>Surat Keterangan Usaha</option>
                            <option value="sktm_dewasa" {{ request('jenis_surat') == 'sktm_dewasa' ? 'selected' : '' }}>SKTM Dewasa</option>
                            <option value="sktm_anak" {{ request('jenis_surat') == 'sktm_anak' ? 'selected' : '' }}>SKTM Anak</option>
                            <option value="domisili" {{ request('jenis_surat') == 'domisili' ? 'selected' : '' }}>Domisili</option>
                            <option value="keterangan-domisili" {{ request('jenis_surat') == 'keterangan-domisili' ? 'selected' : '' }}>Keterangan Domisili</option>
                            <option value="pengantar" {{ request('jenis_surat') == 'pengantar' ? 'selected' : '' }}>Surat Pengantar</option>
                            <option value="pindah" {{ request('jenis_surat') == 'pindah' ? 'selected' : '' }}>Keterangan Pindah</option>
                            <option value="kematian" {{ request('jenis_surat') == 'kematian' ? 'selected' : '' }}>Keterangan Kematian</option>
                            <option value="kelahiran" {{ request('jenis_surat') == 'kelahiran' ? 'selected' : '' }}>Keterangan Kelahiran</option>
                            <option value="tidak-mampu-dewasa" {{ request('jenis_surat') == 'tidak-mampu-dewasa' ? 'selected' : '' }}>Tidak Mampu (Dewasa)</option>
                            <option value="tidak-mampu-anak" {{ request('jenis_surat') == 'tidak-mampu-anak' ? 'selected' : '' }}>Tidak Mampu (Anak)</option>
                        </select>
                    </div>
                    <div>
                        <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                        <select name="tahun" id="tahun"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Tahun</option>
                            @for($year = date('Y'); $year >= 2020; $year--)
                                <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                                class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-search mr-2"></i>
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-file-alt text-blue-600 text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Total Surat</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($paginatedSurats->total()) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-day text-green-600 text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Hari Ini</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($paginatedSurats->filter(function($surat) { return $surat->created_at >= today(); })->count()) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-week text-yellow-600 text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Minggu Ini</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($paginatedSurats->filter(function($surat) { return $surat->created_at >= now()->startOfWeek(); })->count()) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar text-purple-600 text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Tahun {{ request('tahun', date('Y')) }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($paginatedSurats->filter(function($surat) { return $surat->created_at->year == request('tahun', date('Y')); })->count()) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Surat List -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                @if($paginatedSurats->count() > 0)
                    <!-- Desktop Table View -->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nomor Surat
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jenis Surat
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Penduduk
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal Dibuat
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Dibuat Oleh
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Sumber
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($paginatedSurats as $index => $surat)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $paginatedSurats->firstItem() + $index }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $surat->nomor_surat }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($surat->jenis_surat == 'sku') bg-orange-100 text-orange-800
                                                @elseif($surat->jenis_surat == 'sktm_dewasa') bg-indigo-100 text-indigo-800
                                                @elseif($surat->jenis_surat == 'sktm_anak') bg-pink-100 text-pink-800
                                                @elseif($surat->jenis_surat == 'domisili') bg-blue-100 text-blue-800
                                                @elseif($surat->jenis_surat == 'keterangan-domisili') bg-blue-100 text-blue-800
                                                @elseif($surat->jenis_surat == 'pengantar') bg-green-100 text-green-800
                                                @elseif($surat->jenis_surat == 'pindah') bg-yellow-100 text-yellow-800
                                                @elseif($surat->jenis_surat == 'kematian') bg-red-100 text-red-800
                                                @elseif($surat->jenis_surat == 'kelahiran') bg-purple-100 text-purple-800
                                                @elseif($surat->jenis_surat == 'tidak-mampu-dewasa') bg-indigo-100 text-indigo-800
                                                @elseif($surat->jenis_surat == 'tidak-mampu-anak') bg-pink-100 text-pink-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucwords(str_replace('-', ' ', $surat->jenis_surat)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $surat->penduduk->nama }}</div>
                                            <div class="text-sm text-gray-500">{{ $surat->penduduk->nik }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $surat->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $surat->creator->name ?? 'System' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($surat->source == 'pengajuan')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <i class="fas fa-file-alt mr-1"></i>
                                                    Pengajuan
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-plus mr-1"></i>
                                                    Langsung
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                @if($surat->source == 'pengajuan')
                                                    <a href="{{ route('admin.surat-pengajuan.show', $surat->pengajuan_id) }}"
                                                       class="text-blue-600 hover:text-blue-900"
                                                       title="Lihat Detail Pengajuan">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.surat-pengajuan.pdf', $surat->pengajuan_id) }}"
                                                       class="text-green-600 hover:text-green-900"
                                                       title="Download PDF">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.surat-pengajuan.download-legacy', $surat->surat_id) }}?preview=1"
                                                       target="_blank"
                                                       class="text-blue-600 hover:text-blue-900"
                                                       title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.surat-pengajuan.edit', $surat->surat_id) }}"
                                                       class="text-yellow-600 hover:text-yellow-900"
                                                       title="Edit Surat">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('admin.surat-pengajuan.download-legacy', $surat->surat_id) }}"
                                                       class="text-green-600 hover:text-green-900"
                                                       title="Download PDF">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <a href="{{ route('admin.surat-pengajuan.download-legacy', $surat->surat_id) }}?print=1"
                                                       class="text-purple-600 hover:text-purple-900"
                                                       title="Print Surat"
                                                       target="_blank">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                    @can('surat.delete')
                                                        <button onclick="confirmDelete('{{ $surat->surat_id }}', '{{ $surat->nomor_surat }}')"
                                                                class="text-red-600 hover:text-red-900"
                                                                title="Hapus Surat">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endcan
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $paginatedSurats->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-file-alt text-gray-400 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Surat</h3>
                        <p class="text-gray-500 mb-6">Belum ada surat yang dibuat dan disimpan.</p>
                        <a href="{{ route('admin.surat-pengajuan.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Buat Surat Pertama
                        </a>
                    </div>
                @endif
            </div>

            <!-- Mobile Card View -->
            @if($paginatedSurats->count() > 0)
            <div class="lg:hidden px-1 sm:px-2 py-4 space-y-3">
                @foreach($paginatedSurats as $index => $surat)
                <div class="bg-white rounded-2xl shadow-lg border-0 p-4 sm:p-6 hover:shadow-xl transition-all duration-300 w-full">
                    <!-- Header with Nomor Surat and Status -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3 sm:space-x-4">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-file-alt text-white text-lg sm:text-xl"></i>
                            </div>
                            <div class="flex-1 min-w-0 overflow-hidden">
                                <h3 class="text-base sm:text-lg font-bold text-gray-900 truncate" title="{{ $surat->nomor_surat }}">{{ $surat->nomor_surat }}</h3>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if($surat->jenis_surat == 'sku') bg-orange-100 text-orange-800
                                        @elseif($surat->jenis_surat == 'sktm_dewasa') bg-indigo-100 text-indigo-800
                                        @elseif($surat->jenis_surat == 'sktm_anak') bg-pink-100 text-pink-800
                                        @elseif($surat->jenis_surat == 'domisili') bg-blue-100 text-blue-800
                                        @elseif($surat->jenis_surat == 'keterangan-domisili') bg-blue-100 text-blue-800
                                        @elseif($surat->jenis_surat == 'pengantar') bg-green-100 text-green-800
                                        @elseif($surat->jenis_surat == 'pindah') bg-yellow-100 text-yellow-800
                                        @elseif($surat->jenis_surat == 'kematian') bg-red-100 text-red-800
                                        @elseif($surat->jenis_surat == 'kelahiran') bg-purple-100 text-purple-800
                                        @elseif($surat->jenis_surat == 'tidak-mampu-dewasa') bg-indigo-100 text-indigo-800
                                        @elseif($surat->jenis_surat == 'tidak-mampu-anak') bg-pink-100 text-pink-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucwords(str_replace('-', ' ', $surat->jenis_surat)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Information Grid - Mobile Optimized -->
                    <div class="space-y-3 mb-4">
                        <!-- Penduduk Info -->
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-3">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-user text-blue-500 mr-2 text-sm"></i>
                                <span class="text-xs sm:text-sm font-medium text-gray-700">Penduduk</span>
                            </div>
                            <p class="text-sm text-gray-900 font-medium">{{ $surat->penduduk->nama }}</p>
                            <p class="text-xs text-gray-600">{{ $surat->penduduk->nik }}</p>
                        </div>

                        <!-- Tanggal dan Dibuat Oleh -->
                        <div class="grid grid-cols-2 gap-2">
                            <!-- Tanggal -->
                            <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-2.5">
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-calendar text-purple-500 mr-1 text-xs"></i>
                                    <span class="text-xs font-medium text-gray-700">Tanggal</span>
                                </div>
                                <p class="text-xs text-gray-900">{{ $surat->created_at->format('d/m/Y H:i') }}</p>
                            </div>

                            <!-- Dibuat Oleh -->
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-2.5">
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-user-tie text-gray-500 mr-1 text-xs"></i>
                                    <span class="text-xs font-medium text-gray-700">Dibuat Oleh</span>
                                </div>
                                <p class="text-xs text-gray-900">{{ $surat->creator->name ?? 'System' }}</p>
                            </div>
                        </div>

                        <!-- Sumber -->
                        <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-xl p-3">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-tag text-yellow-500 mr-2 text-sm"></i>
                                <span class="text-xs sm:text-sm font-medium text-gray-700">Sumber</span>
                            </div>
                            @if($surat->source == 'pengajuan')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-file-alt mr-1"></i>
                                    Pengajuan
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-plus mr-1"></i>
                                    Langsung
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons - Mobile Optimized -->
                    <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:gap-2">
                        <!-- Detail & Download Row -->
                        <div class="flex gap-2">
                            @if($surat->source == 'pengajuan')
                                <a href="{{ route('admin.surat-pengajuan.show', $surat->pengajuan_id) }}" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                                    <i class="fas fa-eye mr-2 text-sm"></i>
                                    <span class="text-sm font-medium">Detail</span>
                                </a>
                                <a href="{{ route('admin.surat-pengajuan.pdf', $surat->pengajuan_id) }}" class="flex-1 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                                    <i class="fas fa-download mr-2 text-sm"></i>
                                    <span class="text-sm font-medium">PDF</span>
                                </a>
                            @else
                                <a href="{{ route('admin.surat-pengajuan.download-legacy', $surat->surat_id) }}?preview=1" target="_blank" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                                    <i class="fas fa-eye mr-2 text-sm"></i>
                                    <span class="text-sm font-medium">Detail</span>
                                </a>
                                <a href="{{ route('admin.surat-pengajuan.edit', $surat->surat_id) }}" class="flex-1 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                                    <i class="fas fa-edit mr-2 text-sm"></i>
                                    <span class="text-sm font-medium">Edit</span>
                                </a>
                                <a href="{{ route('admin.surat-pengajuan.download-legacy', $surat->surat_id) }}" class="flex-1 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                                    <i class="fas fa-download mr-2 text-sm"></i>
                                    <span class="text-sm font-medium">PDF</span>
                                </a>
                            @endif
                        </div>

                        <!-- Print & Delete Row -->
                        <div class="flex gap-2">
                            @if($surat->source != 'pengajuan')
                                <a href="{{ route('admin.surat-pengajuan.download-legacy', $surat->surat_id) }}?print=1" target="_blank" class="flex-1 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                                    <i class="fas fa-print mr-2 text-sm"></i>
                                    <span class="text-sm font-medium">Print</span>
                                </a>
                                @can('surat.delete')
                                <button onclick="confirmDelete('{{ $surat->surat_id }}', '{{ $surat->nomor_surat }}')" class="flex-1 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                                    <i class="fas fa-trash mr-2 text-sm"></i>
                                    <span class="text-sm font-medium">Hapus</span>
                                </button>
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <!-- Hidden Delete Form -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@noncescript
function confirmDelete(suratId, nomorSurat) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        html: `Apakah Anda yakin ingin menghapus surat <strong>${nomorSurat}</strong>?<br><br><span class="text-red-600">Tindakan ini tidak dapat dibatalkan!</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Set form action and submit
            const form = document.getElementById('deleteForm');
            form.action = `/admin/surat-pengajuan/legacy/${suratId}`;
            form.submit();
        }
    });
}
@endnoncescript
@endpush

