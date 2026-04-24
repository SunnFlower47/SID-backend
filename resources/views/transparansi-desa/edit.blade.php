@extends('layouts.app')

@section('title', 'Edit Data Transparansi Desa')
@section('subtitle', 'Edit informasi transparansi desa')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 via-purple-700 to-purple-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="fas fa-chart-line text-yellow-300 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Edit Data Transparansi Desa</h1>
                <p class="text-purple-100 mt-1">Edit informasi transparansi desa</p>
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
        <!-- Form Header -->
        <div class="px-6 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
            <h2 class="text-xl font-semibold text-gray-900">Informasi Transparansi Desa</h2>
            <p class="text-sm text-gray-600 mt-1">Edit form di bawah ini untuk mengubah data transparansi desa</p>
        </div>

        <!-- Form Content -->
        <form action="{{ route('transparansi-desa.update', $transparansiDesa->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Jenis Data -->
            <div>
                <label for="jenis_data" class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis Data <span class="text-red-500">*</span>
                </label>
                <select id="jenis_data" name="jenis_data" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('jenis_data') border-red-500 @enderror">
                    <option value="">Pilih Jenis Data</option>
                    <option value="apbdes" {{ old('jenis_data', $transparansiDesa->jenis_data) == 'apbdes' ? 'selected' : '' }}>APBDes</option>
                    <option value="proyek" {{ old('jenis_data', $transparansiDesa->jenis_data) == 'proyek' ? 'selected' : '' }}>Proyek Desa</option>
                    <option value="laporan_keuangan" {{ old('jenis_data', $transparansiDesa->jenis_data) == 'laporan_keuangan' ? 'selected' : '' }}>Laporan Keuangan</option>
                    <option value="dokumen_anggaran" {{ old('jenis_data', $transparansiDesa->jenis_data) == 'dokumen_anggaran' ? 'selected' : '' }}>Dokumen Anggaran</option>
                    <option value="lainnya" {{ old('jenis_data', $transparansiDesa->jenis_data) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('jenis_data')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Judul dan Tahun -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="judul" name="judul" value="{{ old('judul', $transparansiDesa->judul) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('judul') border-red-500 @enderror"
                           placeholder="Masukkan judul data transparansi">
                    @error('judul')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">
                        Tahun <span class="text-red-500">*</span>
                    </label>
                    <select id="tahun" name="tahun" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('tahun') border-red-500 @enderror">
                        <option value="">Pilih Tahun</option>
                        @for($i = date('Y'); $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ old('tahun', $transparansiDesa->tahun) == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                    @error('tahun')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea id="deskripsi" name="deskripsi" rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('deskripsi') border-red-500 @enderror"
                          placeholder="Masukkan deskripsi data transparansi">{{ old('deskripsi', $transparansiDesa->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- File Upload -->
            <div>
                <label for="file_dokumen" class="block text-sm font-medium text-gray-700 mb-2">
                    File Dokumen
                </label>

                @if($transparansiDesa->file_dokumen)
                <!-- Current File -->
                <div class="mb-4 p-4 bg-gray-50 rounded-lg border">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-file text-purple-600"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-900">File saat ini:</p>
                                <p class="text-sm text-gray-600">{{ basename($transparansiDesa->file_dokumen) }}</p>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $transparansiDesa->file_dokumen) }}" target="_blank"
                           class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                            <i class="fas fa-download mr-1"></i> Download
                        </a>
                    </div>
                </div>
                @endif

                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-purple-400 transition-colors">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="file_dokumen" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                                <span>Upload file baru</span>
                                <input id="file_dokumen" name="file_dokumen" type="file" class="sr-only" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                            </label>
                            <p class="pl-1">atau drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PDF, DOC, XLS, atau gambar (MAX. 10MB)</p>
                        <p class="text-xs text-gray-400">Kosongkan jika tidak ingin mengubah file</p>
                    </div>
                </div>
                @error('file_dokumen')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status dan Urutan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status
                    </label>
                    <select id="status" name="status"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="aktif" {{ old('status', $transparansiDesa->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tidak_aktif" {{ old('status', $transparansiDesa->status) == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="draft" {{ old('status', $transparansiDesa->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="urutan" class="block text-sm font-medium text-gray-700 mb-2">
                        Urutan Tampil
                    </label>
                    <input type="number" id="urutan" name="urutan" value="{{ old('urutan', $transparansiDesa->urutan) }}" min="1"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('urutan') border-red-500 @enderror"
                           placeholder="Urutan tampil">
                    @error('urutan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <button type="submit"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                    <i class="fas fa-save mr-2"></i>
                    Update Data
                </button>
                <a href="{{ route('transparansi-desa.index') }}"
                   class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// SweetAlert2 untuk notifikasi
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        timer: 3000,
        showConfirmButton: false
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session('error') }}',
        confirmButtonText: 'OK'
    });
@endif

// File upload preview
document.getElementById('file_dokumen').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const fileSize = file.size / 1024 / 1024; // MB
        if (fileSize > 10) {
            Swal.fire({
                icon: 'warning',
                title: 'File Terlalu Besar!',
                text: 'Ukuran file maksimal 10MB',
                confirmButtonText: 'OK'
            });
            e.target.value = '';
        }
    }
});
</script>
@endsection
