@extends('layouts.app')

@section('title', 'Pengaduan Warga')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-red-600 via-red-700 to-red-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Pengaduan Warga</h1>
                    <p class="text-red-100 mt-1">Kelola pengaduan dan keluhan warga</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                @can('pengaduan.create')
                <a href="{{ route('pengaduan.create') }}" class="group flex items-center px-4 py-2.5 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Pengaduan
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-blue-600 uppercase tracking-wide">Total Pengaduan</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-comments text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-yellow-600 uppercase tracking-wide">Pengaduan Baru</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['baru'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-exclamation-circle text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-blue-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-blue-500 uppercase tracking-wide">Sedang Diproses</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['diproses'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-clock text-2xl text-blue-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-green-600 uppercase tracking-wide">Selesai</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['selesai'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h6 class="text-lg font-semibold text-gray-900">Filter & Pencarian</h6>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('pengaduan.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                        <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300"
                               id="search" name="search" value="{{ request('search') }}" placeholder="Cari pengaduan...">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300"
                                id="status" name="status">
                            <option value="">Semua Status</option>
                            <option value="baru" {{ request('status') == 'baru' ? 'selected' : '' }}>Baru</option>
                            <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div>
                        <label for="prioritas" class="block text-sm font-medium text-gray-700 mb-2">Prioritas</label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300"
                                id="prioritas" name="prioritas">
                            <option value="">Semua Prioritas</option>
                            <option value="rendah" {{ request('prioritas') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                            <option value="sedang" {{ request('prioritas') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="tinggi" {{ request('prioritas') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                            <option value="darurat" {{ request('prioritas') == 'darurat' ? 'selected' : '' }}>Darurat</option>
                        </select>
                    </div>
                    <div>
                        <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300"
                                id="kategori" name="kategori">
                            <option value="">Semua Kategori</option>
                            <option value="infrastruktur" {{ request('kategori') == 'infrastruktur' ? 'selected' : '' }}>Infrastruktur</option>
                            <option value="keamanan" {{ request('kategori') == 'keamanan' ? 'selected' : '' }}>Keamanan</option>
                            <option value="kebersihan" {{ request('kategori') == 'kebersihan' ? 'selected' : '' }}>Kebersihan</option>
                            <option value="administrasi" {{ request('kategori') == 'administrasi' ? 'selected' : '' }}>Administrasi</option>
                            <option value="lainnya" {{ request('kategori') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div class="flex items-end space-x-3">
                        <button type="submit" class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center">
                            <i class="fas fa-search mr-2"></i> Filter
                        </button>
                        <a href="{{ route('pengaduan.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center">
                            <i class="fas fa-times mr-2"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h6 class="text-lg font-semibold text-gray-900">Daftar Pengaduan Warga</h6>
        </div>
        <div class="overflow-x-auto">
            @if($pengaduans->count() > 0)
                <!-- Mobile Card View -->
                <div class="block lg:hidden">
                    <div class="p-4 space-y-4">
                        @foreach($pengaduans as $index => $pengaduan)
                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow cursor-pointer group" onclick="window.location='{{ route('pengaduan.show', $pengaduan) }}'">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3 flex-1 min-w-0">
                                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center group-hover:bg-red-200 transition-colors flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-semibold text-gray-900 truncate group-hover:text-red-900 transition-colors">
                                            {{ $pengaduan->judul }}
                                        </h4>
                                        <p class="text-xs text-gray-500 truncate">{{ $pengaduan->nama_pelapor }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons - Always Visible -->
                            <div class="flex items-center justify-end space-x-2 mb-4">
                                <button class="flex items-center px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors text-sm font-medium"
                                        onclick="event.stopPropagation(); window.location='{{ route('pengaduan.show', $pengaduan) }}'"
                                        title="Lihat Detail">
                                    <i class="fas fa-eye mr-1"></i>
                                    Detail
                                </button>
                                @can('pengaduan.edit')
                                <a href="{{ route('pengaduan.edit', $pengaduan) }}"
                                   class="flex items-center px-3 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg transition-colors text-sm font-medium"
                                   onclick="event.stopPropagation()"
                                   title="Edit Data">
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit
                                </a>
                                @endcan
                                @can('pengaduan.delete')
                                <button onclick="event.stopPropagation(); confirmDelete('{{ $pengaduan->id }}', '{{ $pengaduan->judul }}')"
                                        class="flex items-center px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg transition-colors text-sm font-medium"
                                        title="Hapus Data">
                                    <i class="fas fa-trash mr-1"></i>
                                    Hapus
                                </button>
                                @endcan
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Kategori:</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($pengaduan->kategori) }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Prioritas:</span>
                                    @if($pengaduan->prioritas == 'darurat')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $pengaduan->prioritas_label }}
                                        </span>
                                    @elseif($pengaduan->prioritas == 'tinggi')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            {{ $pengaduan->prioritas_label }}
                                        </span>
                                    @elseif($pengaduan->prioritas == 'sedang')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ $pengaduan->prioritas_label }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $pengaduan->prioritas_label }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Status:</span>
                                    @if($pengaduan->status == 'baru')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ $pengaduan->status_label }}
                                        </span>
                                    @elseif($pengaduan->status == 'diproses')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $pengaduan->status_label }}
                                        </span>
                                    @elseif($pengaduan->status == 'selesai')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $pengaduan->status_label }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $pengaduan->status_label }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Tanggal:</span>
                                    <span class="text-sm text-gray-900">{{ $pengaduan->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Desktop Table View -->
                <div class="hidden lg:block">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pengaduan
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pelapor
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kategori
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Prioritas
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pengaduans as $pengaduan)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-medium text-gray-900 truncate max-w-xs">
                                                    {{ $pengaduan->judul }}
                                                </div>
                                                @if($pengaduan->deskripsi)
                                                <div class="text-xs text-gray-500 truncate max-w-xs">
                                                    {{ Str::limit($pengaduan->deskripsi, 50) }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $pengaduan->nama_pelapor }}</div>
                            @if($pengaduan->nik_pelapor)
                                        <div class="text-xs text-gray-500 font-mono">{{ $pengaduan->nik_pelapor }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($pengaduan->kategori) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($pengaduan->prioritas == 'darurat')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $pengaduan->prioritas_label }}
                                </span>
                            @elseif($pengaduan->prioritas == 'tinggi')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    {{ $pengaduan->prioritas_label }}
                                </span>
                            @elseif($pengaduan->prioritas == 'sedang')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ $pengaduan->prioritas_label }}
                                </span>
                            @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $pengaduan->prioritas_label }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($pengaduan->status == 'baru')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ $pengaduan->status_label }}
                                </span>
                            @elseif($pengaduan->status == 'diproses')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $pengaduan->status_label }}
                                </span>
                            @elseif($pengaduan->status == 'selesai')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $pengaduan->status_label }}
                                </span>
                            @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $pengaduan->status_label }}
                                </span>
                            @endif
                        </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $pengaduan->created_at->format('d/m/Y') }}
                                        <div class="text-xs">{{ $pengaduan->created_at->format('H:i') }}</div>
                        </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('pengaduan.show', $pengaduan) }}"
                                               class="inline-flex items-center px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors text-sm font-medium"
                                   title="Lihat Detail">
                                                <i class="fas fa-eye mr-1"></i>
                                                Detail
                                </a>
                                @can('pengaduan.edit')
                                <a href="{{ route('pengaduan.edit', $pengaduan) }}"
                                               class="inline-flex items-center px-3 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg transition-colors text-sm font-medium"
                                               title="Edit Data">
                                                <i class="fas fa-edit mr-1"></i>
                                                Edit
                                </a>
                                @endcan
                                @can('pengaduan.delete')
                                            <button onclick="confirmDelete('{{ $pengaduan->id }}', '{{ $pengaduan->judul }}')"
                                                    class="inline-flex items-center px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg transition-colors text-sm font-medium"
                                                    title="Hapus Data">
                                                <i class="fas fa-trash mr-1"></i>
                                                Hapus
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                                @endforeach
                </tbody>
            </table>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-exclamation-triangle text-6xl text-gray-400 mb-6"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Pengaduan</h3>
                    <p class="text-gray-500 mb-6">Mulai dengan menambahkan pengaduan baru</p>
                    @can('pengaduan.create')
                    <a href="{{ route('pengaduan.create') }}" class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i> Tambah Pengaduan Pertama
                    </a>
                    @endcan
                </div>
            @endif
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 flex justify-center">
            {{ $pengaduans->links() }}
        </div>
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
@endnoncescript
@endsection


