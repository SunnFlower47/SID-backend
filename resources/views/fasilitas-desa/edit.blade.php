@extends('layouts.app')

@section('title', 'Edit Fasilitas Desa')
@section('subtitle', 'Edit data fasilitas dan infrastruktur desa')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="fas fa-building text-yellow-300 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Edit Fasilitas Desa</h1>
                <p class="text-green-100 mt-1">Edit data fasilitas dan infrastruktur desa</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-lg border-0 p-6 sm:p-8">
        <form action="{{ route('fasilitas-desa.update', $fasilitasDesa) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Nama Fasilitas -->
                <div class="lg:col-span-2">
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Fasilitas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama" id="nama" value="{{ old('nama', $fasilitasDesa->nama) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nama') border-red-500 @enderror"
                           placeholder="Masukkan nama fasilitas">
                    @error('nama')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Jenis -->
                <div>
                    <label for="jenis" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Fasilitas <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis" id="jenis" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('jenis') border-red-500 @enderror">
                        <option value="">Pilih Jenis Fasilitas</option>
                        @foreach($jenisOptions as $value => $label)
                        <option value="{{ $value }}" {{ old('jenis', $fasilitasDesa->jenis) == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('jenis')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Status Aktif -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="status_aktif" value="1" {{ old('status_aktif', $fasilitasDesa->status_aktif) ? 'checked' : '' }}
                                   class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Aktif</span>
                        </label>
                    </div>
                </div>

                <!-- Alamat -->
                <div class="lg:col-span-2">
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alamat" id="alamat" rows="3" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('alamat') border-red-500 @enderror"
                              placeholder="Masukkan alamat lengkap fasilitas">{{ old('alamat', $fasilitasDesa->alamat) }}</textarea>
                    @error('alamat')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- RT -->
                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700 mb-2">RT</label>
                    <input type="text" name="rt" id="rt" value="{{ old('rt', $fasilitasDesa->rt) }}"
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
                    <input type="text" name="rw" id="rw" value="{{ old('rw', $fasilitasDesa->rw) }}"
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
                    <input type="text" name="dusun" id="dusun" value="{{ old('dusun', $fasilitasDesa->dusun) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('dusun') border-red-500 @enderror"
                           placeholder="Dusun">
                    @error('dusun')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Kontak -->
                <div>
                    <label for="kontak" class="block text-sm font-medium text-gray-700 mb-2">Kontak</label>
                    <input type="text" name="kontak" id="kontak" value="{{ old('kontak', $fasilitasDesa->kontak) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('kontak') border-red-500 @enderror"
                           placeholder="Nomor telepon atau kontak">
                    @error('kontak')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Jam Operasional -->
                <div>
                    <label for="jam_operasional" class="block text-sm font-medium text-gray-700 mb-2">Jam Operasional</label>
                    <input type="text" name="jam_operasional" id="jam_operasional" value="{{ old('jam_operasional', $fasilitasDesa->jam_operasional) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('jam_operasional') border-red-500 @enderror"
                           placeholder="Contoh: 08:00 - 17:00">
                    @error('jam_operasional')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Latitude -->
                <div>
                    <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                    <input type="number" step="any" name="latitude" id="latitude" value="{{ old('latitude', $fasilitasDesa->latitude) }}"
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
                    <input type="number" step="any" name="longitude" id="longitude" value="{{ old('longitude', $fasilitasDesa->longitude) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('longitude') border-red-500 @enderror"
                           placeholder="Longitude">
                    @error('longitude')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="lg:col-span-2">
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('deskripsi') border-red-500 @enderror"
                              placeholder="Deskripsi fasilitas">{{ old('deskripsi', $fasilitasDesa->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Foto -->
                <div class="lg:col-span-2">
                    <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">Foto</label>

                    @if($fasilitasDesa->foto)
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Foto saat ini:</p>
                        <img src="{{ Storage::url($fasilitasDesa->foto) }}" alt="{{ $fasilitasDesa->nama }}"
                             class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                    </div>
                    @endif

                    <input type="file" name="foto" id="foto" accept="image/*"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('foto') border-red-500 @enderror">
                    <p class="mt-2 text-sm text-gray-500">Format: JPG, PNG, GIF. Maksimal 2MB. Kosongkan jika tidak ingin mengubah foto.</p>
                    @error('foto')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <button type="submit" class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-xl hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Update Fasilitas
                </button>
                <a href="{{ route('fasilitas-desa.show', $fasilitasDesa) }}" class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-blue-500 text-white font-medium rounded-xl hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-lg">
                    <i class="fas fa-eye mr-2"></i>
                    Lihat Detail
                </a>
                <a href="{{ route('fasilitas-desa.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gray-500 text-white font-medium rounded-xl hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-lg">
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
