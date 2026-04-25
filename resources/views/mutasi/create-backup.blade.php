@extends('layouts.app')

@section('title', 'Tambah Mutasi')
@section('subtitle', 'Tambah data mutasi penduduk')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-plus text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Tambah Mutasi</h1>
                    <p class="text-green-100 text-sm sm:text-base">Input data mutasi penduduk baru</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('mutasi.index') }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-lg border-0 p-6 sm:p-8">
        <!-- Global Error Display -->
        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Terjadi kesalahan validasi:
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="flex items-center space-x-3 mb-8">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-edit text-green-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Form Data Mutasi</h3>
                <p class="text-sm text-gray-600">Lengkapi data mutasi penduduk dengan benar</p>
            </div>
        </div>

        <form action="{{ route('mutasi.store') }}" method="POST" class="space-y-8" id="mutasiForm" onsubmit="return false;">
            @csrf

            <!-- Pilih Jenis Mutasi -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6 sm:p-8 border border-green-200">
                <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-exchange-alt text-green-600"></i>
                    </div>
                    Pilih Jenis Mutasi
                </h3>
                <div>
                    <label for="jenis_mutasi" class="block text-sm font-medium text-gray-700 mb-3">
                        <i class="fas fa-list-alt text-green-600 mr-2"></i>Jenis Mutasi
                    </label>
                    <select name="jenis_mutasi" id="jenis_mutasi"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors" required>
                        <option value="">Pilih jenis mutasi...</option>
                        <option value="kelahiran">Kelahiran</option>
                        <option value="kematian">Kematian</option>
                        <option value="pindah_masuk">Pindah Masuk</option>
                        <option value="pindah_keluar">Pindah Keluar</option>
                        <option value="pindah_rt_rw">Pindah RT/RW/Dusun</option>
                        <option value="pisah_kk">Pisah KK</option>
                    </select>
                    @error('jenis_mutasi') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Form Dinamis Berdasarkan Jenis Mutasi -->
            <div id="formContent" class="space-y-6">
                <div id="defaultMessage" class="text-center py-12 text-gray-500">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-info-circle text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Pilih Jenis Mutasi</h3>
                    <p class="text-gray-600">Pilih jenis mutasi di atas untuk menampilkan form yang sesuai</p>
                </div>

                <!-- Form Kelahiran -->
                <div id="formKelahiran" class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6 sm:p-8 border border-green-200" style="display: none;">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-baby text-green-600"></i>
                        </div>
                        Data Kelahiran
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nama_bayi" class="block text-sm font-medium text-gray-700 mb-2">Nama Bayi</label>
                            <input type="text" name="nama_bayi" id="nama_bayi"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors"
                                   placeholder="Nama lengkap bayi" required>
                        </div>
                        <div>
                            <label for="jenis_kelamin_bayi" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                            <select name="jenis_kelamin_bayi" id="jenis_kelamin_bayi"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors" required>
                                <option value="">Pilih jenis kelamin...</option>
                                <option value="LAKI-LAKI">Laki-laki</option>
                                <option value="PEREMPUAN">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" id="tempat_lahir"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                                   placeholder="Tempat lahir bayi" required>
                        </div>
                        <div>
                            <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500" required>
                        </div>
                        <div>
                            <label for="nama_ayah" class="block text-sm font-medium text-gray-700 mb-2">Nama Ayah</label>
                            <input type="text" name="nama_ayah" id="nama_ayah"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                                   placeholder="Nama ayah bayi" required>
                        </div>
                        <div>
                            <label for="nama_ibu" class="block text-sm font-medium text-gray-700 mb-2">Nama Ibu</label>
                            <input type="text" name="nama_ibu" id="nama_ibu"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                                   placeholder="Nama ibu bayi" required>
                        </div>
                        <div>
                            <label for="nkk_kelahiran" class="block text-sm font-medium text-gray-700 mb-2">No Kartu Keluarga</label>
                            <div class="relative">
                                <input type="text" id="nkk_search_kelahiran"
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                                       placeholder="Cari No KK (NKK atau nama kepala keluarga)..."
                                       autocomplete="off">
                                <input type="hidden" name="nkk" id="nkk_kelahiran">
                                <div id="nkk_search_results_kelahiran" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                                <div id="nkk_search_loading_kelahiran" class="absolute right-3 top-3 hidden">
                                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                </div>
                            </div>
                            <div id="selected_nkk_kelahiran" class="mt-2 hidden">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-green-900" id="selected_nkk_name_kelahiran"></p>
                                            <p class="text-sm text-green-700" id="selected_nkk_info_kelahiran"></p>
                                        </div>
                                        <button type="button" onclick="clearNKKSelection('kelahiran')" class="text-green-400 hover:text-green-600">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="tanggal_mutasi_kelahiran" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mutasi</label>
                            <input type="date" name="tanggal_mutasi" id="tanggal_mutasi_kelahiran"
                                   value="{{ old('tanggal_mutasi', now()->format('Y-m-d')) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500" required>
                        </div>
                    </div>
                </div>

                <!-- Form Kematian -->
                <div id="formKematian" class="bg-gradient-to-r from-red-50 to-rose-50 rounded-xl p-6 border border-red-200" style="display: none;">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-user-times text-red-600 mr-3"></i>
                        Data Kematian
                    </h3>

                    <!-- Data Penduduk yang Meninggal -->
                    <div class="bg-white rounded-lg p-4 mb-6 border border-red-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-user text-red-600 mr-2"></i>
                            Data Penduduk yang Meninggal
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="penduduk_id_kematian" class="block text-sm font-medium text-gray-700 mb-2">Penduduk yang Meninggal</label>
                                <div class="relative">
                                    <input type="text" id="penduduk_search_kematian"
                                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                                           placeholder="Cari penduduk (NIK, nama, atau No KK)..."
                                           autocomplete="off">
                                    <input type="hidden" name="penduduk_id" id="penduduk_id_kematian">
                                    <div id="penduduk_search_results_kematian" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                                    <div id="penduduk_search_loading_kematian" class="absolute right-3 top-3 hidden">
                                        <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                    </div>
                                </div>
                                <div id="selected_penduduk_kematian" class="mt-2 hidden">
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-medium text-red-900" id="selected_penduduk_name_kematian"></p>
                                                <p class="text-sm text-red-700" id="selected_penduduk_info_kematian"></p>
                                            </div>
                                            <button type="button" onclick="clearPendudukSelection('kematian')" class="text-red-400 hover:text-red-600">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Data Penduduk</label>
                                <div id="penduduk_data_display_kematian" class="mt-1 p-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600">
                                    <p class="mb-1"><strong>Nama:</strong> <span id="display_nama_kematian">-</span></p>
                                    <p class="mb-1"><strong>Jenis Kelamin:</strong> <span id="display_jenis_kelamin_kematian">-</span></p>
                                    <p class="mb-1"><strong>Umur:</strong> <span id="display_umur_kematian">-</span></p>
                                    <p class="mb-1"><strong>Agama:</strong> <span id="display_agama_kematian">-</span></p>
                                    <p class="mb-0"><strong>Alamat:</strong> <span id="display_alamat_kematian">-</span></p>
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
                            <div>
                                <label for="hari_meninggal" class="block text-sm font-medium text-gray-700 mb-2">Hari Meninggal</label>
                                <select name="hari_meninggal" id="hari_meninggal"
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required>
                                    <option value="">Pilih hari...</option>
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                    <option value="Sabtu">Sabtu</option>
                                    <option value="Minggu">Minggu</option>
                                </select>
                                @error('hari_meninggal') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="tanggal_mutasi_kematian" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Meninggal</label>
                                <input type="date" name="tanggal_mutasi" id="tanggal_mutasi_kematian"
                                       value="{{ old('tanggal_mutasi', now()->format('Y-m-d')) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required>
                                @error('tanggal_mutasi') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="jam_meninggal" class="block text-sm font-medium text-gray-700 mb-2">Jam Meninggal</label>
                                <input type="time" name="jam_meninggal" id="jam_meninggal"
                                       value="{{ old('jam_meninggal', '21:00') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required>
                                @error('jam_meninggal') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="bertempat_di" class="block text-sm font-medium text-gray-700 mb-2">Bertempat di</label>
                                <input type="text" name="bertempat_di" id="bertempat_di"
                                       value="{{ old('bertempat_di', 'Rumah Duka') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required
                                       placeholder="Rumah Duka, Rumah Sakit, dll">
                                @error('bertempat_di') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="alasan" class="block text-sm font-medium text-gray-700 mb-2">Penyebab Kematian</label>
                                <input type="text" name="alasan" id="alasan"
                                       value="{{ old('alasan', 'Sakit') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required
                                       placeholder="Penyebab kematian">
                                @error('alasan') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Detail Pemakaman -->
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
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                    <option value="Sabtu">Sabtu</option>
                                    <option value="Minggu">Minggu</option>
                                </select>
                                @error('hari_pemakaman') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="tanggal_pemakaman" class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                                <input type="date" name="tanggal_pemakaman" id="tanggal_pemakaman"
                                       value="{{ old('tanggal_pemakaman', now()->format('Y-m-d')) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required>
                                @error('tanggal_pemakaman') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="jam_pemakaman" class="block text-sm font-medium text-gray-700 mb-2">Jam</label>
                                <input type="time" name="jam_pemakaman" id="jam_pemakaman"
                                       value="{{ old('jam_pemakaman', '16:00') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required>
                                @error('jam_pemakaman') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="lokasi_pemakaman" class="block text-sm font-medium text-gray-700 mb-2">Dimakamkan di</label>
                                <input type="text" name="lokasi_pemakaman" id="lokasi_pemakaman"
                                       value="{{ old('lokasi_pemakaman', 'TPU Desa Cibatu') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required
                                       placeholder="TPU Desa Cibatu, dll">
                                @error('lokasi_pemakaman') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Pindah Masuk -->
                <div id="formPindahMasuk" class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl p-6 border border-blue-200" style="display: none;">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-user-plus text-blue-600 mr-3"></i>
                        Data Pindah Masuk
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nama_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" name="nama" id="nama_pindah_masuk"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Nama lengkap penduduk" required>
                        </div>
                        <div>
                            <label for="nik_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">NIK</label>
                            <div class="relative">
                                <input type="text" name="nik" id="nik_pindah_masuk"
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="16 digit NIK" maxlength="16" required>
                                <div id="nik_check_loading_pindah_masuk" class="absolute right-3 top-3 hidden">
                                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                </div>
                            </div>
                            <div id="nikStatusInfoPindahMasuk" class="mt-2 text-sm" style="display: none;">
                                <div id="nikNewInfoPindahMasuk" class="text-green-700 bg-green-50 p-2 rounded-lg border border-green-200" style="display: none;">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    <strong>NIK Tersedia:</strong> NIK ini belum terdaftar di database
                                </div>
                                <div id="nikExistingInfoPindahMasuk" class="text-red-700 bg-red-50 p-2 rounded-lg border border-red-200" style="display: none;">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <strong>NIK Sudah Ada:</strong> <span id="existingNIKDetailsPindahMasuk"></span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Format: 16 digit angka</p>
                        </div>
                        <div>
                            <label for="jenis_kelamin_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin_pindah_masuk"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih jenis kelamin...</option>
                                <option value="LAKI-LAKI">Laki-laki</option>
                                <option value="PEREMPUAN">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label for="agama_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Agama</label>
                            <select name="agama" id="agama_pindah_masuk"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih agama...</option>
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Konghucu">Konghucu</option>
                            </select>
                        </div>
                        <div>
                            <label for="status_perkawinan_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Status Perkawinan</label>
                            <select name="status_perkawinan" id="status_perkawinan_pindah_masuk"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih status perkawinan...</option>
                                <option value="Belum Kawin">Belum Kawin</option>
                                <option value="Kawin">Kawin</option>
                                <option value="Cerai Hidup">Cerai Hidup</option>
                                <option value="Cerai Mati">Cerai Mati</option>
                            </select>
                        </div>
                        <div>
                            <label for="tempat_lahir_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" id="tempat_lahir_pindah_masuk"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Tempat lahir" required>
                        </div>
                        <div>
                            <label for="tanggal_lahir_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir_pindah_masuk"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label for="kedudukan_keluarga_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Kedudukan dalam Keluarga</label>
                            <select name="kedudukan_keluarga" id="kedudukan_keluarga_pindah_masuk"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih kedudukan...</option>
                                <option value="Kepala Keluarga">Kepala Keluarga</option>
                                <option value="Istri">Istri</option>
                                <option value="Anak">Anak</option>
                                <option value="Menantu">Menantu</option>
                                <option value="Cucu">Cucu</option>
                                <option value="Orangtua">Orangtua</option>
                                <option value="Mertua">Mertua</option>
                                <option value="Famili Lain">Famili Lain</option>
                                <option value="Pembantu">Pembantu</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label for="pendidikan_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Pendidikan</label>
                            <input type="text" name="pendidikan" id="pendidikan_pindah_masuk"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Contoh: SD, SMP, SMA, S1, dll" required>
                        </div>
                        <div>
                            <label for="pekerjaan_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan</label>
                            <input type="text" name="pekerjaan" id="pekerjaan_pindah_masuk"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Pekerjaan (opsional)">
                        </div>
                        <div>
                            <label for="nama_ayah_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Nama Ayah</label>
                            <input type="text" name="nama_ayah" id="nama_ayah_pindah_masuk"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Masukkan nama ayah">
                        </div>
                        <div>
                            <label for="nama_ibu_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Nama Ibu</label>
                            <input type="text" name="nama_ibu" id="nama_ibu_pindah_masuk"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Masukkan nama ibu">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilihan KK</label>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <input type="radio" id="kk_existing_pindah_masuk" name="kk_option_pindah_masuk" value="existing" class="mr-2">
                                    <label for="kk_existing_pindah_masuk" class="text-sm font-medium text-gray-700">Gabung ke KK yang sudah ada</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" id="kk_new_pindah_masuk" name="kk_option_pindah_masuk" value="new" class="mr-2">
                                    <label for="kk_new_pindah_masuk" class="text-sm font-medium text-gray-700">Buat KK baru</label>
                                </div>
                            </div>
                        </div>

                        <!-- Existing KK Selection -->
                        <div id="existingKKContainerPindahMasuk" class="md:col-span-2" style="display: none;">
                            <label for="nkk_existing_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Pilih KK yang sudah ada</label>
                            <div class="relative">
                                <input type="text" id="nkk_search_pindah_masuk"
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Cari No KK (NKK atau nama kepala keluarga)..."
                                       autocomplete="off">
                                <input type="hidden" name="nkk" id="nkk_existing_pindah_masuk">
                                <div id="nkk_search_results_pindah_masuk" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                                <div id="nkk_search_loading_pindah_masuk" class="absolute right-3 top-3 hidden">
                                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                </div>
                            </div>
                            <div id="selected_nkk_pindah_masuk" class="mt-2 hidden">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-blue-900" id="selected_nkk_name_pindah_masuk"></p>
                                            <p class="text-sm text-blue-700" id="selected_nkk_info_pindah_masuk"></p>
                                        </div>
                                        <button type="button" onclick="clearNKKSelection('pindah_masuk')" class="text-blue-400 hover:text-blue-600">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- New KK Input -->
                        <div id="newKKContainerPindahMasuk" class="md:col-span-2" style="display: none;">
                            <label for="nkk_new_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">No KK Baru</label>
                            <div class="relative">
                                <input type="text" name="nkk" id="nkk_new_pindah_masuk"
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Masukkan No KK baru (16 digit)"
                                       maxlength="16">
                                <div id="nkk_check_loading_pindah_masuk" class="absolute right-3 top-3 hidden">
                                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                </div>
                            </div>
                            <div id="nkkStatusInfoPindahMasuk" class="mt-2 text-sm" style="display: none;">
                                <div id="nkkNewInfoPindahMasuk" class="text-blue-700 bg-blue-50 p-2 rounded-lg border border-blue-200" style="display: none;">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <strong>KK Baru:</strong> Akan membuat keluarga baru dengan NKK ini
                                </div>
                                <div id="nkkExistingInfoPindahMasuk" class="text-orange-700 bg-orange-50 p-2 rounded-lg border border-orange-200" style="display: none;">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <strong>KK Sudah Ada:</strong> <span id="existingKKDetailsPindahMasuk"></span>
                                    <div class="mt-2">
                                        <button type="button" id="joinExistingKKPindahMasuk" class="text-sm bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded">
                                            <i class="fas fa-plus mr-1"></i>Gabung ke KK ini
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Format: 16 digit angka</p>
                        </div>
                        <div>
                            <label for="alamat_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                            <textarea name="alamat" id="alamat_pindah_masuk"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Alamat lengkap" rows="3" required></textarea>
                        </div>
                        <div>
                            <label for="rt_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">RT</label>
                            <select name="rt" id="rt_pindah_masuk"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih RT...</option>
                                <option value="001">001</option>
                                <option value="002">002</option>
                                <option value="003">003</option>
                                <option value="004">004</option>
                                <option value="005">005</option>
                                <option value="006">006</option>
                                <option value="007">007</option>
                                <option value="008">008</option>
                                <option value="009">009</option>
                                <option value="010">010</option>
                            </select>
                        </div>
                        <div>
                            <label for="rw_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">RW</label>
                            <select name="rw" id="rw_pindah_masuk"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih RW...</option>
                                <option value="001">001</option>
                                <option value="002">002</option>
                                <option value="003">003</option>
                                <option value="004">004</option>
                                <option value="005">005</option>
                            </select>
                        </div>
                        <div>
                            <label for="kategori_mutasi_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Kategori Mutasi</label>
                            <select name="kategori_mutasi" id="kategori_mutasi_pindah_masuk"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih kategori...</option>
                                <option value="dalam_kota">Dalam Kota</option>
                                <option value="luar_kota">Luar Kota</option>
                                <option value="luar_negeri">Luar Negeri</option>
                            </select>
                        </div>
                        <div>
                            <label for="asal_tujuan_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Asal Tempat</label>
                            <input type="text" name="asal_tujuan" id="asal_tujuan_pindah_masuk"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Asal daerah/kota" required>
                        </div>
                        <div>
                            <label for="tanggal_mutasi_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pindah</label>
                            <input type="date" name="tanggal_mutasi" id="tanggal_mutasi_pindah_masuk"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="md:col-span-2">
                            <label for="alasan_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Alasan Pindah (Opsional)</label>
                            <textarea name="alasan" id="alasan_pindah_masuk"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Alasan pindah masuk ke desa" rows="3"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label for="keterangan_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Keterangan Tambahan (Opsional)</label>
                            <textarea name="keterangan" id="keterangan_pindah_masuk"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Keterangan tambahan tentang penduduk" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Form Pindah Keluar -->
                <div id="formPindahKeluar" class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl p-6 border border-orange-200" style="display: none;">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-user-minus text-orange-600 mr-3"></i>
                        Data Pindah Keluar
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="penduduk_id_pindah_keluar" class="block text-sm font-medium text-gray-700 mb-2">Penduduk yang Pindah Keluar</label>
                            <div class="relative">
                                <input type="text" id="penduduk_search_pindah_keluar"
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="Cari penduduk (NIK, nama, atau No KK)..."
                                       autocomplete="off">
                                <input type="hidden" name="penduduk_id" id="penduduk_id_pindah_keluar">
                                <div id="penduduk_search_results_pindah_keluar" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                                <div id="penduduk_search_loading_pindah_keluar" class="absolute right-3 top-3 hidden">
                                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                </div>
                            </div>
                            <div id="selected_penduduk_pindah_keluar" class="mt-2 hidden">
                                <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-orange-900" id="selected_penduduk_name_pindah_keluar"></p>
                                            <p class="text-sm text-orange-700" id="selected_penduduk_info_pindah_keluar"></p>
                                        </div>
                                        <button type="button" onclick="clearPendudukSelection('pindah_keluar')" class="text-orange-400 hover:text-orange-600">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="kategori_mutasi_pindah_keluar" class="block text-sm font-medium text-gray-700 mb-2">Kategori Mutasi</label>
                            <select name="kategori_mutasi" id="kategori_mutasi_pindah_keluar"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500" required>
                                <option value="">Pilih kategori...</option>
                                <option value="dalam_kota">Dalam Kota</option>
                                <option value="luar_kota">Luar Kota</option>
                                <option value="luar_negeri">Luar Negeri</option>
                            </select>
                        </div>
                        <div>
                            <label for="asal_tujuan_pindah_keluar" class="block text-sm font-medium text-gray-700 mb-2">Tujuan</label>
                            <input type="text" name="asal_tujuan" id="asal_tujuan_pindah_keluar"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                   placeholder="Tujuan pindah" required>
                        </div>
                        <div>
                            <label for="tanggal_mutasi_pindah_keluar" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pindah Keluar</label>
                            <input type="date" name="tanggal_mutasi" id="tanggal_mutasi_pindah_keluar"
                                   value="{{ old('tanggal_mutasi', now()->format('Y-m-d')) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500" required>
                        </div>
                        <div class="md:col-span-2">
                            <label for="alasan_pindah_keluar" class="block text-sm font-medium text-gray-700 mb-2">Alasan Pindah</label>
                            <textarea name="alasan" id="alasan_pindah_keluar" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                      placeholder="Alasan pindah keluar dari desa"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Form Pindah RT/RW -->
                <div id="formPindahRTRW" class="bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl p-6 border border-purple-200" style="display: none;">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-home text-purple-600 mr-3"></i>
                        Data Pindah RT/RW/Dusun
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nkk_pindah_rt_rw" class="block text-sm font-medium text-gray-700 mb-2">No KK yang Akan Pindah</label>
                            <div class="relative">
                                <input type="text" id="nkk_search_pindah_rt_rw"
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500"
                                       placeholder="Cari No KK (NKK atau nama kepala keluarga)..."
                                       autocomplete="off">
                                <input type="hidden" name="nkk" id="nkk_pindah_rt_rw">
                                <div id="nkk_search_results_pindah_rt_rw" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                                <div id="nkk_search_loading_pindah_rt_rw" class="absolute right-3 top-3 hidden">
                                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                </div>
                            </div>
                            <div id="selected_nkk_pindah_rt_rw" class="mt-2 hidden">
                                <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-purple-900" id="selected_nkk_name_pindah_rt_rw"></p>
                                            <p class="text-sm text-purple-700" id="selected_nkk_info_pindah_rt_rw"></p>
                                        </div>
                                        <button type="button" onclick="clearNKKSelection('pindah_rt_rw')" class="text-purple-400 hover:text-purple-600">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="rt_tujuan" class="block text-sm font-medium text-gray-700 mb-2">RT Tujuan</label>
                            <select name="rt_tujuan" id="rt_tujuan"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500" required>
                                <option value="">Pilih RT...</option>
                                <option value="001">RT 001 (Dusun Satu)</option>
                                <option value="002">RT 002 (Dusun Satu)</option>
                                <option value="003">RT 003 (Dusun Satu)</option>
                                <option value="004">RT 004 (Dusun Satu)</option>
                                <option value="005">RT 005 (Dusun Dua)</option>
                                <option value="006">RT 006 (Dusun Dua)</option>
                                <option value="007">RT 007 (Dusun Satu)</option>
                                <option value="008">RT 008 (Dusun Satu)</option>
                                <option value="009">RT 009 (Dusun Dua)</option>
                                <option value="010">RT 010 (Dusun Dua)</option>
                            </select>
                        </div>
                        <div>
                            <label for="rw_tujuan" class="block text-sm font-medium text-gray-700 mb-2">RW Tujuan</label>
                            <select name="rw_tujuan" id="rw_tujuan"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500" required>
                                <option value="">Pilih RW...</option>
                                <option value="001">RW 001 (RT 001, 002)</option>
                                <option value="002">RW 002 (RT 003, 004)</option>
                                <option value="003">RW 003 (RT 007, 008)</option>
                                <option value="004">RW 004 (RT 005, 006)</option>
                                <option value="005">RW 005 (RT 009, 010)</option>
                            </select>
                        </div>
                        <div>
                            <label for="dusun_tujuan" class="block text-sm font-medium text-gray-700 mb-2">Dusun Tujuan</label>
                            <select name="dusun_tujuan" id="dusun_tujuan"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Pilih dusun...</option>
                                <option value="Dusun 1">Dusun 1</option>
                                <option value="Dusun 2">Dusun 2</option>
                            </select>
                        </div>
                        <div>
                            <label for="alamat_tujuan" class="block text-sm font-medium text-gray-700 mb-2">Alamat Tujuan</label>
                            <input type="text" name="alamat_tujuan" id="alamat_tujuan"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="Alamat lengkap tujuan">
                        </div>
                        <div>
                            <label for="tanggal_mutasi_pindah_rt_rw" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pindah</label>
                            <input type="date" name="tanggal_mutasi" id="tanggal_mutasi_pindah_rt_rw"
                                   value="{{ old('tanggal_mutasi', now()->format('Y-m-d')) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500" required>
                        </div>
                        <!-- Info Pindah Satu KK -->
                        <div class="md:col-span-2">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-green-600 mr-2 mt-1"></i>
                                    <div>
                                        <h4 class="font-medium text-green-900">Pindah RT/RW Satu KK</h4>
                                        <p class="text-sm text-green-700 mt-1">
                                            <strong>Seluruh anggota keluarga</strong> akan ikut pindah ke RT/RW/Dusun baru.
                                            No KK tetap sama, hanya alamat yang berubah.
                                        </p>
                                        <p class="text-sm text-blue-700 mt-2">
                                            <i class="fas fa-lightbulb mr-1"></i>
                                            <strong>Catatan:</strong> Untuk memisahkan anggota keluarga (buat KK baru), gunakan menu <strong>"Pisah KK"</strong>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <h4 class="font-medium text-green-900 mb-3">Info KK yang Akan Dipindah</h4>
                                <div id="infoKKLama">
                                    <p class="text-sm text-green-700">
                                        Pilih No KK terlebih dahulu untuk melihat info KK yang akan dipindah.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label for="alasan_pindah_rt_rw" class="block text-sm font-medium text-gray-700 mb-2">Alasan Pindah (Opsional)</label>
                            <input type="text" name="asal_tujuan" id="alasan_pindah_rt_rw"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="Contoh: Rumah baru, mengikuti keluarga, dll">
                        </div>
                    </div>
                </div>

                <!-- Form Pisah KK -->
                <div id="formPisahKK" class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200" style="display: none;">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-scissors text-green-600 mr-3"></i>
                        Data Pisah KK
                    </h3>

                    <!-- Person Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="penduduk_id_pisah" class="block text-sm font-medium text-gray-700 mb-2">Penduduk yang Akan Pisah KK</label>
                            <div class="relative">
                                <input type="text" id="penduduk_search_pisah"
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                                       placeholder="Cari penduduk (NIK, nama, atau No KK)..."
                                       autocomplete="off">
                                <input type="hidden" name="penduduk_id" id="penduduk_id_pisah">
                                <div id="penduduk_search_results_pisah" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                                <div id="penduduk_search_loading_pisah" class="absolute right-3 top-3 hidden">
                                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                </div>
                            </div>
                            <div id="selected_penduduk_pisah" class="mt-2 hidden">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-green-900" id="selected_penduduk_name_pisah"></p>
                                            <p class="text-sm text-green-700" id="selected_penduduk_info_pisah"></p>
                                        </div>
                                        <button type="button" onclick="clearPendudukSelection('pisah_kk')" class="text-green-400 hover:text-green-600">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="kategori_mutasi_pisah" class="block text-sm font-medium text-gray-700 mb-2">Kategori Pisah KK</label>
                            <select name="kategori_mutasi" id="kategori_mutasi_pisah"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500" required>
                                <option value="">Pilih kategori...</option>
                                <option value="dalam_desa">Dalam Desa (Tetap di Desa Cibatu)</option>
                                <option value="dalam_kota">Dalam Kota (Pindah ke desa/kota lain)</option>
                                <option value="luar_kota">Luar Kota (Pindah ke kota lain)</option>
                                <option value="luar_negeri">Luar Negeri (Pindah ke luar negeri)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Current Family Info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h4 class="font-medium text-blue-900 mb-3">Info Keluarga Saat Ini</h4>
                        <div id="currentFamilyInfo">
                            <p class="text-sm text-blue-700">Pilih penduduk terlebih dahulu untuk melihat info keluarga.</p>
                        </div>
                    </div>

                    <!-- KK Options (only for dalam_desa) -->
                    <div id="kkOptionsContainer" class="mb-6" style="display: none;">
                        <h4 class="font-medium text-gray-900 mb-3">Opsi Kartu Keluarga</h4>
                        <div class="space-y-4">
                            <div class="flex items-center space-x-4">
                                <input type="radio" id="kk_new" name="kk_option" value="new" class="text-green-600 focus:ring-green-500">
                                <label for="kk_new" class="text-sm font-medium text-gray-700">Buat KK Baru (Input NKK Manual)</label>
                            </div>
                            <div class="flex items-center space-x-4">
                                <input type="radio" id="kk_existing" name="kk_option" value="existing" class="text-green-600 focus:ring-green-500">
                                <label for="kk_existing" class="text-sm font-medium text-gray-700">Gabung ke KK yang Sudah Ada</label>
                            </div>
                        </div>
                    </div>

                    <!-- Status Kependudukan -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">Status Kependudukan Setelah Pisah KK</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="kedudukan_keluarga_pisah" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kedudukan dalam Keluarga <span class="text-red-500">*</span>
                                </label>
                                <select name="kedudukan_keluarga_pisah" id="kedudukan_keluarga_pisah"
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500" required>
                                    <option value="">Pilih kedudukan...</option>
                                    <option value="Kepala Keluarga">Kepala Keluarga</option>
                                    <option value="Istri">Istri</option>
                                    <option value="Anak">Anak</option>
                                    <option value="Menantu">Menantu</option>
                                    <option value="Cucu">Cucu</option>
                                    <option value="Orangtua">Orangtua</option>
                                    <option value="Mertua">Mertua</option>
                                    <option value="Famili Lain">Famili Lain</option>
                                    <option value="Pembantu">Pembantu</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">
                                    <span id="kedudukanInfo">Pilih opsi KK terlebih dahulu untuk melihat info kedudukan</span>
                                </p>
                            </div>
                            <div>
                                <label for="status_perkawinan_pisah" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status Perkawinan
                                </label>
                                <select name="status_perkawinan_pisah" id="status_perkawinan_pisah"
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
                                    <option value="">Pilih status...</option>
                                    <option value="Belum Kawin">Belum Kawin</option>
                                    <option value="Kawin">Kawin</option>
                                    <option value="Cerai Hidup">Cerai Hidup</option>
                                    <option value="Cerai Mati">Cerai Mati</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Input NKK Baru (only for dalam_desa + new) -->
                    <div id="newKKContainer" class="mb-6" style="display: none;">
                        <label for="nkk_baru_pisah" class="block text-sm font-medium text-gray-700 mb-2">No KK Baru</label>
                        <div class="relative">
                            <input type="text" name="nkk_baru" id="nkk_baru_pisah"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                                   placeholder="Masukkan No KK baru (16 digit)"
                                   maxlength="16">
                            <div id="nkk_check_loading" class="absolute right-3 top-3 hidden">
                                <i class="fas fa-spinner fa-spin text-gray-400"></i>
                            </div>
                        </div>
                        <div id="nkkStatusInfo" class="mt-2 text-sm" style="display: none;">
                            <div id="nkkNewInfo" class="text-blue-700 bg-blue-50 p-2 rounded-lg border border-blue-200" style="display: none;">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>KK Baru:</strong> Akan membuat keluarga baru dengan NKK ini
                            </div>
                    <div id="nkkExistingInfo" class="text-orange-700 bg-orange-50 p-2 rounded-lg border border-orange-200" style="display: none;">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>KK Sudah Ada:</strong> <span id="existingKKDetails"></span>
                        <div class="mt-2">
                            <button type="button" id="joinExistingKK" class="text-sm bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded">
                                <i class="fas fa-plus mr-1"></i>Gabung ke KK ini
                            </button>
                        </div>
                    </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Format: 16 digit angka</p>
                    </div>

                    <!-- Select Existing KK (only for dalam_desa + existing) -->
                    <div id="existingKKContainer" class="mb-6" style="display: none;">
                        <label for="nkk_existing_pisah" class="block text-sm font-medium text-gray-700 mb-2">Pilih No KK yang Sudah Ada</label>
                        <div class="relative">
                            <input type="text" name="nkk_existing" id="nkk_existing_pisah"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                                   placeholder="Ketik No KK atau nama kepala keluarga..."
                                   autocomplete="off">
                            <input type="hidden" name="nkk_existing_id" id="nkk_existing_id">

                            <!-- Search Results Dropdown -->
                            <div id="kkSearchResults" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                                <!-- Results will be loaded here via AJAX -->
                            </div>

                            <!-- Loading indicator -->
                            <div id="kkSearchLoading" class="absolute right-3 top-3 hidden">
                                <i class="fas fa-spinner fa-spin text-gray-400"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Ketik minimal 3 karakter untuk mencari KK</p>
                    </div>

                    <!-- New Address (only for new KK or keluar desa) -->
                    <div id="addressContainer" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6" style="display: none;">
                        <div class="md:col-span-2">
                            <h4 class="font-medium text-gray-900 mb-3">Alamat KK Baru</h4>
                        </div>
                        <div class="md:col-span-2">
                            <label for="alamat_pisah" class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                            <textarea name="alamat" id="alamat_pisah" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                                      placeholder="Masukkan alamat lengkap untuk KK baru"></textarea>
                        </div>
                        <div>
                            <label for="rt_pisah" class="block text-sm font-medium text-gray-700 mb-2">RT</label>
                            <select name="rt" id="rt_pisah"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
                                <option value="">Pilih RT</option>
                                @for($i = 1; $i <= 10; $i++)
                                    @php $rtValue = str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $rtValue }}">RT {{ $rtValue }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="rw_pisah" class="block text-sm font-medium text-gray-700 mb-2">RW</label>
                            <select name="rw" id="rw_pisah"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
                                <option value="">Pilih RW</option>
                                @for($i = 1; $i <= 5; $i++)
                                    @php $rwValue = str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $rwValue }}">RW {{ $rwValue }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Info for joining existing KK -->
                    <div id="existingKKInfo" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6" style="display: none;">
                        <h4 class="font-medium text-blue-900 mb-2">Info KK yang Akan Dituju</h4>
                        <div id="selectedKKInfo">
                            <p class="text-sm text-blue-700">Pilih No KK terlebih dahulu untuk melihat info alamat.</p>
                        </div>
                    </div>


                    <!-- Mutation Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="tanggal_mutasi_pisah" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pisah KK</label>
                            <input type="date" name="tanggal_mutasi" id="tanggal_mutasi_pisah"
                                   value="{{ old('tanggal_mutasi', now()->format('Y-m-d')) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500" required>
                        </div>
                        <div>
                            <label for="alasan_pisah" class="block text-sm font-medium text-gray-700 mb-2">Alasan Pisah KK</label>
                            <input type="text" name="alasan" id="alasan_pisah"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                                   placeholder="Contoh: Menikah, mandiri, dll">
                        </div>
                        <div id="tujuanPisahContainer" class="md:col-span-2" style="display: none;">
                            <label for="asal_tujuan_pisah" class="block text-sm font-medium text-gray-700 mb-2">Tujuan Pisah KK</label>
                            <input type="text" name="asal_tujuan" id="asal_tujuan_pisah"
                                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                                   placeholder="Contoh: Desa/Kota tujuan, alamat lengkap">
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-yellow-600 mr-2 mt-1"></i>
                            <div>
                                <h4 class="font-medium text-yellow-900">Informasi Pisah KK</h4>
                                <ul class="text-sm text-yellow-800 mt-2 space-y-1">
                                    <li>• Proses ini akan tercatat dalam riwayat mutasi</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('mutasi.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors flex items-center">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" id="submitBtn"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-save mr-2"></i>
                    <span id="submitText">Simpan Mutasi</span>
                </button>
            </div>
        </form>
    </div>
</div>

@noncescript

// Form validation and submission
document.getElementById('mutasiForm').addEventListener('submit', function(e) {
    const jenisMutasi = document.getElementById('jenis_mutasi').value;
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');

    if (!jenisMutasi) {
        e.preventDefault();
        alert('Pilih jenis mutasi terlebih dahulu!');
        return;
    }

    submitBtn.disabled = true;
    submitText.textContent = 'Menyimpan...';

    if (!validateForm(jenisMutasi)) {
        e.preventDefault();
        submitBtn.disabled = false;
        submitText.textContent = 'Simpan Mutasi';
        return;
    }
});

function validateForm(jenisMutasi) {
    switch(jenisMutasi) {
        case 'kelahiran': return validateKelahiran();
        case 'kematian': return validateKematian();
        case 'pindah_masuk': return validatePindahMasuk();
        case 'pindah_keluar': return validatePindahKeluar();
        case 'pindah_rt_rw': return validatePindahRTRW();
        case 'pisah_kk': return validatePisahKK();
        default: return true;
    }
}

function validateKelahiran() {
    const required = ['nama_bayi', 'jenis_kelamin_bayi', 'tempat_lahir', 'tanggal_lahir', 'nama_ayah', 'nama_ibu', 'nkk', 'tanggal_mutasi'];
    return validateRequiredFields(required);
}

function validateKematian() {
    const required = ['penduduk_id_kematian', 'tanggal_mutasi_kematian', 'hari_meninggal', 'jam_meninggal', 'bertempat_di', 'hari_pemakaman', 'tanggal_pemakaman', 'jam_pemakaman', 'lokasi_pemakaman', 'alasan'];

    for (const fieldName of required) {
        const field = document.getElementById(fieldName);
        if (!field || !field.value.trim()) {
            const fieldLabel = fieldName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: `${fieldLabel} harus diisi!`,
                confirmButtonColor: '#ef4444'
            });
            if (field) field.focus();
            return false;
        }
    }

    return true;
}

function validatePindahMasuk() {
    const required = ['nama', 'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'kedudukan_keluarga', 'nkk', 'alamat', 'rt', 'rw', 'kategori_mutasi', 'asal_tujuan', 'tanggal_mutasi'];

    for (let field of required) {
        const element = document.getElementById(field + '_pindah_masuk') || document.getElementById(field);
        if (!element || !element.value.trim()) {
            showError(`Field ${field.replace('_', ' ')} harus diisi!`);
            if (element) element.focus();
            return false;
        }
    }

    return true;
}

function validatePindahKeluar() {
        const pendudukId = document.getElementById('penduduk_id_pindah_keluar');
        if (!pendudukId.value) {
            showError('Field penduduk harus dipilih!');
            pendudukId.focus();
            return false;
        }
        return true;
    }

function validatePindahMasuk() {
    const kkOption = document.querySelector('input[name="kk_option"]:checked');
    if (!kkOption) {
        showError('Pilih opsi KK terlebih dahulu!');
        return false;
    }

    if (kkOption.value === 'existing') {
        // Validate new KK data
        const nkkBaru = document.getElementById('nkk_baru');
        const alamatNkk = document.getElementById('alamat_nkk');
        const rtNkk = document.getElementById('rt_nkk');
        const rwNkk = document.getElementById('rw_nkk');

        if (!nkkBaru.value) {
            alert('Field No KK baru harus diisi!');
            nkkBaru.focus();
            return false;
        }
        if (!alamatNkk.value) {
            alert('Field alamat harus diisi!');
            alamatNkk.focus();
            return false;
        }
        if (!rtNkk.value) {
            alert('Field RT harus dipilih!');
            rtNkk.focus();
            return false;
        }
        if (!rwNkk.value) {
            alert('Field RW harus dipilih!');
            rwNkk.focus();
            return false;
        }

        // Validate kepala keluarga data
        const namaKepala = document.getElementById('nama_kepala_keluarga');
        const nikKepala = document.getElementById('nik_kepala_keluarga');
        const jenisKelaminKepala = document.getElementById('jenis_kelamin_kepala');
        const tempatLahirKepala = document.getElementById('tempat_lahir_kepala');
        const tanggalLahirKepala = document.getElementById('tanggal_lahir_kepala');

        if (!namaKepala.value) {
            alert('Field nama kepala keluarga harus diisi!');
            namaKepala.focus();
            return false;
        }
        if (!nikKepala.value) {
            alert('Field NIK kepala keluarga harus diisi!');
            nikKepala.focus();
            return false;
        }
        if (!/^\d{16}$/.test(nikKepala.value)) {
            alert('NIK kepala keluarga harus 16 digit angka!');
            nikKepala.focus();
            return false;
        }
        if (!jenisKelaminKepala.value) {
            alert('Field jenis kelamin kepala keluarga harus dipilih!');
            jenisKelaminKepala.focus();
            return false;
        }
        if (!tempatLahirKepala.value) {
            alert('Field tempat lahir kepala keluarga harus diisi!');
            tempatLahirKepala.focus();
            return false;
        }
        if (!tanggalLahirKepala.value) {
            alert('Field tanggal lahir kepala keluarga harus diisi!');
            tanggalLahirKepala.focus();
            return false;
        }
    }

    // Validate family members if any
    const familyMemberNames = document.querySelectorAll('input[name="anggota_nama[]"]');
    for (let i = 0; i < familyMemberNames.length; i++) {
        if (familyMemberNames[i].value) {
            const memberContainer = familyMemberNames[i].closest('.mb-6');
            const nikInput = memberContainer.querySelector('input[name="anggota_nik[]"]');
            const kedudukanSelect = memberContainer.querySelector('select[name="anggota_kedudukan[]"]');
            const jenisKelaminSelect = memberContainer.querySelector('select[name="anggota_jenis_kelamin[]"]');
            const tempatLahirInput = memberContainer.querySelector('input[name="anggota_tempat_lahir[]"]');
            const tanggalLahirInput = memberContainer.querySelector('input[name="anggota_tanggal_lahir[]"]');

            if (!nikInput.value) {
                alert(`NIK anggota keluarga #${i + 1} harus diisi!`);
                nikInput.focus();
                return false;
            }
            if (!/^\d{16}$/.test(nikInput.value)) {
                alert(`NIK anggota keluarga #${i + 1} harus 16 digit angka!`);
                nikInput.focus();
                return false;
            }
            if (!kedudukanSelect.value) {
                alert(`Kedudukan anggota keluarga #${i + 1} harus dipilih!`);
                kedudukanSelect.focus();
                return false;
            }
            if (!jenisKelaminSelect.value) {
                alert(`Jenis kelamin anggota keluarga #${i + 1} harus dipilih!`);
                jenisKelaminSelect.focus();
                return false;
            }
            if (!tempatLahirInput.value) {
                alert(`Tempat lahir anggota keluarga #${i + 1} harus diisi!`);
                tempatLahirInput.focus();
                return false;
            }
            if (!tanggalLahirInput.value) {
                alert(`Tanggal lahir anggota keluarga #${i + 1} harus diisi!`);
                tanggalLahirInput.focus();
                return false;
            }
        }
    }

    return true;
}

function validatePindahKeluar() {
    const pendudukId = document.getElementById('penduduk_id_pindah_keluar').value;
    const kategoriMutasi = document.getElementById('kategori_mutasi').value;
    const asalTujuan = document.getElementById('asal_tujuan').value;
    const tanggalMutasi = document.getElementById('tanggal_mutasi').value;

    if (!pendudukId) {
        showError('Pilih penduduk yang pindah!');
        document.getElementById('penduduk_id_pindah_keluar').focus();
        return false;
    }

    if (!kategoriMutasi) {
        showError('Pilih kategori mutasi!');
        return false;
    }

    if (!asalTujuan) {
        showError('Alamat tujuan harus diisi!');
        return false;
    }

    if (!tanggalMutasi) {
        showError('Tanggal mutasi harus diisi!');
        return false;
    }

    return true;
}

function validatePindahRTRW() {
    const nkk = document.getElementById('nkk_pindah_rt_rw').value;
    const tanggalMutasi = document.getElementById('tanggal_mutasi_pindah_rt_rw').value;
    const rtTujuan = document.getElementById('rt_tujuan').value;
    const rwTujuan = document.getElementById('rw_tujuan').value;

    if (!nkk) {
        showError('Pilih No KK yang akan pindah!');
        document.getElementById('nkk_pindah_rt_rw').focus();
        return false;
    }

    if (!tanggalMutasi) {
        showError('Tanggal mutasi harus diisi!');
        document.getElementById('tanggal_mutasi_pindah_rt_rw').focus();
        return false;
    }

    if (!rtTujuan) {
        alert('RT tujuan harus diisi!');
        document.getElementById('rt_tujuan').focus();
        return false;
    }

    if (!rwTujuan) {
        alert('RW tujuan harus diisi!');
        document.getElementById('rw_tujuan').focus();
        return false;
    }

    // Untuk pindah RT/RW satu KK, semua anggota ikut pindah
    return true;
}

function validatePisahKK() {
    const pendudukId = document.getElementById('penduduk_id_pisah').value;
    const kategoriMutasi = document.getElementById('kategori_mutasi_pisah').value;
    const tanggalMutasi = document.getElementById('tanggal_mutasi_pisah').value;

    if (!pendudukId) {
        alert('Pilih penduduk yang akan Pisah KK!');
        document.getElementById('penduduk_id_pisah').focus();
        return false;
    }

    if (!kategoriMutasi) {
        alert('Pilih kategori mutasi!');
        document.getElementById('kategori_mutasi_pisah').focus();
        return false;
    }

    if (!tanggalMutasi) {
        alert('Tanggal mutasi harus diisi!');
        document.getElementById('tanggal_mutasi_pisah').focus();
        return false;
    }

    // Validasi status kependudukan
    const kedudukanKeluarga = document.getElementById('kedudukan_keluarga_pisah').value;
    if (!kedudukanKeluarga) {
        alert('Kedudukan dalam keluarga harus diisi!');
        document.getElementById('kedudukan_keluarga_pisah').focus();
        return false;
    }

    // Validasi berdasarkan kategori
    if (kategoriMutasi === 'dalam_desa') {
        const kkOption = document.querySelector('input[name="kk_option"]:checked');
        if (!kkOption) {
            alert('Pilih opsi Kartu Keluarga!');
            return false;
        }

        if (kkOption.value === 'existing') {
            const nkkExistingId = document.getElementById('nkk_existing_id').value;
            if (!nkkExistingId) {
                alert('Pilih No KK yang sudah ada!');
                document.getElementById('nkk_existing_pisah').focus();
                return false;
            }
        } else if (kkOption.value === 'new') {
            const nkkBaru = document.getElementById('nkk_baru_pisah').value;
            if (!nkkBaru) {
                alert('No KK baru harus diisi!');
                document.getElementById('nkk_baru_pisah').focus();
                return false;
            }
            if (nkkBaru.length !== 16) {
                alert('No KK harus 16 digit!');
                document.getElementById('nkk_baru_pisah').focus();
                return false;
            }
        }
    } else {
        // Keluar desa - alamat wajib
        const alamat = document.getElementById('alamat_pisah').value;
        const rt = document.getElementById('rt_pisah').value;
        const rw = document.getElementById('rw_pisah').value;

        if (!alamat) {
            alert('Alamat harus diisi!');
            document.getElementById('alamat_pisah').focus();
            return false;
        }

        if (!rt) {
            alert('RT harus diisi!');
            document.getElementById('rt_pisah').focus();
            return false;
        }

        if (!rw) {
            alert('RW harus diisi!');
            document.getElementById('rw_pisah').focus();
            return false;
        }
    }

    return true;
}

// Global functions that can be accessed from onclick handlers
// Function to auto-set hari based on tanggal
function setHariFromTanggal() {
    const tanggalInput = document.getElementById('tanggal_mutasi_kematian');
    const hariSelect = document.getElementById('hari_meninggal');

    if (tanggalInput && hariSelect && tanggalInput.value) {
        const date = new Date(tanggalInput.value);
        const hariNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const hariName = hariNames[date.getDay()];

        // Set the selected option
        for (let option of hariSelect.options) {
            if (option.value === hariName) {
                option.selected = true;
                break;
            }
        }
    }
}

// Function to auto-set hari pemakaman based on tanggal pemakaman
function setHariPemakamanFromTanggal() {
    const tanggalInput = document.getElementById('tanggal_pemakaman');
    const hariSelect = document.getElementById('hari_pemakaman');

    if (tanggalInput && hariSelect && tanggalInput.value) {
        const date = new Date(tanggalInput.value);
        const hariNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const hariName = hariNames[date.getDay()];

        // Set the selected option
        for (let option of hariSelect.options) {
            if (option.value === hariName) {
                option.selected = true;
                break;
            }
        }
    }
}

// Function to clear NKK selection
function clearNKKSelection(formType) {
    const nkkInput = document.getElementById(`nkk_${formType}`);
    const selectedNKKDiv = document.getElementById(`selected_nkk_${formType}`);
    const searchInput = document.getElementById(`nkk_search_${formType}`);

    if (nkkInput) nkkInput.value = '';
    if (selectedNKKDiv) selectedNKKDiv.classList.add('hidden');
    if (searchInput) searchInput.value = '';
}

// Function to clear penduduk selection
function clearPendudukSelection(formType) {
    // Handle special case for pisah_kk form
    const actualFormType = formType === 'pisah_kk' ? 'pisah' : formType;

    const pendudukIdInput = document.getElementById(`penduduk_id_${actualFormType}`);
    const selectedPendudukDiv = document.getElementById(`selected_penduduk_${actualFormType}`);
    const searchInput = document.getElementById(`penduduk_search_${actualFormType}`);

    if (pendudukIdInput) pendudukIdInput.value = '';
    if (selectedPendudukDiv) selectedPendudukDiv.classList.add('hidden');
    if (searchInput) searchInput.value = '';
}

// Event listener untuk NKK select - update info KK
document.addEventListener('DOMContentLoaded', function() {
    // Event listeners akan ditambahkan saat form ditampilkan

    // Function to show/hide forms based on mutation type
    function showMutasiForm(jenisMutasi) {
        console.log('Selected mutation type:', jenisMutasi);

        // Hide all forms
        const defaultMessage = document.getElementById('defaultMessage');
        const formKelahiran = document.getElementById('formKelahiran');
        const formKematian = document.getElementById('formKematian');
        const formPindahMasuk = document.getElementById('formPindahMasuk');
        const formPindahKeluar = document.getElementById('formPindahKeluar');
        const formPindahRTRW = document.getElementById('formPindahRTRW');
        const formPisahKK = document.getElementById('formPisahKK');

        console.log('Form elements found:', {
            defaultMessage: !!defaultMessage,
            formKelahiran: !!formKelahiran,
            formKematian: !!formKematian,
            formPindahMasuk: !!formPindahMasuk,
            formPindahKeluar: !!formPindahKeluar,
            formPindahRTRW: !!formPindahRTRW,
            formPisahKK: !!formPisahKK
        });

        if (defaultMessage) defaultMessage.style.display = 'none';
        if (formKelahiran) formKelahiran.style.display = 'none';
        if (formKematian) formKematian.style.display = 'none';
        if (formPindahMasuk) formPindahMasuk.style.display = 'none';
        if (formPindahKeluar) formPindahKeluar.style.display = 'none';
        if (formPindahRTRW) formPindahRTRW.style.display = 'none';
        if (formPisahKK) formPisahKK.style.display = 'none';

        if (!jenisMutasi) {
            if (defaultMessage) defaultMessage.style.display = 'block';
            return;
        }

        // Disable all form fields first
        disableAllFormFields();

        // Show selected form and enable its fields
        console.log('Showing form for:', jenisMutasi);
        switch(jenisMutasi) {
            case 'kelahiran':
                if (formKelahiran) {
                    formKelahiran.style.display = 'block';
                    console.log('Showing formKelahiran');
                    enableFormFields('formKelahiran');
                }
                break;
            case 'kematian':
                console.log('Processing kematian case');
                if (formKematian) {
                    formKematian.style.display = 'block';
                    console.log('Showing formKematian - display set to block');
                    enableFormFields('formKematian');

                    // Add event listeners for kematian form
                    setTimeout(() => {
                        const tanggalInputKematian = document.getElementById('tanggal_mutasi_kematian');
                        if (tanggalInputKematian) {
                            tanggalInputKematian.addEventListener('change', setHariFromTanggal);
                        }

                        const tanggalPemakamanInput = document.getElementById('tanggal_pemakaman');
                        if (tanggalPemakamanInput) {
                            tanggalPemakamanInput.addEventListener('change', setHariPemakamanFromTanggal);
                        }
                    }, 100);
                } else {
                    console.error('formKematian element not found!');
                }
                break;
            case 'pindah_masuk':
                if (formPindahMasuk) {
                    formPindahMasuk.style.display = 'block';
                    console.log('Showing formPindahMasuk');
                    enableFormFields('formPindahMasuk');
                }
                break;
            case 'pindah_keluar':
                if (formPindahKeluar) {
                    formPindahKeluar.style.display = 'block';
                    console.log('Showing formPindahKeluar');
                    enableFormFields('formPindahKeluar');
                }
                break;
            case 'pindah_rt_rw':
                if (formPindahRTRW) {
                    formPindahRTRW.style.display = 'block';
                    console.log('Showing formPindahRTRW');
                    enableFormFields('formPindahRTRW');
                }
                break;
            case 'pisah_kk':
                if (formPisahKK) {
                    formPisahKK.style.display = 'block';
                    console.log('Showing formPisahKK');
                    enableFormFields('formPisahKK');
                }
                break;
            default:
                console.log('Unknown mutation type:', jenisMutasi);
        }
    }

    // Add event listener for jenis_mutasi select
    const jenisMutasiSelect = document.getElementById('jenis_mutasi');
    console.log('jenisMutasiSelect found:', !!jenisMutasiSelect);
    if (jenisMutasiSelect) {
        jenisMutasiSelect.addEventListener('change', function() {
            console.log('jenis_mutasi changed to:', this.value);
            showMutasiForm(this.value);
        });
    }

    const nkkSelect = document.getElementById('nkk_pindah_rt_rw');
    const infoKKLama = document.getElementById('infoKKLama');

    if (nkkSelect && infoKKLama) {
        nkkSelect.addEventListener('change', function() {
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                const kkInfo = selectedOption.text;

                // Extract info dari text option (format: "No KK: XXXXX - Kepala Keluarga - RT XX/RW XX - X orang")
                const match = kkInfo.match(/No KK: (\d+) - (.+) - RT (\d+)\/RW (\d+) - (\d+) orang/);

                if (match) {
                    const [, noKK, kepalaKeluarga, rt, rw, jumlahAnggota] = match;

                    infoKKLama.innerHTML = `
                        <div class="text-sm text-green-700">
                            <p><strong>No KK:</strong> ${noKK}</p>
                            <p><strong>Kepala Keluarga:</strong> ${kepalaKeluarga}</p>
                            <p><strong>RT/RW Asal:</strong> RT ${rt}/RW ${rw}</p>
                            <p><strong>Jumlah Anggota:</strong> ${jumlahAnggota} orang</p>
                            <p class="mt-2 p-2 bg-green-100 rounded border border-green-300">
                                <i class="fas fa-users mr-1"></i>
                                <strong>Semua ${jumlahAnggota} anggota keluarga</strong> dalam KK ini akan ikut pindah ke alamat baru.
                            </p>
                        </div>
                    `;
                }
            } else {
                infoKKLama.innerHTML = `
                    <p class="text-sm text-green-700">
                        Pilih No KK terlebih dahulu untuk melihat info KK yang akan dipindah.
                    </p>
                `;
            }
        });
    }
});

function disableAllFormFields() {
    // Disable all form fields in hidden forms
    const forms = ['formKelahiran', 'formKematian', 'formPindahMasuk', 'formPindahKeluar', 'formPindahRTRW', 'formPisahKK'];
    forms.forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.disabled = true;
                input.removeAttribute('required');
            });
        }
    });
}

function enableFormFields(formId) {
    const form = document.getElementById(formId);
    if (form) {
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.disabled = false;

            // Re-add required attribute for specific fields based on form type
            switch(formId) {
                case 'formKelahiran':
                    if (['nama_bayi', 'jenis_kelamin_bayi', 'tempat_lahir', 'tanggal_lahir', 'nama_ayah', 'nama_ibu', 'nkk', 'tanggal_mutasi'].includes(input.name)) {
                        input.setAttribute('required', 'required');
                    }
                    break;

                case 'formKematian':
                    if (['penduduk_id', 'tanggal_mutasi', 'hari_meninggal', 'jam_meninggal', 'bertempat_di', 'hari_pemakaman', 'tanggal_pemakaman', 'jam_pemakaman', 'lokasi_pemakaman', 'alasan'].includes(input.name)) {
                        input.setAttribute('required', 'required');
                    }
                    break;

                case 'formPindahMasuk':
                    if (['nama', 'nik', 'jenis_kelamin', 'agama', 'status_perkawinan', 'tempat_lahir', 'tanggal_lahir', 'kedudukan_keluarga', 'pendidikan', 'nkk', 'alamat', 'rt', 'rw', 'kategori_mutasi', 'asal_tujuan', 'tanggal_mutasi'].includes(input.name)) {
                        input.setAttribute('required', 'required');
                    }
                    break;

                case 'formPindahKeluar':
                    if (['penduduk_id', 'kategori_mutasi', 'asal_tujuan', 'tanggal_mutasi'].includes(input.name)) {
                        input.setAttribute('required', 'required');
                    }
                    break;

                case 'formPindahRTRW':
                    if (['nkk', 'tanggal_mutasi', 'rt_tujuan', 'rw_tujuan', 'asal_tujuan'].includes(input.name)) {
                        input.setAttribute('required', 'required');
                    }
                    // alamat_tujuan is optional
                    break;

                case 'formPisahKK':
                    if (['penduduk_id', 'kategori_mutasi', 'tanggal_mutasi'].includes(input.name)) {
                        input.setAttribute('required', 'required');
                    }
                    // Set required for alamat, rt, rw only if not dalam_desa
                    const kategoriPisah = document.getElementById('kategori_mutasi_pisah');
                    if (kategoriPisah && kategoriPisah.value !== 'dalam_desa') {
                        if (['alamat', 'rt', 'rw'].includes(input.name)) {
                            input.setAttribute('required', 'required');
                        }
                    }
                    // Check if dalam_desa and KK option
                    const kkExistingRadio = document.getElementById('kk_existing');
                    const kkNewRadio = document.getElementById('kk_new');
                    if (kategoriPisah && kategoriPisah.value === 'dalam_desa') {
                        if (kkExistingRadio && kkExistingRadio.checked && input.name === 'nkk_existing') {
                            input.setAttribute('required', 'required');
                        }
                        if (kkNewRadio && kkNewRadio.checked && input.name === 'nkk_baru') {
                            input.setAttribute('required', 'required');
                        }
                    }
                    break;
            }
        });
    }
}

function validateRequiredFields(fields) {
    for (let field of fields) {
        const element = document.getElementById(field);
        if (element && !element.value.trim()) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: `Field ${field} harus diisi!`,
                confirmButtonColor: '#ef4444'
            });
            element.focus();
            return false;
        }
    }
    return true;
}

function resetFormValidation() {
    document.querySelectorAll('input, select').forEach(input => {
        input.classList.remove('border-red-500', 'border-green-500');
    });
}

// Toggle KK Form
function toggleKKForm(option) {
    const kkExistingForm = document.getElementById('kkExistingForm');
    const kkNewForm = document.getElementById('kkNewForm');
    const kepalaKeluargaForm = document.getElementById('kepalaKeluargaForm');

    if (option === 'existing') {
        // Show existing KK form, hide new KK form and kepala keluarga form
        kkExistingForm.style.display = 'block';
        kkNewForm.style.display = 'none';
        kepalaKeluargaForm.style.display = 'none';

        // Enable existing form fields
        document.getElementById('nkk_existing').required = true;

        // Disable new form fields
        document.getElementById('nkk_baru').required = false;
        document.getElementById('alamat_nkk').required = false;
        document.getElementById('rt_nkk').required = false;
        document.getElementById('rw_nkk').required = false;

        // Disable kepala keluarga fields
        document.getElementById('nama_kepala_keluarga').required = false;
        document.getElementById('nik_kepala_keluarga').required = false;
        document.getElementById('jenis_kelamin_kepala').required = false;
        document.getElementById('tempat_lahir_kepala').required = false;
        document.getElementById('tanggal_lahir_kepala').required = false;

        // Add event listener to NKK dropdown
        const nkkSelect = document.getElementById('nkk_existing');
        nkkSelect.addEventListener('change', function() {
            loadKepalaKeluargaInfo(this.value);
        });
    } else {
        // Show new KK form and kepala keluarga form, hide existing KK form
        kkExistingForm.style.display = 'none';
        kkNewForm.style.display = 'block';
        kepalaKeluargaForm.style.display = 'block';

        // Disable existing form fields
        document.getElementById('nkk_existing').required = false;

        // Enable new form fields
        document.getElementById('nkk_baru').required = true;
        document.getElementById('alamat_nkk').required = true;
        document.getElementById('rt_nkk').required = true;
        document.getElementById('rw_nkk').required = true;

        // Enable kepala keluarga fields
        document.getElementById('nama_kepala_keluarga').required = true;
        document.getElementById('nik_kepala_keluarga').required = true;
        document.getElementById('jenis_kelamin_kepala').required = true;
        document.getElementById('tempat_lahir_kepala').required = true;
        document.getElementById('tanggal_lahir_kepala').required = true;
    }
}

// Update RW based on RT selection
function updateRWFromRT() {
    const rtSelect = document.getElementById('rt_nkk');
    const rwSelect = document.getElementById('rw_nkk');
    const rt = rtSelect.value;

    // Clear RW options
    rwSelect.innerHTML = '<option value="">Pilih RW...</option>';

    // Map RT to RW
    const rtRwMap = {
        '001': '001', '002': '001',  // RT 001,002 → RW 001
        '003': '002', '004': '002',  // RT 003,004 → RW 002
        '007': '003', '008': '003',  // RT 007,008 → RW 003
        '005': '004', '006': '004',  // RT 005,006 → RW 004
        '009': '005', '010': '005'   // RT 009,010 → RW 005
    };

    if (rt && rtRwMap[rt]) {
        const rw = rtRwMap[rt];
        const rtList = Object.keys(rtRwMap).filter(key => rtRwMap[key] === rw);
        rwSelect.innerHTML = `<option value="${rw}">RW ${rw} (RT ${rtList.join(', ')})</option>`;
        rwSelect.value = rw;
    }
}

// Family Members Management
let familyMemberCount = 0;

function addFamilyMember(kedudukan = '') {
    familyMemberCount++;
    const container = document.getElementById('familyMembersContainer');
    const emptyMessage = document.getElementById('emptyFamilyMessage');

    // Hide empty state message
    if (emptyMessage) {
        emptyMessage.style.display = 'none';
    }

    // Set default values based on kedudukan
    let defaultJenisKelamin = '';
    let defaultStatusPerkawinan = 'Belum Kawin';

    if (kedudukan === 'Istri') {
        defaultJenisKelamin = 'P';
        defaultStatusPerkawinan = 'Kawin';
    } else if (kedudukan === 'Anak') {
        defaultStatusPerkawinan = 'Belum Kawin';
    }

    const memberHtml = `
        <div id="familyMember${familyMemberCount}" class="mb-6 p-4 bg-white rounded-lg border border-purple-200 relative">
            <div class="flex justify-between items-center mb-4">
                <h5 class="text-md font-medium text-gray-900">👤 Anggota Keluarga #${familyMemberCount}</h5>
                <button type="button" onclick="removeFamilyMember(${familyMemberCount})"
                        class="text-red-600 hover:text-red-800 p-1 rounded">
                    <i class="fas fa-trash text-sm"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="anggota_nama[]"
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Nama lengkap anggota keluarga" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">NIK</label>
                    <input type="text" name="anggota_nik[]" maxlength="16"
                           class="nik-input mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500"
                           placeholder="16 digit NIK" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kedudukan dalam Keluarga</label>
                    <select name="anggota_kedudukan[]"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500" required>
                        <option value="">Pilih kedudukan...</option>
                        <option value="Istri" ${kedudukan === 'Istri' ? 'selected' : ''}>Istri</option>
                        <option value="Anak" ${kedudukan === 'Anak' ? 'selected' : ''}>Anak</option>
                        <option value="Menantu">Menantu</option>
                        <option value="Cucu">Cucu</option>
                        <option value="Orang Tua">Orang Tua</option>
                        <option value="Mertua">Mertua</option>
                        <option value="Saudara">Saudara</option>
                        <option value="Famili Lain">Famili Lain</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                    <select name="anggota_jenis_kelamin[]"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500" required>
                        <option value="">Pilih jenis kelamin...</option>
                        <option value="LAKI-LAKI" ${defaultJenisKelamin === 'LAKI-LAKI' ? 'selected' : ''}>Laki-laki</option>
                        <option value="PEREMPUAN" ${defaultJenisKelamin === 'PEREMPUAN' ? 'selected' : ''}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir</label>
                    <input type="text" name="anggota_tempat_lahir[]"
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Tempat lahir" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                    <input type="date" name="anggota_tanggal_lahir[]"
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan</label>
                    <input type="text" name="anggota_pekerjaan[]"
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Pekerjaan (opsional)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Perkawinan</label>
                    <select name="anggota_status_perkawinan[]"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        <option value="Belum Kawin" ${defaultStatusPerkawinan === 'Belum Kawin' ? 'selected' : ''}>Belum Kawin</option>
                        <option value="Kawin" ${defaultStatusPerkawinan === 'Kawin' ? 'selected' : ''}>Kawin</option>
                        <option value="Cerai Hidup">Cerai Hidup</option>
                        <option value="Cerai Mati">Cerai Mati</option>
                    </select>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', memberHtml);
}

function removeFamilyMember(memberId) {
    const memberElement = document.getElementById(`familyMember${memberId}`);
    if (memberElement) {
        memberElement.remove();
    }

    // Check if no more family members, show empty state
    const container = document.getElementById('familyMembersContainer');
    const emptyMessage = document.getElementById('emptyFamilyMessage');

    if (container && container.children.length === 0 && emptyMessage) {
        emptyMessage.style.display = 'block';
    }
}

// Auto-format NIK input (including dynamic family members)
document.addEventListener('input', function(e) {
    if (e.target.id === 'nik' || e.target.id === 'nik_kepala_keluarga' || e.target.classList.contains('nik-input')) {
        let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
        if (value.length > 16) value = value.substring(0, 16);
        e.target.value = value;
    }

    if (e.target.id === 'nkk_baru') {
        let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
        if (value.length > 16) value = value.substring(0, 16);
        e.target.value = value;
    }
});

// Load Kepala Keluarga Info when KK is selected
function loadKepalaKeluargaInfo(kkId) {
    if (!kkId) {
        // Clear info display if exists
        const infoDiv = document.getElementById('kkInfoDisplay');
        if (infoDiv) {
            infoDiv.remove();
        }
        return;
    }

    // Create or get info display div
    let infoDiv = document.getElementById('kkInfoDisplay');
    if (!infoDiv) {
        const kkExistingForm = document.getElementById('kkExistingForm');
        infoDiv = document.createElement('div');
        infoDiv.id = 'kkInfoDisplay';
        infoDiv.className = 'mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200';
        kkExistingForm.appendChild(infoDiv);
    }

    // Show loading state
    infoDiv.innerHTML = '<p class="text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat info kepala keluarga...</p>';

    // Fetch KK info from server
    fetch(`/api/v1/kartu-keluarga/${kkId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayKKInfo(data, infoDiv);
            } else {
                infoDiv.innerHTML = '<p class="text-red-500"><i class="fas fa-exclamation-triangle mr-2"></i>Gagal memuat info kepala keluarga</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            infoDiv.innerHTML = '<p class="text-red-500"><i class="fas fa-exclamation-triangle mr-2"></i>Error memuat data kepala keluarga</p>';
        });
}

function displayKKInfo(response, infoDiv) {
    const kkData = response.data;
    const hasKepalaKeluarga = response.has_kepala_keluarga;
    const kepalaKeluargaData = response.kepala_keluarga_data;
    const kepalaKeluargaForm = document.getElementById('kepalaKeluargaForm');

    // Basic KK Info
    let infoHtml = `
        <h5 class="font-medium text-gray-900 mb-3 flex items-center">
            <i class="fas fa-id-card text-blue-600 mr-2"></i>
            Info Kartu Keluarga
        </h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-4">
            <div class="flex justify-between">
                <span class="font-medium text-gray-700">No KK:</span>
                <span class="text-gray-900 font-mono">${kkData.no_kk}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium text-gray-700">RT/RW:</span>
                <span class="text-gray-900">RT ${kkData.rt}/RW ${kkData.rw}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium text-gray-700">Dusun:</span>
                <span class="text-gray-900">${kkData.dusun}</span>
            </div>
            <div class="md:col-span-2">
                <span class="font-medium text-gray-700">Alamat:</span>
                <span class="text-gray-900 ml-2">${kkData.alamat}</span>
            </div>
        </div>
    `;

    if (hasKepalaKeluarga) {
        // KK has kepala keluarga - show info and hide form
        infoHtml += `
            <div class="border-t pt-4">
                <h6 class="font-medium text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-crown text-green-600 mr-2"></i>
                    Kepala Keluarga
                </h6>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Nama:</span>
                        <span class="text-gray-900 font-medium">${kepalaKeluargaData.nama}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">NIK:</span>
                        <span class="text-gray-900 font-mono">${kepalaKeluargaData.nik}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Jenis Kelamin:</span>
                        <span class="text-gray-900">${kepalaKeluargaData.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Pekerjaan:</span>
                        <span class="text-gray-900">${kepalaKeluargaData.pekerjaan || '-'}</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 p-3 bg-green-100 rounded-lg border border-green-200">
                <p class="text-sm text-green-700 flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <strong>KK sudah memiliki kepala keluarga.</strong>
                </p>
                <p class="text-xs text-green-600 mt-1">
                    Anggota keluarga baru akan ditambahkan ke KK ini. Tidak perlu mengisi form kepala keluarga.
                </p>
            </div>
        `;

        // Hide kepala keluarga form
        if (kepalaKeluargaForm) {
            kepalaKeluargaForm.style.display = 'none';
        }

    } else {
        // KK doesn't have kepala keluarga - show warning and show form
        infoHtml += `
            <div class="border-t pt-4">
                <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-3 mt-1"></i>
                        <div>
                            <h6 class="font-medium text-yellow-800 mb-2">KK Belum Memiliki Kepala Keluarga</h6>
                            <p class="text-sm text-yellow-700 mb-3">
                                Kartu Keluarga ini belum memiliki data kepala keluarga.
                                Anda harus mengisi data kepala keluarga terlebih dahulu.
                            </p>
                            <div class="text-xs text-yellow-600">
                                <strong>Catatan:</strong> Kepala keluarga harus diisi sebelum menambah anggota keluarga lainnya.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 p-3 bg-blue-100 rounded-lg border border-blue-200">
                <p class="text-sm text-blue-700 flex items-center">
                    <i class="fas fa-arrow-down mr-2"></i>
                    <strong>Silakan isi form kepala keluarga di bawah ini.</strong>
                </p>
            </div>
        `;

        // Show kepala keluarga form
        if (kepalaKeluargaForm) {
            kepalaKeluargaForm.style.display = 'block';
            // Enable kepala keluarga fields
            enableKepalaKeluargaFields();
        }
    }

    infoDiv.innerHTML = infoHtml;
}

function enableKepalaKeluargaFields() {
    const fields = ['nama_kepala_keluarga', 'nik_kepala_keluarga', 'jenis_kelamin_kepala',
                   'tempat_lahir_kepala', 'tanggal_lahir_kepala'];
    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.required = true;
            field.disabled = false;
        }
    });
}

// Search KK function (OLD - DEPRECATED)
function searchNKKOld() {
    const searchInput = document.getElementById('nkk_search');
    const nkkSelect = document.getElementById('nkk_existing');
    const noResultsDiv = document.getElementById('no_nkk_results');
    const searchTerm = searchInput.value.toLowerCase().trim();

    let visibleCount = 0;

    // Loop through all options except the first one (placeholder)
    for (let i = 1; i < nkkSelect.options.length; i++) {
        const option = nkkSelect.options[i];
        const nkk = option.getAttribute('data-nkk') || '';
        const kepala = option.getAttribute('data-kepala') || '';
        const rt = option.getAttribute('data-rt') || '';
        const rw = option.getAttribute('data-rw') || '';
        const alamat = option.getAttribute('data-alamat') || '';

        // Check if search term matches any of the data
        const matches = nkk.includes(searchTerm) ||
                       kepala.includes(searchTerm) ||
                       rt.includes(searchTerm) ||
                       rw.includes(searchTerm) ||
                       alamat.includes(searchTerm) ||
                       (rt + rw).includes(searchTerm) || // RT+RW combined
                       ('rt' + rt).includes(searchTerm) || // "rt001"
                       ('rw' + rw).includes(searchTerm);   // "rw001"

        if (searchTerm === '' || matches) {
            option.style.display = '';
            visibleCount++;
        } else {
            option.style.display = 'none';
        }
    }

    // Show/hide no results message
    if (searchTerm !== '' && visibleCount === 0) {
        noResultsDiv.classList.remove('hidden');
        nkkSelect.style.borderColor = '#f59e0b'; // Yellow border
    } else {
        noResultsDiv.classList.add('hidden');
        nkkSelect.style.borderColor = ''; // Reset border
    }

    // Update search results info
    updateSearchInfo(searchTerm, visibleCount);
}

function updateSearchInfo(searchTerm, visibleCount) {
    const searchInput = document.getElementById('kk_search');

    if (searchTerm === '') {
        searchInput.style.backgroundColor = '';
        searchInput.placeholder = 'Ketik untuk mencari KK...';
    } else if (visibleCount > 0) {
        searchInput.style.backgroundColor = '#f0fdf4'; // Light green
        searchInput.placeholder = `${visibleCount} KK ditemukan`;
    } else {
        searchInput.style.backgroundColor = '#fefce8'; // Light yellow
        searchInput.placeholder = 'Tidak ada hasil';
    }
}

// Clear search function
function clearNKKSearch() {
    document.getElementById('nkk_search').value = '';
    searchNKK();
}

// Toggle tujuan field for Pisah KK based on kategori
document.addEventListener('DOMContentLoaded', function() {
    const kategoriPisah = document.getElementById('kategori_mutasi_pisah');
    const tujuanContainer = document.getElementById('tujuanPisahContainer');
    const tujuanInput = document.getElementById('asal_tujuan_pisah');
    const kkOptionsContainer = document.getElementById('kkOptionsContainer');
    const existingKKContainer = document.getElementById('existingKKContainer');
    const newKKContainer = document.getElementById('newKKContainer');
    const kkNewRadio = document.getElementById('kk_new');
    const kkExistingRadio = document.getElementById('kk_existing');
    const nkkExistingSelect = document.getElementById('nkk_existing_pisah');
    const nkkBaruInput = document.getElementById('nkk_baru_pisah');
    const nkkStatusInfo = document.getElementById('nkkStatusInfo');
    const nkkNewInfo = document.getElementById('nkkNewInfo');
    const nkkExistingInfo = document.getElementById('nkkExistingInfo');

    // Check NKK status when input changes
    if (nkkBaruInput && nkkStatusInfo && nkkNewInfo && nkkExistingInfo) {
        let nkkCheckTimeout;
        nkkBaruInput.addEventListener('input', function() {
            clearTimeout(nkkCheckTimeout);
            const nkkValue = this.value.trim();

            if (nkkValue.length === 16) {
                nkkCheckTimeout = setTimeout(() => {
                    checkNKKExists(nkkValue);
                }, 500); // 500ms debounce
            } else {
                hideNKKStatusInfo();
            }
        });
    }


    if (kategoriPisah && tujuanContainer && tujuanInput && kkOptionsContainer && existingKKContainer) {
        kategoriPisah.addEventListener('change', function() {
            if (this.value === 'dalam_desa') {
                // Dalam desa - show KK options, hide tujuan
                tujuanContainer.style.display = 'none';
                tujuanInput.removeAttribute('required');
                kkOptionsContainer.style.display = 'block';
            } else {
                // Keluar desa - hide KK options, show tujuan
                tujuanContainer.style.display = 'block';
                tujuanInput.setAttribute('required', 'required');
                kkOptionsContainer.style.display = 'none';
                existingKKContainer.style.display = 'none';
            }
        });
    }

    // Toggle KK selection based on radio button
    if (kkNewRadio && kkExistingRadio && existingKKContainer && newKKContainer && nkkExistingSelect && nkkBaruInput) {
        const addressContainer = document.getElementById('addressContainer');
        const existingKKInfo = document.getElementById('existingKKInfo');
        const alamatPisah = document.getElementById('alamat_pisah');
        const rtPisah = document.getElementById('rt_pisah');
        const rwPisah = document.getElementById('rw_pisah');
        const kedudukanKeluargaPisah = document.getElementById('kedudukan_keluarga_pisah');
        const statusPerkawinanPisah = document.getElementById('status_perkawinan_pisah');
        const kedudukanInfo = document.getElementById('kedudukanInfo');

        kkNewRadio.addEventListener('change', function() {
            if (this.checked) {
                existingKKContainer.style.display = 'none';
                newKKContainer.style.display = 'block';
                addressContainer.style.display = 'block';
                existingKKInfo.style.display = 'none';
                nkkExistingSelect.removeAttribute('required');
                nkkBaruInput.setAttribute('required', 'required');
                alamatPisah.setAttribute('required', 'required');
                rtPisah.setAttribute('required', 'required');
                rwPisah.setAttribute('required', 'required');

                // Set default kedudukan untuk KK baru
                if (kedudukanKeluargaPisah) {
                    kedudukanKeluargaPisah.value = 'Kepala Keluarga';
                    kedudukanInfo.textContent = 'Akan menjadi Kepala Keluarga di KK baru';
                }
                if (statusPerkawinanPisah) {
                    statusPerkawinanPisah.value = 'Kawin';
                }
            }
        });

        kkExistingRadio.addEventListener('change', function() {
            if (this.checked) {
                existingKKContainer.style.display = 'block';
                newKKContainer.style.display = 'none';
                addressContainer.style.display = 'none';
                existingKKInfo.style.display = 'block';
                nkkExistingSelect.setAttribute('required', 'required');
                nkkBaruInput.removeAttribute('required');
                alamatPisah.removeAttribute('required');
                rtPisah.removeAttribute('required');
                rwPisah.removeAttribute('required');

                // Set default kedudukan untuk gabung KK existing
                if (kedudukanKeluargaPisah) {
                    kedudukanKeluargaPisah.value = 'Anggota Keluarga';
                    kedudukanInfo.textContent = 'Akan menjadi Anggota Keluarga di KK yang sudah ada';
                }
                if (statusPerkawinanPisah) {
                    statusPerkawinanPisah.value = '';
                }
            }
        });
    }

    // KK Search functionality
    const nkkExistingInput = document.getElementById('nkk_existing_pisah');
    const nkkExistingId = document.getElementById('nkk_existing_id');
    const kkSearchResults = document.getElementById('kkSearchResults');
    const kkSearchLoading = document.getElementById('kkSearchLoading');
    const selectedKKInfo = document.getElementById('selectedKKInfo');

    let searchTimeout;
    let clickHandlerAdded = false; // Prevent duplicate event listeners

    if (nkkExistingInput && kkSearchResults) {
        // Show/hide search results
        nkkExistingInput.addEventListener('focus', function() {
            if (this.value.length >= 3) {
                kkSearchResults.classList.remove('hidden');
            }
        });

        // Hide search results when clicking outside (only add once)
        if (!clickHandlerAdded) {
            document.addEventListener('click', function(e) {
                if (nkkExistingInput && kkSearchResults &&
                    !nkkExistingInput.contains(e.target) &&
                    !kkSearchResults.contains(e.target)) {
                    kkSearchResults.classList.add('hidden');
                }
            });
            clickHandlerAdded = true;
        }

        // Search as user types
        nkkExistingInput.addEventListener('input', function() {
            const query = this.value.trim();

            // Clear previous timeout
            clearTimeout(searchTimeout);

            if (query.length < 3) {
                kkSearchResults.classList.add('hidden');
                return;
            }

            // Show loading
            if (kkSearchLoading) {
                kkSearchLoading.classList.remove('hidden');
            }
            kkSearchResults.classList.remove('hidden');

            // Debounce search
            searchTimeout = setTimeout(() => {
                searchKK(query);
            }, 300);
        });
    }

    function searchKK(query) {
        console.log('Searching for:', query);
        console.log('Route URL:', `{{ route('mutasi.search-kk') }}`);

        const url = `{{ route('mutasi.search-kk') }}?q=${encodeURIComponent(query)}`;
        console.log('Full URL:', url);
        console.log('Current location:', window.location.href);
        console.log('Base URL:', window.location.origin);

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin',
            cache: 'no-cache'
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);

            if (!response.ok) {
                if (response.status === 401) {
                    console.log('Unauthorized - redirecting to login');
                    window.location.href = '/login';
                    return;
                }
                console.error('HTTP error:', response.status, response.statusText);
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            kkSearchLoading.classList.add('hidden');
            displaySearchResults(data);
        })
        .catch(error => {
            console.error('Search error:', error);
            console.error('Error details:', {
                message: error.message,
                stack: error.stack,
                url: url
            });
            kkSearchLoading.classList.add('hidden');
            kkSearchResults.innerHTML = '<div class="p-3 text-red-600">Error saat mencari KK: ' + error.message + '</div>';
        });
    }

    function displaySearchResults(results) {
        if (!kkSearchResults) {
            console.warn('kkSearchResults element not found');
            return;
        }

        if (!results || results.length === 0) {
            kkSearchResults.innerHTML = '<div class="p-3 text-gray-500">Tidak ada KK yang ditemukan</div>';
            return;
        }

        let html = '';
        results.forEach((family, index) => {
            html += `
                <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 kk-search-item"
                     data-nkk="${family.nkk || ''}"
                     data-kepala="${family.kepala_keluarga || ''}"
                     data-rt="${family.rt || ''}"
                     data-rw="${family.rw || ''}"
                     data-dusun="${family.dusun || ''}"
                     data-alamat="${family.alamat || ''}"
                     data-anggota="${family.jumlah_anggota || 0}">
                    <div class="font-medium text-gray-900">No KK: ${family.nkk}</div>
                    <div class="text-sm text-gray-600">Kepala: ${family.kepala_keluarga}</div>
                    <div class="text-xs text-gray-500">RT ${family.rt}/RW ${family.rw} - ${family.jumlah_anggota} anggota</div>
                </div>
            `;
        });

        kkSearchResults.innerHTML = html;

        // Add click event listeners to search results
        const searchItems = kkSearchResults.querySelectorAll('.kk-search-item');
        searchItems.forEach(item => {
            item.addEventListener('click', function() {
                const nkk = this.dataset.nkk;
                const kepalaKeluarga = this.dataset.kepala;
                const rt = this.dataset.rt;
                const rw = this.dataset.rw;
                const dusun = this.dataset.dusun;
                const alamat = this.dataset.alamat;
                const jumlahAnggota = this.dataset.anggota;

                selectKK(nkk, kepalaKeluarga, rt, rw, dusun, alamat, jumlahAnggota);
            });
        });
    }

    // Add event listeners for penduduk search
    const pendudukSearchInputs = ['kematian', 'pindah_keluar', 'pisah'];
    pendudukSearchInputs.forEach(formType => {
        const searchInput = document.getElementById(`penduduk_search_${formType}`);
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchPenduduk(formType);
                }, 300); // 300ms debounce
            });
        }
    });

    // Add event listeners for NKK search
    const nkkSearchInputs = ['kelahiran', 'pindah_rt_rw'];
    nkkSearchInputs.forEach(formType => {
        const searchInput = document.getElementById(`nkk_search_${formType}`);
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchNKK(formType);
                }, 300); // 300ms debounce
            });
        }
    });

    // NKK baru input event listener is already handled above in the radio button section

    // Add event listener for NKK action button
    const joinExistingKKBtn = document.getElementById('joinExistingKK');

    if (joinExistingKKBtn) {
        joinExistingKKBtn.addEventListener('click', function() {
            // Switch to existing KK option
            const existingRadio = document.getElementById('kk_existing');
            if (existingRadio) {
                existingRadio.checked = true;
                existingRadio.dispatchEvent(new Event('change'));

                // Fill the existing KK search with current NKK
                const nkkBaru = document.getElementById('nkk_baru_pisah').value;
                const existingSearch = document.getElementById('nkk_existing_pisah');
                if (existingSearch) {
                    existingSearch.value = nkkBaru;
                    existingSearch.dispatchEvent(new Event('input'));
                }
            }
        });
    }

    // Add event listeners for NKK search (Pindah Masuk)
    const nkkSearchInputPindahMasuk = document.getElementById('nkk_search_pindah_masuk');
    const nkkSearchResultsPindahMasuk = document.getElementById('nkk_search_results_pindah_masuk');
    const nkkSearchLoadingPindahMasuk = document.getElementById('nkk_search_loading_pindah_masuk');

    if (nkkSearchInputPindahMasuk && nkkSearchResultsPindahMasuk && nkkSearchLoadingPindahMasuk) {
        let searchTimeoutPindahMasuk;
        nkkSearchInputPindahMasuk.addEventListener('input', function() {
            clearTimeout(searchTimeoutPindahMasuk);
            const query = this.value.trim();

            if (query.length >= 3) {
                searchTimeoutPindahMasuk = setTimeout(() => {
                    searchNKK('pindah_masuk');
                }, 300); // 300ms debounce
            } else {
                nkkSearchResultsPindahMasuk.classList.add('hidden');
            }
        });
    }

    function selectKK(nkk, kepalaKeluarga, rt, rw, dusun, alamat, jumlahAnggota, formType = '') {
        console.log('selectKK called with:', { nkk, kepalaKeluarga, formType });
        // Prevent infinite loop
        if (!nkk || !kepalaKeluarga) {
            console.warn('selectKK called with invalid parameters');
            return;
        }

        // Handle different form types
        if (formType === 'pindah_masuk') {
            // Set values for Pindah Masuk form
            const nkkExistingInputPindahMasuk = document.getElementById('nkk_existing_pindah_masuk');
            const selectedNKKDivPindahMasuk = document.getElementById('selected_nkk_pindah_masuk');
            const selectedNKKNamePindahMasuk = document.getElementById('selected_nkk_name_pindah_masuk');
            const selectedNKKInfoPindahMasuk = document.getElementById('selected_nkk_info_pindah_masuk');
            const nkkSearchResultsPindahMasuk = document.getElementById('nkk_search_results_pindah_masuk');
            const nkkSearchInputPindahMasuk = document.getElementById('nkk_search_pindah_masuk');

            console.log('Setting values for Pindah Masuk:', {
                nkkExistingInputPindahMasuk: !!nkkExistingInputPindahMasuk,
                selectedNKKDivPindahMasuk: !!selectedNKKDivPindahMasuk,
                selectedNKKNamePindahMasuk: !!selectedNKKNamePindahMasuk,
                selectedNKKInfoPindahMasuk: !!selectedNKKInfoPindahMasuk
            });

            if (nkkExistingInputPindahMasuk) {
                nkkExistingInputPindahMasuk.value = nkk;
            }

            if (selectedNKKDivPindahMasuk && selectedNKKNamePindahMasuk && selectedNKKInfoPindahMasuk) {
                selectedNKKNamePindahMasuk.textContent = kepalaKeluarga;
                selectedNKKInfoPindahMasuk.textContent = `No KK: ${nkk} - RT ${rt}/RW ${rw} - ${jumlahAnggota} anggota`;
                selectedNKKDivPindahMasuk.classList.remove('hidden');
            }

            if (nkkSearchResultsPindahMasuk) {
                nkkSearchResultsPindahMasuk.classList.add('hidden');
            }

            if (nkkSearchInputPindahMasuk) {
                nkkSearchInputPindahMasuk.value = '';
            }
        } else {
            // Set input values for other forms
            if (nkkExistingInput) {
                nkkExistingInput.value = `No KK: ${nkk} - ${kepalaKeluarga}`;
            }
            if (nkkExistingId) {
                nkkExistingId.value = nkk;
            }

            // Hide search results
            if (kkSearchResults) {
                kkSearchResults.classList.add('hidden');
            }

            // Show KK info
            if (selectedKKInfo) {
                selectedKKInfo.innerHTML = `
                    <div class="text-sm text-blue-700">
                        <p><strong>No KK: ${nkk} - ${kepalaKeluarga}</strong></p>
                        <p class="text-xs">RT ${rt}/RW ${rw} - ${jumlahAnggota} anggota</p>
                        <p class="mt-1">Alamat akan mengikuti KK yang dipilih (tidak perlu input alamat baru)</p>
                    </div>
                `;
            }
        }
    }

    // Penduduk search functions
    function searchPenduduk(formType) {
        const searchInput = document.getElementById(`penduduk_search_${formType}`);
        const searchResults = document.getElementById(`penduduk_search_results_${formType}`);
        const searchLoading = document.getElementById(`penduduk_search_loading_${formType}`);

        if (!searchInput || !searchResults || !searchLoading) {
            console.warn(`Search elements not found for form type: ${formType}`);
            return;
        }

        const query = searchInput.value.trim();

        if (query.length < 3) {
            searchResults.classList.add('hidden');
            return;
        }

        searchLoading.classList.remove('hidden');
        searchResults.classList.add('hidden');

        fetch(`{{ route('mutasi.search-penduduk') }}?q=${encodeURIComponent(query)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin',
            cache: 'no-cache'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            searchLoading.classList.add('hidden');
            displayPendudukSearchResults(data, formType);
        })
        .catch(error => {
            console.error('Search error:', error);
            searchLoading.classList.add('hidden');
            searchResults.innerHTML = '<div class="p-3 text-red-600">Error saat mencari penduduk: ' + error.message + '</div>';
            searchResults.classList.remove('hidden');
        });
    }

    function displayPendudukSearchResults(results, formType) {
        const searchResults = document.getElementById(`penduduk_search_results_${formType}`);

        if (!searchResults) {
            console.warn(`Search results element not found for form type: ${formType}`);
            return;
        }

        if (!results || results.length === 0) {
            searchResults.innerHTML = '<div class="p-3 text-gray-500">Tidak ada penduduk yang ditemukan</div>';
            searchResults.classList.remove('hidden');
            return;
        }

        let html = '';
        results.forEach(penduduk => {
            html += `
                <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 penduduk-search-item"
                     data-id="${penduduk.id}"
                     data-nik="${penduduk.nik || ''}"
                     data-nama="${penduduk.nama || ''}"
                     data-nkk="${penduduk.nkk || ''}"
                     data-rt="${penduduk.rt || ''}"
                     data-rw="${penduduk.rw || ''}"
                     data-dusun="${penduduk.dusun || ''}"
                     data-kedudukan="${penduduk.kedudukan_keluarga || ''}">
                    <div class="font-medium text-gray-900">${penduduk.nama}</div>
                    <div class="text-sm text-gray-600">NIK: ${penduduk.nik}</div>
                    <div class="text-xs text-gray-500">RT ${penduduk.rt}/RW ${penduduk.rw} - ${penduduk.kedudukan_keluarga}</div>
                </div>
            `;
        });

        searchResults.innerHTML = html;
        searchResults.classList.remove('hidden');

        // Add click event listeners to search results
        const searchItems = searchResults.querySelectorAll('.penduduk-search-item');
        searchItems.forEach(item => {
            item.addEventListener('click', function() {
                const id = this.dataset.id;
                const nik = this.dataset.nik;
                const nama = this.dataset.nama;
                const nkk = this.dataset.nkk;
                const rt = this.dataset.rt;
                const rw = this.dataset.rw;
                const dusun = this.dataset.dusun;
                const kedudukan = this.dataset.kedudukan;

                selectPenduduk(id, nik, nama, nkk, rt, rw, dusun, kedudukan, formType);
            });
        });
    }

    function selectPenduduk(id, nik, nama, nkk, rt, rw, dusun, kedudukan, formType) {
        const pendudukIdInput = document.getElementById(`penduduk_id_${formType}`);
        const selectedPendudukDiv = document.getElementById(`selected_penduduk_${formType}`);
        const selectedPendudukName = document.getElementById(`selected_penduduk_name_${formType}`);
        const selectedPendudukInfo = document.getElementById(`selected_penduduk_info_${formType}`);
        const searchResults = document.getElementById(`penduduk_search_results_${formType}`);
        const searchInput = document.getElementById(`penduduk_search_${formType}`);

        if (!pendudukIdInput || !selectedPendudukDiv || !selectedPendudukName || !selectedPendudukInfo) {
            console.warn(`Selection elements not found for form type: ${formType}`);
            return;
        }

        // Set hidden input value
        pendudukIdInput.value = id;

        // Update display
        selectedPendudukName.textContent = nama;
        selectedPendudukInfo.textContent = `NIK: ${nik} - RT ${rt}/RW ${rw} - ${kedudukan}`;

        // Show selected penduduk info
        selectedPendudukDiv.classList.remove('hidden');

        // Hide search results and clear input
        if (searchResults) searchResults.classList.add('hidden');
        if (searchInput) searchInput.value = '';

        // Auto-fill penduduk data for kematian form
        if (formType === 'kematian') {
            fillPendudukDataForKematian(id);
        }
    }

    // Function to fetch and display penduduk data for kematian form
    function fillPendudukDataForKematian(pendudukId) {
        fetch(`/api/penduduk/${pendudukId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const penduduk = data.penduduk;

                    // Calculate age
                    const birthDate = new Date(penduduk.tanggal_lahir);
                    const today = new Date();
                    let age = today.getFullYear() - birthDate.getFullYear();
                    const monthDiff = today.getMonth() - birthDate.getMonth();
                    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                        age--;
                    }

                    // Update display
                    document.getElementById('display_nama_kematian').textContent = penduduk.nama;
                    document.getElementById('display_jenis_kelamin_kematian').textContent = penduduk.jenis_kelamin;
                    document.getElementById('display_umur_kematian').textContent = `${age} Tahun`;
                    document.getElementById('display_agama_kematian').textContent = penduduk.agama || '-';
                    document.getElementById('display_alamat_kematian').textContent = `${penduduk.alamat}, RT ${penduduk.rt}/RW ${penduduk.rw}`;
                }
            })
            .catch(error => {
                console.error('Error fetching penduduk data:', error);
            });
    }

    // NKK search functions
    function searchNKK(formType) {
        console.log('searchNKK called with formType:', formType);
        const searchInput = document.getElementById(`nkk_search_${formType}`);
        const searchResults = document.getElementById(`nkk_search_results_${formType}`);
        const searchLoading = document.getElementById(`nkk_search_loading_${formType}`);

        console.log('Search elements found:', {
            searchInput: !!searchInput,
            searchResults: !!searchResults,
            searchLoading: !!searchLoading
        });

        if (!searchInput || !searchResults || !searchLoading) {
            console.warn(`NKK search elements not found for form type: ${formType}`);
            return;
        }

        const query = searchInput.value.trim();

        if (query.length < 3) {
            searchResults.classList.add('hidden');
            return;
        }

        searchLoading.classList.remove('hidden');
        searchResults.classList.add('hidden');

        fetch(`{{ route('mutasi.search-kk') }}?q=${encodeURIComponent(query)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin',
            cache: 'no-cache'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            searchLoading.classList.add('hidden');
            displayNKKSearchResults(data, formType);
        })
        .catch(error => {
            console.error('NKK search error:', error);
            searchLoading.classList.add('hidden');
            searchResults.innerHTML = '<div class="p-3 text-red-600">Error saat mencari KK: ' + error.message + '</div>';
            searchResults.classList.remove('hidden');
        });
    }

    function displayNKKSearchResults(results, formType) {
        const searchResults = document.getElementById(`nkk_search_results_${formType}`);

        if (!searchResults) {
            console.warn(`NKK search results element not found for form type: ${formType}`);
            return;
        }

        if (!results || results.length === 0) {
            searchResults.innerHTML = '<div class="p-3 text-gray-500">Tidak ada KK yang ditemukan</div>';
            searchResults.classList.remove('hidden');
            return;
        }

        let html = '';
        results.forEach(family => {
            html += `
                <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 nkk-search-item"
                     data-nkk="${family.nkk || ''}"
                     data-kepala="${family.kepala_keluarga || ''}"
                     data-rt="${family.rt || ''}"
                     data-rw="${family.rw || ''}"
                     data-dusun="${family.dusun || ''}"
                     data-alamat="${family.alamat || ''}"
                     data-anggota="${family.jumlah_anggota || 0}">
                    <div class="font-medium text-gray-900">No KK: ${family.nkk}</div>
                    <div class="text-sm text-gray-600">Kepala: ${family.kepala_keluarga}</div>
                    <div class="text-xs text-gray-500">RT ${family.rt}/RW ${family.rw} - ${family.jumlah_anggota} anggota</div>
                </div>
            `;
        });

        searchResults.innerHTML = html;
        searchResults.classList.remove('hidden');

        // Add click event listeners to search results
        const searchItems = searchResults.querySelectorAll('.nkk-search-item');
        console.log(`Adding click listeners to ${searchItems.length} search items for form type: ${formType}`);
        searchItems.forEach(item => {
            item.addEventListener('click', function() {
                const nkk = this.dataset.nkk;
                const kepalaKeluarga = this.dataset.kepala;
                const rt = this.dataset.rt;
                const rw = this.dataset.rw;
                const dusun = this.dataset.dusun;
                const alamat = this.dataset.alamat;
                const jumlahAnggota = this.dataset.anggota;

                console.log('Click event triggered with formType:', formType);
                selectNKK(nkk, kepalaKeluarga, rt, rw, dusun, alamat, jumlahAnggota, formType);
            });
        });
    }

    function selectNKK(nkk, kepalaKeluarga, rt, rw, dusun, alamat, jumlahAnggota, formType) {
        console.log('selectNKK called with:', { nkk, kepalaKeluarga, formType });

        // Handle different form types with correct IDs
        let nkkInput, selectedNKKDiv, selectedNKKName, selectedNKKInfo, searchResults, searchInput;

        if (formType === 'pindah_masuk') {
            nkkInput = document.getElementById('nkk_existing_pindah_masuk');
            selectedNKKDiv = document.getElementById('selected_nkk_pindah_masuk');
            selectedNKKName = document.getElementById('selected_nkk_name_pindah_masuk');
            selectedNKKInfo = document.getElementById('selected_nkk_info_pindah_masuk');
            searchResults = document.getElementById('nkk_search_results_pindah_masuk');
            searchInput = document.getElementById('nkk_search_pindah_masuk');
        } else {
            nkkInput = document.getElementById(`nkk_${formType}`);
            selectedNKKDiv = document.getElementById(`selected_nkk_${formType}`);
            selectedNKKName = document.getElementById(`selected_nkk_name_${formType}`);
            selectedNKKInfo = document.getElementById(`selected_nkk_info_${formType}`);
            searchResults = document.getElementById(`nkk_search_results_${formType}`);
            searchInput = document.getElementById(`nkk_search_${formType}`);
        }

        console.log('Elements found:', {
            nkkInput: !!nkkInput,
            selectedNKKDiv: !!selectedNKKDiv,
            selectedNKKName: !!selectedNKKName,
            selectedNKKInfo: !!selectedNKKInfo,
            searchResults: !!searchResults,
            searchInput: !!searchInput
        });

        if (!nkkInput || !selectedNKKDiv || !selectedNKKName || !selectedNKKInfo) {
            console.warn(`NKK selection elements not found for form type: ${formType}`);
            return;
        }

        // Set hidden input value
        nkkInput.value = nkk;

        // Update display
        selectedNKKName.textContent = `No KK: ${nkk} - ${kepalaKeluarga}`;
        selectedNKKInfo.textContent = `RT ${rt}/RW ${rw} - ${jumlahAnggota} anggota`;

        // Show selected NKK info
        selectedNKKDiv.classList.remove('hidden');

        // Hide search results and clear input
        if (searchResults) searchResults.classList.add('hidden');
        if (searchInput) searchInput.value = '';
    }

    // NKK Check functions for Pisah KK
    function checkNKKExists(nkk) {
        const loading = document.getElementById('nkk_check_loading');
        const statusInfo = document.getElementById('nkkStatusInfo');
        const newInfo = document.getElementById('nkkNewInfo');
        const existingInfo = document.getElementById('nkkExistingInfo');

        if (loading) loading.classList.remove('hidden');
        if (statusInfo) statusInfo.style.display = 'none';

        fetch(`{{ route('mutasi.check-nkk') }}?nkk=${encodeURIComponent(nkk)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin',
            cache: 'no-cache'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (loading) loading.classList.add('hidden');

            if (data && Array.isArray(data) && data.length > 0) {
                // NKK already exists
                const family = data[0];
                showExistingNKKInfo(family);
            } else {
                // NKK is new
                showNewNKKInfo();
            }
        })
        .catch(error => {
            console.error('NKK check error:', error);
            if (loading) loading.classList.add('hidden');
            hideNKKStatusInfo();
        });
    }

    function showNewNKKInfo() {
        const statusInfo = document.getElementById('nkkStatusInfo');
        const newInfo = document.getElementById('nkkNewInfo');
        const existingInfo = document.getElementById('nkkExistingInfo');

        if (statusInfo) statusInfo.style.display = 'block';
        if (newInfo) newInfo.style.display = 'block';
        if (existingInfo) existingInfo.style.display = 'none';
    }

    function showExistingNKKInfo(family) {
        const statusInfo = document.getElementById('nkkStatusInfo');
        const newInfo = document.getElementById('nkkNewInfo');
        const existingInfo = document.getElementById('nkkExistingInfo');
        const existingDetails = document.getElementById('existingKKDetails');

        if (statusInfo) statusInfo.style.display = 'block';
        if (newInfo) newInfo.style.display = 'none';
        if (existingInfo) existingInfo.style.display = 'block';
        if (existingDetails) {
            existingDetails.textContent = `Kepala: ${family.kepala_keluarga} - RT ${family.rt}/RW ${family.rw} - ${family.jumlah_anggota} anggota`;
        }
    }

    function hideNKKStatusInfo() {
        const statusInfo = document.getElementById('nkkStatusInfo');
        if (statusInfo) statusInfo.style.display = 'none';
    }

    // Pindah Masuk KK Options
    const kkExistingRadioPindahMasuk = document.getElementById('kk_existing_pindah_masuk');
    const kkNewRadioPindahMasuk = document.getElementById('kk_new_pindah_masuk');
    const existingKKContainerPindahMasuk = document.getElementById('existingKKContainerPindahMasuk');
    const newKKContainerPindahMasuk = document.getElementById('newKKContainerPindahMasuk');

    if (kkExistingRadioPindahMasuk && kkNewRadioPindahMasuk && existingKKContainerPindahMasuk && newKKContainerPindahMasuk) {
        kkExistingRadioPindahMasuk.addEventListener('change', function() {
            if (this.checked) {
                existingKKContainerPindahMasuk.style.display = 'block';
                newKKContainerPindahMasuk.style.display = 'none';
                // Clear new KK input
                const newKKInput = document.getElementById('nkk_new_pindah_masuk');
                if (newKKInput) newKKInput.value = '';
                hideNKKStatusInfoPindahMasuk();
            }
        });

        kkNewRadioPindahMasuk.addEventListener('change', function() {
            if (this.checked) {
                existingKKContainerPindahMasuk.style.display = 'none';
                newKKContainerPindahMasuk.style.display = 'block';
                // Clear existing KK selection
                clearNKKSelection('pindah_masuk');
            }
        });
    }

    // NKK Check functions for Pindah Masuk
    function checkNKKExistsPindahMasuk(nkk) {
        const loading = document.getElementById('nkk_check_loading_pindah_masuk');
        const statusInfo = document.getElementById('nkkStatusInfoPindahMasuk');
        const newInfo = document.getElementById('nkkNewInfoPindahMasuk');
        const existingInfo = document.getElementById('nkkExistingInfoPindahMasuk');

        if (loading) loading.classList.remove('hidden');
        if (statusInfo) statusInfo.style.display = 'none';

        fetch(`{{ route('mutasi.check-nkk') }}?nkk=${encodeURIComponent(nkk)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin',
            cache: 'no-cache'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (loading) loading.classList.add('hidden');

            if (data && Array.isArray(data) && data.length > 0) {
                // NKK already exists
                const family = data[0];
                showExistingNKKInfoPindahMasuk(family);
            } else {
                // NKK is new
                showNewNKKInfoPindahMasuk();
            }
        })
        .catch(error => {
            console.error('NKK check error:', error);
            if (loading) loading.classList.add('hidden');
            hideNKKStatusInfoPindahMasuk();
        });
    }

    function showNewNKKInfoPindahMasuk() {
        const statusInfo = document.getElementById('nkkStatusInfoPindahMasuk');
        const newInfo = document.getElementById('nkkNewInfoPindahMasuk');
        const existingInfo = document.getElementById('nkkExistingInfoPindahMasuk');

        if (statusInfo) statusInfo.style.display = 'block';
        if (newInfo) newInfo.style.display = 'block';
        if (existingInfo) existingInfo.style.display = 'none';
    }

    function showExistingNKKInfoPindahMasuk(family) {
        const statusInfo = document.getElementById('nkkStatusInfoPindahMasuk');
        const newInfo = document.getElementById('nkkNewInfoPindahMasuk');
        const existingInfo = document.getElementById('nkkExistingInfoPindahMasuk');
        const existingDetails = document.getElementById('existingKKDetailsPindahMasuk');

        if (statusInfo) statusInfo.style.display = 'block';
        if (newInfo) newInfo.style.display = 'none';
        if (existingInfo) existingInfo.style.display = 'block';
        if (existingDetails) {
            existingDetails.textContent = `Kepala: ${family.kepala_keluarga} - RT ${family.rt}/RW ${family.rw} - ${family.jumlah_anggota} anggota`;
        }
    }

    function hideNKKStatusInfoPindahMasuk() {
        const statusInfo = document.getElementById('nkkStatusInfoPindahMasuk');
        if (statusInfo) statusInfo.style.display = 'none';
    }

    // Add event listener for NKK baru input (Pindah Masuk)
    const nkkBaruInputPindahMasuk = document.getElementById('nkk_new_pindah_masuk');
    if (nkkBaruInputPindahMasuk) {
        let nkkCheckTimeoutPindahMasuk;
        nkkBaruInputPindahMasuk.addEventListener('input', function() {
            clearTimeout(nkkCheckTimeoutPindahMasuk);
            const nkkValue = this.value.trim();

            if (nkkValue.length === 16) {
                nkkCheckTimeoutPindahMasuk = setTimeout(() => {
                    checkNKKExistsPindahMasuk(nkkValue);
                }, 500); // 500ms debounce
            } else {
                hideNKKStatusInfoPindahMasuk();
            }
        });
    }

    // Add event listener for join existing KK button (Pindah Masuk)
    const joinExistingKKBtnPindahMasuk = document.getElementById('joinExistingKKPindahMasuk');
    if (joinExistingKKBtnPindahMasuk) {
        joinExistingKKBtnPindahMasuk.addEventListener('click', function() {
            // Switch to existing KK option
            const existingRadio = document.getElementById('kk_existing_pindah_masuk');
            if (existingRadio) {
                existingRadio.checked = true;
                existingRadio.dispatchEvent(new Event('change'));

                // Fill the existing KK search with current NKK
                const nkkBaru = document.getElementById('nkk_new_pindah_masuk').value;
                const existingSearch = document.getElementById('nkk_search_pindah_masuk');
                if (existingSearch) {
                    existingSearch.value = nkkBaru;
                    existingSearch.dispatchEvent(new Event('input'));
                }
            }
        });
    }

    // NIK Check functions for Pindah Masuk
    function checkNIKExistsPindahMasuk(nik) {
        const loading = document.getElementById('nik_check_loading_pindah_masuk');
        const statusInfo = document.getElementById('nikStatusInfoPindahMasuk');
        const newInfo = document.getElementById('nikNewInfoPindahMasuk');
        const existingInfo = document.getElementById('nikExistingInfoPindahMasuk');

        if (loading) loading.classList.remove('hidden');
        if (statusInfo) statusInfo.style.display = 'none';

        fetch(`{{ route('penduduk.check-nik') }}?nik=${encodeURIComponent(nik)}&exclude_id=`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin',
            cache: 'no-cache'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (loading) loading.classList.add('hidden');

            if (data.exists) {
                // NIK already exists
                showExistingNIKInfoPindahMasuk(data.data);
            } else {
                // NIK is new
                showNewNIKInfoPindahMasuk();
            }
        })
        .catch(error => {
            console.error('NIK check error:', error);
            if (loading) loading.classList.add('hidden');
            hideNIKStatusInfoPindahMasuk();
        });
    }

    function showNewNIKInfoPindahMasuk() {
        const statusInfo = document.getElementById('nikStatusInfoPindahMasuk');
        const newInfo = document.getElementById('nikNewInfoPindahMasuk');
        const existingInfo = document.getElementById('nikExistingInfoPindahMasuk');

        if (statusInfo) statusInfo.style.display = 'block';
        if (newInfo) newInfo.style.display = 'block';
        if (existingInfo) existingInfo.style.display = 'none';
    }

    function showExistingNIKInfoPindahMasuk(data) {
        const statusInfo = document.getElementById('nikStatusInfoPindahMasuk');
        const newInfo = document.getElementById('nikNewInfoPindahMasuk');
        const existingInfo = document.getElementById('nikExistingInfoPindahMasuk');
        const existingDetails = document.getElementById('existingNIKDetailsPindahMasuk');

        if (statusInfo) statusInfo.style.display = 'block';
        if (newInfo) newInfo.style.display = 'none';
        if (existingInfo) existingInfo.style.display = 'block';
        if (existingDetails) {
            existingDetails.textContent = `${data.nama} - No KK: ${data.nkk} - RT ${data.rt}/RW ${data.rw}`;
        }
    }

    function hideNIKStatusInfoPindahMasuk() {
        const statusInfo = document.getElementById('nikStatusInfoPindahMasuk');
        if (statusInfo) statusInfo.style.display = 'none';
    }

    // Add event listener for NIK input (Pindah Masuk)
    const nikInputPindahMasuk = document.getElementById('nik_pindah_masuk');
    if (nikInputPindahMasuk) {
        let nikCheckTimeoutPindahMasuk;
        nikInputPindahMasuk.addEventListener('input', function() {
            clearTimeout(nikCheckTimeoutPindahMasuk);
            const nikValue = this.value.trim();

            if (nikValue.length === 16) {
                nikCheckTimeoutPindahMasuk = setTimeout(() => {
                    checkNIKExistsPindahMasuk(nikValue);
                }, 500); // 500ms debounce
            } else {
                hideNIKStatusInfoPindahMasuk();
            }
        });
    }

});
@endnoncescript

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@noncescript
// SweetAlert functions
function showSuccess(message) {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: message,
        confirmButtonColor: '#10b981'
    });
}

function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: message,
        confirmButtonColor: '#ef4444'
    });
}

function showWarning(message) {
    Swal.fire({
        icon: 'warning',
        title: 'Peringatan!',
        text: message,
        confirmButtonColor: '#f59e0b'
    });
}

function showLoading(message = 'Memproses...') {
    Swal.fire({
        title: message,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

// Update form submission to use SweetAlert
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up form submission');
    const form = document.querySelector('#mutasiForm');
    console.log('Form found:', form);

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            console.log('Form submitted via AJAX');

            const formData = new FormData(this);
            const jenisMutasi = formData.get('jenis_mutasi');

            if (!jenisMutasi) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Pilih jenis mutasi terlebih dahulu!',
                    confirmButtonColor: '#ef4444'
                });
                return;
            }

            // Validate form based on mutation type
            console.log('Validating form for:', jenisMutasi);
            if (!validateForm(jenisMutasi)) {
                console.log('Form validation failed');
                return;
            }
            console.log('Form validation passed');

            showLoading('Menyimpan data mutasi...');

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                Swal.close();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Data mutasi berhasil disimpan!',
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        // Redirect to mutasi index after success
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            // Reset form if no redirect
                            form.reset();
                            document.querySelectorAll('[id^="form"]').forEach(form => {
                                if (form.id !== 'defaultMessage') {
                                    form.style.display = 'none';
                                }
                            });
                            document.getElementById('defaultMessage').style.display = 'block';
                            document.getElementById('jenis_mutasi').value = '';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'Terjadi kesalahan saat menyimpan data!',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error details:', error);
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menyimpan data: ' + error.message,
                    confirmButtonColor: '#ef4444'
                });
            });
        });
    }
});
@endnoncescript
@endsection


