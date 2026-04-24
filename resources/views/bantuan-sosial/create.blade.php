@extends('layouts.app')

@section('title', 'Bantuan Sosial')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-plus text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Tambah Program Bantuan Sosial</h1>
                    <p class="text-green-100 text-sm sm:text-base">Buat program bantuan sosial baru</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('bantuan-sosial.index') }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
        <div class="px-6 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-edit text-green-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Form Data Program</h3>
                    <p class="text-sm text-gray-600">Lengkapi data program bantuan sosial dengan benar</p>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 border-b border-gray-200">
            <h6 class="text-lg font-semibold text-gray-900">Informasi Program</h6>
        </div>
        <div class="p-6">
            <form action="{{ route('bantuan-sosial.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nama_program" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Program <span class="text-red-500">*</span>
                        </label>
                        <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 @error('nama_program') border-red-500 @enderror"
                               id="nama_program" name="nama_program" value="{{ old('nama_program') }}"
                               placeholder="Contoh: Bantuan Langsung Tunai 2024" required>
                        @error('nama_program')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="jenis_bantuan" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Bantuan <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 @error('jenis_bantuan') border-red-500 @enderror"
                                id="jenis_bantuan" name="jenis_bantuan" required>
                            <option value="">Pilih Jenis Bantuan</option>
                            <option value="BLT" {{ old('jenis_bantuan') == 'BLT' ? 'selected' : '' }}>BLT (Bantuan Langsung Tunai)</option>
                            <option value="PKH" {{ old('jenis_bantuan') == 'PKH' ? 'selected' : '' }}>PKH (Program Keluarga Harapan)</option>
                            <option value="BPNT" {{ old('jenis_bantuan') == 'BPNT' ? 'selected' : '' }}>BPNT (Bantuan Pangan Non Tunai)</option>
                            <option value="Bansos Lainnya" {{ old('jenis_bantuan') == 'Bansos Lainnya' ? 'selected' : '' }}>Bansos Lainnya</option>
                        </select>
                        @error('jenis_bantuan')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi Program <span class="text-red-500">*</span>
                    </label>
                    <textarea class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 @error('deskripsi') border-red-500 @enderror"
                              id="deskripsi" name="deskripsi" rows="4" placeholder="Deskripsikan program bantuan sosial..." required>{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="nilai_bantuan" class="block text-sm font-medium text-gray-700 mb-2">
                            Nilai Bantuan (Rp)
                        </label>
                        <input type="number" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 @error('nilai_bantuan') border-red-500 @enderror"
                               id="nilai_bantuan" name="nilai_bantuan" value="{{ old('nilai_bantuan') }}"
                               placeholder="0" step="0.01">
                        @error('nilai_bantuan')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="periode" class="block text-sm font-medium text-gray-700 mb-2">
                            Periode <span class="text-red-500">*</span>
                        </label>
                        <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 @error('periode') border-red-500 @enderror"
                               id="periode" name="periode" value="{{ old('periode') }}"
                               placeholder="Contoh: 2024, 2024-2025" required>
                        @error('periode')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 @error('tanggal_mulai') border-red-500 @enderror"
                               id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required>
                        @error('tanggal_mulai')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 @error('tanggal_selesai') border-red-500 @enderror"
                               id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required>
                        @error('tanggal_selesai')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 @error('status') border-red-500 @enderror"
                                id="status" name="status" required>
                            <option value="">Pilih Status</option>
                            <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="ditangguhkan" {{ old('status') == 'ditangguhkan' ? 'selected' : '' }}>Ditangguhkan</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="kuota_penerima" class="block text-sm font-medium text-gray-700 mb-2">
                            Kuota Penerima
                        </label>
                        <input type="number" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 @error('kuota_penerima') border-red-500 @enderror"
                               id="kuota_penerima" name="kuota_penerima" value="{{ old('kuota_penerima') }}"
                               placeholder="0">
                        @error('kuota_penerima')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="sumber_dana" class="block text-sm font-medium text-gray-700 mb-2">
                        Sumber Dana <span class="text-red-500">*</span>
                    </label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 @error('sumber_dana') border-red-500 @enderror"
                            id="sumber_dana" name="sumber_dana" required>
                        <option value="">Pilih Sumber Dana</option>
                        <option value="APBN" {{ old('sumber_dana') == 'APBN' ? 'selected' : '' }}>APBN</option>
                        <option value="APBD" {{ old('sumber_dana') == 'APBD' ? 'selected' : '' }}>APBD</option>
                        <option value="Swasta" {{ old('sumber_dana') == 'Swasta' ? 'selected' : '' }}>Swasta</option>
                        <option value="Lainnya" {{ old('sumber_dana') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('sumber_dana')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6">
                    <label for="kriteria_penerima" class="block text-sm font-medium text-gray-700 mb-2">
                        Kriteria Penerima <span class="text-red-500">*</span>
                    </label>
                    <textarea class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 @error('kriteria_penerima') border-red-500 @enderror"
                              id="kriteria_penerima" name="kriteria_penerima" rows="3" placeholder="Deskripsikan kriteria penerima bantuan..." required>{{ old('kriteria_penerima') }}</textarea>
                    @error('kriteria_penerima')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-4 mt-8">
                    <a href="{{ route('bantuan-sosial.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300">
                        Batal
                    </a>
                    <button type="submit" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300">
                        <i class="fas fa-save mr-2"></i> Simpan Program
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
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
</script>
@endsection
