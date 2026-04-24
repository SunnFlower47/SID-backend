@extends('layouts.app')

@section('title', 'Detail Transparansi Desa')
@section('subtitle', 'Detail informasi transparansi desa')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 via-purple-700 to-purple-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Detail Transparansi Desa</h1>
                    <p class="text-purple-100 mt-1">{{ $transparansiDesa->judul }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('transparansi-desa.edit', $transparansiDesa->id) }}"
                   class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Data
                </a>
                <a href="{{ route('transparansi-desa.index') }}"
                   class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Detail Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
                <div class="px-6 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
                    <h2 class="text-xl font-semibold text-gray-900">Informasi Dasar</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Jenis Data</label>
                            <p class="text-lg font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $transparansiDesa->jenis_data) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tahun</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $transparansiDesa->tahun }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Judul</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $transparansiDesa->judul }}</p>
                    </div>

                    @if($transparansiDesa->deskripsi)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Deskripsi</label>
                        <p class="text-gray-700 leading-relaxed">{{ $transparansiDesa->deskripsi }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- File Information -->
            @if($transparansiDesa->file_dokumen)
            <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
                <div class="px-6 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
                    <h2 class="text-xl font-semibold text-gray-900">File Dokumen</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file text-purple-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ basename($transparansiDesa->file_dokumen) }}</p>
                                <p class="text-sm text-gray-500">File dokumen</p>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $transparansiDesa->file_dokumen) }}" target="_blank"
                           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white rounded-lg font-medium shadow-lg hover:shadow-xl transition-all duration-200">
                            <i class="fas fa-download mr-2"></i>
                            Download
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Information -->
            <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
                <div class="px-6 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
                    <h2 class="text-xl font-semibold text-gray-900">Status</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($transparansiDesa->status == 'aktif') bg-green-100 text-green-800
                            @elseif($transparansiDesa->status == 'tidak_aktif') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            @if($transparansiDesa->status == 'aktif')
                                <i class="fas fa-check-circle mr-1"></i> Aktif
                            @elseif($transparansiDesa->status == 'tidak_aktif')
                                <i class="fas fa-times-circle mr-1"></i> Tidak Aktif
                            @else
                                <i class="fas fa-edit mr-1"></i> Draft
                            @endif
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Urutan Tampil</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $transparansiDesa->urutan }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
                <div class="px-6 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
                    <h2 class="text-xl font-semibold text-gray-900">Aksi</h2>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('transparansi-desa.edit', $transparansiDesa->id) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Data
                    </a>

                    <a href="{{ route('transparansi-desa.index') }}"
                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-all duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Daftar
                    </a>

                    <button onclick="confirmDelete()"
                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all duration-200">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus Data
                    </button>
                </div>
            </div>

            <!-- Technical Information -->
            <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
                <div class="px-6 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
                    <h2 class="text-xl font-semibold text-gray-900">Informasi Teknis</h2>
                </div>
                <div class="p-6 space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">ID</label>
                        <p class="text-sm font-mono text-gray-600">{{ $transparansiDesa->id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Dibuat</label>
                        <p class="text-sm text-gray-600">{{ $transparansiDesa->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Diupdate</label>
                        <p class="text-sm text-gray-600">{{ $transparansiDesa->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="delete-form" action="{{ route('transparansi-desa.destroy', $transparansiDesa->id) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

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

// Konfirmasi hapus
function confirmDelete() {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data transparansi desa akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form').submit();
        }
    });
}
</script>
@endsection
