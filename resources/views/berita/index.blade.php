@extends('layouts.app')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-newspaper text-yellow-300 text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white">Berita & Pengumuman</h1>
                        <p class="text-green-100 mt-1">Kelola berita dan pengumuman desa</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    @can('berita.create')
                    <a href="{{ route('berita.create') }}" class="group flex items-center px-4 py-2.5 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Berita
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100">
                        <i class="fas fa-newspaper text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Berita</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $beritas->total() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Diterbitkan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $beritas->where('status', 'published')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Draft</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $beritas->where('status', 'draft')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100">
                        <i class="fas fa-tags text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Kategori</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $beritas->pluck('kategori')->unique()->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="bg-white rounded-2xl shadow-lg border-0 p-6">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100 -mx-6 -mt-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900">Filter & Pencarian</h2>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" placeholder="Cari berita..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                <div class="flex gap-2">
                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Semua Kategori</option>
                        <option value="berita">Berita</option>
                        <option value="pengumuman">Pengumuman</option>
                        <option value="agenda">Agenda</option>
                    </select>
                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Semua Status</option>
                        <option value="published">Diterbitkan</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Berita Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($beritas as $item)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $item->kategori === 'pengumuman' ? 'bg-red-100 text-red-800' : ($item->kategori === 'agenda' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                            {{ ucfirst($item->kategori) }}
                        </span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $item->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $item->status === 'published' ? 'Diterbitkan' : 'Draft' }}
                        </span>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 mb-3 line-clamp-2">{{ $item->judul }}</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ Str::limit(strip_tags($item->konten), 120) }}</p>

                    <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            <span>{{ $item->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-user mr-1"></i>
                            <span>{{ $item->author ?? 'Admin' }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex space-x-2">
                            @can('berita.edit')
                            <a href="{{ route('berita.edit', $item) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-md transition-colors">
                                <i class="fas fa-edit mr-1"></i>
                                Edit
                            </a>
                            @endcan
                            <a href="{{ route('berita.show', $item) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-md transition-colors">
                                <i class="fas fa-eye mr-1"></i>
                                Lihat
                            </a>
                        </div>
                        @can('berita.delete')
                        <form id="delete-form-{{ $item->id }}" action="{{ route('berita.destroy', $item) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition-colors" onclick="confirmDelete({{ $item->id }}, '{{ $item->judul }}')">
                                <i class="fas fa-trash mr-1"></i>
                                Hapus
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-newspaper text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada berita</h3>
                    <p class="text-gray-500 mb-6">Mulai buat berita pertama untuk desa Anda</p>
                    @can('berita.create')
                    <a href="{{ route('berita.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-700 text-white text-sm font-medium rounded-lg hover:from-green-700 hover:to-emerald-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Berita Pertama
                    </a>
                    @endcan
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($beritas->hasPages())
        <div class="mt-8">
            {{ $beritas->links() }}
        </div>
        @endif
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

// Session messages handled by global component
@endnoncescript
@endsection


