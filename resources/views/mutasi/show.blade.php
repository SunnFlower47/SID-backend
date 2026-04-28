@extends('layouts.app')

@section('title', 'Detail Mutasi')
@section('subtitle', 'Informasi lengkap data mutasi')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Detail Mutasi</h1>
                    <p class="text-green-100 text-sm sm:text-base">Informasi lengkap mengenai mutasi penduduk</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                @can('mutasi.edit')
                <a href="{{ route('mutasi.data.edit', $mutasi) }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Data
                </a>
                @endcan
                @can('mutasi.delete')
                <button type="button" id="mutasiActionBtn" class="group flex items-center px-6 py-3 {{ $mutasi->isSoftDeleteType() ? 'bg-amber-500/80 backdrop-blur-sm hover:bg-amber-500' : 'bg-red-500/80 backdrop-blur-sm hover:bg-red-500' }} text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas {{ $mutasi->isSoftDeleteType() ? 'fa-undo' : 'fa-times' }} mr-2"></i>
                    {{ $mutasi->isSoftDeleteType() ? 'Undo Data' : 'Cancel Mutasi' }}
                </button>
                @endcan
                <a href="{{ route('mutasi.data.index') }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Detail Dinamis Berdasarkan Jenis Mutasi -->
    @if($mutasi->jenis_mutasi == 'kematian')
        <!-- Detail Kematian -->
        <div class="bg-gradient-to-r from-red-50 to-rose-50 rounded-2xl shadow-lg border border-red-200 p-6 sm:p-8">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-times text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Detail Kematian</h3>
                    <p class="text-sm text-gray-600">Informasi lengkap mengenai kematian penduduk</p>
                </div>
            </div>

            <!-- Data Penduduk yang Meninggal -->
            <div class="bg-white rounded-xl p-6 mb-6 border border-red-200">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-user text-red-600 mr-2"></i>
                    Data Penduduk yang Meninggal
                </h4>
                @if($mutasi->penduduk)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">NIK</label>
                            <p class="text-gray-900 font-mono">{{ $mutasi->penduduk->nik }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Nama Lengkap</label>
                            <p class="text-gray-900 font-medium">{{ $mutasi->penduduk->nama }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Jenis Kelamin</label>
                            <p class="text-gray-900">{{ $mutasi->penduduk->jenis_kelamin }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Umur</label>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($mutasi->penduduk->tanggal_lahir)->age }} Tahun</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Agama</label>
                            <p class="text-gray-900">{{ $mutasi->penduduk->agama ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Alamat</label>
                            <p class="text-gray-900">{{ $mutasi->penduduk->alamat }}, RT {{ $mutasi->penduduk->rt_label }}/RW {{ $mutasi->penduduk->rw_label }}</p>
                        </div>
                    </div>
                @else
                    <div class="text-red-600 text-center py-4">
                        <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                        <p>Data penduduk tidak ditemukan</p>
                    </div>
                @endif
            </div>

            <!-- Detail Kematian -->
            <div class="bg-white rounded-xl p-6 mb-6 border border-red-200">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-calendar-alt text-red-600 mr-2"></i>
                    Detail Kematian
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Tanggal Meninggal</label>
                        <p class="text-gray-900 font-medium">{{ $mutasi->tanggal_mutasi->format('d F Y') }}</p>
                    </div>
                    @if($mutasi->data_kematian && isset($mutasi->data_kematian['hari']))
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Hari Meninggal</label>
                        <p class="text-gray-900 font-medium">{{ $mutasi->data_kematian['hari'] }}</p>
                    </div>
                    @endif
                    @if($mutasi->data_kematian && isset($mutasi->data_kematian['jam']))
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Jam Meninggal</label>
                        <p class="text-gray-900 font-medium">{{ $mutasi->data_kematian['jam'] }}</p>
                    </div>
                    @endif
                    @if($mutasi->data_kematian && isset($mutasi->data_kematian['bertempat_di']))
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Bertempat di</label>
                        <p class="text-gray-900">{{ $mutasi->data_kematian['bertempat_di'] }}</p>
                    </div>
                    @endif
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-2">Penyebab Kematian</label>
                        <p class="text-gray-900">{{ $mutasi->alasan }}</p>
                    </div>
                </div>
            </div>

            <!-- Detail Pemakaman -->
            <div class="bg-white rounded-xl p-6 border border-red-200">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>
                    Dimakamkan pada
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @if($mutasi->data_pemakaman && isset($mutasi->data_pemakaman['hari']))
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Hari</label>
                        <p class="text-gray-900 font-medium">{{ $mutasi->data_pemakaman['hari'] }}</p>
                    </div>
                    @endif
                    @if($mutasi->data_pemakaman && isset($mutasi->data_pemakaman['tanggal']))
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Tanggal</label>
                        <p class="text-gray-900 font-medium">{{ \Carbon\Carbon::parse($mutasi->data_pemakaman['tanggal'])->format('d F Y') }}</p>
                    </div>
                    @endif
                    @if($mutasi->data_pemakaman && isset($mutasi->data_pemakaman['jam']))
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Jam</label>
                        <p class="text-gray-900 font-medium">{{ $mutasi->data_pemakaman['jam'] }}</p>
                    </div>
                    @endif
                    @if($mutasi->data_pemakaman && isset($mutasi->data_pemakaman['lokasi']))
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Dimakamkan di</label>
                        <p class="text-gray-900">{{ $mutasi->data_pemakaman['lokasi'] }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    @elseif($mutasi->jenis_mutasi == 'kelahiran')
        <!-- Detail Kelahiran -->
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl shadow-lg border border-green-200 p-6 sm:p-8">
        <div class="flex items-center space-x-3 mb-8">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-baby text-green-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Detail Kelahiran</h3>
                    <p class="text-sm text-gray-600">Informasi lengkap mengenai kelahiran bayi</p>
                </div>
            </div>

            <!-- Data Bayi -->
            <div class="bg-white rounded-xl p-6 mb-6 border border-green-200">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-baby text-green-600 mr-2"></i>
                    Data Bayi
                </h4>
                @if($mutasi->penduduk)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Nama Bayi</label>
                            <p class="text-gray-900 font-medium">{{ $mutasi->penduduk->nama }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Jenis Kelamin</label>
                            <p class="text-gray-900">{{ $mutasi->penduduk->jenis_kelamin }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Tempat Lahir</label>
                            <p class="text-gray-900">{{ $mutasi->penduduk->tempat_lahir ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Tanggal Lahir</label>
                            <p class="text-gray-900">{{ $mutasi->penduduk->tanggal_lahir ? \Carbon\Carbon::parse($mutasi->penduduk->tanggal_lahir)->format('d F Y') : '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">No Kartu Keluarga</label>
                            <p class="text-gray-900 font-mono">{{ $mutasi->penduduk->nkk }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Tanggal Mutasi</label>
                            <p class="text-gray-900">{{ $mutasi->tanggal_mutasi->format('d F Y') }}</p>
                        </div>
                    </div>
                @else
                    <div class="text-red-600 text-center py-4">
                        <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                        <p>Data bayi tidak ditemukan</p>
                    </div>
                @endif
            </div>

            <!-- Data Orang Tua -->
            <div class="bg-white rounded-xl p-6 border border-green-200">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-users text-green-600 mr-2"></i>
                    Data Orang Tua
                </h4>
                @if($mutasi->penduduk)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Nama Ayah</label>
                            <p class="text-gray-900">{{ $mutasi->penduduk->nama_ayah ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Nama Ibu</label>
                            <p class="text-gray-900">{{ $mutasi->penduduk->nama_ibu ?? '-' }}</p>
                        </div>
                    </div>
                @else
                    <div class="text-red-600 text-center py-4">
                        <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                        <p>Data orang tua tidak ditemukan</p>
                    </div>
                @endif
            </div>
        </div>

    @elseif($mutasi->jenis_mutasi == 'pisah_kk')
        <!-- Detail Pisah KK -->
        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-2xl shadow-lg border border-purple-200 p-6 sm:p-8">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-purple-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Detail Pisah KK</h3>
                    <p class="text-sm text-gray-600">Informasi lengkap mengenai pemisahan kartu keluarga</p>
                </div>
            </div>

            <!-- Data Penduduk yang Pisah KK -->
            <div class="bg-white rounded-xl p-6 mb-6 border border-purple-200">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-user text-purple-600 mr-2"></i>
                    Data Penduduk yang Pisah KK
                </h4>
                @if($mutasi->penduduk)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">NIK</label>
                            <p class="text-gray-900 font-mono">{{ $mutasi->penduduk->nik }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Nama Lengkap</label>
                            <p class="text-gray-900 font-medium">{{ $mutasi->penduduk->nama }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Jenis Kelamin</label>
                            <p class="text-gray-900">{{ $mutasi->penduduk->jenis_kelamin }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Agama</label>
                            <p class="text-gray-900">{{ $mutasi->penduduk->agama ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Status Perkawinan</label>
                            <p class="text-gray-900">{{ $mutasi->penduduk->status_perkawinan ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Kedudukan Keluarga</label>
                            <p class="text-gray-900">{{ $mutasi->penduduk->kedudukan_keluarga ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-2">Alamat Asli</label>
                            <p class="text-gray-900">{{ $mutasi->penduduk->alamat }}, RT {{ $mutasi->penduduk->rt_label }}/RW {{ $mutasi->penduduk->rw_label }}, {{ $mutasi->penduduk->dusun_label }}</p>
                        </div>
                    </div>
                @else
                    <div class="text-red-600 text-center py-4">
                        <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                        <p>Data penduduk tidak ditemukan</p>
                    </div>
                @endif
            </div>

            <!-- Detail Pisah KK -->
            <div class="bg-white rounded-xl p-6 mb-6 border border-purple-200">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>
                    Detail Pisah KK
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Tanggal Pisah KK</label>
                        <p class="text-gray-900 font-medium">{{ $mutasi->tanggal_mutasi->format('d F Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Kategori</label>
                        <p class="text-gray-900 font-medium">{{ ucfirst(str_replace('_', ' ', $mutasi->kategori_mutasi)) }}</p>
                    </div>
                    @if(in_array($mutasi->kategori_mutasi, ['dalam_kota', 'luar_kota', 'luar_negeri']))
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">NKK Tujuan</label>
                        @php
                            $detailData = is_string($mutasi->detail_tambahan) ? json_decode($mutasi->detail_tambahan, true) : $mutasi->detail_tambahan;
                        @endphp
                        @if($detailData && isset($detailData['tracking']['nkk_tujuan']))
                            <p class="text-gray-900 font-mono">{{ $detailData['tracking']['nkk_tujuan'] }}</p>
                        @else
                            <p class="text-gray-500 italic">Data tidak tersedia</p>
                        @endif
                    </div>
                    @endif
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-2">Asal/Tujuan</label>
                        <p class="text-gray-900">{{ $mutasi->asal_tujuan }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-2">Alasan</label>
                        <p class="text-gray-900">{{ $mutasi->alasan }}</p>
                    </div>
                </div>
            </div>

            <!-- Informasi Tracking (untuk kategori luar desa) -->
            @if(in_array($mutasi->kategori_mutasi, ['dalam_kota', 'luar_kota', 'luar_negeri']) && $detailData && isset($detailData['tracking']))
            <div class="bg-white rounded-xl p-6 border border-purple-200">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-purple-600 mr-2"></i>
                    Informasi Tracking
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">NKK Tujuan</label>
                        <p class="text-gray-900 font-mono">{{ $detailData['tracking']['nkk_tujuan'] ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Alamat Tujuan</label>
                        <p class="text-gray-900">{{ $detailData['tracking']['alamat_tujuan'] ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Kategori Pindah</label>
                        <p class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $detailData['tracking']['kategori_pindah'] ?? '-')) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Tanggal Pindah</label>
                        <p class="text-gray-900">
                            {{ !empty($detailData['tracking']['tanggal_pindah'])
                                ? \Carbon\Carbon::parse($detailData['tracking']['tanggal_pindah'])->format('d F Y')
                                : '-' }}
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

    @elseif($mutasi->jenis_mutasi == 'pindah_masuk')
        <!-- Detail Pindah Masuk -->
        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl shadow-lg border border-blue-200 p-6 sm:p-8">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-arrow-right text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Detail Pindah Masuk</h3>
                    <p class="text-sm text-gray-600">Informasi lengkap mengenai penduduk yang pindah masuk</p>
                </div>
            </div>

            <!-- Data Penduduk -->
            <div class="bg-white rounded-xl p-6 mb-6 border border-blue-200">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-user text-blue-600 mr-2"></i>
                    Data Penduduk
                </h4>
                @if($mutasi->penduduk)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">NIK</label>
                            <p class="text-gray-900 font-mono">{{ $mutasi->penduduk->nik }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Nama Lengkap</label>
                            <p class="text-gray-900 font-medium">{{ $mutasi->penduduk->nama }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Jenis Kelamin</label>
                            <p class="text-gray-900">{{ $mutasi->penduduk->jenis_kelamin }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Agama</label>
                            <p class="text-gray-900">{{ $mutasi->penduduk->agama ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Status Perkawinan</label>
                            <p class="text-gray-900">{{ $mutasi->penduduk->status_perkawinan ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Pendidikan</label>
                            <p class="text-gray-900">{{ $mutasi->penduduk->pendidikan ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-2">Alamat</label>
                            <p class="text-gray-900">{{ $mutasi->penduduk->alamat }}, RT {{ $mutasi->penduduk->rt_label }}/RW {{ $mutasi->penduduk->rw_label }}, {{ $mutasi->penduduk->dusun_label }}</p>
                        </div>
                    </div>
                @else
                    <div class="text-red-600 text-center py-4">
                        <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                        <p>Data penduduk tidak ditemukan</p>
                    </div>
                @endif
            </div>

            <!-- Detail Pindah Masuk -->
            <div class="bg-white rounded-xl p-6 border border-blue-200">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                    Detail Pindah Masuk
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Tanggal Masuk</label>
                        <p class="text-gray-900 font-medium">{{ $mutasi->tanggal_mutasi->format('d F Y') }}</p>
            </div>
            <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Asal</label>
                        <p class="text-gray-900">{{ $mutasi->asal_tujuan }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-2">Alasan</label>
                        <p class="text-gray-900">{{ $mutasi->alasan }}</p>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Detail Default -->
        <div class="bg-white rounded-2xl shadow-lg border-0 p-6 sm:p-8">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-info-circle text-gray-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Detail Mutasi</h3>
                    <p class="text-sm text-gray-600">Informasi lengkap data mutasi penduduk</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-xl p-4">
                <label class="block text-sm font-medium text-gray-500 mb-2 uppercase tracking-wide">Jenis Mutasi</label>
                @php
                    $jenisColors = [
                        'kelahiran' => 'bg-green-100 text-green-800',
                        'kematian' => 'bg-red-100 text-red-800',
                        'pindah_masuk' => 'bg-blue-100 text-blue-800',
                        'pindah_keluar' => 'bg-yellow-100 text-yellow-800'
                    ];
                    $jenisIcons = [
                        'kelahiran' => 'fa-baby',
                        'kematian' => 'fa-cross',
                        'pindah_masuk' => 'fa-arrow-right',
                        'pindah_keluar' => 'fa-arrow-left'
                    ];
                @endphp
                <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium {{ $jenisColors[$mutasi->jenis_mutasi] ?? 'bg-gray-100 text-gray-800' }}">
                    <i class="fas {{ $jenisIcons[$mutasi->jenis_mutasi] ?? 'fa-question' }} mr-2"></i>
                    {{ ucfirst(str_replace('_', ' ', $mutasi->jenis_mutasi)) }}
                </span>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <label class="block text-sm font-medium text-gray-500 mb-2 uppercase tracking-wide">Kategori Mutasi</label>
                <p class="text-gray-900 font-medium">{{ ucfirst(str_replace('_', ' ', $mutasi->kategori_mutasi)) }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <label class="block text-sm font-medium text-gray-500 mb-2 uppercase tracking-wide">Tanggal Mutasi</label>
                <p class="text-gray-900 font-medium">{{ $mutasi->tanggal_mutasi->format('d F Y') }}</p>
            </div>
                @if($mutasi->jenis_mutasi == 'pindah_rt_rw' && !empty($mutasi->asal_tujuan) && str_contains($mutasi->asal_tujuan, ' ? '))
                    @php
                        $parts = explode(' ? ', $mutasi->asal_tujuan);
                        $asal = $parts[0] ?? '';
                        $tujuan = $parts[1] ?? '';
                    @endphp
                    <div class="bg-gray-50 rounded-xl p-4">
                        <label class="block text-sm font-medium text-gray-500 mb-2 uppercase tracking-wide">Asal</label>
                        <p class="text-gray-900 font-medium">{{ $asal }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <label class="block text-sm font-medium text-gray-500 mb-2 uppercase tracking-wide">Tujuan</label>
                        <p class="text-gray-900 font-medium">{{ $tujuan }}</p>
                    </div>
                @else
            <div class="bg-gray-50 rounded-xl p-4">
                <label class="block text-sm font-medium text-gray-500 mb-2 uppercase tracking-wide">Asal/Tujuan</label>
                <p class="text-gray-900 font-medium">{{ $mutasi->asal_tujuan }}</p>
            </div>
                @endif
            <div class="lg:col-span-2 bg-gray-50 rounded-xl p-4">
                <label class="block text-sm font-medium text-gray-500 mb-2 uppercase tracking-wide">Alasan</label>
                <p class="text-gray-900 leading-relaxed">{{ $mutasi->alasan }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Link ke Detail Penduduk (jika ada) -->
    @if($mutasi->penduduk && $mutasi->jenis_mutasi != 'kelahiran')
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Data Penduduk Terkait</h3>
                <p class="text-sm text-gray-600">Lihat detail lengkap data penduduk</p>
            </div>
            <a href="{{ route('penduduk.show', $mutasi->penduduk) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <i class="fas fa-eye mr-2"></i>
                Lihat Detail Penduduk
            </a>
        </div>
    </div>
    @endif
</div>

@noncescript
document.addEventListener('DOMContentLoaded', function () {
    const actionBtn = document.getElementById('mutasiActionBtn');
    if (actionBtn) {
        actionBtn.addEventListener('click', confirmAction);
    }
});

// SweetAlert untuk konfirmasi cancel/undo
function confirmAction() {

    @if($mutasi->isSoftDeleteType())
        // UNDO - Kembalikan data yang di-soft delete
        Swal.fire({
            title: 'Konfirmasi Undo',
            text: 'Apakah Anda yakin ingin mengembalikan data mutasi ini? Data penduduk akan dikembalikan ke database.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Undo!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Mengembalikan...',
                    text: 'Sedang mengembalikan data mutasi',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit undo request
                fetch('{{ route('mutasi.undo', $mutasi) }}', {
                    method: 'POST',
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
                        text: 'Data mutasi berhasil dikembalikan.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mengembalikan data.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            }
        });
    @else
        // CANCEL - Batalkan mutasi
    Swal.fire({
            title: 'Konfirmasi Cancel',
            text: 'Apakah Anda yakin ingin membatalkan mutasi ini? Log mutasi akan dihapus.',
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
                fetch('{{ route('mutasi.cancel', $mutasi) }}', {
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
    @endif
}
@endnoncescript
@endsection


