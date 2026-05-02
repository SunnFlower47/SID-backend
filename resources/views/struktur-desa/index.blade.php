@extends('layouts.app')

@section('title', 'Struktur Desa')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center mb-4 sm:mb-0">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-sitemap text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">Struktur Desa</h1>
                        <p class="text-green-100 text-sm sm:text-base">Kelola data struktur organisasi dan kepemimpinan desa</p>
                    </div>
                </div>
                @can('pelayanan_informasi')
                <a href="{{ route('struktur-desa.create') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Data
                </a>
                @endcan
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-blue-600 uppercase tracking-wide">Total Data</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total'] ?? 0) }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-users text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-green-600 uppercase tracking-wide">Aktif</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['aktif'] ?? 0) }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-purple-600 uppercase tracking-wide">Kepala Desa</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['kepala_desa'] ?? 0) }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-crown text-2xl text-purple-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-orange-600 uppercase tracking-wide">Ketua RT</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['ketua_rt'] ?? 0) }}</p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-home text-2xl text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Organizational Chart View -->
        <div class="bg-white rounded-2xl shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Struktur Organisasi Desa</h3>
            </div>
            <div class="p-6">
                @if(isset($strukturByCategory) && $strukturByCategory->count() > 0)
                    <div class="space-y-6">
                        <!-- Pimpinan Desa (Kepala Desa, Sekretaris) -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($strukturByCategory as $kategori => $data)
                                @if($kategori !== 'kepala_dusun' && $kategori !== 'staf_kaur' && $kategori !== 'ketua_rt' && $kategori !== 'ketua_rw')
                                <div class="bg-gradient-to-r {{ $kategori == 'kepala_desa' ? 'from-blue-50 to-blue-100' : ($kategori == 'sekretaris' ? 'from-green-50 to-green-100' : 'from-purple-50 to-purple-100') }} rounded-xl p-6 border border-gray-200">
                                    <div class="flex items-center mb-4">
                                        <div class="p-2 rounded-lg {{ $kategori == 'kepala_desa' ? 'bg-blue-200' : ($kategori == 'sekretaris' ? 'bg-green-200' : 'bg-purple-200') }}">
                                            <i class="fas {{ $kategori == 'kepala_desa' ? 'fa-crown' : ($kategori == 'sekretaris' ? 'fa-user-tie' : 'fa-users') }} {{ $kategori == 'kepala_desa' ? 'text-blue-600' : ($kategori == 'sekretaris' ? 'text-green-600' : 'text-purple-600') }}"></i>
                                        </div>
                                        <h4 class="ml-3 text-lg font-semibold {{ $kategori == 'kepala_desa' ? 'text-blue-900' : ($kategori == 'sekretaris' ? 'text-green-900' : 'text-purple-900') }}">
                                            {{ $data->first()->kategori_label ?? ucfirst(str_replace('_', ' ', $kategori)) }}
                                        </h4>
                                    </div>
                                    <div class="space-y-4">
                                        @foreach($data as $item)
                                        <div class="flex items-center p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                            @if($item->foto)
                                            <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama }}"
                                                 class="w-12 h-12 rounded-full object-cover mr-4">
                                            @else
                                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mr-4">
                                                <i class="fas fa-user text-gray-400"></i>
                                            </div>
                                            @endif
                                            <div class="flex-1">
                                                <h5 class="font-semibold text-gray-900">{{ $item->nama }}</h5>
                                                <p class="text-sm text-gray-600">{{ $item->jabatan }}</p>
                                                @if($item->no_hp)
                                                <p class="text-xs text-gray-500">
                                                    <i class="fas fa-phone mr-1"></i> {{ $item->no_hp }}
                                                </p>
                                                @endif
                                            </div>
                                            <div class="flex space-x-2">
                                                @can('pelayanan_informasi')
                                                <a href="{{ route('struktur-desa.show', $item) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('pelayanan_informasi')
                                                <a href="{{ route('struktur-desa.edit', $item) }}" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>

                        <!-- KAUR (Kepala Urusan) -->
                        @if(isset($strukturByCategory['staf_kaur']) && $strukturByCategory['staf_kaur']->count() > 0)
                        <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 rounded-xl p-6 border border-gray-200">
                            <div class="flex items-center mb-6">
                                <div class="p-2 rounded-lg bg-indigo-200">
                                    <i class="fas fa-users-cog text-indigo-600"></i>
                                </div>
                                <h4 class="ml-3 text-lg font-semibold text-indigo-900">
                                    Staf KAUR
                                </h4>
                            </div>

                            <!-- Grid 3 kolom untuk Staf KAUR -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($strukturByCategory['staf_kaur'] as $item)
                                <div class="flex items-center p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                    @if($item->foto)
                                    <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama }}"
                                         class="w-12 h-12 rounded-full object-cover mr-4">
                                    @else
                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    @endif
                                    <div class="flex-1">
                                        <h5 class="font-semibold text-gray-900">{{ $item->nama }}</h5>
                                        <p class="text-sm text-gray-600">{{ $item->jabatan }}</p>
                                        @if($item->no_hp)
                                        <p class="text-xs text-gray-500">
                                            <i class="fas fa-phone mr-1"></i> {{ $item->no_hp }}
                                        </p>
                                        @endif
                                    </div>
                                    <div class="flex space-x-2">
                                        @can('pelayanan_informasi')
                                        <a href="{{ route('struktur-desa.show', $item) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('pelayanan_informasi')
                                        <a href="{{ route('struktur-desa.edit', $item) }}" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Kepala Dusun - Layout Kiri Kanan -->
                        @if(isset($strukturByCategory['kepala_dusun']) && $strukturByCategory['kepala_dusun']->count() > 0)
                        <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-xl p-6 border border-gray-200">
                            <div class="flex items-center mb-6">
                                <div class="p-2 rounded-lg bg-orange-200">
                                    <i class="fas fa-home text-orange-600"></i>
                                </div>
                                <h4 class="ml-3 text-lg font-semibold text-orange-900">
                                    Kepala Dusun
                                </h4>
                            </div>

                            <!-- Layout Kiri Kanan untuk Kepala Dusun -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($strukturByCategory['kepala_dusun'] as $index => $item)
                                <div class="flex items-center p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                    @if($item->foto)
                                    <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama }}"
                                         class="w-12 h-12 rounded-full object-cover mr-4">
                                    @else
                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    @endif
                                    <div class="flex-1">
                                        <h5 class="font-semibold text-gray-900">{{ $item->nama }}</h5>
                                        <p class="text-sm text-gray-600">{{ $item->jabatan }}</p>
                                        @if($item->dusun_label)
                                        <p class="text-xs text-orange-600 font-medium">
                                            <i class="fas fa-map-marker-alt mr-1"></i> {{ $item->dusun_label }}
                                        </p>
                                        @endif
                                        @if($item->no_hp)
                                        <p class="text-xs text-gray-500">
                                            <i class="fas fa-phone mr-1"></i> {{ $item->no_hp }}
                                        </p>
                                        @endif
                                    </div>
                                    <div class="flex space-x-2">
                                        @can('pelayanan_informasi')
                                        <a href="{{ route('struktur-desa.show', $item) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('pelayanan_informasi')
                                        <a href="{{ route('struktur-desa.edit', $item) }}" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Ketua RT dan RW -->
                        @if((isset($strukturByCategory['ketua_rt']) && $strukturByCategory['ketua_rt']->count() > 0) || (isset($strukturByCategory['ketua_rw']) && $strukturByCategory['ketua_rw']->count() > 0))
                        <div class="bg-gradient-to-r from-teal-50 to-teal-100 rounded-xl p-6 border border-gray-200">
                            <div class="flex items-center mb-6">
                                <div class="p-2 rounded-lg bg-teal-200">
                                    <i class="fas fa-sitemap text-teal-600"></i>
                                </div>
                                <h4 class="ml-3 text-lg font-semibold text-teal-900">
                                    Ketua RT & RW
                                </h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Ketua RT -->
                                @if(isset($strukturByCategory['ketua_rt']) && $strukturByCategory['ketua_rt']->count() > 0)
                                <div class="space-y-4">
                                    <h5 class="font-semibold text-teal-800 mb-3">Ketua RT</h5>
                                    @foreach($strukturByCategory['ketua_rt'] as $item)
                                    <div class="flex items-center p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                        @if($item->foto)
                                        <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama }}"
                                             class="w-12 h-12 rounded-full object-cover mr-4">
                                        @else
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mr-4">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                        @endif
                                        <div class="flex-1">
                                            <h6 class="font-semibold text-gray-900">{{ $item->nama }}</h6>
                                            <p class="text-sm text-gray-600">{{ $item->jabatan }}</p>
                                            @if($item->rt_label)
                                            <p class="text-xs text-teal-600 font-medium">
                                                <i class="fas fa-home mr-1"></i> RT {{ $item->rt_label }}
                                            </p>
                                            @endif
                                            @if($item->no_hp)
                                            <p class="text-xs text-gray-500">
                                                <i class="fas fa-phone mr-1"></i> {{ $item->no_hp }}
                                            </p>
                                            @endif
                                        </div>
                                        <div class="flex space-x-2">
                                            @can('pelayanan_informasi')
                                            <a href="{{ route('struktur-desa.show', $item) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('pelayanan_informasi')
                                            <a href="{{ route('struktur-desa.edit', $item) }}" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                <!-- Ketua RW -->
                                @if(isset($strukturByCategory['ketua_rw']) && $strukturByCategory['ketua_rw']->count() > 0)
                                <div class="space-y-4">
                                    <h5 class="font-semibold text-teal-800 mb-3">Ketua RW</h5>
                                    @foreach($strukturByCategory['ketua_rw'] as $item)
                                    <div class="flex items-center p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                        @if($item->foto)
                                        <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama }}"
                                             class="w-12 h-12 rounded-full object-cover mr-4">
                                        @else
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mr-4">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                        @endif
                                        <div class="flex-1">
                                            <h6 class="font-semibold text-gray-900">{{ $item->nama }}</h6>
                                            <p class="text-sm text-gray-600">{{ $item->jabatan }}</p>
                                            @if($item->rw_label)
                                            <p class="text-xs text-teal-600 font-medium">
                                                <i class="fas fa-home mr-1"></i> RW {{ $item->rw_label }}
                                            </p>
                                            @endif
                                            @if($item->no_hp)
                                            <p class="text-xs text-gray-500">
                                                <i class="fas fa-phone mr-1"></i> {{ $item->no_hp }}
                                            </p>
                                            @endif
                                        </div>
                                        <div class="flex space-x-2">
                                            @can('pelayanan_informasi')
                                            <a href="{{ route('struktur-desa.show', $item) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('pelayanan_informasi')
                                            <a href="{{ route('struktur-desa.edit', $item) }}" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-users text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data struktur desa</h3>
                        <p class="text-gray-500 mb-6">Mulai tambah data struktur organisasi desa</p>
                        @can('pelayanan_informasi')
                        <a href="{{ route('struktur-desa.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white text-sm font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Data Pertama
                        </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-2xl shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Data Lengkap Struktur Desa</h3>
            </div>

            @if($struktur->count() > 0)
                <!-- Mobile Card View -->
                <div class="block lg:hidden">
                    <div class="p-4 space-y-4">
                        @foreach($struktur as $item)
                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow cursor-pointer group" onclick="window.location='{{ route('struktur-desa.show', $item) }}'">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3 flex-1 min-w-0">
                                    @if($item->foto)
                                    <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama }}" class="w-12 h-12 rounded-full object-cover flex-shrink-0">
                                    @else
                                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-semibold text-lg">{{ strtoupper(substr($item->nama, 0, 1)) }}</span>
                                    </div>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-semibold text-gray-900 truncate group-hover:text-green-900 transition-colors">
                                            {{ $item->nama }}
                                        </h4>
                                        <p class="text-sm text-gray-600 truncate">{{ $item->jabatan }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $item->kategori }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons - Always Visible -->
                            <div class="flex flex-wrap items-center justify-end gap-2 mb-4">
                                <button class="flex items-center px-2 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors text-xs font-medium"
                                        onclick="event.stopPropagation(); window.location='{{ route('struktur-desa.show', $item) }}'"
                                        title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @can('pelayanan_informasi')
                                <a href="{{ route('struktur-desa.edit', $item) }}"
                                   class="flex items-center px-2 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg transition-colors text-xs font-medium"
                                   onclick="event.stopPropagation()"
                                   title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('pelayanan_informasi')
                                <button onclick="event.stopPropagation(); confirmDelete({{ $item->id }}, '{{ addslashes($item->nama) }}')"
                                        class="flex items-center px-2 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg transition-colors text-xs font-medium"
                                        title="Hapus Data">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endcan
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Kontak:</span>
                                    <div class="text-right">
                                        @if($item->no_hp)
                                        <div class="text-sm text-gray-900">{{ $item->no_hp }}</div>
                                        @endif
                                        @if($item->email)
                                        <div class="text-xs text-gray-500">{{ $item->email }}</div>
                                        @endif
                                        @if(!$item->no_hp && !$item->email)
                                        <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Status:</span>
                                    @if($item->status_aktif)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Desktop Table View -->
                <div class="hidden lg:block">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($struktur as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $loop->iteration + ($struktur->currentPage() - 1) * $struktur->perPage() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($item->foto)
                                    <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama }}"
                                         class="w-10 h-10 rounded-full object-cover mr-3">
                                    @else
                                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $item->nama }}</div>
                                        @if($item->nik)
                                        <div class="text-sm text-gray-500">NIK: {{ $item->nik }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->jabatan }}</div>
                                @if($item->tugas_wewenang)
                                <div class="text-sm text-gray-500">{{ Str::limit($item->tugas_wewenang, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->kategori == 'kepala_desa' ? 'bg-blue-100 text-blue-800' : ($item->kategori == 'sekretaris' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                                    {{ $item->kategori_label ?? ucfirst(str_replace('_', ' ', $item->kategori)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($item->no_hp)
                                <div class="flex items-center">
                                    <i class="fas fa-phone mr-2"></i> {{ $item->no_hp }}
                                </div>
                                @endif
                                @if($item->email)
                                <div class="flex items-center">
                                    <i class="fas fa-envelope mr-2"></i> {{ $item->email }}
                                </div>
                                @endif
                                @if(!$item->no_hp && !$item->email)
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->status_aktif)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Aktif
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Tidak Aktif
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    @can('pelayanan_informasi')
                                    <a href="{{ route('struktur-desa.show', $item) }}" class="inline-flex items-center px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors text-sm font-medium" title="Lihat Detail">
                                        <i class="fas fa-eye mr-1"></i>
                                        Detail
                                    </a>
                                    @endcan
                                    @can('pelayanan_informasi')
                                    <a href="{{ route('struktur-desa.edit', $item) }}" class="inline-flex items-center px-3 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg transition-colors text-sm font-medium" title="Edit Data">
                                        <i class="fas fa-edit mr-1"></i>
                                        Edit
                                    </a>
                                    @endcan
                                    @can('pelayanan_informasi')
                                    <button type="button" class="inline-flex items-center px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg transition-colors text-sm font-medium" title="Hapus Data" onclick="confirmDelete({{ $item->id }}, '{{ $item->nama }}')">
                                        <i class="fas fa-trash mr-1"></i>
                                        Hapus
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-users text-gray-400 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data struktur desa</h3>
                                <p class="text-gray-500">Mulai tambah data struktur organisasi desa</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                    </div>
                </div>

                <!-- Pagination -->
                @if($struktur->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $struktur->links() }}
                </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-users text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data struktur desa</h3>
                    <p class="text-gray-500">Mulai tambah data struktur organisasi desa</p>
                </div>
            @endif
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

// SweetAlert untuk konfirmasi delete
function confirmDelete(strukturId, strukturName) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus struktur "${strukturName}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form delete
            document.getElementById('delete-form-' + strukturId).submit();
        }
    });
}
@endnoncescript
@endsection

