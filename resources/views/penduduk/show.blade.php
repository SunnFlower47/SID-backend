@extends('layouts.app')

@section('title', 'Detail Penduduk')
@section('subtitle', 'Informasi lengkap data penduduk')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">{{ $penduduk->nama }}</h1>
                    <p class="text-green-100 mt-1 font-mono">{{ $penduduk->nik }}</p>
                    @if($penduduk->trashed())
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-2">
                            <i class="fas fa-trash mr-1"></i>
                            Data Terhapus
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                @if(!$penduduk->trashed())
                @can('kependudukan')
                <a href="{{ route('penduduk.edit', $penduduk) }}"
                   class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                        <i class="fas fa-edit mr-2"></i>
                        <span class="hidden sm:inline">Edit</span>
                    </a>
                    @endcan
                @can('kependudukan')
                <button type="button" onclick="confirmDeletePenduduk()"
                        class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-trash mr-2"></i>
                    <span class="hidden sm:inline">Hapus</span>
                </button>
                @endcan
                @endif
                <a href="{{ route('penduduk.index', session('penduduk_index_query', [])) }}"
                   class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    <span class="hidden sm:inline">Kembali</span>
                </a>
            </div>
        </div>
    </div>

    @if($penduduk->trashed())
    <!-- Banner Penduduk Terhapus -->
    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-xl shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400 text-lg"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-semibold text-red-800">Data Penduduk Terhapus</h3>
                <p class="text-sm text-red-700 mt-1">
                    Penduduk ini telah dihapus melalui mutasi
                    @php
                        $mutasiTerkait = $penduduk->mutasis->whereIn('jenis_mutasi', ['kematian', 'pindah_keluar', 'pisah_kk'])->first();
                    @endphp
                    @if($mutasiTerkait)
                        (<strong>{{ ucfirst(str_replace('_', ' ', $mutasiTerkait->jenis_mutasi)) }}</strong>
                        pada {{ $mutasiTerkait->tanggal_mutasi->format('d M Y') }}).
                    @else
                        .
                    @endif
                    Untuk mengembalikan data, gunakan tombol <strong>Undo</strong> di menu <strong>Mutasi</strong>.
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Summary -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-3 sm:p-4">
            <p class="text-[11px] sm:text-xs font-medium text-gray-500 uppercase tracking-wide">NIK</p>
            <p class="mt-1 text-sm sm:text-base font-semibold text-gray-900 font-mono break-all">{{ $penduduk->nik }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-3 sm:p-4">
            <p class="text-[11px] sm:text-xs font-medium text-gray-500 uppercase tracking-wide">No. KK</p>
            <p class="mt-1 text-sm sm:text-base font-semibold text-gray-900 font-mono break-all">{{ $penduduk->nkk ?: '-' }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-3 sm:p-4">
            <p class="text-[11px] sm:text-xs font-medium text-gray-500 uppercase tracking-wide">RT / RW</p>
            <p class="mt-1 text-sm sm:text-base font-semibold text-gray-900">{{ $penduduk->rt_label }} / {{ $penduduk->rw_label }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-3 sm:p-4">
            <p class="text-[11px] sm:text-xs font-medium text-gray-500 uppercase tracking-wide">Status</p>
            <p class="mt-1 text-sm sm:text-base font-semibold text-gray-900">{{ $penduduk->status_perkawinan ?: '-' }}</p>
        </div>
    </div>

    <!-- Status & Mutation Info -->
    @if($penduduk->mutasis->count() > 0)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Informasi Mutasi</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Penduduk ini memiliki {{ $penduduk->mutasis->count() }} mutasi:</p>
                    <ul class="list-disc list-inside mt-1">
                        @foreach($penduduk->mutasis as $mutasi)
                        <li>
                            <strong>{{ ucfirst(str_replace('_', ' ', $mutasi->jenis_mutasi)) }}</strong> -
                            {{ $mutasi->kategori_mutasi }}
                            @if($mutasi->tanggal_mutasi)
                                ({{ $mutasi->tanggal_mutasi->format('d M Y') }})
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Left Column - Personal Info -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    Informasi Pribadi
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                            <label class="block text-[11px] sm:text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Nama Lengkap</label>
                            <p class="text-gray-900 font-semibold">{{ $penduduk->nama }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                            <label class="block text-[11px] sm:text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">NIK</label>
                            <p class="text-gray-900 font-mono text-sm break-all">{{ $penduduk->nik }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                            <label class="block text-[11px] sm:text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">NKK</label>
                            <p class="text-gray-900 font-mono text-sm break-all">{{ $penduduk->nkk }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Jenis Kelamin</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $penduduk->jenis_kelamin == 'LAKI-LAKI' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                <i class="fas {{ $penduduk->jenis_kelamin == 'LAKI-LAKI' ? 'fa-male' : 'fa-female' }} mr-1"></i>
                                {{ $penduduk->jenis_kelamin == 'LAKI-LAKI' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                            <label class="block text-[11px] sm:text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Tempat Lahir</label>
                            <p class="text-gray-900 font-medium">{{ $penduduk->tempat_lahir ?: '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                            <label class="block text-[11px] sm:text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Tanggal Lahir</label>
                            <p class="text-gray-900 font-medium">{{ $penduduk->tanggal_lahir ? $penduduk->tanggal_lahir->format('d F Y') : '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                            <label class="block text-[11px] sm:text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Usia</label>
                            <p class="text-gray-900 font-semibold text-lg">{{ $penduduk->usia }} tahun</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                            <label class="block text-[11px] sm:text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Agama</label>
                            <p class="text-gray-900 font-medium">{{ $penduduk->agama ?: '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Family Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-users text-green-600"></i>
                        </div>
                        Informasi Keluarga
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Status Perkawinan</label>
                            <p class="text-gray-900 font-medium">{{ $penduduk->status_perkawinan ?: '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Kedudukan dalam Keluarga</label>
                            <p class="text-gray-900 font-medium">{{ $penduduk->kedudukan_keluarga ?: '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Nama Ayah</label>
                            <p class="text-gray-900 font-medium">{{ $penduduk->nama_ayah ?: '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Nama Ibu</label>
                            <p class="text-gray-900 font-medium">{{ $penduduk->nama_ibu ?: '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Education & Work Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-graduation-cap text-purple-600"></i>
                        </div>
                        Pendidikan & Pekerjaan
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Pendidikan</label>
                            <p class="text-gray-900">{{ $penduduk->pendidikan ?: '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Pekerjaan</label>
                            <p class="text-gray-900">{{ $penduduk->pekerjaan ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="xl:col-span-1 space-y-6">
                <!-- Address Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-map-marker-alt text-orange-600"></i>
                        </div>
                        Alamat
                    </h3>
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="block text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">Alamat Lengkap</label>
                            <p class="text-gray-900 text-sm leading-relaxed">{{ $penduduk->alamat ?: '-' }}</p>
                        </div>
                        <div class="grid grid-cols-3 gap-2 sm:gap-3">
                            <div class="bg-blue-50 rounded-lg p-2 sm:p-3 text-center">
                                <label class="block text-xs font-medium text-gray-500 mb-1">RT</label>
                                <p class="text-gray-900 font-semibold text-sm">RT {{ $penduduk->rt_label }}</p>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-2 sm:p-3 text-center">
                                <label class="block text-xs font-medium text-gray-500 mb-1">RW</label>
                                <p class="text-gray-900 font-semibold text-sm">RW {{ $penduduk->rw_label }}</p>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-2 sm:p-3 text-center">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Dusun</label>
                                <p class="text-gray-900 font-semibold text-sm">{{ $penduduk->dusun_label }}</p>
                            </div>
                        </div>
                        @if($penduduk->keterangan)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Keterangan</label>
                                    <p class="text-sm text-yellow-800">{{ $penduduk->keterangan }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Mutation History -->
                @if($penduduk->mutasis->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-history text-red-600"></i>
                        </div>
                        Riwayat Mutasi
                    </h3>
                    <div class="space-y-3">
                        @foreach($penduduk->mutasis as $mutasi)
                        <div class="border border-gray-200 rounded-lg p-3 sm:p-4">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-2">
                                <span class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $mutasi->jenis_mutasi)) }}</span>
                                <span class="text-xs text-gray-500 mt-1 sm:mt-0">{{ $mutasi->tanggal_mutasi ? $mutasi->tanggal_mutasi->format('d M Y') : '-' }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ $mutasi->kategori_mutasi }}</p>
                            @if($mutasi->alasan)
                            <p class="text-xs text-gray-500 mt-1">{{ $mutasi->alasan }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Family Structure -->
        @if($penduduk->kartu_keluarga_id)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-home text-indigo-600"></i>
                </div>
                Struktur Keluarga
            </h3>

            <!-- Family Operations Buttons -->
            @can('kependudukan')
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 sm:p-6 mb-6 border border-blue-200">
                <h4 class="text-md font-semibold text-blue-900 mb-4 flex items-center">
                    <div class="w-6 h-6 bg-blue-200 rounded-lg flex items-center justify-center mr-2">
                        <i class="fas fa-tools text-blue-600 text-sm"></i>
                    </div>
                    Operasi Keluarga
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Quick Fix - No Log -->
                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                        <h5 class="text-sm font-semibold text-blue-900 mb-3 flex items-center">
                            <i class="fas fa-edit mr-2"></i>Edit Cepat
                        </h5>
                        <a href="{{ route('penduduk.family.address.form', $penduduk->kartu_keluarga_id) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md text-sm w-full">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            Update Alamat Keluarga
                        </a>
                        <p class="text-xs text-blue-700 mt-2">Koreksi alamat tanpa log mutasi</p>
                    </div>

                    <!-- Formal Process - With Log -->
                    <div class="bg-orange-50 rounded-xl p-4 border border-orange-200">
                        <h5 class="text-sm font-semibold text-orange-900 mb-3 flex items-center">
                            <i class="fas fa-file-alt mr-2"></i>Proses Resmi
                        </h5>
                        <a href="{{ route('mutasi.data.create') }}?jenis_mutasi=pindah_rt_rw&kartu_keluarga_id={{ $penduduk->kartu_keluarga_id }}"
                           class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-3 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md text-sm w-full">
                            <i class="fas fa-exchange-alt mr-2"></i>
                            Pindah RT/RW
                        </a>
                        <p class="text-xs text-orange-700 mt-2">Dengan log mutasi resmi</p>
                        <p class="text-xs text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Untuk Pisah KK, gunakan menu <strong>Mutasi</strong>
                        </p>
                    </div>
                </div>
                <div class="bg-yellow-50 rounded-xl p-3 mt-4 border border-yellow-200">
                    <p class="text-xs text-yellow-800 text-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        Semua operasi akan mempengaruhi anggota keluarga dengan No KK: <strong>{{ $penduduk->nkk }}</strong>
                    </p>
                </div>
            </div>
            @endcan

            <!-- KK Information -->
            <div class="bg-gray-50 rounded-xl p-4 sm:p-6 mb-6">
                <h4 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="w-6 h-6 bg-gray-200 rounded-lg flex items-center justify-center mr-2">
                        <i class="fas fa-id-card text-gray-600 text-sm"></i>
                    </div>
                    Informasi Kartu Keluarga
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white rounded-lg p-3">
                        <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">No. KK</label>
                        <p class="text-gray-900 font-mono text-sm">{{ $penduduk->nkk }}</p>
                    </div>
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-gray-900 text-sm">{{ optional($penduduk->kartuKeluarga)->nama_kepala_keluarga ?? 'Tidak ada kepala keluarga' }}</p>
                    </div>
                    <div class="sm:col-span-2 bg-white rounded-lg p-3">
                        <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Alamat KK</label>
                        <p class="text-gray-900 text-sm">{{ $penduduk->alamat }}</p>
                    </div>
                    <div class="sm:col-span-2 bg-white rounded-lg p-3">
                        <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">RT/RW/Dusun</label>
                        <p class="text-gray-900 text-sm">
                            RT {{ $penduduk->rt_label }} / RW {{ $penduduk->rw_label }}
                            @if($penduduk->dusun_label !== '-')
                                / {{ $penduduk->dusun_label }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Family Members -->
            <div>
                @php
                    $anggotaKeluarga = optional($penduduk->kartuKeluarga)->penduduks ?? collect();
                @endphp
                <h4 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                    <div class="w-6 h-6 bg-indigo-200 rounded-lg flex items-center justify-center mr-2">
                        <i class="fas fa-users text-indigo-600 text-sm"></i>
                    </div>
                    Anggota Keluarga ({{ $anggotaKeluarga->count() }} orang)
                </h4>

                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kelamin</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usia</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kedudukan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($anggotaKeluarga as $anggota)
                            <tr class="{{ $anggota->id == $penduduk->id ? 'bg-blue-50' : 'hover:bg-gray-50' }}">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 {{ $anggota->id == $penduduk->id ? 'bg-blue-100' : 'bg-gray-100' }} rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user {{ $anggota->id == $penduduk->id ? 'text-blue-600' : 'text-gray-600' }} text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <a href="{{ route('penduduk.show', $anggota) }}"
                                                   class="hover:text-blue-700 hover:underline">
                                                    {{ $anggota->nama }}
                                                </a>
                                                @if($anggota->id == $penduduk->id)
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        Anda
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-mono text-gray-900">
                                    {{ $anggota->nik }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $anggota->jenis_kelamin == 'LAKI-LAKI' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                        <i class="fas {{ $anggota->jenis_kelamin == 'LAKI-LAKI' ? 'fa-male' : 'fa-female' }} mr-1"></i>
                                        {{ $anggota->jenis_kelamin == 'LAKI-LAKI' ? 'Laki-laki' : 'Perempuan' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $anggota->usia }} tahun
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $anggota->kedudukan_keluarga ?: '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $anggota->status_perkawinan ?: '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <a href="{{ route('penduduk.show', $anggota) }}"
                                       class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden space-y-3">
                    @foreach($anggotaKeluarga as $anggota)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 {{ $anggota->id == $penduduk->id ? 'ring-2 ring-blue-500' : '' }}">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 {{ $anggota->id == $penduduk->id ? 'bg-blue-100' : 'bg-gray-100' }} rounded-full flex items-center justify-center">
                                    <i class="fas fa-user {{ $anggota->id == $penduduk->id ? 'text-blue-600' : 'text-gray-600' }} text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('penduduk.show', $anggota) }}" class="hover:text-blue-700 hover:underline">
                                            {{ $anggota->nama }}
                                        </a>
                                    </div>
                                    @if($anggota->id == $penduduk->id)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            Anda
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-gray-500">NIK:</span>
                                <p class="font-mono text-gray-900">{{ $anggota->nik }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Usia:</span>
                                <p class="text-gray-900">{{ $anggota->usia }} tahun</p>
                            </div>
                            <div>
                                <span class="text-gray-500">JK:</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $anggota->jenis_kelamin == 'LAKI-LAKI' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                    <i class="fas {{ $anggota->jenis_kelamin == 'LAKI-LAKI' ? 'fa-male' : 'fa-female' }} mr-1"></i>
                                    {{ $anggota->jenis_kelamin == 'LAKI-LAKI' ? 'L' : 'P' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-500">Kedudukan:</span>
                                <p class="text-gray-900">{{ $anggota->kedudukan_keluarga ?: '-' }}</p>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <a href="{{ route('penduduk.show', $anggota) }}"
                               class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors">
                                <i class="fas fa-eye mr-1"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="delete-form" method="POST" action="{{ route('penduduk.destroy', $penduduk) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@noncescript
function confirmDeletePenduduk() {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        html: `Apakah Anda yakin ingin menghapus data penduduk <strong>{{ addslashes($penduduk->nama) }}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form').submit();
        }
    });
}
@endnoncescript

<!-- Session messages handled by global component -->

<!-- Session messages handled by global component -->

@endsection

