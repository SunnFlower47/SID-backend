@extends('layouts.app')

@section('title', 'Detail Pengaduan')
@section('subtitle', 'Lihat detail pengaduan warga')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Detail Pengaduan</h1>
                    <p class="text-red-100 text-sm sm:text-base">Informasi lengkap pengaduan dari warga</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                @can('pengaduan.edit')
                <a href="{{ route('pengaduan.edit', $pengaduan) }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                @endcan
                <a href="{{ route('pengaduan.index') }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Pengaduan Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $pengaduan->judul }}</h2>
                        <div class="flex items-center space-x-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($pengaduan->status === 'baru') bg-yellow-100 text-yellow-800
                                @elseif($pengaduan->status === 'diproses') bg-blue-100 text-blue-800
                                @elseif($pengaduan->status === 'selesai') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($pengaduan->status) }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($pengaduan->prioritas === 'rendah') bg-green-100 text-green-800
                                @elseif($pengaduan->prioritas === 'sedang') bg-yellow-100 text-yellow-800
                                @elseif($pengaduan->prioritas === 'tinggi') bg-orange-100 text-orange-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($pengaduan->prioritas) }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                {{ ucfirst($pengaduan->kategori) }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Dibuat</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $pengaduan->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Deskripsi Pengaduan</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $pengaduan->deskripsi }}</p>
                    </div>
                </div>

                <!-- Lokasi -->
                @if($pengaduan->lokasi)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Lokasi</h3>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-map-marker-alt text-green-600"></i>
                        <span class="text-gray-700">{{ $pengaduan->lokasi }}</span>
                    </div>
                </div>
                @endif

                <!-- Foto -->
                @if($pengaduan->foto && count($pengaduan->foto) > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Foto Pendukung</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($pengaduan->foto as $foto)
                        <div class="relative group">
                            <img src="{{ Storage::url($foto) }}"
                                 alt="Foto pengaduan"
                                 class="w-full h-32 object-cover rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
                                 onclick="openImageModal('{{ Storage::url($foto) }}')">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition-opacity"></i>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Tanggapan -->
                @if($pengaduan->tanggapan)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Tanggapan Admin</h3>
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $pengaduan->tanggapan }}</p>
                        @if($pengaduan->tanggal_tanggapan)
                        <p class="text-sm text-gray-500 mt-2">
                            <i class="fas fa-clock mr-1"></i>
                            Ditanggapi pada {{ $pengaduan->tanggal_tanggapan->format('d M Y, H:i') }}
                        </p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Info Pelapor -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pelapor</h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nama</label>
                        <p class="text-gray-900">{{ $pengaduan->nama_pelapor }}</p>
                    </div>
                    @if($pengaduan->nik_pelapor)
                    <div>
                        <label class="text-sm font-medium text-gray-500">NIK</label>
                        <p class="text-gray-900 font-mono">{{ $pengaduan->nik_pelapor }}</p>
                    </div>
                    @endif
                    @if($pengaduan->telepon)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Telepon</label>
                        <p class="text-gray-900">{{ $pengaduan->telepon }}</p>
                    </div>
                    @endif
                    @if($pengaduan->email)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <p class="text-gray-900">{{ $pengaduan->email }}</p>
                    </div>
                    @endif
                    <div>
                        <label class="text-sm font-medium text-gray-500">Alamat</label>
                        <p class="text-gray-900">{{ $pengaduan->alamat }}</p>
                    </div>
                </div>
            </div>

            <!-- Admin yang Menangani -->
            @if($pengaduan->user)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Admin Penanggung Jawab</h3>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-green-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ $pengaduan->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $pengaduan->user->email }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Pengaduan dibuat</p>
                            <p class="text-xs text-gray-500">{{ $pengaduan->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    @if($pengaduan->status !== 'baru')
                    <div class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Status diubah menjadi {{ ucfirst($pengaduan->status) }}</p>
                            <p class="text-xs text-gray-500">{{ $pengaduan->updated_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    @endif
                    @if($pengaduan->tanggal_tanggapan)
                    <div class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-purple-500 rounded-full mt-2"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Ditanggapi admin</p>
                            <p class="text-xs text-gray-500">{{ $pengaduan->tanggal_tanggapan->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <button onclick="closeImageModal()" class="absolute -top-10 right-0 text-white hover:text-gray-300">
            <i class="fas fa-times text-2xl"></i>
        </button>
        <img id="modalImage" src="" alt="Foto pengaduan" class="max-w-full max-h-full rounded-lg">
    </div>
</div>

<script>
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});
</script>

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

// SweetAlert untuk konfirmasi delete
function confirmDelete(id, title) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus pengaduan "${title}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form delete
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/pengaduan/${id}`;

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';

            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';

            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection
