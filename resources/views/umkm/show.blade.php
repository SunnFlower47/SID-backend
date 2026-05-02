@extends('layouts.app')

@section('title', 'Detail Data UMKM')
@section('subtitle', 'Detail data Usaha Mikro, Kecil, dan Menengah')

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
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Detail Data UMKM</h1>
                <p class="text-green-100 mt-1">Detail data Usaha Mikro, Kecil, dan Menengah</p>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- UMKM Info -->
            <div class="bg-white rounded-2xl shadow-lg border-0 p-6">
                <div class="flex items-start space-x-4">
                    @if($umkm->foto_usaha && is_array($umkm->foto_usaha) && count($umkm->foto_usaha) > 0)
                    <img src="{{ Storage::url($umkm->foto_usaha[0]) }}" alt="{{ $umkm->nama_usaha }}"
                         class="w-20 h-20 object-cover rounded-xl border border-gray-200">
                    @else
                    <div class="w-20 h-20 bg-gray-100 rounded-xl flex items-center justify-center border border-gray-200">
                        <i class="fas fa-store text-gray-400 text-2xl"></i>
                    </div>
                    @endif
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $umkm->nama_usaha }}</h2>
                        <div class="flex items-center space-x-4 mt-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-tag mr-2"></i>
                                {{ ucfirst($umkm->jenis_usaha) }}
                            </span>
                            @if($umkm->status_usaha == 'aktif')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-2"></i>
                                Aktif
                            </span>
                            @elseif($umkm->status_usaha == 'tutup')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-2"></i>
                                Tutup
                            </span>
                            @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-exchange-alt mr-2"></i>
                                Pindah
                            </span>
                            @endif
                            @if($umkm->is_unggulan)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-star mr-2"></i>
                                Unggulan
                            </span>
                            @endif
                            @if($umkm->is_verified)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-certificate mr-2"></i>
                                Terverifikasi
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pemilik Info -->
            <div class="bg-white rounded-2xl shadow-lg border-0 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-user text-green-600 mr-2"></i>
                    Informasi Pemilik
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Nama Pemilik</label>
                        <p class="text-gray-900 font-medium">{{ $umkm->nama_pemilik }}</p>
                    </div>
                    @if($umkm->nik_pemilik)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">NIK Pemilik</label>
                        <p class="text-gray-900 font-mono">{{ $umkm->nik_pemilik }}</p>
                    </div>
                    @endif
                    @if($umkm->no_telepon)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">No Telepon</label>
                        <p class="text-gray-900 flex items-center">
                            <i class="fas fa-phone mr-2 text-green-600"></i>
                            {{ $umkm->no_telepon }}
                        </p>
                    </div>
                    @endif
                    @if($umkm->email)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Email</label>
                        <p class="text-gray-900 flex items-center">
                            <i class="fas fa-envelope mr-2 text-green-600"></i>
                            {{ $umkm->email }}
                        </p>
                    </div>
                    @endif
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
                        <label class="block text-sm font-medium text-gray-500">Alamat Usaha</label>
                        <p class="text-gray-900">{{ $umkm->alamat_usaha }}</p>
                    </div>
                    @if($umkm->rt_label || $umkm->rw_label || $umkm->dusun_label)
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @if($umkm->rt_label)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">RT</label>
                            <p class="text-gray-900">{{ $umkm->rt_label }}</p>
                        </div>
                        @endif
                        @if($umkm->rw_label)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">RW</label>
                            <p class="text-gray-900">{{ $umkm->rw_label }}</p>
                        </div>
                        @endif
                        @if($umkm->dusun_label)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Dusun</label>
                            <p class="text-gray-900">{{ $umkm->dusun_label }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                    @if($umkm->latitude && $umkm->longitude)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Latitude</label>
                            <p class="text-gray-900 font-mono">{{ $umkm->latitude }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Longitude</label>
                            <p class="text-gray-900 font-mono">{{ $umkm->longitude }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Detail Usaha -->
            <div class="bg-white rounded-2xl shadow-lg border-0 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-chart-line text-green-600 mr-2"></i>
                    Detail Usaha
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @if($umkm->tanggal_berdiri)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Tanggal Berdiri</label>
                        <p class="text-gray-900">{{ \Carbon\Carbon::parse($umkm->tanggal_berdiri)->format('d M Y') }}</p>
                    </div>
                    @endif
                    @if($umkm->jumlah_karyawan)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Jumlah Karyawan</label>
                        <p class="text-gray-900">{{ $umkm->jumlah_karyawan }} orang</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Produk Unggulan -->
            @if($umkm->produk_unggulan && is_array($umkm->produk_unggulan) && count($umkm->produk_unggulan) > 0)
            <div class="bg-white rounded-2xl shadow-lg border-0 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-star text-green-600 mr-2"></i>
                    Produk Unggulan
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($umkm->produk_unggulan as $produk)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-star mr-1"></i>
                        {{ $produk }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Deskripsi Usaha -->
            @if($umkm->deskripsi_usaha)
            <div class="bg-white rounded-2xl shadow-lg border-0 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-green-600 mr-2"></i>
                    Deskripsi Usaha
                </h3>
                <p class="text-gray-700 leading-relaxed">{{ $umkm->deskripsi_usaha }}</p>
            </div>
            @endif

            <!-- Foto Usaha -->
            @if($umkm->foto_usaha && is_array($umkm->foto_usaha) && count($umkm->foto_usaha) > 0)
            <div class="bg-white rounded-2xl shadow-lg border-0 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-images text-green-600 mr-2"></i>
                    Foto Usaha
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($umkm->foto_usaha as $foto)
                    <div class="relative group">
                        <img src="{{ Storage::url($foto) }}" alt="Foto usaha"
                             class="w-full h-32 object-cover rounded-lg border border-gray-200 hover:shadow-lg transition-shadow cursor-pointer"
                             onclick="openImageModal('{{ Storage::url($foto) }}')">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 rounded-lg transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="bg-white rounded-2xl shadow-lg border-0 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi</h3>
                <div class="space-y-3">
                    @can('pelayanan_informasi')
                    <a href="{{ route('umkm.edit', $umkm) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-medium rounded-xl hover:from-yellow-600 hover:to-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-200 shadow-lg">
                        <i class="fas fa-edit mr-2"></i>
                        Edit UMKM
                    </a>
                    @endcan

                    <a href="{{ route('umkm.index') }}"
                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-lg">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Daftar
                    </a>

                    @can('pelayanan_informasi')
                    <button onclick="confirmDelete({{ $umkm->id }}, '{{ $umkm->nama_usaha }}')"
                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-medium rounded-xl hover:from-red-600 hover:to-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-lg">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus UMKM
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
                        <p class="text-gray-900">{{ $umkm->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Diperbarui</label>
                        <p class="text-gray-900">{{ $umkm->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
@can('pelayanan_informasi')
<form id="delete-form-{{ $umkm->id }}" action="{{ route('umkm.destroy', $umkm) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endcan

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <img id="modalImage" src="" alt="Foto usaha" class="max-w-full max-h-full rounded-lg">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@noncescript
// Open image modal
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
}

// Close image modal
function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

// Close modal on background click
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

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
function confirmDelete(umkmId, umkmName) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus UMKM "${umkmName}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form delete
            document.getElementById('delete-form-' + umkmId).submit();
        }
    });
}
@endnoncescript
@endsection

