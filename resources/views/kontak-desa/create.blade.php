@extends('layouts.app')

@section('title', 'Tambah Kontak Desa')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center mb-4 sm:mb-0">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-phone text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">Tambah Kontak Desa</h1>
                    <p class="text-green-100 text-sm sm:text-base">Tambah data kontak dan informasi komunikasi desa</p>
                </div>
            </div>
            <a href="{{ route('kontak-desa.index') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <form action="{{ route('kontak-desa.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-6">
                <!-- Nama dan Jabatan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="nama"
                               name="nama"
                               value="{{ old('nama') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nama') border-red-500 @enderror"
                               placeholder="Masukkan nama lengkap"
                               required>
                        @error('nama')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="jabatan" class="block text-sm font-medium text-gray-700 mb-2">
                            Jabatan
                        </label>
                        <input type="text"
                               id="jabatan"
                               name="jabatan"
                               value="{{ old('jabatan') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('jabatan') border-red-500 @enderror"
                               placeholder="Masukkan jabatan">
                        @error('jabatan')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Jenis dan Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="jenis" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis <span class="text-red-500">*</span>
                        </label>
                        <select id="jenis"
                                name="jenis"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('jenis') border-red-500 @enderror"
                                required>
                            <option value="">Pilih Jenis</option>
                            <option value="kantor_desa" {{ old('jenis') == 'kantor_desa' ? 'selected' : '' }}>Kantor Desa</option>
                            <option value="kepala_desa" {{ old('jenis') == 'kepala_desa' ? 'selected' : '' }}>Kepala Desa</option>
                            <option value="sekretaris" {{ old('jenis') == 'sekretaris' ? 'selected' : '' }}>Sekretaris Desa</option>
                            <option value="bendahara" {{ old('jenis') == 'bendahara' ? 'selected' : '' }}>Bendahara Desa</option>
                            <option value="kasi_pemerintahan" {{ old('jenis') == 'kasi_pemerintahan' ? 'selected' : '' }}>Kasi Pemerintahan</option>
                            <option value="kasi_kesejahteraan" {{ old('jenis') == 'kasi_kesejahteraan' ? 'selected' : '' }}>Kasi Kesejahteraan</option>
                            <option value="kasi_pelayanan" {{ old('jenis') == 'kasi_pelayanan' ? 'selected' : '' }}>Kasi Pelayanan</option>
                            <option value="kepala_dusun" {{ old('jenis') == 'kepala_dusun' ? 'selected' : '' }}>Kepala Dusun</option>
                            <option value="ketua_rw" {{ old('jenis') == 'ketua_rw' ? 'selected' : '' }}>Ketua RW</option>
                            <option value="ketua_rt" {{ old('jenis') == 'ketua_rt' ? 'selected' : '' }}>Ketua RT</option>
                            <option value="ketua_bumdes" {{ old('jenis') == 'ketua_bumdes' ? 'selected' : '' }}>Ketua BUMDes</option>
                            <option value="puskesmas" {{ old('jenis') == 'puskesmas' ? 'selected' : '' }}>Puskesmas</option>
                            <option value="posyandu" {{ old('jenis') == 'posyandu' ? 'selected' : '' }}>Posyandu</option>
                            <option value="sekolah" {{ old('jenis') == 'sekolah' ? 'selected' : '' }}>Sekolah</option>
                            <option value="masjid" {{ old('jenis') == 'masjid' ? 'selected' : '' }}>Masjid</option>
                            <option value="lainnya" {{ old('jenis') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('jenis')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status_aktif" class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>
                        <select id="status_aktif"
                                name="status_aktif"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('status_aktif') border-red-500 @enderror">
                            <option value="1" {{ old('status_aktif', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('status_aktif') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        @error('status_aktif')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Alamat -->
                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat
                    </label>
                    <textarea id="alamat"
                              name="alamat"
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('alamat') border-red-500 @enderror"
                              placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- RT, RW, Dusun -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="rt" class="block text-sm font-medium text-gray-700 mb-2">
                            RT
                        </label>
                        <input type="text"
                               id="rt"
                               name="rt"
                               value="{{ old('rt') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('rt') border-red-500 @enderror"
                               placeholder="Masukkan RT">
                        @error('rt')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="rw" class="block text-sm font-medium text-gray-700 mb-2">
                            RW
                        </label>
                        <input type="text"
                               id="rw"
                               name="rw"
                               value="{{ old('rw') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('rw') border-red-500 @enderror"
                               placeholder="Masukkan RW">
                        @error('rw')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="dusun" class="block text-sm font-medium text-gray-700 mb-2">
                            Dusun
                        </label>
                        <input type="text"
                               id="dusun"
                               name="dusun"
                               value="{{ old('dusun') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('dusun') border-red-500 @enderror"
                               placeholder="Masukkan dusun">
                        @error('dusun')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Kontak -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="no_telepon" class="block text-sm font-medium text-gray-700 mb-2">
                            No. Telepon
                        </label>
                        <input type="text"
                               id="no_telepon"
                               name="no_telepon"
                               value="{{ old('no_telepon') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('no_telepon') border-red-500 @enderror"
                               placeholder="Masukkan nomor telepon">
                        @error('no_telepon')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-2">
                            No. HP
                        </label>
                        <input type="text"
                               id="no_hp"
                               name="no_hp"
                               value="{{ old('no_hp') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('no_hp') border-red-500 @enderror"
                               placeholder="Masukkan nomor HP">
                        @error('no_hp')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Email dan Website -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror"
                               placeholder="Masukkan email">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                            Website
                        </label>
                        <input type="url"
                               id="website"
                               name="website"
                               value="{{ old('website') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('website') border-red-500 @enderror"
                               placeholder="Masukkan website">
                        @error('website')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Social Media -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="facebook" class="block text-sm font-medium text-gray-700 mb-2">
                            Facebook
                        </label>
                        <input type="url"
                               id="facebook"
                               name="facebook"
                               value="{{ old('facebook') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('facebook') border-red-500 @enderror"
                               placeholder="Masukkan link Facebook">
                        @error('facebook')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="instagram" class="block text-sm font-medium text-gray-700 mb-2">
                            Instagram
                        </label>
                        <input type="url"
                               id="instagram"
                               name="instagram"
                               value="{{ old('instagram') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('instagram') border-red-500 @enderror"
                               placeholder="Masukkan link Instagram">
                        @error('instagram')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="youtube" class="block text-sm font-medium text-gray-700 mb-2">
                            YouTube
                        </label>
                        <input type="url"
                               id="youtube"
                               name="youtube"
                               value="{{ old('youtube') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('youtube') border-red-500 @enderror"
                               placeholder="Masukkan link YouTube">
                        @error('youtube')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- WhatsApp dan Jam Operasional -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-2">
                            WhatsApp
                        </label>
                        <input type="text"
                               id="whatsapp"
                               name="whatsapp"
                               value="{{ old('whatsapp') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('whatsapp') border-red-500 @enderror"
                               placeholder="Masukkan nomor WhatsApp">
                        @error('whatsapp')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="jam_operasional" class="block text-sm font-medium text-gray-700 mb-2">
                            Jam Operasional
                        </label>
                        <input type="text"
                               id="jam_operasional"
                               name="jam_operasional"
                               value="{{ old('jam_operasional') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('jam_operasional') border-red-500 @enderror"
                               placeholder="Contoh: 08:00 - 16:00">
                        @error('jam_operasional')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Deskripsi dan Foto -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi
                        </label>
                        <textarea id="deskripsi"
                                  name="deskripsi"
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('deskripsi') border-red-500 @enderror"
                                  placeholder="Masukkan deskripsi">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">
                            Foto
                        </label>
                        <input type="file"
                               id="foto"
                               name="foto"
                               accept="image/*"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('foto') border-red-500 @enderror">
                        @error('foto')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Urutan Tampil -->
                <div>
                    <label for="urutan" class="block text-sm font-medium text-gray-700 mb-2">
                        Urutan Tampil
                    </label>
                    <input type="number"
                           id="urutan"
                           name="urutan"
                           value="{{ old('urutan') }}"
                           min="1"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('urutan') border-red-500 @enderror"
                           placeholder="Masukkan urutan tampil">
                    @error('urutan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
                    <a href="{{ route('kontak-desa.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    <button type="submit" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Data
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@noncescript
// SweetAlert untuk notifikasi success
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
