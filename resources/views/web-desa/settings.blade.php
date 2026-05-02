@extends('layouts.app')

@section('title', 'Pengaturan Web Desa')
@section('subtitle', 'Kelola pengaturan website desa')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-blue-50">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-xl mr-4">
                    <i class="fas fa-cog text-green-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Pengaturan Web Desa</h2>
                    <p class="text-gray-600">Kelola pengaturan website desa Cibatu</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <form action="{{ route('web-desa.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Informasi Desa -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Informasi Desa</h3>

                        <!-- Nama Desa -->
                        <div>
                            <label for="nama_desa" class="block text-sm font-semibold text-gray-700 mb-2">Nama Desa *</label>
                            <input type="text"
                                   id="nama_desa"
                                   name="nama_desa"
                                   value="{{ old('nama_desa', 'Desa Cibatu') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('nama_desa') border-red-500 @enderror"
                                   placeholder="Masukkan nama desa"
                                   required>
                            @error('nama_desa')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kecamatan -->
                        <div>
                            <label for="kecamatan" class="block text-sm font-semibold text-gray-700 mb-2">Kecamatan *</label>
                            <input type="text"
                                   id="kecamatan"
                                   name="kecamatan"
                                   value="{{ old('kecamatan', 'Cibatu') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('kecamatan') border-red-500 @enderror"
                                   placeholder="Masukkan nama kecamatan"
                                   required>
                            @error('kecamatan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kabupaten -->
                        <div>
                            <label for="kabupaten" class="block text-sm font-semibold text-gray-700 mb-2">Kabupaten *</label>
                            <input type="text"
                                   id="kabupaten"
                                   name="kabupaten"
                                   value="{{ old('kabupaten', 'Purwakarta') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('kabupaten') border-red-500 @enderror"
                                   placeholder="Masukkan nama kabupaten"
                                   required>
                            @error('kabupaten')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Provinsi -->
                        <div>
                            <label for="provinsi" class="block text-sm font-semibold text-gray-700 mb-2">Provinsi *</label>
                            <input type="text"
                                   id="provinsi"
                                   name="provinsi"
                                   value="{{ old('provinsi', 'Jawa Barat') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('provinsi') border-red-500 @enderror"
                                   placeholder="Masukkan nama provinsi"
                                   required>
                            @error('provinsi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kode Pos -->
                        <div>
                            <label for="kode_pos" class="block text-sm font-semibold text-gray-700 mb-2">Kode Pos</label>
                            <input type="text"
                                   id="kode_pos"
                                   name="kode_pos"
                                   value="{{ old('kode_pos', '41162') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('kode_pos') border-red-500 @enderror"
                                   placeholder="Masukkan kode pos">
                            @error('kode_pos')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Kontak & Media Sosial -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Kontak & Media Sosial</h3>

                        <!-- Alamat Lengkap -->
                        <div>
                            <label for="alamat" class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap</label>
                            <textarea id="alamat"
                                      name="alamat"
                                      rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('alamat') border-red-500 @enderror"
                                      placeholder="Masukkan alamat lengkap desa">{{ old('alamat', 'Jl. Cibatu Km. 15, Desa Cibatu, Kec. Cibatu, Kab. Purwakarta23, Desa Cibatu, Kec. Cibatu, Kab. Purwakarta, Jawa Barat 41162') }}</textarea>
                            @error('alamat')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Telepon -->
                        <div>
                            <label for="telepon" class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon</label>
                            <input type="text"
                                   id="telepon"
                                   name="telepon"
                                   value="{{ old('telepon', '0264-123456') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('telepon') border-red-500 @enderror"
                                   placeholder="Masukkan nomor telepon">
                            @error('telepon')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', 'desa.cibatu@purwakarta.go.id') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('email') border-red-500 @enderror"
                                   placeholder="Masukkan email desa">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Website -->
                        <div>
                            <label for="website" class="block text-sm font-semibold text-gray-700 mb-2">Website</label>
                            <input type="url"
                                   id="website"
                                   name="website"
                                   value="{{ old('website', 'https://desa-cibatu.purwakarta.go.id') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('website') border-red-500 @enderror"
                                   placeholder="Masukkan URL website">
                            @error('website')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Facebook -->
                        <div>
                            <label for="facebook" class="block text-sm font-semibold text-gray-700 mb-2">Facebook</label>
                            <input type="url"
                                   id="facebook"
                                   name="facebook"
                                   value="{{ old('facebook', 'https://facebook.com/desacibatu') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('facebook') border-red-500 @enderror"
                                   placeholder="Masukkan URL Facebook">
                            @error('facebook')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Instagram -->
                        <div>
                            <label for="instagram" class="block text-sm font-semibold text-gray-700 mb-2">Instagram</label>
                            <input type="url"
                                   id="instagram"
                                   name="instagram"
                                   value="{{ old('instagram', 'https://instagram.com/desacibatu') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('instagram') border-red-500 @enderror"
                                   placeholder="Masukkan URL Instagram">
                            @error('instagram')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Logo Desa -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Logo Desa</h3>

                    <div class="flex items-start space-x-6">
                        <!-- Current Logo -->
                        <div class="flex-shrink-0">
                            <img src="{{ asset('assets/images/logo-desa-cibatu.png') }}"
                                 alt="Logo Desa Cibatu"
                                 class="w-24 h-24 object-contain border border-gray-200 rounded-lg">
                            <p class="text-xs text-gray-500 mt-2 text-center">Logo saat ini</p>
                        </div>

                        <!-- Upload New Logo -->
                        <div class="flex-1">
                            <label for="logo" class="block text-sm font-semibold text-gray-700 mb-2">Upload Logo Baru</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-green-400 transition-colors duration-200">
                                <div class="space-y-1 text-center">
                                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="logo" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                                            <span>Upload logo baru</span>
                                            <input id="logo" name="logo" type="file" class="sr-only" accept="image/*">
                                        </label>
                                        <p class="pl-1">atau drag & drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF hingga 2MB</p>
                                </div>
                            </div>
                            @error('logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100 mt-8">
                    <a href="{{ route('berita.index') }}"
                       class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

