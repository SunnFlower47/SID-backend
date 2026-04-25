@extends('layouts.app')

@section('title', 'Data UMKM')
@section('subtitle', 'Kelola data Usaha Mikro, Kecil, dan Menengah')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-store text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Data UMKM</h1>
                    <p class="text-green-100 mt-1">Kelola data Usaha Mikro, Kecil, dan Menengah</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                @can('umkm.create')
                <a href="{{ route('umkm.create') }}" class="group flex items-center px-4 py-2.5 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah UMKM
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100">
                    <i class="fas fa-store text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total UMKM</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['aktif'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100">
                    <i class="fas fa-star text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Unggulan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['unggulan'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100">
                    <i class="fas fa-certificate text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Terverifikasi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['verified'] ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg border-0 p-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-filter text-green-600 mr-2"></i>
            <h3 class="text-lg font-semibold text-gray-900">Filter & Pencarian</h3>
        </div>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="Nama usaha, pemilik, alamat..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="tutup" {{ request('status') == 'tutup' ? 'selected' : '' }}>Tutup</option>
                    <option value="pindah" {{ request('status') == 'pindah' ? 'selected' : '' }}>Pindah</option>
                </select>
            </div>
            <div>
                <label for="jenis_usaha" class="block text-sm font-medium text-gray-700 mb-2">Jenis Usaha</label>
                <select name="jenis_usaha" id="jenis_usaha" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Semua Jenis</option>
                    <option value="makanan" {{ request('jenis_usaha') == 'makanan' ? 'selected' : '' }}>Makanan</option>
                    <option value="minuman" {{ request('jenis_usaha') == 'minuman' ? 'selected' : '' }}>Minuman</option>
                    <option value="kerajinan" {{ request('jenis_usaha') == 'kerajinan' ? 'selected' : '' }}>Kerajinan</option>
                    <option value="jasa" {{ request('jenis_usaha') == 'jasa' ? 'selected' : '' }}>Jasa</option>
                    <option value="perdagangan" {{ request('jenis_usaha') == 'perdagangan' ? 'selected' : '' }}>Perdagangan</option>
                    <option value="pertanian" {{ request('jenis_usaha') == 'pertanian' ? 'selected' : '' }}>Pertanian</option>
                    <option value="peternakan" {{ request('jenis_usaha') == 'peternakan' ? 'selected' : '' }}>Peternakan</option>
                    <option value="lainnya" {{ request('jenis_usaha') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <div>
                <label for="is_unggulan" class="block text-sm font-medium text-gray-700 mb-2">Unggulan</label>
                <select name="is_unggulan" id="is_unggulan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Semua</option>
                    <option value="1" {{ request('is_unggulan') == '1' ? 'selected' : '' }}>Ya</option>
                    <option value="0" {{ request('is_unggulan') == '0' ? 'selected' : '' }}>Tidak</option>
                </select>
            </div>
            <div class="flex items-end space-x-3">
                <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('umkm.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-200">
                    <i class="fas fa-refresh"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-lg border-0">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Daftar UMKM</h3>
        </div>

        @if($umkms->count() > 0)
        <!-- Mobile Card View -->
        <div class="block lg:hidden p-4 space-y-4">
            @foreach($umkms as $umkm)
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <div class="flex items-start space-x-3">
                    @if($umkm->foto_usaha && is_array($umkm->foto_usaha) && count($umkm->foto_usaha) > 0)
                    <img src="{{ Storage::url($umkm->foto_usaha[0]) }}" alt="{{ $umkm->nama_usaha }}"
                         class="w-12 h-12 rounded-lg object-cover">
                    @else
                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-store text-gray-400"></i>
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $umkm->nama_usaha }}</h4>
                        <p class="text-xs text-gray-500 mt-1">{{ $umkm->nama_pemilik }}</p>
                        <p class="text-xs text-gray-600 mt-1">{{ Str::limit($umkm->alamat_usaha, 50) }}</p>
                        <div class="flex items-center justify-between mt-2">
                            <div class="flex space-x-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ ucfirst($umkm->jenis_usaha) }}
                                </span>
                                @if($umkm->status_usaha == 'aktif')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Aktif
                                </span>
                                @elseif($umkm->status_usaha == 'tutup')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Tutup
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pindah
                                </span>
                                @endif
                                @if($umkm->is_unggulan)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-star text-xs mr-1"></i> Unggulan
                                </span>
                                @endif
                            </div>
                            <div class="flex space-x-2">
                                @can('umkm.view')
                                <a href="{{ route('umkm.show', $umkm) }}" class="text-blue-600 hover:text-blue-800 p-1" title="Lihat">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                @endcan
                                @can('umkm.edit')
                                <a href="{{ route('umkm.edit', $umkm) }}" class="text-yellow-600 hover:text-yellow-800 p-1" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                @endcan
                                @can('umkm.delete')
                                <button type="button" class="text-red-600 hover:text-red-800 p-1" title="Hapus" onclick="confirmDelete({{ $umkm->id }}, '{{ $umkm->nama_usaha }}')">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Usaha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemilik</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Usaha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unggulan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($umkms as $umkm)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $loop->iteration + ($umkms->currentPage() - 1) * $umkms->perPage() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($umkm->foto_usaha && is_array($umkm->foto_usaha) && count($umkm->foto_usaha) > 0)
                                <img src="{{ Storage::url($umkm->foto_usaha[0]) }}" alt="{{ $umkm->nama_usaha }}"
                                     class="w-10 h-10 rounded-lg object-cover mr-3">
                                @else
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-store text-gray-400"></i>
                                </div>
                                @endif
<div>
                                    <div class="text-sm font-medium text-gray-900">{{ $umkm->nama_usaha }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($umkm->alamat_usaha, 30) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $umkm->nama_pemilik }}</div>
                            @if($umkm->no_telepon)
                            <div class="text-sm text-gray-500">
                                <i class="fas fa-phone mr-1"></i> {{ $umkm->no_telepon }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ ucfirst($umkm->jenis_usaha) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($umkm->status_usaha == 'aktif')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Aktif
                            </span>
                            @elseif($umkm->status_usaha == 'tutup')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Tutup
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Pindah
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($umkm->is_unggulan)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-star mr-1"></i> Unggulan
                            </span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                @can('umkm.view')
                                <a href="{{ route('umkm.show', $umkm) }}" class="inline-flex items-center px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors text-sm font-medium" title="Lihat">
                                    <i class="fas fa-eye mr-1"></i> Lihat
                                </a>
                                @endcan
                                @can('umkm.edit')
                                <a href="{{ route('umkm.edit', $umkm) }}" class="inline-flex items-center px-3 py-2 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 rounded-lg transition-colors text-sm font-medium" title="Edit">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                @endcan
                                @can('umkm.delete')
                                <form id="delete-form-{{ $umkm->id }}" action="{{ route('umkm.destroy', $umkm) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="inline-flex items-center px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg transition-colors text-sm font-medium" title="Hapus" onclick="confirmDelete({{ $umkm->id }}, '{{ $umkm->nama_usaha }}')">
                                        <i class="fas fa-trash mr-1"></i> Hapus
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-store text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data UMKM</h3>
            <p class="text-gray-500 mb-6">Mulai tambah data UMKM pertama</p>
            @can('umkm.create')
            <a href="{{ route('umkm.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white text-sm font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                <i class="fas fa-plus mr-2"></i>
                Tambah UMKM Pertama
            </a>
            @endcan
        </div>
        @endif

        <!-- Pagination -->
        @if($umkms->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $umkms->links() }}
        </div>
        @endif
    </div>
</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@noncescript
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

// Session messages handled by global component
@endnoncescript
@endsection

