@extends('layouts.app')

@section('title', 'Data Penduduk')
@section('subtitle', 'Kelola data penduduk desa Cibatu')

@section('content')
<div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-yellow-300 text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white">Data Penduduk</h1>
                        <p class="text-green-100 mt-1">Kelola data penduduk desa Cibatu</p>
                        <p class="text-green-200 text-sm mt-1">
                            <i class="fas fa-database mr-1"></i>
                            Total: {{ $penduduks->total() }} penduduk
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <!-- Action buttons can be added here if needed -->
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 mb-6">
            @can('penduduk.create')
            <a href="{{ route('penduduk.create') }}" class="group flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                    <i class="fas fa-plus text-white text-sm"></i>
                </div>
                <div class="text-left leading-tight">
                    <p class="font-semibold text-sm sm:text-base">Tambah Penduduk</p>
                    <p class="text-blue-100 text-xs sm:text-sm">Input data penduduk baru</p>
                </div>
            </a>
            @endcan

            @can('penduduk.export')
            <button onclick="exportExcel()"
               class="group flex items-center justify-center px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                    <i class="fas fa-file-excel text-white text-sm"></i>
                </div>
                <div class="text-left leading-tight">
                    <p class="font-semibold text-sm sm:text-base">Export Excel</p>
                    <p class="text-green-100 text-xs sm:text-sm">Download data Penduduk</p>
                </div>
            </button>
            @endcan
        </div>
        <!-- Filter Card -->
        @php
            $activeFilterCount = collect([
                request('search'),
                request('rt_id') ?? request('rt'),
                request('rw_id') ?? request('rw'),
                request('jenis_kelamin'),
                request('filter_umur'),
            ])->filter(fn($v) => filled($v))->count();
        @endphp

        <div class="bg-white rounded-2xl shadow-lg border-0 p-4 sm:p-6 mb-6">
            <div class="flex items-center justify-between gap-3 mb-4">
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-filter text-green-500 mr-2"></i>
                    Filter & Pencarian
                </h3>

                <button type="button" id="toggleFilterBtn" class="sm:hidden inline-flex items-center px-3 py-2 text-sm font-semibold rounded-xl bg-green-50 text-green-700 border border-green-200">
                    <i class="fas fa-sliders-h mr-2"></i>
                    <span id="toggleFilterText">Tampilkan</span>
                    @if($activeFilterCount > 0)
                        <span class="ml-2 inline-flex items-center justify-center min-w-[1.5rem] h-6 px-2 rounded-full bg-green-600 text-white text-xs">{{ $activeFilterCount }}</span>
                    @endif
                </button>
            </div>

            <form method="GET" action="{{ route('penduduk.index') }}" class="space-y-4" id="filterForm">
                <div id="filterPanel" class="hidden sm:block space-y-4">
                    <!-- Search Input -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-search text-green-500 mr-2"></i>
                            Pencarian Data
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari NIK, nama, No KK..."
                                   class="w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm bg-white">
                        </div>
                    </div>

                    <!-- Filter Options -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <!-- RT Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">RT</label>
                            <select name="rt_id" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white">
                                <option value="">Semua RT</option>
                                @foreach($rtList as $rt)
                                    <option value="{{ $rt->id }}" {{ (request('rt_id') ?? request('rt')) == $rt->id ? 'selected' : '' }}>RT {{ $rt->kode }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- RW Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">RW</label>
                            <select name="rw_id" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                                <option value="">Semua RW</option>
                                @foreach($rwList as $rw)
                                    <option value="{{ $rw->id }}" {{ (request('rw_id') ?? request('rw')) == $rw->id ? 'selected' : '' }}>RW {{ $rw->kode }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jenis Kelamin Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-sm bg-white">
                                <option value="">Semua</option>
                                <option value="LAKI-LAKI" {{ request('jenis_kelamin') == 'LAKI-LAKI' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="PEREMPUAN" {{ request('jenis_kelamin') == 'PEREMPUAN' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <!-- Filter Umur -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter Umur</label>
                            <select name="filter_umur" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                                <option value="">Semua Umur</option>
                                <optgroup label="Kategori Umur">
                                    <option value="bayi" {{ request('filter_umur') == 'bayi' ? 'selected' : '' }}>Bayi (0-1 tahun)</option>
                                    <option value="balita" {{ request('filter_umur') == 'balita' ? 'selected' : '' }}>Balita (2-5 tahun)</option>
                                    <option value="anak" {{ request('filter_umur') == 'anak' ? 'selected' : '' }}>Anak (6-12 tahun)</option>
                                    <option value="remaja" {{ request('filter_umur') == 'remaja' ? 'selected' : '' }}>Remaja (13-17 tahun)</option>
                                    <option value="dewasa_muda" {{ request('filter_umur') == 'dewasa_muda' ? 'selected' : '' }}>Dewasa Muda (18-25 tahun)</option>
                                    <option value="dewasa" {{ request('filter_umur') == 'dewasa' ? 'selected' : '' }}>Dewasa (26-59 tahun)</option>
                                    <option value="lansia" {{ request('filter_umur') == 'lansia' ? 'selected' : '' }}>Lansia (=60 tahun)</option>
                                </optgroup>
                                <optgroup label="Filter Numerik">
                                    <option value="umur_20_keatas" {{ request('filter_umur') == 'umur_20_keatas' ? 'selected' : '' }}>=20 tahun</option>
                                    <option value="umur_20_kebawah" {{ request('filter_umur') == 'umur_20_kebawah' ? 'selected' : '' }}><20 tahun</option>
                                    <option value="umur_40_keatas" {{ request('filter_umur') == 'umur_40_keatas' ? 'selected' : '' }}>=40 tahun</option>
                                    <option value="umur_40_kebawah" {{ request('filter_umur') == 'umur_40_kebawah' ? 'selected' : '' }}><40 tahun</option>
                                    <option value="umur_60_keatas" {{ request('filter_umur') == 'umur_60_keatas' ? 'selected' : '' }}>=60 tahun</option>
                                    <option value="umur_60_kebawah" {{ request('filter_umur') == 'umur_60_kebawah' ? 'selected' : '' }}><60 tahun</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-2 sm:justify-end">
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl shadow-sm transition-colors text-sm font-semibold">
                            <i class="fas fa-search mr-2"></i>
                            Terapkan
                        </button>
                        <a href="{{ route('penduduk.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-600 hover:bg-gray-700 text-white rounded-xl shadow-sm transition-colors text-sm font-semibold">
                            <i class="fas fa-refresh mr-2"></i>
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
        <div class="px-6 py-6 border-b border-gray-100 bg-gradient-to-r from-green-50 to-blue-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-list text-green-500 mr-3"></i>
                        Daftar Penduduk
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Total {{ $penduduks->total() }} data penduduk</p>
                </div>
                <div class="hidden sm:block w-8 h-1 bg-gradient-to-r from-green-500 to-green-600 rounded-full"></div>
            </div>
        </div>

        @if($penduduks->count() > 0)
            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                                Nama & Kedudukan
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                NIK
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                No KK & Kepala Keluarga
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                JK & Usia
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-80">
                                Alamat & RT/RW
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                Pekerjaan
                            </th>
                            <th class="px-4 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $currentKK = null; @endphp
                        @foreach($penduduks as $index => $penduduk)
                            @php
                                $isNewFamily = $currentKK !== $penduduk->nkk;
                                $currentKK = $penduduk->nkk;

                                $kedudukanColors = [
                                    'Kepala Keluarga' => 'bg-blue-100 text-blue-800',
                                    'Istri' => 'bg-pink-100 text-pink-800',
                                    'Anak' => 'bg-green-100 text-green-800',
                                    'Menantu' => 'bg-yellow-100 text-yellow-800',
                                    'Cucu' => 'bg-purple-100 text-purple-800',
                                    'Orang Tua' => 'bg-orange-100 text-orange-800',
                                    'Famili Lain' => 'bg-gray-100 text-gray-800',
                                    'Lainnya' => 'bg-gray-100 text-gray-800'
                                ];
                                $kedudukanIcons = [
                                    'Kepala Keluarga' => 'fa-crown',
                                    'Istri' => 'fa-heart',
                                    'Anak' => 'fa-child',
                                    'Menantu' => 'fa-user-friends',
                                    'Cucu' => 'fa-baby',
                                    'Orang Tua' => 'fa-user-clock',
                                    'Famili Lain' => 'fa-users',
                                    'Lainnya' => 'fa-user'
                                ];
                            @endphp

                            @if($isNewFamily && $index > 0)
                                <!-- Family Separator -->
                                <tr>
                                    <td colspan="7" class="px-0 py-0">
                                        <div class="h-2 bg-gradient-to-r from-green-100 to-blue-100"></div>
                                    </td>
                                </tr>
                            @endif

                            @php
                                $rowClass = $isNewFamily ? 'bg-green-50' : (strtoupper($penduduk->kedudukan_keluarga) === 'KEPALA KELUARGA' ? 'bg-blue-50' : 'bg-white');
                            @endphp
                            <tr class="{{ $rowClass }} hover:bg-gray-100 cursor-pointer transition-colors group" onclick="window.location='{{ route('penduduk.show', $penduduk) }}'">
                                <!-- Nama & Kedudukan -->
                                <td class="px-4 py-4">
                                    <div class="flex items-center space-x-3">
                                        @if(strtoupper($penduduk->kedudukan_keluarga) === 'KEPALA KELUARGA')
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                                <i class="fas fa-crown text-white text-sm"></i>
                                            </div>
                                        @elseif(strtoupper($penduduk->kedudukan_keluarga) === 'ISTRI')
                                            <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                                                <i class="fas fa-heart text-white text-sm"></i>
                                            </div>
                                        @elseif(strtoupper($penduduk->kedudukan_keluarga) === 'ANAK')
                                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                                                <i class="fas fa-child text-white text-sm"></i>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center shadow-lg">
                                                <i class="fas fa-user text-white text-sm"></i>
                                            </div>
                                        @endif
                                        <div class="min-w-0 flex-1 overflow-hidden">
                                            <div class="text-sm font-bold text-gray-900 truncate" title="{{ $penduduk->nama }}">{{ $penduduk->nama }}</div>
                                            @if(strtoupper($penduduk->kedudukan_keluarga) === 'KEPALA KELUARGA')
                                                <div class="text-xs text-green-600 font-medium">Kepala Keluarga</div>
                                            @endif
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $kedudukanColors[$penduduk->kedudukan_keluarga] ?? 'bg-gray-100 text-gray-800' }}">
                                                <i class="fas {{ $kedudukanIcons[$penduduk->kedudukan_keluarga] ?? 'fa-user' }} mr-1"></i>
                                                {{ $penduduk->kedudukan_keluarga }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- NIK -->
                                <td class="px-4 py-4">
                                    <div class="text-sm font-mono text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $penduduk->nik }}</div>
                                </td>

                                <!-- No KK & Kepala Keluarga -->
                                <td class="px-4 py-4">
                                    <div class="space-y-2">
                                        <div class="font-mono text-sm text-gray-900 bg-green-50 px-3 py-2 rounded-lg">{{ $penduduk->nkk }}</div>
                                        @php
                                            // Get kepala keluarga from database
                                            $kepalaKeluarga = null;

                                            // Check if current penduduk is kepala keluarga (case insensitive)
                                            if (strtoupper($penduduk->kedudukan_keluarga) === 'KEPALA KELUARGA') {
                                                $kepalaKeluarga = $penduduk;
                                            } else {
                                                // Find kepala keluarga with same NKK from database
                                                $kepalaKeluarga = \App\Models\Penduduk::where('nkk', $penduduk->nkk)
                                                    ->where(function($query) {
                                                        $query->where('kedudukan_keluarga', 'KEPALA KELUARGA')
                                                              ->orWhere('kedudukan_keluarga', 'Kepala Keluarga')
                                                              ->orWhere('kedudukan_keluarga', 'kepala keluarga');
                                                    })
                                                    ->first();
                                            }
                                        @endphp
                                        <div class="text-sm font-medium text-gray-700">{{ $kepalaKeluarga ? $kepalaKeluarga->nama : 'Tidak ada kepala keluarga' }}</div>
                                    </div>
                                </td>

                                <!-- JK & Usia -->
                                <td class="px-4 py-4">
                                    <div class="space-y-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $penduduk->jenis_kelamin == 'LAKI-LAKI' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                            <i class="fas {{ $penduduk->jenis_kelamin == 'LAKI-LAKI' ? 'fa-male' : 'fa-female' }} mr-1"></i>
                                            {{ $penduduk->jenis_kelamin == 'LAKI-LAKI' ? 'L' : 'P' }}
                                        </span>
                                        <div class="text-sm font-bold text-gray-900">{{ $penduduk->usia }} th</div>
                                    </div>
                                </td>

                                <!-- Alamat & RT/RW -->
                                <td class="px-4 py-4">
                                    <div class="text-sm text-gray-900">
                                        <div class="font-medium">{{ Str::limit($penduduk->alamat, 50) }}</div>
                                        <div class="text-xs text-gray-600 mt-1">RT {{ $penduduk->rt_label }} / RW {{ $penduduk->rw_label }}</div>
                                        @if($penduduk->dusun_label !== '-')
                                            <div class="text-xs text-gray-500">{{ $penduduk->dusun_label }}</div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Pekerjaan -->
                                <td class="px-4 py-4">
                                    <div class="text-sm text-gray-900 font-medium">{{ $penduduk->pekerjaan ?: '-' }}</div>
                                </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button class="group flex items-center px-3 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105"
                                            onclick="event.stopPropagation(); window.location='{{ route('penduduk.show', $penduduk) }}'"
                                            title="Lihat Detail">
                                        <i class="fas fa-eye text-sm mr-1"></i>
                                        <span class="text-sm font-medium hidden sm:inline">Detail</span>
                                    </button>
                                    @can('penduduk.edit')
                                    <a href="{{ route('penduduk.edit', $penduduk) }}"
                                       class="group flex items-center px-3 py-2 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105"
                                       onclick="event.stopPropagation()"
                                       title="Edit Data">
                                        <i class="fas fa-edit text-sm mr-1"></i>
                                        <span class="text-sm font-medium hidden sm:inline">Edit</span>
                                    </a>
                                    @endcan
                                    <!-- Tombol KK dihapus karena terlalu ribet untuk struktur data saat ini -->
                                    @can('penduduk.delete')
                                    <button onclick="event.stopPropagation(); confirmDelete('{{ $penduduk->id }}', '{{ $penduduk->nama }}')"
                                            class="group flex items-center px-3 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105"
                                            title="Hapus Data">
                                        <i class="fas fa-trash text-sm mr-1"></i>
                                        <span class="text-sm font-medium hidden sm:inline">Hapus</span>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="lg:hidden px-1 sm:px-2 py-4 space-y-3">
                @php $currentKK = null; @endphp
                @foreach($penduduks as $index => $penduduk)
                    @php
                        $isNewFamily = $currentKK !== $penduduk->nkk;
                        $currentKK = $penduduk->nkk;
                        $kedudukanColors = [
                            'KEPALA KELUARGA' => 'bg-green-100 text-green-800',
                            'ISTRI' => 'bg-pink-100 text-pink-800',
                            'ANAK' => 'bg-blue-100 text-blue-800',
                            'MENANTU' => 'bg-purple-100 text-purple-800',
                            'CUCU' => 'bg-yellow-100 text-yellow-800',
                            'ORANGTUA' => 'bg-indigo-100 text-indigo-800',
                            'MERTUA' => 'bg-red-100 text-red-800',
                            'FAMILI LAIN' => 'bg-gray-100 text-gray-800',
                            'PEMBANTU' => 'bg-orange-100 text-orange-800',
                            'LAINNYA' => 'bg-gray-100 text-gray-800'
                        ];
                        $kedudukanColor = $kedudukanColors[$penduduk->kedudukan] ?? 'bg-gray-100 text-gray-800';
                    @endphp

                    <!-- Family Header (if new family) -->
                    @if($isNewFamily)
                        @php
                            // Find kepala keluarga for this family from database
                            $kepalaKeluargaMobile = \App\Models\Penduduk::where('nkk', $penduduk->nkk)
                                ->where(function($query) {
                                    $query->where('kedudukan_keluarga', 'KEPALA KELUARGA')
                                          ->orWhere('kedudukan_keluarga', 'Kepala Keluarga')
                                          ->orWhere('kedudukan_keluarga', 'kepala keluarga');
                                })
                                ->first();
                        @endphp
                        <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-2xl p-4 border-l-4 border-green-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-bold text-gray-900">No KK: {{ $penduduk->nkk }}</h4>
                                    <p class="text-sm text-gray-600">Kepala Keluarga: {{ $kepalaKeluargaMobile ? $kepalaKeluargaMobile->nama : 'Belum ditentukan' }}</p>
                                </div>
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-home text-white text-lg"></i>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Person Card -->
                    <div class="bg-white rounded-2xl shadow-lg border-0 p-4 sm:p-6 hover:shadow-xl transition-all duration-300 w-full">
                        <!-- Header with Avatar and Basic Info -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3 sm:space-x-4">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-user text-white text-lg sm:text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0 overflow-hidden">
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900 truncate" title="{{ $penduduk->nama }}">{{ $penduduk->nama }}</h3>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $kedudukanColor }}">
                                            <i class="fas fa-user-tag mr-1"></i>
                                            {{ $penduduk->kedudukan }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $penduduk->jenis_kelamin == 'LAKI-LAKI' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                            <i class="fas {{ $penduduk->jenis_kelamin == 'LAKI-LAKI' ? 'fa-male' : 'fa-female' }} mr-1"></i>
                                            {{ $penduduk->jenis_kelamin == 'LAKI-LAKI' ? 'L' : 'P' }}
                                        </span>
                                        <span class="text-xs font-medium text-gray-600">{{ $penduduk->usia }} th</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Information Grid - Mobile Optimized -->
                        <div class="space-y-2 mb-4">
                            <!-- NIK -->
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-3">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-id-card text-gray-500 mr-2 text-sm"></i>
                                    <span class="text-xs sm:text-sm font-medium text-gray-700">NIK</span>
                                </div>
                                <p class="text-xs sm:text-sm text-gray-900 font-mono break-all truncate" title="{{ $penduduk->nik }}">{{ $penduduk->nik }}</p>
                            </div>

                            <!-- Pekerjaan -->
                            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-3 sm:p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-briefcase text-blue-500 mr-2 text-sm"></i>
                                    <span class="text-xs sm:text-sm font-medium text-gray-700">Pekerjaan</span>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-xs sm:text-sm text-gray-900 truncate" title="{{ $penduduk->pekerjaan ?: '-' }}">{{ $penduduk->pekerjaan ?: '-' }}</p>
                                </div>
                            </div>

                            <!-- Alamat -->
                            <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-3 sm:p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-map-marker-alt text-green-500 mr-2 text-sm"></i>
                                    <span class="text-xs sm:text-sm font-medium text-gray-700">Alamat</span>
                                </div>
                                <div class="overflow-hidden mb-2">
                                    <p class="text-xs sm:text-sm text-gray-900 truncate" title="{{ $penduduk->alamat }}">{{ $penduduk->alamat }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 text-xs text-gray-600">
                                    <span class="inline-flex items-center"><i class="fas fa-home mr-1"></i>RT {{ $penduduk->rt_label }} / RW {{ $penduduk->rw_label }}</span>
                                    @if($penduduk->dusun_label !== '-')
                                        <span class="inline-flex items-center"><i class="fas fa-map mr-1"></i>{{ $penduduk->dusun_label }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Additional Info for Mobile -->
                            <div class="grid grid-cols-2 gap-2">
                                <!-- Status Pernikahan -->
                                <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-3">
                                    <div class="flex items-center mb-1">
                                        <i class="fas fa-heart text-purple-500 mr-1 text-xs"></i>
                                        <span class="text-xs font-medium text-gray-700">Status</span>
                                    </div>
                                    <p class="text-xs text-gray-900">{{ $penduduk->status_perkawinan ?: '-' }}</p>
                                </div>

                                <!-- Agama -->
                                <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 rounded-xl p-3">
                                    <div class="flex items-center mb-1">
                                        <i class="fas fa-pray text-indigo-500 mr-1 text-xs"></i>
                                        <span class="text-xs font-medium text-gray-700">Agama</span>
                                    </div>
                                    <p class="text-xs text-gray-900">{{ $penduduk->agama ?: '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons - Mobile Optimized -->
                        <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:gap-2">
                            <!-- Detail & Edit Row -->
                            <div class="flex gap-2">
                                <a href="{{ route('penduduk.show', $penduduk) }}" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                                    <i class="fas fa-eye mr-2 text-sm"></i>
                                    <span class="text-sm font-medium">Detail</span>
                                </a>
                                @can('penduduk.edit')
                                <a href="{{ route('penduduk.edit', $penduduk) }}" class="flex-1 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                                    <i class="fas fa-edit mr-2 text-sm"></i>
                                    <span class="text-sm font-medium">Edit</span>
                                </a>
                                @endcan
                            </div>

                            <!-- Hapus Button - Full Width -->
                            @can('penduduk.delete')
                                <button onclick="confirmDelete('{{ $penduduk->id }}', '{{ $penduduk->nama }}')" class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                                <i class="fas fa-trash mr-2 text-sm"></i>
                                <span class="text-sm font-medium">Hapus</span>
                            </button>
                            @endcan
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="px-6 py-6 border-t border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
                {{ $penduduks->appends(request()->query())->links('vendor.pagination.tailwind') }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16 px-6">
                <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-users text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Tidak ada data penduduk</h3>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">Mulai dengan menambahkan data penduduk baru ke dalam sistem</p>
                @can('penduduk.create')
                <a href="{{ route('penduduk.create') }}" class="group inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-plus text-white text-sm"></i>
                    </div>
                    <span class="font-bold text-lg">Tambah Penduduk Pertama</span>
                </a>
                @endcan
            </div>
        @endif
    </div>

<script nonce="{{ $csp_nonce }}">
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggleFilterBtn');
    const toggleText = document.getElementById('toggleFilterText');
    const panel = document.getElementById('filterPanel');

    if (!toggleBtn || !panel) return;

    const isMobile = () => window.innerWidth < 640;
    let isFilterOpen = false;

    const setState = (open) => {
        isFilterOpen = !!open;
        panel.classList.toggle('hidden', !isFilterOpen && isMobile());
        if (toggleText && isMobile()) {
            toggleText.textContent = isFilterOpen ? 'Sembunyikan' : 'Tampilkan';
        }
    };

    // Auto-open when active filter exists
    const hasActiveFilters = {{ $activeFilterCount > 0 ? 'true' : 'false' }};
    setState(hasActiveFilters);

    toggleBtn.addEventListener('click', function () {
        setState(!isFilterOpen);
    });

    window.addEventListener('resize', function () {
        if (!isMobile()) {
            panel.classList.remove('hidden');
        } else {
            // Jangan reset ke hasActiveFilters saat keyboard mobile membuka/menutup viewport
            panel.classList.toggle('hidden', !isFilterOpen);
            if (toggleText) {
                toggleText.textContent = isFilterOpen ? 'Sembunyikan' : 'Tampilkan';
            }
        }
    });
});

// SweetAlert untuk konfirmasi delete
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus data ${name}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form delete
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('/penduduk') }}/${id}`;

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';

            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';

            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Export Excel function
function exportExcel() {
    // Show loading modal
    Swal.fire({
        title: 'Memproses Export Excel...',
        text: 'Mohon tunggu, data sedang diproses. Proses ini mungkin memakan waktu beberapa menit untuk data yang besar.',
        icon: 'info',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: function() {
            Swal.showLoading();
        }
    });

    // Create a hidden iframe to handle download
    var iframe = document.createElement('iframe');
    iframe.style.display = 'none';

    // Bawa filter aktif dari URL saat ini ke endpoint export
    const params = new URLSearchParams(window.location.search);
    params.delete('page'); // export tidak perlu pagination page
    const queryString = params.toString();
    iframe.src = '{{ route("penduduk.export.excel") }}' + (queryString ? ('?' + queryString) : '');

    // Add iframe to body
    document.body.appendChild(iframe);

    // Listen for iframe load event
    iframe.onload = function() {
        // Close loading modal after iframe loads (download started)
        setTimeout(function() {
            if (Swal.isVisible()) {
                Swal.close();
            }
            // Remove iframe after download
            document.body.removeChild(iframe);
        }, 1000);
    };

    // Fallback: close modal after 8 seconds if iframe doesn't load
    setTimeout(function() {
        if (Swal.isVisible()) {
            Swal.close();
        }
        if (document.body.contains(iframe)) {
            document.body.removeChild(iframe);
        }
    }, 13000);
}
</script>

<!-- Session messages handled by global component -->


<!-- JavaScript untuk update kepala keluarga dihapus karena tombol KK sudah dihapus -->

@endsection




