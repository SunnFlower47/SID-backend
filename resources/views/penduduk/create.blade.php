@extends('layouts.app')

@section('title', 'Tambah Penduduk')
@section('subtitle', 'Input data penduduk')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-plus text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Tambah Data Penduduk</h1>
                    <p class="text-green-100 text-sm sm:text-base">Input data penduduk baru ke dalam sistem</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('penduduk.index') }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
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

        <form method="POST" action="{{ route('penduduk.store') }}" class="space-y-6">
            @csrf

            <!-- Personal Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    Informasi Pribadi
                </h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label for="nik" class="block text-sm font-medium text-gray-700 mb-2">NIK *</label>
                        <div class="relative">
                            <input type="text" id="nik" name="nik" value="{{ old('nik') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nik') border-red-300 @enderror"
                                   placeholder="Masukkan NIK 16 digit" maxlength="16">
                            <div id="nik_check_loading" class="absolute right-3 top-3 hidden">
                                <i class="fas fa-spinner fa-spin text-gray-400"></i>
                            </div>
                        </div>
                        <div id="nikStatusInfo" class="mt-2 text-sm" style="display: none;">
                            <div id="nikNewInfo" class="text-green-700 bg-green-50 p-2 rounded-lg border border-green-200" style="display: none;">
                                <i class="fas fa-check-circle mr-1"></i>
                                <strong>NIK Tersedia:</strong> NIK ini belum terdaftar di database
                            </div>
                            <div id="nikExistingInfo" class="text-red-700 bg-red-50 p-2 rounded-lg border border-red-200" style="display: none;">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                <strong>NIK Sudah Ada:</strong> <span id="existingNIKDetails"></span>
                            </div>
                        </div>
                        @error('nik')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Format: 16 digit angka</p>
                    </div>

                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama') border-red-300 @enderror"
                               placeholder="Masukkan nama lengkap">
                        @error('nama')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin *</label>
                        <select id="jenis_kelamin" name="jenis_kelamin" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jenis_kelamin') border-red-300 @enderror">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="LAKI-LAKI" {{ old('jenis_kelamin') == 'LAKI-LAKI' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="PEREMPUAN" {{ old('jenis_kelamin') == 'PEREMPUAN' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir *</label>
                        <input type="text" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tempat_lahir') border-red-300 @enderror"
                               placeholder="Masukkan tempat lahir">
                        @error('tempat_lahir')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir *</label>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_lahir') border-red-300 @enderror">
                        @error('tanggal_lahir')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="agama" class="block text-sm font-medium text-gray-700 mb-2">Agama *</label>
                        <select id="agama" name="agama" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('agama') border-red-300 @enderror">
                            <option value="">Pilih Agama</option>
                            <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                        @error('agama')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Family Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-home text-white text-sm"></i>
                    </div>
                    Informasi Keluarga
                </h3>
                <div class="grid grid-cols-1 gap-6">
                    <!-- Pilihan KK -->
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <h4 class="text-md font-medium text-gray-900 mb-3">Pilih Kartu Keluarga</h4>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="radio" name="kk_option" value="existing" class="mr-2" onchange="toggleKKOption('existing')" checked>
                                <span>Pilih dari KK yang sudah ada</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="kk_option" value="manual" class="mr-2" onchange="toggleKKOption('manual')">
                                <span>Input No. KK manual (jika KK belum terdaftar)</span>
                            </label>
                        </div>

                    </div>

                    <!-- Form KK Existing -->
                    <div id="kkExistingOption" class="grid grid-cols-1 gap-6 p-4 bg-green-50 rounded-lg border border-green-200">

                        <!-- KK Search Results -->
                        <div>
                            <label for="nkk_existing" class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Kartu Keluarga *
                            </label>
                            <div class="relative">
                                <input type="text" id="nkk_search_penduduk_new"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Cari No KK (NKK atau nama kepala keluarga)..."
                                       autocomplete="off">
                                <input type="hidden" name="nkk_existing" id="nkk_existing">
                                <div id="nkk_search_results_penduduk" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                                <div id="nkk_search_loading_penduduk" class="absolute right-3 top-3 hidden">
                                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                </div>
                            </div>
                            <div id="selected_nkk_penduduk" class="mt-2 hidden">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-blue-900" id="selected_nkk_name_penduduk"></p>
                                            <p class="text-sm text-blue-700" id="selected_nkk_info_penduduk"></p>
                                        </div>
                                        <button type="button" onclick="clearNKKSelectionPenduduk()" class="text-blue-400 hover:text-blue-600">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @error('nkk_existing')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- KK Info Display -->
                        <div id="kkInfoDisplay" style="display: none;">
                            <!-- Info KK akan dimuat di sini -->
                        </div>
                    </div>

                    <!-- Form KK Manual -->
                    <div id="kkManualOption" class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200" style="display: none;">
                        <div>
                            <label for="nkk_manual_input" class="block text-sm font-medium text-gray-700 mb-2">No. KK *</label>
                            <div class="relative">
                                <input type="text" id="nkk_manual_input" name="nkk" value="{{ old('nkk') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nkk') border-red-300 @enderror"
                                       placeholder="Masukkan No. KK 16 digit" maxlength="16">
                                <div id="nkk_check_loading_manual" class="absolute right-3 top-3 hidden">
                                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                </div>
                            </div>
                            <div id="nkkStatusInfoManual" class="mt-2 text-sm" style="display: none;">
                                <div id="nkkNewInfoManual" class="text-green-700 bg-green-50 p-2 rounded-lg border border-green-200" style="display: none;">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    <strong>NKK Tersedia:</strong> NKK ini belum terdaftar di database
                                </div>
                                <div id="nkkExistingInfoManual" class="text-red-700 bg-red-50 p-2 rounded-lg border border-red-200" style="display: none;">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <strong>NKK Sudah Ada:</strong> <span id="existingNKKDetailsManual"></span>
                                    <div class="mt-2">
                                        <button type="button" onclick="switchToExistingKK()" class="text-blue-600 hover:text-blue-800 underline text-sm">
                                            Gunakan opsi "Pilih dari KK yang sudah ada"
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @error('nkk')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Format: 16 digit angka</p>
                        </div>
                        <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                            <p class="text-sm text-yellow-700">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Catatan:</strong> Gunakan opsi ini hanya jika KK belum terdaftar di sistem.
                                Pastikan No. KK yang diinput benar dan valid.
                            </p>
                        </div>
                    </div>

                    <div>
                        <label for="status_perkawinan" class="block text-sm font-medium text-gray-700 mb-2">Status Perkawinan *</label>
                        <select id="status_perkawinan" name="status_perkawinan" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status_perkawinan') border-red-300 @enderror">
                            <option value="">Pilih Status Perkawinan</option>
                            <option value="Belum Kawin" {{ old('status_perkawinan') == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                            <option value="Kawin" {{ old('status_perkawinan') == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                            <option value="Cerai Hidup" {{ old('status_perkawinan') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                            <option value="Cerai Mati" {{ old('status_perkawinan') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                        </select>
                        @error('status_perkawinan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="kedudukan_keluarga" class="block text-sm font-medium text-gray-700 mb-2">Kedudukan Keluarga *</label>
                        <select id="kedudukan_keluarga" name="kedudukan_keluarga" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kedudukan_keluarga') border-red-300 @enderror">
                            <option value="">Pilih Kedudukan</option>
                            <option value="Kepala Keluarga" {{ old('kedudukan_keluarga') == 'Kepala Keluarga' ? 'selected' : '' }}>Kepala Keluarga</option>
                            <option value="Istri" {{ old('kedudukan_keluarga') == 'Istri' ? 'selected' : '' }}>Istri</option>
                            <option value="Anak" {{ old('kedudukan_keluarga') == 'Anak' ? 'selected' : '' }}>Anak</option>
                            <option value="Cucu" {{ old('kedudukan_keluarga') == 'Cucu' ? 'selected' : '' }}>Cucu</option>
                            <option value="Lainnya" {{ old('kedudukan_keluarga') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('kedudukan_keluarga')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Education & Work -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-graduation-cap text-white text-sm"></i>
                    </div>
                    Pendidikan & Pekerjaan
                </h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label for="pendidikan" class="block text-sm font-medium text-gray-700 mb-2">Pendidikan</label>
                        <input type="text" id="pendidikan" name="pendidikan" value="{{ old('pendidikan') }}"
                               placeholder="Contoh: SD, SMP, SMA, S1, dll"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pendidikan') border-red-300 @enderror">
                        @error('pendidikan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="pekerjaan" class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan *</label>
                        <input type="text" id="pekerjaan" name="pekerjaan" value="{{ old('pekerjaan') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pekerjaan') border-red-300 @enderror"
                               placeholder="Masukkan pekerjaan">
                        @error('pekerjaan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Parents Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <div class="w-8 h-8 bg-gradient-to-br from-pink-500 to-pink-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-heart text-white text-sm"></i>
                    </div>
                    Informasi Orang Tua
                </h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label for="nama_ayah" class="block text-sm font-medium text-gray-700 mb-2">Nama Ayah</label>
                        <input type="text" id="nama_ayah" name="nama_ayah" value="{{ old('nama_ayah') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_ayah') border-red-300 @enderror"
                               placeholder="Masukkan nama ayah">
                        @error('nama_ayah')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nama_ibu" class="block text-sm font-medium text-gray-700 mb-2">Nama Ibu</label>
                        <input type="text" id="nama_ibu" name="nama_ibu" value="{{ old('nama_ibu') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_ibu') border-red-300 @enderror"
                               placeholder="Masukkan nama ibu">
                        @error('nama_ibu')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-map-marker-alt text-white text-sm"></i>
                    </div>
                    Informasi Alamat
                </h3>
                <div class="space-y-6">
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap *</label>
                        <textarea id="alamat" name="alamat" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('alamat') border-red-300 @enderror"
                                  placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label for="rw_id" class="block text-sm font-medium text-gray-700 mb-2">RW Master *</label>
                            <select id="rw_id" name="rw_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('rw_id') border-red-300 @enderror">
                                <option value="">Pilih RW</option>
                                @foreach(($rws ?? collect()) as $rw)
                                    <option value="{{ $rw->id }}" {{ (string)old('rw_id') === (string)$rw->id ? 'selected' : '' }}>RW {{ $rw->kode }} - {{ $rw->nama }}</option>
                                @endforeach
                            </select>
                            @error('rw_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="rt_id" class="block text-sm font-medium text-gray-700 mb-2">RT Master *</label>
                            <select id="rt_id" name="rt_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('rt_id') border-red-300 @enderror">
                                <option value="">Pilih RT</option>
                            </select>
                            @error('rt_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="dusun" class="block text-sm font-medium text-gray-700 mb-2">Dusun</label>
                            <input type="text" id="dusun" name="dusun" value="{{ old('dusun') }}" readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                                   placeholder="Dusun otomatis dari RT master">
                            <p class="text-xs text-gray-500 mt-1">Auto dari mapping master wilayah</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="rw" class="block text-sm font-medium text-gray-700 mb-2">RW (kode)</label>
                            <input type="text" id="rw" name="rw" value="{{ old('rw') }}" readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
                            @error('rw')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="rt" class="block text-sm font-medium text-gray-700 mb-2">RT (kode)</label>
                            <input type="text" id="rt" name="rt" value="{{ old('rt') }}" readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
                            @error('rt')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('keterangan') border-red-300 @enderror"
                                  placeholder="Masukkan keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center justify-center transition-colors shadow-md order-1">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Data
                </button>
                <a href="{{ route('penduduk.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg flex items-center justify-center transition-colors shadow-md order-2">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
            </div>

            <!-- Anggota Keluarga Lainnya -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-users text-white text-sm"></i>
                        </div>
                        Anggota Keluarga Lainnya (Opsional)
                    </h3>
                    <button type="button" onclick="addFamilyMember()"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors shadow-md">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Anggota
                    </button>
                </div>

                <div id="familyMembersContainer" class="space-y-4">
                    <!-- Family members will be added here dynamically -->
                </div>

                <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-sm text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Tips:</strong> Anda bisa menambahkan anggota keluarga lainnya (istri, anak, dll) dalam satu form ini.
                        Semua data akan tersimpan dengan No KK yang sama. Alamat lengkap, RT, RW, dan Dusun akan otomatis mengikuti data Kepala Keluarga.
                    </p>
                </div>
            </div>
        </form>
    </div>
</div>

<script nonce="{{ $csp_nonce }}">
const masterRwOptions = @json($masterRwOptions ?? []);

function syncMasterWilayahFromSelection() {
    const rwId = document.getElementById('rw_id')?.value;
    const rtId = document.getElementById('rt_id')?.value;
    const rwInput = document.getElementById('rw');
    const rtInput = document.getElementById('rt');
    const dusunInput = document.getElementById('dusun');

    const rwObj = masterRwOptions.find(r => String(r.id) === String(rwId));
    const rtObj = rwObj?.rts?.find(r => String(r.id) === String(rtId));

    if (rwInput) rwInput.value = rwObj?.kode || '';
    if (rtInput) rtInput.value = rtObj?.kode || '';
    if (dusunInput) dusunInput.value = rtObj?.dusun || '';
}

function populateRtByRw() {
    const rwId = document.getElementById('rw_id')?.value;
    const rtSelect = document.getElementById('rt_id');
    if (!rtSelect) return;

    rtSelect.innerHTML = '<option value="">Pilih RT</option>';
    const rwObj = masterRwOptions.find(r => String(r.id) === String(rwId));
    (rwObj?.rts || []).forEach(rt => {
        const opt = document.createElement('option');
        opt.value = rt.id;
        opt.textContent = `RT ${rt.kode}${rt.dusun ? ` - ${rt.dusun}` : ''}`;
        rtSelect.appendChild(opt);
    });

    syncMasterWilayahFromSelection();
}

// Toggle KK Option
function toggleKKOption(option) {
    const kkExistingOption = document.getElementById('kkExistingOption');
    const kkManualOption = document.getElementById('kkManualOption');
    const kartuKeluargaSelect = document.getElementById('nkk');
    const nkkInput = document.getElementById('nkk');


    if (option === 'existing') {
        // Show existing KK option, hide manual

        if (kkExistingOption) {
            kkExistingOption.style.display = 'block';
            kkExistingOption.style.visibility = 'visible';
            // Force show with important
            kkExistingOption.setAttribute('style', 'display: block !important; visibility: visible !important;');
        }
        if (kkManualOption) {
            kkManualOption.style.display = 'none';
            kkManualOption.style.visibility = 'hidden';
        }

        // Enable/disable fields
        const nkkManual = document.getElementById('nkk_manual_input');
        if (nkkManual) {
            nkkManual.required = false;
            nkkManual.disabled = true;
            nkkManual.value = '';
        }
        
        const rwMaster = document.getElementById('rw_id');
        const rtMaster = document.getElementById('rt_id');
        if (rwMaster) { rwMaster.required = false; rwMaster.disabled = true; }
        if (rtMaster) { rtMaster.required = false; rtMaster.disabled = true; }

        // Clear KK info display
        const kkInfoDisplay = document.getElementById('kkInfoDisplay');
        if (kkInfoDisplay) {
            kkInfoDisplay.style.display = 'none';
            kkInfoDisplay.innerHTML = '';
        }

        // Clear search
        const searchInput = document.getElementById('nkk_search_penduduk_new');
        if (searchInput) {
            searchInput.value = '';
        }

        // Clear KK selection and reset address fields
        clearNKKSelectionPenduduk();
    } else if (option === 'manual') {
        // Show manual KK option, hide existing

        if (kkExistingOption) {
            kkExistingOption.style.display = 'none';
            kkExistingOption.style.visibility = 'hidden';
        }
        if (kkManualOption) {
            kkManualOption.style.display = 'block';
            kkManualOption.style.visibility = 'visible';
            kkManualOption.style.opacity = '1';
            // Force show with important
            kkManualOption.setAttribute('style', 'display: block !important; visibility: visible !important;');
        }

        // Enable/disable fields
        const nkkManual = document.getElementById('nkk_manual_input');
        if (nkkManual) {
            nkkManual.required = true;
            nkkManual.disabled = false;
            setTimeout(() => {
                nkkManual.focus(); 
            }, 100);
        }

        // Clear KK info display
        const kkInfoDisplay = document.getElementById('kkInfoDisplay');
        if (kkInfoDisplay) {
            kkInfoDisplay.style.display = 'none';
            kkInfoDisplay.innerHTML = '';
        }

        // Manual mode: alamat tetap manual, RT/RW wajib dari master
        const alamatTextarea = document.getElementById('alamat');
        const rwText = document.getElementById('rw');
        const rtText = document.getElementById('rt');
        const dusunText = document.getElementById('dusun');
        const rwMaster = document.getElementById('rw_id');
        const rtMaster = document.getElementById('rt_id');

        if (alamatTextarea) {
            alamatTextarea.readOnly = false;
            alamatTextarea.classList.remove('bg-gray-100');
            alamatTextarea.value = '';
        }

        if (rwMaster) { rwMaster.required = true; rwMaster.disabled = false; }
        if (rtMaster) { rtMaster.required = true; rtMaster.disabled = false; rtMaster.innerHTML = '<option value="">Pilih RT</option>'; }

        if (rtText) rtText.value = '';
        if (rwText) rwText.value = '';
        if (dusunText) dusunText.value = '';

        console.log('Manual KK option selected, wilayah diambil dari master');
    }
}

// Load KK Info
function loadKKInfo(kkId) {
    const kkInfoDisplay = document.getElementById('kkInfoDisplay');

    if (!kkId) {
        kkInfoDisplay.style.display = 'none';
        kkInfoDisplay.innerHTML = '';
        return;
    }

    // Show loading
    kkInfoDisplay.style.display = 'block';
    kkInfoDisplay.innerHTML = '<p class="text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat info KK...</p>';

    // Fetch KK info
    fetch(`/api/v1/kartu-keluarga/${kkId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayKKInfoPenduduk(data, kkInfoDisplay);
            } else {
                kkInfoDisplay.innerHTML = '<p class="text-red-500">Gagal memuat info KK</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            kkInfoDisplay.innerHTML = '<p class="text-red-500">Error memuat data KK</p>';
        });
}

function displayKKInfoPenduduk(response, infoDiv) {
    const kkData = response.data;
    const hasKepalaKeluarga = response.has_kepala_keluarga;
    const kepalaKeluargaData = response.kepala_keluarga_data;

    let infoHtml = `
        <div class="p-4 bg-green-50 rounded-lg border border-green-200">
            <h5 class="font-medium text-gray-900 mb-3 flex items-center">
                <i class="fas fa-id-card text-green-600 mr-2"></i>
                Info Kartu Keluarga Terpilih
            </h5>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-sm">
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
    `;

    if (hasKepalaKeluarga) {
        infoHtml += `
                <div class="md:col-span-2 border-t pt-3 mt-3">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Kepala Keluarga:</span>
                        <span class="text-gray-900 font-medium">${kepalaKeluargaData.nama}</span>
                    </div>
                </div>
        `;
    } else {
        infoHtml += `
                <div class="md:col-span-2 border-t pt-3 mt-3">
                    <div class="flex items-center text-yellow-700">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span class="text-sm">KK belum memiliki kepala keluarga</span>
                    </div>
                </div>
        `;
    }

    infoHtml += `
            </div>
            <div class="mt-3 p-2 bg-blue-100 rounded text-sm text-blue-700">
                <i class="fas fa-info-circle mr-1"></i>
                Penduduk baru akan ditambahkan ke KK ini
            </div>
        </div>
    `;

    infoDiv.innerHTML = infoHtml;
}

// Auto-format NIK, NKK, and RT
document.addEventListener('input', function(e) {
    if (e.target.id === 'nkk' || e.target.id === 'nik' || e.target.id === 'nkk_manual_input') {
        let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
        if (value.length > 16) value = value.substring(0, 16);
        e.target.value = value;
    } else if (e.target.id === 'rt') {
        let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
        if (value.length > 3) value = value.substring(0, 3);
        e.target.value = value;
    }
});

// Search KK function for penduduk form (AJAX)
function searchKKPenduduk() {
    const searchInput = document.getElementById('nkk_search_penduduk_new');
    const searchResults = document.getElementById('nkk_search_results_penduduk');
    const searchLoading = document.getElementById('nkk_search_loading_penduduk');

    if (!searchInput || !searchResults) {
        console.log('searchKKPenduduk: Required elements not found');
        return;
    }

    const query = searchInput.value.trim();

    if (query.length < 3) {
        searchResults.classList.add('hidden');
        return;
    }

    if (searchLoading) searchLoading.classList.remove('hidden');

    fetch(`{{ route('mutasi.search-kk') }}?query=${encodeURIComponent(query)}`, {
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
        if (searchLoading) searchLoading.classList.add('hidden');
        displayNKKSearchResultsPenduduk(data);
    })
    .catch(error => {
        console.error('Search KK error:', error);
        if (searchLoading) searchLoading.classList.add('hidden');
        searchResults.classList.add('hidden');
    });
}

function displayNKKSearchResultsPenduduk(results) {
    const searchResults = document.getElementById('nkk_search_results_penduduk');

    if (!results || results.length === 0) {
        searchResults.innerHTML = '<div class="p-3 text-gray-500 text-center">Tidak ada KK ditemukan</div>';
        searchResults.classList.remove('hidden');
        return;
    }

    let html = '';
    results.forEach(family => {
        const kepala = family.kepala_keluarga || family.nama || 'Tidak diketahui';
        const rt = family.rt ?? '-';
        const rw = family.rw ?? '-';
        const jumlah = family.jumlah_anggota ?? '-';
        const dusun = family.dusun ?? '';
        const alamat = family.alamat ?? '';
        const rtId = family.rt_id ?? '';
        const rwId = family.rw_id ?? '';

        html += `
            <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                 data-nkk="${family.nkk ?? ''}"
                 data-kepala="${kepala}"
                 data-rt="${rt}"
                 data-rw="${rw}"
                 data-rt-id="${rtId}"
                 data-rw-id="${rwId}"
                 data-dusun="${dusun}"
                 data-alamat="${alamat}"
                 data-jumlah="${jumlah}">
                <div class="font-medium text-gray-900">${family.nkk ?? '-'}</div>
                <div class="text-sm text-gray-600">${kepala} - RT ${rt}/RW ${rw}</div>
                <div class="text-xs text-gray-500">${jumlah} anggota</div>
            </div>
        `;
    });

    searchResults.innerHTML = html;
    searchResults.classList.remove('hidden');

    // Add click listeners
    const searchItems = searchResults.querySelectorAll('[data-nkk]');
    searchItems.forEach(item => {
        item.addEventListener('click', function() {
            const nkk = this.getAttribute('data-nkk');
            const kepalaKeluarga = this.getAttribute('data-kepala');
            const rt = this.getAttribute('data-rt');
            const rw = this.getAttribute('data-rw');
            const rtId = this.getAttribute('data-rt-id');
            const rwId = this.getAttribute('data-rw-id');
            const dusun = this.getAttribute('data-dusun');
            const alamat = this.getAttribute('data-alamat');
            const jumlahAnggota = this.getAttribute('data-jumlah');

            selectNKKPenduduk(nkk, kepalaKeluarga, rt, rw, dusun, alamat, jumlahAnggota, rtId, rwId);
        });
    });
}

function selectNKKPenduduk(nkk, kepalaKeluarga, rt, rw, dusun, alamat, jumlahAnggota, rtId = '', rwId = '') {
    const nkkInput = document.getElementById('nkk_existing');
    const selectedNKKDiv = document.getElementById('selected_nkk_penduduk');
    const selectedNKKName = document.getElementById('selected_nkk_name_penduduk');
    const selectedNKKInfo = document.getElementById('selected_nkk_info_penduduk');
    const searchResults = document.getElementById('nkk_search_results_penduduk');
    const searchInput = document.getElementById('nkk_search_penduduk_new');

    if (!nkkInput || !selectedNKKDiv || !selectedNKKName || !selectedNKKInfo) {
        console.warn('NKK selection elements not found');
        return;
    }

    // Set hidden input value
    nkkInput.value = nkk;

    // Update display
    selectedNKKName.textContent = `${nkk} - ${kepalaKeluarga}`;
    selectedNKKInfo.textContent = `RT ${rt}/RW ${rw} - ${jumlahAnggota} anggota`;

    // Auto-fill address fields from selected KK
    const alamatTextarea = document.getElementById('alamat');
    if (alamatTextarea) {
        alamatTextarea.value = alamat;
        alamatTextarea.readOnly = true;
        alamatTextarea.classList.add('bg-gray-100');
    }

    // Auto-fill Master Wilayah from selected KK (Source of Truth)
    if (rwId) {
        const rwMaster = document.getElementById('rw_id');
        if (rwMaster) {
            rwMaster.value = rwId;
            populateRtByRw();
            
            if (rtId) {
                const rtMaster = document.getElementById('rt_id');
                if (rtMaster) {
                    rtMaster.value = rtId;
                    syncMasterWilayahFromSelection();
                }
            }
        }
    } else {
        // Fallback for older data without IDs
        const rtSelect = document.getElementById('rt');
        const rwSelect = document.getElementById('rw');
        const dusunSelect = document.getElementById('dusun');
        
        if (rtSelect) { rtSelect.value = rt; rtSelect.readOnly = true; rtSelect.classList.add('bg-gray-100'); }
        if (rwSelect) { rwSelect.value = rw; rwSelect.readOnly = true; rwSelect.classList.add('bg-gray-100'); }
        if (dusunSelect) { dusunSelect.value = dusun; dusunSelect.readOnly = true; dusunSelect.classList.add('bg-gray-100'); }
    }

    // Show selected div, hide search results
    selectedNKKDiv.classList.remove('hidden');
    searchResults.classList.add('hidden');
    if (searchInput) searchInput.value = '';

    console.log('KK selected, address fields auto-filled:', { alamat, rt, rw, dusun });
}

function clearNKKSelectionPenduduk() {
    const nkkInput = document.getElementById('nkk_existing');
    const selectedNKKDiv = document.getElementById('selected_nkk_penduduk');
    const searchResults = document.getElementById('nkk_search_results_penduduk');
    const searchInput = document.getElementById('nkk_search_penduduk_new');

    if (nkkInput) nkkInput.value = '';
    if (selectedNKKDiv) selectedNKKDiv.classList.add('hidden');
    if (searchResults) searchResults.classList.add('hidden');
    if (searchInput) searchInput.value = '';

    // Reset address fields to normal state
    const alamatTextarea = document.getElementById('alamat');
    const rtSelect = document.getElementById('rt');
    const rwSelect = document.getElementById('rw');
    const dusunSelect = document.getElementById('dusun');

    if (alamatTextarea) {
        alamatTextarea.value = '';
        alamatTextarea.readOnly = false;
        alamatTextarea.classList.remove('bg-gray-100');
    }

    if (rtSelect) {
        rtSelect.value = '';
        rtSelect.readOnly = true;
        rtSelect.classList.add('bg-gray-100');
    }

    if (rwSelect) {
        rwSelect.value = '';
        rwSelect.readOnly = true;
        rwSelect.classList.add('bg-gray-100');
    }

    if (dusunSelect) {
        dusunSelect.value = '';
        dusunSelect.readOnly = true;
        dusunSelect.classList.add('bg-gray-100');
    }

    console.log('KK selection cleared, address fields reset to normal');
}

// NKK Check functions for manual input
function checkNKKExistsManual(nkk) {
    const loading = document.getElementById('nkk_check_loading_manual');
    const statusInfo = document.getElementById('nkkStatusInfoManual');
    const newInfo = document.getElementById('nkkNewInfoManual');
    const existingInfo = document.getElementById('nkkExistingInfoManual');

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

        if (data && data.length > 0) {
            // NKK already exists
            showExistingNKKInfoManual(data[0]);
        } else {
            // NKK is new
            showNewNKKInfoManual();
        }
    })
    .catch(error => {
        console.error('NKK check error:', error);
        if (loading) loading.classList.add('hidden');
        hideNKKStatusInfoManual();
    });
}

function showNewNKKInfoManual() {
    const statusInfo = document.getElementById('nkkStatusInfoManual');
    const newInfo = document.getElementById('nkkNewInfoManual');
    const existingInfo = document.getElementById('nkkExistingInfoManual');

    if (statusInfo) statusInfo.style.display = 'block';
    if (newInfo) newInfo.style.display = 'block';
    if (existingInfo) existingInfo.style.display = 'none';
}

function showExistingNKKInfoManual(data) {
    const statusInfo = document.getElementById('nkkStatusInfoManual');
    const newInfo = document.getElementById('nkkNewInfoManual');
    const existingInfo = document.getElementById('nkkExistingInfoManual');
    const existingDetails = document.getElementById('existingNKKDetailsManual');

    if (statusInfo) statusInfo.style.display = 'block';
    if (newInfo) newInfo.style.display = 'none';
    if (existingInfo) existingInfo.style.display = 'block';
    if (existingDetails) {
        existingDetails.textContent = `${data.kepala_keluarga} - No KK: ${data.nkk} - RT ${data.rt}/RW ${data.rw}`;
    }

    // AUTO-FILL Alamat (Source of Truth)
    if (data.alamat) {
        const alamatInput = document.getElementById('alamat');
        if (alamatInput) alamatInput.value = data.alamat;
    }

    // AUTO-FILL Master Wilayah
    if (data.rw_id) {
        const rwMaster = document.getElementById('rw_id');
        if (rwMaster) {
            rwMaster.value = data.rw_id;
            // Trigger change to populate RTs
            populateRtByRw();
            
            if (data.rt_id) {
                const rtMaster = document.getElementById('rt_id');
                if (rtMaster) {
                    rtMaster.value = data.rt_id;
                    syncMasterWilayahFromSelection();
                }
            }
        }
    }
}

function hideNKKStatusInfoManual() {
    const statusInfo = document.getElementById('nkkStatusInfoManual');
    if (statusInfo) statusInfo.style.display = 'none';
}

function switchToExistingKK() {
    // Switch to existing KK option
    const existingRadio = document.querySelector('input[name="kk_option"][value="existing"]');
    if (existingRadio) {
        existingRadio.checked = true;
        existingRadio.dispatchEvent(new Event('change'));

        // Fill the existing KK search with current NKK
        const nkkManual = document.getElementById('nkk').value;
        const existingSearch = document.getElementById('nkk_search_penduduk_new');
        if (existingSearch && nkkManual) {
            existingSearch.value = nkkManual;
            existingSearch.dispatchEvent(new Event('input'));
        }
    }
}

// Family Members Management
let familyMemberCount = 0;

function addFamilyMember() {
    familyMemberCount++;
    const container = document.getElementById('familyMembersContainer');

    const memberDiv = document.createElement('div');
    memberDiv.className = 'bg-gray-50 rounded-lg p-4 border border-gray-200';
    memberDiv.id = `familyMember_${familyMemberCount}`;

    memberDiv.innerHTML = `
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-md font-medium text-gray-900">Anggota Keluarga ${familyMemberCount}</h4>
            <button type="button" onclick="removeFamilyMember(${familyMemberCount})"
                    class="text-red-600 hover:text-red-800 p-1">
                <i class="fas fa-trash"></i>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">NIK</label>
                <div class="relative">
                    <input type="text" name="family_members[${familyMemberCount}][nik]"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="16 digit NIK" maxlength="16" required>
                    <div id="nik_check_loading_family_${familyMemberCount}" class="absolute right-3 top-3 hidden">
                        <i class="fas fa-spinner fa-spin text-gray-400"></i>
                    </div>
                </div>
                <div id="nikStatusInfo_family_${familyMemberCount}" class="mt-2 text-sm" style="display: none;">
                    <div id="nikNewInfo_family_${familyMemberCount}" class="text-green-700 bg-green-50 p-2 rounded-lg border border-green-200" style="display: none;">
                        <i class="fas fa-check-circle mr-1"></i>
                        <strong>NIK Tersedia:</strong> NIK ini belum terdaftar di database
                    </div>
                    <div id="nikExistingInfo_family_${familyMemberCount}" class="text-red-700 bg-red-50 p-2 rounded-lg border border-red-200" style="display: none;">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>NIK Sudah Ada:</strong> <span id="existingNIKDetails_family_${familyMemberCount}"></span>
                    </div>
                </div>
            </div>

            <!-- Alamat disembunyikan karena otomatis mengikuti Kepala Keluarga (Source of Truth) -->

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" name="family_members[${familyMemberCount}][nama]"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Nama lengkap" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                <select name="family_members[${familyMemberCount}][jenis_kelamin]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="LAKI-LAKI">Laki-laki</option>
                    <option value="PEREMPUAN">Perempuan</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kedudukan dalam Keluarga</label>
                <select name="family_members[${familyMemberCount}][kedudukan_keluarga]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Pilih Kedudukan</option>
                    <option value="Istri">Istri</option>
                    <option value="Anak">Anak</option>
                    <option value="Cucu">Cucu</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir</label>
                <input type="text" name="family_members[${familyMemberCount}][tempat_lahir]"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Tempat lahir" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                <input type="date" name="family_members[${familyMemberCount}][tanggal_lahir]"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Agama</label>
                <select name="family_members[${familyMemberCount}][agama]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Pilih Agama</option>
                    <option value="Islam">Islam</option>
                    <option value="Kristen">Kristen</option>
                    <option value="Katolik">Katolik</option>
                    <option value="Hindu">Hindu</option>
                    <option value="Buddha">Buddha</option>
                    <option value="Konghucu">Konghucu</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Perkawinan</label>
                <select name="family_members[${familyMemberCount}][status_perkawinan]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Status Perkawinan</option>
                    <option value="Belum Kawin">Belum Kawin</option>
                    <option value="Kawin">Kawin</option>
                    <option value="Cerai Hidup">Cerai Hidup</option>
                    <option value="Cerai Mati">Cerai Mati</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan</label>
                <input type="text" name="family_members[${familyMemberCount}][pekerjaan]"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Pekerjaan" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pendidikan</label>
                <input type="text" name="family_members[${familyMemberCount}][pendidikan]"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Contoh: SD, SMP, SMA, S1, dll" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Ayah</label>
                <input type="text" name="family_members[${familyMemberCount}][nama_ayah]"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Nama ayah">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Ibu</label>
                <input type="text" name="family_members[${familyMemberCount}][nama_ibu]"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Nama ibu">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">RT</label>
                <input type="text" name="family_members[${familyMemberCount}][rt]"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                       placeholder="RT otomatis dari KK" readonly>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">RW</label>
                <input type="text" name="family_members[${familyMemberCount}][rw]"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                       placeholder="RW otomatis dari KK" readonly>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dusun</label>
                <input type="text" name="family_members[${familyMemberCount}][dusun]"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                       placeholder="Dusun otomatis dari KK" readonly>
            </div>
        </div>
    `;

    container.appendChild(memberDiv);

    // Auto-fill address fields from main form
    updateFamilyMemberAddress(familyMemberCount);

    // Add NIK validation for family members
    const nikInput = memberDiv.querySelector('input[name*="[nik]"]');
    if (nikInput) {
        let nikCheckTimeout;
        nikInput.addEventListener('input', function() {
            clearTimeout(nikCheckTimeout);
            const nikValue = this.value.trim();

            // Hide status info while typing
            const nikStatusInfo = document.getElementById(`nikStatusInfo_family_${familyMemberCount}`);
            const nikNewInfo = document.getElementById(`nikNewInfo_family_${familyMemberCount}`);
            const nikExistingInfo = document.getElementById(`nikExistingInfo_family_${familyMemberCount}`);

            if (nikStatusInfo) nikStatusInfo.style.display = 'none';
            if (nikNewInfo) nikNewInfo.style.display = 'none';
            if (nikExistingInfo) nikExistingInfo.style.display = 'none';

            if (nikValue.length === 16) {
                nikCheckTimeout = setTimeout(() => {
                    checkNIKExistsForFamilyMember(nikValue, familyMemberCount);
                }, 500);
            }
        });
    }

    console.log(`Family member ${familyMemberCount} added`);
}

function removeFamilyMember(memberId) {
    const memberDiv = document.getElementById(`familyMember_${memberId}`);
    if (memberDiv) {
        memberDiv.remove();
        console.log(`Family member ${memberId} removed`);
    }
}

function checkNIKExistsForFamilyMember(nik, memberId) {
    console.log(`Checking NIK for family member ${memberId}:`, nik);

    const loading = document.getElementById(`nik_check_loading_family_${memberId}`);
    if (loading) {
        loading.classList.remove('hidden');
    }

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
        if (loading) {
            loading.classList.add('hidden');
        }

        const nikStatusInfo = document.getElementById(`nikStatusInfo_family_${memberId}`);
        const nikNewInfo = document.getElementById(`nikNewInfo_family_${memberId}`);
        const nikExistingInfo = document.getElementById(`nikExistingInfo_family_${memberId}`);
        const existingNIKDetails = document.getElementById(`existingNIKDetails_family_${memberId}`);

        if (data.exists) {
            // NIK already exists - show error alert and info
            showNIKExistsAlertForFamilyMember(nik, data.data, memberId);

            // Show existing NIK info
            if (nikStatusInfo) nikStatusInfo.style.display = 'block';
            if (nikNewInfo) nikNewInfo.style.display = 'none';
            if (nikExistingInfo) nikExistingInfo.style.display = 'block';
            if (existingNIKDetails) {
                existingNIKDetails.innerHTML = `NIK ini sudah terdaftar atas nama <strong>${data.data.nama}</strong>`;
            }
        } else {
            // NIK is new - show success info
            console.log(`NIK ${nik} is available for family member ${memberId}`);

            // Show available NIK info
            if (nikStatusInfo) nikStatusInfo.style.display = 'block';
            if (nikNewInfo) nikNewInfo.style.display = 'block';
            if (nikExistingInfo) nikExistingInfo.style.display = 'none';
        }
    })
    .catch(error => {
        console.error('NIK check error for family member:', error);
        if (loading) {
            loading.classList.add('hidden');
        }
    });
}

function showNIKExistsAlertForFamilyMember(nik, data, memberId) {
    Swal.fire({
        title: 'NIK Sudah Ada!',
        html: `NIK <strong>${nik}</strong> sudah terdaftar atas nama <strong>${data.nama}</strong>.<br>
               Silakan gunakan NIK yang berbeda untuk anggota keluarga ini.`,
        icon: 'warning',
        confirmButtonText: 'OK'
    });

    // Clear the NIK input
    const memberDiv = document.getElementById(`familyMember_${memberId}`);
    const nikInput = memberDiv.querySelector('input[name*="[nik]"]');
    if (nikInput) {
        nikInput.value = '';
        nikInput.focus();
    }
}


function validateRTFormat(rtInput) {
    const value = rtInput.value.trim();

    // Remove any error styling
    rtInput.classList.remove('border-red-500');

    // Check if RT is empty
    if (value === '') {
        return;
    }

    // Check if RT is exactly 3 digits
    if (!/^[0-9]{3}$/.test(value)) {
        rtInput.classList.add('border-red-500');
        return;
    }

    // Check if RT is not 000
    if (value === '000') {
        rtInput.classList.add('border-red-500');
        return;
    }

    // Check if RT is within valid range (001-999)
    const rtNumber = parseInt(value);
    if (rtNumber < 1 || rtNumber > 999) {
        rtInput.classList.add('border-red-500');
        return;
    }

    // RT is valid
    rtInput.classList.remove('border-red-500');
}

function updateDusunFromRT() {
    const rtInput = document.getElementById('rt');
    const dusunInput = document.getElementById('dusun');

    if (!rtInput || !dusunInput) return;

    const rt = rtInput.value.trim();
    if (rt.length === 0) {
        dusunInput.value = '';
        return;
    }

    // Convert RT to 3-digit format
    const rtFormatted = rt.padStart(3, '0');

    // Dusun mapping based on RT (same as backend)
    const dusunSatu = ['001', '002', '003', '004', '007', '008'];
    const dusunDua = ['005', '006', '009', '010'];

    let dusun = 'Dusun Satu'; // Default
    if (dusunSatu.includes(rtFormatted)) {
        dusun = 'Dusun Satu';
    } else if (dusunDua.includes(rtFormatted)) {
        dusun = 'Dusun Dua';
    }

    dusunInput.value = dusun;
    console.log(`RT ${rtFormatted} mapped to ${dusun}`);
}

// NIK Check functions
function checkNIKExists(nik) {
    console.log('checkNIKExists called with NIK:', nik);
    const loading = document.getElementById('nik_check_loading');
    const statusInfo = document.getElementById('nikStatusInfo');
    const newInfo = document.getElementById('nikNewInfo');
    const existingInfo = document.getElementById('nikExistingInfo');

    console.log('Elements found:', {
        loading: !!loading,
        statusInfo: !!statusInfo,
        newInfo: !!newInfo,
        existingInfo: !!existingInfo
    });

    if (loading) loading.classList.remove('hidden');
    if (statusInfo) statusInfo.style.display = 'none';

    console.log('Fetching from:', `{{ route('penduduk.check-nik') }}?nik=${encodeURIComponent(nik)}&exclude_id=`);
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
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (loading) loading.classList.add('hidden');

        if (data.exists) {
            console.log('NIK exists, showing existing info');
            // NIK already exists
            showExistingNIKInfo(data.data);
        } else {
            console.log('NIK is new, showing new info');
            // NIK is new
            showNewNIKInfo();
        }
    })
    .catch(error => {
        console.error('NIK check error:', error);
        if (loading) loading.classList.add('hidden');
        hideNIKStatusInfo();
    });
}

function showNewNIKInfo() {
    const statusInfo = document.getElementById('nikStatusInfo');
    const newInfo = document.getElementById('nikNewInfo');
    const existingInfo = document.getElementById('nikExistingInfo');

    if (statusInfo) statusInfo.style.display = 'block';
    if (newInfo) newInfo.style.display = 'block';
    if (existingInfo) existingInfo.style.display = 'none';
}

function showExistingNIKInfo(data) {
    const statusInfo = document.getElementById('nikStatusInfo');
    const newInfo = document.getElementById('nikNewInfo');
    const existingInfo = document.getElementById('nikExistingInfo');
    const existingDetails = document.getElementById('existingNIKDetails');

    if (statusInfo) statusInfo.style.display = 'block';
    if (newInfo) newInfo.style.display = 'none';
    if (existingInfo) existingInfo.style.display = 'block';
    if (existingDetails) {
        existingDetails.textContent = `${data.nama} - No KK: ${data.nkk} - RT ${data.rt_label}/RW ${data.rw_label}`;
    }
}

function hideNIKStatusInfo() {
    const statusInfo = document.getElementById('nikStatusInfo');
    if (statusInfo) statusInfo.style.display = 'none';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    try {
        console.log('Create Penduduk: DOM loaded');
        const kkOption = document.querySelector('input[name="kk_option"]:checked')?.value || 'existing';
        toggleKKOption(kkOption);
        if (kkOption === 'manual') {
            const oldRwId = @json(old('rw_id'));
            const oldRtId = @json(old('rt_id'));
            if (oldRwId) {
                const rwMaster = document.getElementById('rw_id');
                if (rwMaster) rwMaster.value = String(oldRwId);
                populateRtByRw();
                const rtMaster = document.getElementById('rt_id');
                if (rtMaster && oldRtId) rtMaster.value = String(oldRtId);
                syncMasterWilayahFromSelection();
            }
        }
        console.log('toggleKKOption completed');
    } catch (error) {
        console.error('Error in toggleKKOption:', error);
    }

    // Add event listener for NIK input
    try {
        const nikInput = document.getElementById('nik');
        console.log('NIK input element found:', !!nikInput);

        if (nikInput) {
            let nikCheckTimeout;
            nikInput.addEventListener('input', function() {
                try {
                    console.log('NIK input event triggered, value:', this.value);
                    clearTimeout(nikCheckTimeout);
                    const nikValue = this.value.trim();

                    if (nikValue.length === 16) {
                        console.log('NIK length is 16, calling checkNIKExists');
                        nikCheckTimeout = setTimeout(() => {
                            checkNIKExists(nikValue);
                        }, 500); // 500ms debounce
                    } else {
                        console.log('NIK length is not 16, hiding status info');
                        hideNIKStatusInfo();
                    }
                } catch (error) {
                    console.error('Error in NIK input event:', error);
                }
            });
            console.log('NIK event listener added successfully');
        } else {
            console.error('NIK input element not found!');
        }
    } catch (error) {
        console.error('Error setting up NIK event listener:', error);
    }

    // Add event listener for KK search input
    try {
        const kkSearchInput = document.getElementById('nkk_search_penduduk_new');
        console.log('KK search input element found:', !!kkSearchInput);

        if (kkSearchInput) {
            let kkSearchTimeout;
            kkSearchInput.addEventListener('input', function() {
                try {
                    console.log('KK search input event triggered, value:', this.value);
                    clearTimeout(kkSearchTimeout);
                    const searchValue = this.value.trim();

                    if (searchValue.length >= 3) {
                        console.log('KK search length >= 3, calling searchKKPenduduk');
                        kkSearchTimeout = setTimeout(() => {
                            searchKKPenduduk();
                        }, 300); // 300ms debounce
                    } else {
                        console.log('KK search length < 3, hiding results');
                        const searchResults = document.getElementById('nkk_search_results_penduduk');
                        if (searchResults) searchResults.classList.add('hidden');
                    }
                } catch (error) {
                    console.error('Error in KK search input event:', error);
                }
            });
            console.log('KK search event listener added successfully');
        } else {
            console.error('KK search input element not found!');
        }
    } catch (error) {
        console.error('Error setting up KK search event listener:', error);
    }

    // Add event listener for NKK manual input
    try {
        const nkkManualInput = document.getElementById('nkk');
        console.log('NKK manual input element found:', !!nkkManualInput);

        if (nkkManualInput) {
            let nkkCheckTimeout;
            nkkManualInput.addEventListener('input', function() {
                try {
                    console.log('NKK manual input event triggered, value:', this.value);
                    clearTimeout(nkkCheckTimeout);
                    const nkkValue = this.value.trim();

                    if (nkkValue.length === 16) {
                        console.log('NKK length is 16, calling checkNKKExistsManual');
                        nkkCheckTimeout = setTimeout(() => {
                            checkNKKExistsManual(nkkValue);
                        }, 500); // 500ms debounce
                    } else {
                        console.log('NKK length is not 16, hiding status info');
                        hideNKKStatusInfoManual();
                    }
                } catch (error) {
                    console.error('Error in NKK manual input event:', error);
                }
            });
            console.log('NKK manual event listener added successfully');
        } else {
            console.error('NKK manual input element not found!');
        }
    } catch (error) {
        console.error('Error setting up NKK manual event listener:', error);
    }

    if (rtInput) {
        rtInput.addEventListener('input', function() {
            validateRTFormat(this);
        });
    }

    const rwMaster = document.getElementById('rw_id');
    const rtMaster = document.getElementById('rt_id');
    if (rwMaster) rwMaster.addEventListener('change', populateRtByRw);
    if (rtMaster) rtMaster.addEventListener('change', syncMasterWilayahFromSelection);

    // Form submission handler
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Basic validation
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                } else {
                    field.classList.remove('border-red-500');
                }
            });

            // Validate family members NIK
            const familyMembers = document.querySelectorAll('[name*="family_members"][name*="[nik]"]');
            familyMembers.forEach(nikField => {
                const nikValue = nikField.value.trim();
                if (nikValue && nikValue.length !== 16) {
                    nikField.classList.add('border-red-500');
                    isValid = false;
                } else {
                    nikField.classList.remove('border-red-500');
                }
            });

            if (!isValid) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Mohon lengkapi semua field yang wajib diisi dengan benar.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Show loading
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
            }

            // Submit form
            form.submit();
        });
    }
});
</script>
@endsection



