@extends('layouts.app')

@section('title', 'Kontak Desa')
@section('subtitle', 'Kelola data kontak dan informasi komunikasi desa')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-phone text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Kontak Desa</h1>
                    <p class="text-green-100 mt-1">Kelola data kontak dan informasi komunikasi desa</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                @can('kontak-desa.create')
                <a href="{{ route('kontak-desa.create') }}" class="group flex items-center px-4 py-2.5 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Kontak
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <i class="fas fa-address-book text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Kontak</p>
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
                    <i class="fas fa-building text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Kantor Desa</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['kantor_desa'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-orange-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100">
                    <i class="fas fa-phone text-orange-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Kontak Utama</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['kontak_utama'] ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg border-0 p-6">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Filter & Pencarian</h3>
        </div>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="Nama, jabatan, alamat..."
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <div>
                <label for="jenis" class="block text-sm font-medium text-gray-700 mb-2">Jenis</label>
                <select name="jenis" id="jenis" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">Semua Jenis</option>
                    <option value="kantor_desa" {{ request('jenis') == 'kantor_desa' ? 'selected' : '' }}>Kantor Desa</option>
                    <option value="kepala_desa" {{ request('jenis') == 'kepala_desa' ? 'selected' : '' }}>Kepala Desa</option>
                    <option value="sekretaris" {{ request('jenis') == 'sekretaris' ? 'selected' : '' }}>Sekretaris</option>
                    <option value="puskesmas" {{ request('jenis') == 'puskesmas' ? 'selected' : '' }}>Puskesmas</option>
                    <option value="sekolah" {{ request('jenis') == 'sekolah' ? 'selected' : '' }}>Sekolah</option>
                    <option value="lainnya" {{ request('jenis') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="tidak_aktif" {{ request('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
            <div class="md:col-span-2 lg:col-span-4 flex flex-col sm:flex-row gap-3">
                <button type="submit" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('kontak-desa.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                    <i class="fas fa-refresh mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-lg border-0">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Kontak Desa</h3>
        </div>
        <div class="overflow-x-auto">
            @if(isset($kontaks) && $kontaks->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($kontaks as $kontak)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $loop->iteration + ($kontaks->currentPage() - 1) * $kontaks->perPage() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($kontak->foto)
                                <img src="{{ Storage::url($kontak->foto) }}" alt="{{ $kontak->nama }}"
                                     class="w-10 h-10 rounded-full object-cover mr-3">
                                @else
                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $kontak->nama }}</div>
                                    <div class="text-sm text-gray-500">{{ $kontak->alamat }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $kontak->jabatan }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst(str_replace('_', ' ', $kontak->jenis)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($kontak->no_telepon)
                            <div class="flex items-center">
                                <i class="fas fa-phone mr-2"></i> {{ $kontak->no_telepon }}
                            </div>
                            @endif
                            @if($kontak->email)
                            <div class="flex items-center">
                                <i class="fas fa-envelope mr-2"></i> {{ $kontak->email }}
                            </div>
                            @endif
                            @if(!$kontak->no_telepon && !$kontak->email)
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($kontak->status_aktif)
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
                                @can('kontak-desa.view')
                                <a href="{{ route('kontak-desa.show', $kontak) }}" class="text-blue-600 hover:text-blue-900 p-1 rounded" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                @can('kontak-desa.edit')
                                <a href="{{ route('kontak-desa.edit', $kontak) }}" class="text-yellow-600 hover:text-yellow-900 p-1 rounded" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('kontak-desa.delete')
                                <form id="delete-form-{{ $kontak->id }}" action="{{ route('kontak-desa.destroy', $kontak) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="text-red-600 hover:text-red-900 p-1 rounded" title="Hapus" onclick="confirmDelete({{ $kontak->id }}, '{{ $kontak->nama }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-address-book text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data kontak desa</h3>
                <p class="text-gray-500 mb-6">Mulai tambah data kontak pertama</p>
                @can('kontak-desa.create')
                <a href="{{ route('kontak-desa.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Kontak Pertama
                </a>
                @endcan
            </div>
            @endif
        </div>

        <!-- Pagination -->
        @if(isset($kontaks) && $kontaks->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $kontaks->links() }}
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
function confirmDelete(kontakId, kontakName) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus kontak "${kontakName}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form delete
            document.getElementById('delete-form-' + kontakId).submit();
        }
    });
}
@endnoncescript
@endsection


