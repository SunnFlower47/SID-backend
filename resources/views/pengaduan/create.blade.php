@extends('layouts.app')

@section('title', 'Tambah Pengaduan Warga')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-2xl shadow-xl p-6 sm:p-8 mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-plus text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Tambah Pengaduan</h1>
                    <p class="text-red-100 text-sm sm:text-base">Buat pengaduan baru untuk warga</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('pengaduan.index') }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
                <div class="px-6 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Form Pengaduan Warga</h3>
                            <p class="text-sm text-gray-600">Isi data pengaduan dengan lengkap dan benar</p>
                        </div>
                    </div>
                </div>
                <div class="p-8">
                    <form action="{{ route('pengaduan.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nama_pelapor" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Pelapor <span class="text-red-500">*</span>
                                </label>
                                <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300 @error('nama_pelapor') border-red-500 @enderror"
                                       id="nama_pelapor" name="nama_pelapor" value="{{ old('nama_pelapor') }}"
                                       placeholder="Nama lengkap pelapor" required>
                                @error('nama_pelapor')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="nik_pelapor" class="block text-sm font-medium text-gray-700 mb-2">
                                    NIK Pelapor
                                </label>
                                <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300 @error('nik_pelapor') border-red-500 @enderror"
                                       id="nik_pelapor" name="nik_pelapor" value="{{ old('nik_pelapor') }}"
                                       placeholder="16 digit NIK" maxlength="16">
                                @error('nik_pelapor')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="telepon" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor Telepon
                                </label>
                                <input type="tel" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300 @error('telepon') border-red-500 @enderror"
                                       id="telepon" name="telepon" value="{{ old('telepon') }}"
                                       placeholder="08xxxxxxxxxx">
                                @error('telepon')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email
                                </label>
                                <input type="email" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300 @error('email') border-red-500 @enderror"
                                       id="email" name="email" value="{{ old('email') }}"
                                       placeholder="email@example.com">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat <span class="text-red-500">*</span>
                            </label>
                            <textarea class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300 @error('alamat') border-red-500 @enderror"
                                      id="alamat" name="alamat" rows="3" placeholder="Alamat lengkap pelapor" required>{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kategori <span class="text-red-500">*</span>
                                </label>
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300 @error('kategori') border-red-500 @enderror"
                                        id="kategori" name="kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="infrastruktur" {{ old('kategori') == 'infrastruktur' ? 'selected' : '' }}>Infrastruktur</option>
                                    <option value="keamanan" {{ old('kategori') == 'keamanan' ? 'selected' : '' }}>Keamanan</option>
                                    <option value="kebersihan" {{ old('kategori') == 'kebersihan' ? 'selected' : '' }}>Kebersihan</option>
                                    <option value="administrasi" {{ old('kategori') == 'administrasi' ? 'selected' : '' }}>Administrasi</option>
                                    <option value="lainnya" {{ old('kategori') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('kategori')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="prioritas" class="block text-sm font-medium text-gray-700 mb-2">
                                    Prioritas <span class="text-red-500">*</span>
                                </label>
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300 @error('prioritas') border-red-500 @enderror"
                                        id="prioritas" name="prioritas" required>
                                    <option value="">Pilih Prioritas</option>
                                    <option value="rendah" {{ old('prioritas') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                                    <option value="sedang" {{ old('prioritas') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                                    <option value="tinggi" {{ old('prioritas') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                                    <option value="darurat" {{ old('prioritas') == 'darurat' ? 'selected' : '' }}>Darurat</option>
                                </select>
                                @error('prioritas')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">
                                Judul Pengaduan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300 @error('judul') border-red-500 @enderror"
                                   id="judul" name="judul" value="{{ old('judul') }}"
                                   placeholder="Judul singkat pengaduan" required>
                            @error('judul')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6">
                            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi Pengaduan <span class="text-red-500">*</span>
                            </label>
                            <textarea class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300 @error('deskripsi') border-red-500 @enderror"
                                      id="deskripsi" name="deskripsi" rows="5" placeholder="Deskripsikan pengaduan secara detail..." required>{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6">
                            <label for="lokasi" class="block text-sm font-medium text-gray-700 mb-2">
                                Lokasi Kejadian
                            </label>
                            <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300 @error('lokasi') border-red-500 @enderror"
                                   id="lokasi" name="lokasi" value="{{ old('lokasi') }}"
                                   placeholder="Lokasi kejadian (opsional)">
                            @error('lokasi')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6">
                            <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">
                                Foto Pendukung
                            </label>
                            <input type="file" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300 @error('foto') border-red-500 @enderror"
                                   id="foto" name="foto[]" multiple accept="image/*">
                            <p class="mt-2 text-sm text-gray-500">Upload foto pendukung (maksimal 5 file, format: JPG, PNG, GIF)</p>
                            @error('foto')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-4 mt-8">
                            <a href="{{ route('pengaduan.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300">
                                Batal
                            </a>
                            <button type="submit" class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300">
                                <i class="fas fa-paper-plane mr-2"></i> Kirim Pengaduan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h6 class="text-lg font-semibold text-gray-900">Informasi</h6>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="bg-red-100 p-2 rounded-full">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Prioritas Darurat</h4>
                                <p class="text-sm text-gray-600">Gunakan prioritas darurat hanya untuk kejadian yang memerlukan penanganan segera.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="bg-blue-100 p-2 rounded-full">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Data Pribadi</h4>
                                <p class="text-sm text-gray-600">Data pribadi akan dijaga kerahasiaannya dan hanya digunakan untuk keperluan penanganan pengaduan.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="bg-green-100 p-2 rounded-full">
                                <i class="fas fa-check-circle text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Respon Cepat</h4>
                                <p class="text-sm text-gray-600">Pengaduan akan ditangani sesuai prioritas dan Anda akan mendapat notifikasi status.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
@endnoncescript
@endsection
