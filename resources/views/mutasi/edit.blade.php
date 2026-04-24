@extends('layouts.app')

@section('title', 'Edit Mutasi')
@section('subtitle', 'Ubah data mutasi penduduk')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-edit text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Edit Mutasi</h1>
                    <p class="text-green-100 text-sm sm:text-base">Ubah data mutasi penduduk</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('mutasi.data.show', $mutasi) }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-eye mr-2"></i>
                    Lihat Detail
                </a>
                <a href="{{ route('mutasi.data.index') }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-lg border-0 p-6 sm:p-8">
        <div class="flex items-center space-x-3 mb-8">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-edit text-green-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Form Edit Mutasi</h3>
                <p class="text-sm text-gray-600">Ubah data mutasi penduduk dengan benar</p>
            </div>
        </div>

        <form method="POST" action="{{ route('mutasi.data.update', $mutasi) }}" class="space-y-8" id="mutasiForm">
            @csrf
            @method('PUT')

            <!-- Jenis Mutasi (Read Only) -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-6 border border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-info-circle text-gray-600"></i>
                    </div>
                    Informasi Mutasi
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Mutasi</label>
                        <div class="px-4 py-3 bg-white border border-gray-300 rounded-lg">
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
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $jenisColors[$mutasi->jenis_mutasi] ?? 'bg-gray-100 text-gray-800' }}">
                                <i class="fas {{ $jenisIcons[$mutasi->jenis_mutasi] ?? 'fa-question' }} mr-2"></i>
                                {{ ucfirst(str_replace('_', ' ', $mutasi->jenis_mutasi)) }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Mutasi</label>
                        <div class="px-4 py-3 bg-white border border-gray-300 rounded-lg">
                            <span class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $mutasi->kategori_mutasi)) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Dinamis Berdasarkan Jenis Mutasi -->
            <div id="formContent" class="space-y-6">
                @if($mutasi->jenis_mutasi == 'kematian')
                    <!-- Form Edit Kematian -->
                    <div class="bg-gradient-to-r from-red-50 to-rose-50 rounded-xl p-6 border border-red-200">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-user-times text-red-600 mr-3"></i>
                            Edit Data Kematian
                        </h3>

                        <!-- Data Penduduk yang Meninggal -->
                        <div class="bg-white rounded-lg p-4 mb-6 border border-red-200">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-user text-red-600 mr-2"></i>
                                Data Penduduk yang Meninggal
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="penduduk_id" class="block text-sm font-medium text-gray-700 mb-2">Penduduk yang Meninggal</label>
                                    <select name="penduduk_id" id="penduduk_id"
                                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required>
                                        <option value="">Pilih penduduk...</option>
                                        @foreach($penduduks as $penduduk)
                                            <option value="{{ $penduduk->id }}" {{ old('penduduk_id', $mutasi->penduduk_id) == $penduduk->id ? 'selected' : '' }}>
                                                {{ $penduduk->nama }} ({{ $penduduk->nik }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('penduduk_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Penduduk</label>
                                    <div class="mt-1 p-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600">
                                        @if($mutasi->penduduk)
                                            <p class="mb-1"><strong>Nama:</strong> {{ $mutasi->penduduk->nama }}</p>
                                            <p class="mb-1"><strong>Jenis Kelamin:</strong> {{ $mutasi->penduduk->jenis_kelamin }}</p>
                                            <p class="mb-1"><strong>Umur:</strong> {{ \Carbon\Carbon::parse($mutasi->penduduk->tanggal_lahir)->age }} Tahun</p>
                                            <p class="mb-1"><strong>Agama:</strong> {{ $mutasi->penduduk->agama ?? '-' }}</p>
                                            <p class="mb-0"><strong>Alamat:</strong> {{ $mutasi->penduduk->alamat }}, RT {{ $mutasi->penduduk->rt }}/RW {{ $mutasi->penduduk->rw }}</p>
                                        @else
                                            <p class="text-red-600">Data penduduk tidak ditemukan</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Kematian -->
                        <div class="bg-white rounded-lg p-4 border border-red-200">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-calendar-alt text-red-600 mr-2"></i>
                                Detail Kematian
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @php
                                    $kematian = $mutasi->data_kematian ?? [];
                                @endphp
                                <div>
                                    <label for="hari_meninggal" class="block text-sm font-medium text-gray-700 mb-2">Hari Meninggal</label>
                                    <select name="hari_meninggal" id="hari_meninggal"
                                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required>
                                        <option value="">Pilih hari...</option>
                                        <option value="Senin" {{ old('hari_meninggal', $kematian['hari'] ?? '') == 'Senin' ? 'selected' : '' }}>Senin</option>
                                        <option value="Selasa" {{ old('hari_meninggal', $kematian['hari'] ?? '') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                                        <option value="Rabu" {{ old('hari_meninggal', $kematian['hari'] ?? '') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                                        <option value="Kamis" {{ old('hari_meninggal', $kematian['hari'] ?? '') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                                        <option value="Jumat" {{ old('hari_meninggal', $kematian['hari'] ?? '') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                                        <option value="Sabtu" {{ old('hari_meninggal', $kematian['hari'] ?? '') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                                        <option value="Minggu" {{ old('hari_meninggal', $kematian['hari'] ?? '') == 'Minggu' ? 'selected' : '' }}>Minggu</option>
                                    </select>
                                    @error('hari_meninggal') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="tanggal_mutasi" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Meninggal</label>
                                    <input type="date" name="tanggal_mutasi" id="tanggal_mutasi"
                                           value="{{ old('tanggal_mutasi', $mutasi->tanggal_mutasi->format('Y-m-d')) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required>
                                    @error('tanggal_mutasi') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="jam_meninggal" class="block text-sm font-medium text-gray-700 mb-2">Jam Meninggal</label>
                                    <input type="time" name="jam_meninggal" id="jam_meninggal"
                                           value="{{ old('jam_meninggal', $kematian['jam'] ?? '') }}"
                                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required>
                                    @error('jam_meninggal') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="bertempat_di" class="block text-sm font-medium text-gray-700 mb-2">Bertempat di</label>
                                    <input type="text" name="bertempat_di" id="bertempat_di"
                                           value="{{ old('bertempat_di', $kematian['bertempat_di'] ?? '') }}"
                                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required
                                           placeholder="Rumah Duka, Rumah Sakit, dll">
                                    @error('bertempat_di') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label for="alasan" class="block text-sm font-medium text-gray-700 mb-2">Penyebab Kematian</label>
                                    <input type="text" name="alasan" id="alasan"
                                           value="{{ old('alasan', $mutasi->alasan) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required
                                           placeholder="Penyebab kematian">
                                    @error('alasan') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Detail Pemakaman -->
                        @php
                            $pemakaman = $mutasi->data_pemakaman ?? [];
                        @endphp
                        <div class="bg-white rounded-lg p-4 border border-red-200">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>
                                Dimakamkan pada
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div>
                                    <label for="hari_pemakaman" class="block text-sm font-medium text-gray-700 mb-2">Hari</label>
                                    <select name="hari_pemakaman" id="hari_pemakaman"
                                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required>
                                        <option value="">Pilih hari...</option>
                                        <option value="Senin" {{ old('hari_pemakaman', $pemakaman['hari'] ?? '') == 'Senin' ? 'selected' : '' }}>Senin</option>
                                        <option value="Selasa" {{ old('hari_pemakaman', $pemakaman['hari'] ?? '') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                                        <option value="Rabu" {{ old('hari_pemakaman', $pemakaman['hari'] ?? '') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                                        <option value="Kamis" {{ old('hari_pemakaman', $pemakaman['hari'] ?? '') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                                        <option value="Jumat" {{ old('hari_pemakaman', $pemakaman['hari'] ?? '') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                                        <option value="Sabtu" {{ old('hari_pemakaman', $pemakaman['hari'] ?? '') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                                        <option value="Minggu" {{ old('hari_pemakaman', $pemakaman['hari'] ?? '') == 'Minggu' ? 'selected' : '' }}>Minggu</option>
                                    </select>
                                    @error('hari_pemakaman') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="tanggal_pemakaman" class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                                    <input type="date" name="tanggal_pemakaman" id="tanggal_pemakaman"
                                           value="{{ old('tanggal_pemakaman', $pemakaman['tanggal'] ?? '') }}"
                                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required>
                                    @error('tanggal_pemakaman') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="jam_pemakaman" class="block text-sm font-medium text-gray-700 mb-2">Jam</label>
                                    <input type="time" name="jam_pemakaman" id="jam_pemakaman"
                                           value="{{ old('jam_pemakaman', $pemakaman['jam'] ?? '') }}"
                                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required>
                                    @error('jam_pemakaman') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="lokasi_pemakaman" class="block text-sm font-medium text-gray-700 mb-2">Dimakamkan di</label>
                                    <input type="text" name="lokasi_pemakaman" id="lokasi_pemakaman"
                                           value="{{ old('lokasi_pemakaman', $pemakaman['lokasi'] ?? '') }}"
                                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required
                                           placeholder="TPU Desa Cibatu, dll">
                                    @error('lokasi_pemakaman') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($mutasi->jenis_mutasi == 'kelahiran')
                    <!-- Form Edit Kelahiran -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-baby text-green-600 mr-3"></i>
                            Edit Data Kelahiran
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nama_bayi" class="block text-sm font-medium text-gray-700 mb-2">Nama Bayi</label>
                                <input type="text" name="nama_bayi" id="nama_bayi"
                                       value="{{ old('nama_bayi', $mutasi->penduduk->nama ?? '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors"
                                       placeholder="Nama lengkap bayi" required>
                                @error('nama_bayi') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="jenis_kelamin_bayi" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                                <select name="jenis_kelamin_bayi" id="jenis_kelamin_bayi"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors" required>
                                    <option value="">Pilih jenis kelamin...</option>
                                    <option value="LAKI-LAKI" {{ old('jenis_kelamin_bayi', $mutasi->penduduk->jenis_kelamin ?? '') == 'LAKI-LAKI' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="PEREMPUAN" {{ old('jenis_kelamin_bayi', $mutasi->penduduk->jenis_kelamin ?? '') == 'PEREMPUAN' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin_bayi') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir"
                                       value="{{ old('tempat_lahir', $mutasi->penduduk->tempat_lahir ?? '') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                                       placeholder="Tempat lahir bayi" required>
                                @error('tempat_lahir') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                                       value="{{ old('tanggal_lahir', $mutasi->penduduk->tanggal_lahir ?? '') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500" required>
                                @error('tanggal_lahir') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="nama_ayah" class="block text-sm font-medium text-gray-700 mb-2">Nama Ayah</label>
                                <input type="text" name="nama_ayah" id="nama_ayah"
                                       value="{{ old('nama_ayah', $mutasi->penduduk->nama_ayah ?? '') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                                       placeholder="Nama ayah bayi" required>
                                @error('nama_ayah') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="nama_ibu" class="block text-sm font-medium text-gray-700 mb-2">Nama Ibu</label>
                                <input type="text" name="nama_ibu" id="nama_ibu"
                                       value="{{ old('nama_ibu', $mutasi->penduduk->nama_ibu ?? '') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                                       placeholder="Nama ibu bayi" required>
                                @error('nama_ibu') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="nkk" class="block text-sm font-medium text-gray-700 mb-2">No Kartu Keluarga</label>
                                <input type="text" name="nkk" id="nkk"
                                       value="{{ old('nkk', $mutasi->penduduk->nkk ?? '') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                                       placeholder="No KK" required>
                                @error('nkk') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="tanggal_mutasi" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mutasi</label>
                                <input type="date" name="tanggal_mutasi" id="tanggal_mutasi"
                                       value="{{ old('tanggal_mutasi', $mutasi->tanggal_mutasi->format('Y-m-d')) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500" required>
                                @error('tanggal_mutasi') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Form Edit Default -->
                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl p-6 border border-blue-200">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-edit text-blue-600 mr-3"></i>
                            Edit Data Mutasi
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="penduduk_id" class="block text-sm font-medium text-gray-700 mb-2">Penduduk</label>
                        <select name="penduduk_id" id="penduduk_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('penduduk_id') border-red-500 @enderror"
                                required>
                            <option value="">Pilih Penduduk</option>
                            @foreach($penduduks as $penduduk)
                                <option value="{{ $penduduk->id }}" {{ old('penduduk_id', $mutasi->penduduk_id) == $penduduk->id ? 'selected' : '' }}>
                                    {{ $penduduk->nama }} ({{ $penduduk->nik }})
                                </option>
                            @endforeach
                        </select>
                        @error('penduduk_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                                <label for="kategori_mutasi" class="block text-sm font-medium text-gray-700 mb-2">Kategori Mutasi</label>
                        <select name="kategori_mutasi" id="kategori_mutasi"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kategori_mutasi') border-red-500 @enderror"
                                required>
                            <option value="">Pilih Kategori</option>
                            <option value="dalam_kota" {{ old('kategori_mutasi', $mutasi->kategori_mutasi) == 'dalam_kota' ? 'selected' : '' }}>Dalam Kota</option>
                            <option value="luar_kota" {{ old('kategori_mutasi', $mutasi->kategori_mutasi) == 'luar_kota' ? 'selected' : '' }}>Luar Kota</option>
                            <option value="luar_negeri" {{ old('kategori_mutasi', $mutasi->kategori_mutasi) == 'luar_negeri' ? 'selected' : '' }}>Luar Negeri</option>
                        </select>
                        @error('kategori_mutasi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                                <label for="tanggal_mutasi" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mutasi</label>
                        <input type="date" name="tanggal_mutasi" id="tanggal_mutasi" value="{{ old('tanggal_mutasi', $mutasi->tanggal_mutasi->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_mutasi') border-red-500 @enderror"
                               required>
                        @error('tanggal_mutasi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                                <label for="asal_tujuan" class="block text-sm font-medium text-gray-700 mb-2">Asal/Tujuan</label>
                        <input type="text" name="asal_tujuan" id="asal_tujuan" value="{{ old('asal_tujuan', $mutasi->asal_tujuan) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('asal_tujuan') border-red-500 @enderror"
                               placeholder="Masukkan asal atau tujuan mutasi"
                               required>
                        @error('asal_tujuan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                                <label for="alasan" class="block text-sm font-medium text-gray-700 mb-2">Alasan</label>
                        <textarea name="alasan" id="alasan" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('alasan') border-red-500 @enderror"
                                  placeholder="Jelaskan alasan mutasi"
                                  required>{{ old('alasan', $mutasi->alasan) }}</textarea>
                        @error('alasan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center justify-center transition-colors shadow-md">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
                <a href="{{ route('mutasi.data.show', $mutasi) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg flex items-center justify-center transition-colors shadow-md">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill hari berdasarkan tanggal untuk form kematian
    const tanggalInput = document.getElementById('tanggal_mutasi');
    const hariSelect = document.getElementById('hari_meninggal');

    if (tanggalInput && hariSelect) {
        tanggalInput.addEventListener('change', function() {
            const tanggal = new Date(this.value);
            const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][tanggal.getDay()];

            // Set hari yang sesuai
            hariSelect.value = hari;
        });
    }

    // Auto-fill hari pemakaman berdasarkan tanggal pemakaman
    const tanggalPemakamanInput = document.getElementById('tanggal_pemakaman');
    const hariPemakamanSelect = document.getElementById('hari_pemakaman');

    if (tanggalPemakamanInput && hariPemakamanSelect) {
        tanggalPemakamanInput.addEventListener('change', function() {
            const tanggal = new Date(this.value);
            const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][tanggal.getDay()];

            // Set hari yang sesuai
            hariPemakamanSelect.value = hari;
        });
    }
});
</script>
@endpush

