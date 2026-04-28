@extends('layouts.app')

@section('title', 'Detail Fasilitas Desa')
@section('subtitle', 'Detail data fasilitas dan infrastruktur desa')

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
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Detail Fasilitas Desa</h1>
                <p class="text-green-100 mt-1">Detail data fasilitas dan infrastruktur desa</p>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Fasilitas Info -->
            <div class="bg-white rounded-2xl shadow-lg border-0 p-6">
                <div class="flex items-start space-x-4">
                    @if($fasilitasDesa->foto)
                    <img src="{{ Storage::url($fasilitasDesa->foto) }}" alt="{{ $fasilitasDesa->nama }}"
                         class="w-20 h-20 object-cover rounded-xl border border-gray-200">
                    @else
                    <div class="w-20 h-20 bg-gray-100 rounded-xl flex items-center justify-center border border-gray-200">
                        <i class="fas fa-building text-gray-400 text-2xl"></i>
                    </div>
                    @endif
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $fasilitasDesa->nama }}</h2>
                        <div class="flex items-center space-x-4 mt-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-tag mr-2"></i>
                                {{ $fasilitasDesa->jenis_label }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $fasilitasDesa->status_aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas fa-{{ $fasilitasDesa->status_aktif ? 'check-circle' : 'times-circle' }} mr-2"></i>
                                {{ $fasilitasDesa->status_aktif ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alamat & Lokasi -->
            <div class="bg-white rounded-2xl shadow-lg border-0 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-green-600 mr-2"></i>
                    Alamat & Lokasi
                </h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Alamat Lengkap</label>
                        <p class="text-gray-900">{{ $fasilitasDesa->alamat }}</p>
                    </div>
                    @if($fasilitasDesa->rt_label || $fasilitasDesa->rw_label || $fasilitasDesa->dusun_label)
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @if($fasilitasDesa->rt_label)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">RT</label>
                            <p class="text-gray-900">{{ $fasilitasDesa->rt_label }}</p>
                        </div>
                        @endif
                        @if($fasilitasDesa->rw_label)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">RW</label>
                            <p class="text-gray-900">{{ $fasilitasDesa->rw_label }}</p>
                        </div>
                        @endif
                        @if($fasilitasDesa->dusun_label)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Dusun</label>
                            <p class="text-gray-900">{{ $fasilitasDesa->dusun_label }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                    @if($fasilitasDesa->latitude && $fasilitasDesa->longitude)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Latitude</label>
                            <p class="text-gray-900 font-mono">{{ $fasilitasDesa->latitude }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Longitude</label>
                            <p class="text-gray-900 font-mono">{{ $fasilitasDesa->longitude }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Kontak & Operasional -->
            @if($fasilitasDesa->kontak || $fasilitasDesa->jam_operasional)
            <div class="bg-white rounded-2xl shadow-lg border-0 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-phone text-green-600 mr-2"></i>
                    Kontak & Operasional
                </h3>
                <div class="space-y-3">
                    @if($fasilitasDesa->kontak)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Kontak</label>
                        <p class="text-gray-900 flex items-center">
                            <i class="fas fa-phone mr-2 text-green-600"></i>
                            {{ $fasilitasDesa->kontak }}
                        </p>
                    </div>
                    @endif
                    @if($fasilitasDesa->jam_operasional)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Jam Operasional</label>
                        <p class="text-gray-900 flex items-center">
                            <i class="fas fa-clock mr-2 text-green-600"></i>
                            {{ $fasilitasDesa->jam_operasional }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Deskripsi -->
            @if($fasilitasDesa->deskripsi)
            <div class="bg-white rounded-2xl shadow-lg border-0 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-green-600 mr-2"></i>
                    Deskripsi
                </h3>
                <p class="text-gray-700 leading-relaxed">{{ $fasilitasDesa->deskripsi }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="bg-white rounded-2xl shadow-lg border-0 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi</h3>
                <div class="space-y-3">
                    @can('fasilitas-desa.edit')
                    <a href="{{ route('fasilitas-desa.edit', $fasilitasDesa) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-medium rounded-xl hover:from-yellow-600 hover:to-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-200 shadow-lg">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Fasilitas
                    </a>
                    @endcan

                    <a href="{{ route('fasilitas-desa.index') }}"
                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-lg">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Daftar
                    </a>

                    @can('fasilitas-desa.delete')
                    <button onclick="confirmDelete({{ $fasilitasDesa->id }}, '{{ $fasilitasDesa->nama }}')"
                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-medium rounded-xl hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-lg">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus Fasilitas
                    </button>
                    @endcan
                </div>
            </div>

            <!-- Info Tambahan -->
            <div class="bg-white rounded-2xl shadow-lg border-0 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Dibuat</label>
                        <p class="text-gray-900">{{ $fasilitasDesa->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Diperbarui</label>
                        <p class="text-gray-900">{{ $fasilitasDesa->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
@can('fasilitas-desa.delete')
<form id="delete-form-{{ $fasilitasDesa->id }}" action="{{ route('fasilitas-desa.destroy', $fasilitasDesa) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endcan

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

// SweetAlert untuk konfirmasi delete
function confirmDelete(fasilitasId, fasilitasName) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus fasilitas "${fasilitasName}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form delete
            document.getElementById('delete-form-' + fasilitasId).submit();
        }
    });
}
@endnoncescript
@endsection

