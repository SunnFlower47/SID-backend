@extends('layouts.app')

@section('title', 'Fasilitas Desa')
@section('subtitle', 'Kelola data fasilitas dan infrastruktur desa')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-4 lg:space-y-0">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-building text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Fasilitas Desa</h1>
                    <p class="text-green-100 mt-1">Kelola data fasilitas dan infrastruktur desa</p>
                </div>
            </div>
            @can('fasilitas-desa.create')
            <div class="flex space-x-3">
                <a href="{{ route('fasilitas-desa.create') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-xl transition-all duration-200 shadow-lg backdrop-blur-sm">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Fasilitas
                </a>
            </div>
            @endcan
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <i class="fas fa-building text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Fasilitas</p>
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
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100">
                    <i class="fas fa-hospital text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Sekolah</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['sekolah'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-orange-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100">
                    <i class="fas fa-mosque text-orange-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Masjid</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['masjid'] ?? 0) }}</p>
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
                       placeholder="Nama fasilitas, alamat..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label for="jenis" class="block text-sm font-medium text-gray-700 mb-2">Jenis</label>
                <select name="jenis" id="jenis" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Semua Jenis</option>
                    <option value="sekolah" {{ request('jenis') == 'sekolah' ? 'selected' : '' }}>Sekolah</option>
                    <option value="posyandu" {{ request('jenis') == 'posyandu' ? 'selected' : '' }}>Posyandu</option>
                    <option value="masjid" {{ request('jenis') == 'masjid' ? 'selected' : '' }}>Masjid</option>
                    <option value="gereja" {{ request('jenis') == 'gereja' ? 'selected' : '' }}>Gereja</option>
                    <option value="puskesmas" {{ request('jenis') == 'puskesmas' ? 'selected' : '' }}>Puskesmas</option>
                    <option value="pos_ronda" {{ request('jenis') == 'pos_ronda' ? 'selected' : '' }}>Pos Ronda</option>
                    <option value="balai_desa" {{ request('jenis') == 'balai_desa' ? 'selected' : '' }}>Balai Desa</option>
                    <option value="lapangan" {{ request('jenis') == 'lapangan' ? 'selected' : '' }}>Lapangan</option>
                    <option value="pasar" {{ request('jenis') == 'pasar' ? 'selected' : '' }}>Pasar</option>
                    <option value="lainnya" {{ request('jenis') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="tidak_aktif" {{ request('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
            <div class="flex items-end space-x-3">
                <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('fasilitas-desa.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-200">
                    <i class="fas fa-refresh"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-lg border-0">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Fasilitas Desa</h3>
        </div>

        @if(isset($fasilitas) && $fasilitas->count() > 0)
        <!-- Mobile Card View -->
        <div class="block lg:hidden p-4 space-y-4">
            @foreach($fasilitas as $item)
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <div class="flex items-start space-x-3">
                    @if($item->foto)
                    <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama }}"
                         class="w-12 h-12 rounded-lg object-cover">
                    @else
                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-gray-400"></i>
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $item->nama }}</h4>
                        <p class="text-xs text-gray-500 mt-1">{{ $item->jenis_label }}</p>
                        <p class="text-xs text-gray-600 mt-1">{{ Str::limit($item->alamat, 50) }}</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $item->status_aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $item->status_aktif ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                            <div class="flex space-x-2">
                                @can('fasilitas-desa.view')
                                <a href="{{ route('fasilitas-desa.show', $item) }}" class="text-blue-600 hover:text-blue-800 p-1" title="Lihat">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                @endcan
                                @can('fasilitas-desa.edit')
                                <a href="{{ route('fasilitas-desa.edit', $item) }}" class="text-yellow-600 hover:text-yellow-800 p-1" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                @endcan
                                @can('fasilitas-desa.delete')
                                <button type="button" class="text-red-600 hover:text-red-800 p-1" title="Hapus" onclick="confirmDelete({{ $item->id }}, '{{ $item->nama }}')">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Fasilitas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($fasilitas as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $loop->iteration + ($fasilitas->currentPage() - 1) * $fasilitas->perPage() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($item->foto)
                                <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama }}"
                                     class="w-10 h-10 rounded-lg object-cover mr-3">
                                @else
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-building text-gray-400"></i>
                                </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $item->nama }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($item->deskripsi, 30) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $item->jenis_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ Str::limit($item->alamat, 30) }}</div>
                            @if($item->rt_label && $item->rw_label)
                            <div class="text-sm text-gray-500">RT {{ $item->rt_label }}/RW {{ $item->rw_label }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($item->kontak)
                            <div class="flex items-center">
                                <i class="fas fa-phone mr-2"></i> {{ $item->kontak }}
                            </div>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item->status_aktif)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Tidak Aktif
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                @can('fasilitas-desa.view')
                                <a href="{{ route('fasilitas-desa.show', $item) }}" class="inline-flex items-center px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors text-sm font-medium" title="Lihat">
                                    <i class="fas fa-eye mr-1"></i> Lihat
                                </a>
                                @endcan
                                @can('fasilitas-desa.edit')
                                <a href="{{ route('fasilitas-desa.edit', $item) }}" class="inline-flex items-center px-3 py-2 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 rounded-lg transition-colors text-sm font-medium" title="Edit">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                @endcan
                                @can('fasilitas-desa.delete')
                                <form id="delete-form-{{ $item->id }}" action="{{ route('fasilitas-desa.destroy', $item) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="inline-flex items-center px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg transition-colors text-sm font-medium" title="Hapus" onclick="confirmDelete({{ $item->id }}, '{{ $item->nama }}')">
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
                <i class="fas fa-building text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data fasilitas desa</h3>
            <p class="text-gray-500 mb-6">Mulai tambah data fasilitas pertama</p>
            @can('fasilitas-desa.create')
            <a href="{{ route('fasilitas-desa.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white text-sm font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                <i class="fas fa-plus mr-2"></i>
                Tambah Fasilitas Pertama
            </a>
            @endcan
        </div>
        @endif

        <!-- Pagination -->
        @if(isset($fasilitas) && $fasilitas->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $fasilitas->links() }}
        </div>
        @endif
    </div>
</div>

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

