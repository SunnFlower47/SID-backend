@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detail Berita</h1>
                    <p class="mt-2 text-sm text-gray-600">Lihat detail berita atau pengumuman</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('berita.edit', $berita) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
                    </a>
                    <a href="{{ route('berita.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Berita Detail -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Header Info -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            @if($berita->kategori == 'berita') bg-blue-100 text-blue-800
                            @elseif($berita->kategori == 'pengumuman') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ ucfirst($berita->kategori) }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            @if($berita->status == 'published') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($berita->status) }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ $berita->created_at->format('d M Y, H:i') }}
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $berita->judul }}</h2>

                @if($berita->gambar)
                    <div class="mb-6">
                        <img src="{{ Storage::url($berita->gambar) }}" alt="{{ $berita->judul }}" class="w-full h-64 object-cover rounded-lg">
                    </div>
                @endif

                <div class="prose max-w-none">
                    {!! nl2br(e($berita->konten)) !!}
                </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Terakhir diupdate: {{ $berita->updated_at->format('d M Y, H:i') }}
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('berita.edit', $berita) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-edit mr-2"></i>
                            Edit
                        </a>
                        <form id="delete-form-{{ $berita->id }}" action="{{ route('berita.destroy', $berita) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                    onclick="confirmDelete({{ $berita->id }}, '{{ $berita->judul }}')">
                                <i class="fas fa-trash mr-2"></i>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@noncescript
// SweetAlert untuk konfirmasi delete
function confirmDelete(beritaId, beritaTitle) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus berita "${beritaTitle}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form delete
            document.getElementById('delete-form-' + beritaId).submit();
        }
    });
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
