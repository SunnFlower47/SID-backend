@extends('layouts.app')

@section('title', 'Tambah Data UMKM')
@section('subtitle', 'Tambah data Usaha Mikro, Kecil, dan Menengah baru')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="fas fa-store text-yellow-300 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Tambah Data UMKM</h1>
                <p class="text-green-100 mt-1">Tambah data Usaha Mikro, Kecil, dan Menengah baru</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-lg border-0 p-6 sm:p-8">
        <form action="{{ route('umkm.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Nama Usaha -->
                <div class="lg:col-span-2">
                    <label for="nama_usaha" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Usaha <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_usaha" id="nama_usaha" value="{{ old('nama_usaha') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nama_usaha') border-red-500 @enderror"
                           placeholder="Masukkan nama usaha">
                    @error('nama_usaha')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Nama Pemilik -->
                <div>
                    <label for="nama_pemilik" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Pemilik <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_pemilik" id="nama_pemilik" value="{{ old('nama_pemilik') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nama_pemilik') border-red-500 @enderror"
                           placeholder="Masukkan nama pemilik">
                    @error('nama_pemilik')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- NIK Pemilik -->
                <div>
                    <label for="nik_pemilik" class="block text-sm font-medium text-gray-700 mb-2">NIK Pemilik</label>
                    <input type="text" name="nik_pemilik" id="nik_pemilik" value="{{ old('nik_pemilik') }}" maxlength="16"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nik_pemilik') border-red-500 @enderror"
                           placeholder="16 digit NIK">
                    @error('nik_pemilik')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Alamat Usaha -->
                <div class="lg:col-span-2">
                    <label for="alamat_usaha" class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat Usaha <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alamat_usaha" id="alamat_usaha" rows="3" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('alamat_usaha') border-red-500 @enderror"
                              placeholder="Masukkan alamat lengkap usaha">{{ old('alamat_usaha') }}</textarea>
                    @error('alamat_usaha')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- RT -->
                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700 mb-2">RT</label>
                    <input type="text" name="rt" id="rt" value="{{ old('rt') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('rt') border-red-500 @enderror"
                           placeholder="RT">
                    @error('rt')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- RW -->
                <div>
                    <label for="rw" class="block text-sm font-medium text-gray-700 mb-2">RW</label>
                    <input type="text" name="rw" id="rw" value="{{ old('rw') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('rw') border-red-500 @enderror"
                           placeholder="RW">
                    @error('rw')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Dusun -->
                <div>
                    <label for="dusun" class="block text-sm font-medium text-gray-700 mb-2">Dusun</label>
                    <input type="text" name="dusun" id="dusun" value="{{ old('dusun') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('dusun') border-red-500 @enderror"
                           placeholder="Dusun">
                    @error('dusun')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- No Telepon -->
                <div>
                    <label for="no_telepon" class="block text-sm font-medium text-gray-700 mb-2">No Telepon</label>
                    <input type="text" name="no_telepon" id="no_telepon" value="{{ old('no_telepon') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('no_telepon') border-red-500 @enderror"
                           placeholder="Nomor telepon">
                    @error('no_telepon')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror"
                           placeholder="Email usaha">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Jenis Usaha -->
                <div>
                    <label for="jenis_usaha" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Usaha <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_usaha" id="jenis_usaha" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('jenis_usaha') border-red-500 @enderror">
                        <option value="">Pilih Jenis Usaha</option>
                        <option value="makanan" {{ old('jenis_usaha') == 'makanan' ? 'selected' : '' }}>Makanan</option>
                        <option value="minuman" {{ old('jenis_usaha') == 'minuman' ? 'selected' : '' }}>Minuman</option>
                        <option value="kerajinan" {{ old('jenis_usaha') == 'kerajinan' ? 'selected' : '' }}>Kerajinan</option>
                        <option value="jasa" {{ old('jenis_usaha') == 'jasa' ? 'selected' : '' }}>Jasa</option>
                        <option value="perdagangan" {{ old('jenis_usaha') == 'perdagangan' ? 'selected' : '' }}>Perdagangan</option>
                        <option value="pertanian" {{ old('jenis_usaha') == 'pertanian' ? 'selected' : '' }}>Pertanian</option>
                        <option value="peternakan" {{ old('jenis_usaha') == 'peternakan' ? 'selected' : '' }}>Peternakan</option>
                        <option value="lainnya" {{ old('jenis_usaha') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('jenis_usaha')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Status Usaha -->
                <div>
                    <label for="status_usaha" class="block text-sm font-medium text-gray-700 mb-2">
                        Status Usaha <span class="text-red-500">*</span>
                    </label>
                    <select name="status_usaha" id="status_usaha" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('status_usaha') border-red-500 @enderror">
                        <option value="">Pilih Status Usaha</option>
                        <option value="aktif" {{ old('status_usaha') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tutup" {{ old('status_usaha') == 'tutup' ? 'selected' : '' }}>Tutup</option>
                        <option value="pindah" {{ old('status_usaha') == 'pindah' ? 'selected' : '' }}>Pindah</option>
                    </select>
                    @error('status_usaha')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Tanggal Berdiri -->
                <div>
                    <label for="tanggal_berdiri" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berdiri</label>
                    <input type="date" name="tanggal_berdiri" id="tanggal_berdiri" value="{{ old('tanggal_berdiri') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('tanggal_berdiri') border-red-500 @enderror">
                    @error('tanggal_berdiri')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                <!-- Jumlah Karyawan -->
                <div>
                    <label for="jumlah_karyawan" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Karyawan <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah_karyawan" id="jumlah_karyawan" value="{{ old('jumlah_karyawan') }}" min="0" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('jumlah_karyawan') border-red-500 @enderror"
                           placeholder="Jumlah karyawan">
                    @error('jumlah_karyawan')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Latitude -->
                <div>
                    <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                    <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('latitude') border-red-500 @enderror"
                           placeholder="Latitude">
                    @error('latitude')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Longitude -->
                <div>
                    <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                    <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('longitude') border-red-500 @enderror"
                           placeholder="Longitude">
                    @error('longitude')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Deskripsi Usaha -->
                <div class="lg:col-span-2">
                    <label for="deskripsi_usaha" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Usaha</label>
                    <textarea name="deskripsi_usaha" id="deskripsi_usaha" rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('deskripsi_usaha') border-red-500 @enderror"
                              placeholder="Deskripsi usaha">{{ old('deskripsi_usaha') }}</textarea>
                    @error('deskripsi_usaha')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Produk Unggulan -->
                <div class="lg:col-span-2">
                    <label for="produk_unggulan" class="block text-sm font-medium text-gray-700 mb-2">Produk Unggulan</label>
                    <div id="produk-container">
                        <div class="flex space-x-2 mb-2">
                            <input type="text" name="produk_unggulan[]" placeholder="Nama produk unggulan"
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <button type="button" onclick="addProduk()" class="px-4 py-3 bg-green-500 text-white rounded-xl hover:bg-green-600">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500">Klik tombol + untuk menambah produk unggulan</p>
                </div>

                <!-- Foto Usaha -->
                <div class="lg:col-span-2">
                    <label for="foto_usaha" class="block text-sm font-medium text-gray-700 mb-2">Foto Usaha</label>
                    <input type="file" name="foto_usaha[]" id="foto_usaha" accept="image/*" multiple
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('foto_usaha') border-red-500 @enderror">
                    <p class="mt-2 text-sm text-gray-500">Format: JPG, PNG, GIF. Maksimal 2MB per file. Pilih multiple file untuk upload beberapa foto.</p>
                    @error('foto_usaha')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Checkboxes -->
                <div class="lg:col-span-2">
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_unggulan" value="1" {{ old('is_unggulan') ? 'checked' : '' }}
                                   class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Usaha Unggulan</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_verified" value="1" {{ old('is_verified') ? 'checked' : '' }}
                                   class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Terverifikasi</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <button type="submit" class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-xl hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Simpan UMKM
                </button>
                <a href="{{ route('umkm.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gray-500 text-white font-medium rounded-xl hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-lg">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@noncescript
// Add produk unggulan
function addProduk() {
    const container = document.getElementById('produk-container');
    const div = document.createElement('div');
    div.className = 'flex space-x-2 mb-2';
    div.innerHTML = `
        <input type="text" name="produk_unggulan[]" placeholder="Nama produk unggulan"
               class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
        <button type="button" onclick="removeProduk(this)" class="px-4 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(div);
}

// Remove produk unggulan
function removeProduk(button) {
    button.parentElement.remove();
}

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
@endnoncescript
@endsection
