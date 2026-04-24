@extends('layouts.app')

@section('title', 'Bantuan Sosial')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8 mb-8">
        <div class="flex flex-col lg:flex-row items-center justify-between">
            <div class="flex-1 text-center lg:text-left mb-6 lg:mb-0">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-2 flex items-center justify-center lg:justify-start">
                    <i class="fas fa-hands-helping mr-3 text-yellow-300"></i>
                    Bantuan Sosial
                </h1>
                <p class="text-green-100 text-sm sm:text-base">Kelola program bantuan sosial desa</p>
            </div>
            <div class="flex flex-wrap gap-3">
                @can('bantuan_sosial.create')
                <a href="{{ route('bantuan-sosial.create') }}" class="group flex items-center px-4 py-2.5 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Program
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-blue-600 uppercase tracking-wide">Total Program</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_program'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-list text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-green-600 uppercase tracking-wide">Program Aktif</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['program_aktif'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-blue-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-blue-500 uppercase tracking-wide">Total Penerima</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_penerima'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-users text-2xl text-blue-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-yellow-600 uppercase tracking-wide">Penerima Aktif</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['penerima_aktif'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-user-check text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h6 class="text-lg font-semibold text-gray-900">Filter & Pencarian</h6>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('bantuan-sosial.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                        <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300"
                               id="search" name="search" value="{{ request('search') }}" placeholder="Cari program...">
                    </div>
                    <div>
                        <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300"
                                id="tahun" name="tahun">
                            <option value="">Semua Tahun</option>
                            @for($year = date('Y'); $year >= 2020; $year--)
                                <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300"
                                id="status" name="status">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="ditangguhkan" {{ request('status') == 'ditangguhkan' ? 'selected' : '' }}>Ditangguhkan</option>
                        </select>
                    </div>
                    <div>
                        <label for="jenis_bantuan" class="block text-sm font-medium text-gray-700 mb-2">Jenis Bantuan</label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300"
                                id="jenis_bantuan" name="jenis_bantuan">
                            <option value="">Semua Jenis</option>
                            <option value="BLT" {{ request('jenis_bantuan') == 'BLT' ? 'selected' : '' }}>BLT</option>
                            <option value="PKH" {{ request('jenis_bantuan') == 'PKH' ? 'selected' : '' }}>PKH</option>
                            <option value="BPNT" {{ request('jenis_bantuan') == 'BPNT' ? 'selected' : '' }}>BPNT</option>
                            <option value="Bansos Lainnya" {{ request('jenis_bantuan') == 'Bansos Lainnya' ? 'selected' : '' }}>Bansos Lainnya</option>
                        </select>
                    </div>
                    <div class="flex items-end space-x-3">
                        <button type="submit" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center">
                            <i class="fas fa-search mr-2"></i> Filter
                        </button>
                        <a href="{{ route('bantuan-sosial.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center">
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
            <h6 class="text-lg font-semibold text-gray-900">Daftar Program Bantuan Sosial</h6>
        </div>
        <div class="overflow-x-auto">
            @if($bantuanSosials->count() > 0)
                <!-- Mobile Card View -->
                <div class="block lg:hidden">
                    <div class="p-4 space-y-4">
                        @foreach($bantuanSosials as $index => $bantuan)
                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow cursor-pointer group" onclick="window.location='{{ route('bantuan-sosial.show', $bantuan) }}'">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3 flex-1 min-w-0">
                                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition-colors flex-shrink-0">
                                        <i class="fas fa-hands-helping text-green-600"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-semibold text-gray-900 truncate group-hover:text-green-900 transition-colors">
                                            {{ $bantuan->nama_program }}
                                        </h4>
                                        <p class="text-xs text-gray-500 truncate">{{ Str::limit($bantuan->deskripsi, 50) }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons - Always Visible -->
                            <div class="flex items-center justify-end space-x-2 mb-4">
                                <button class="flex items-center px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors text-sm font-medium"
                                        onclick="event.stopPropagation(); window.location='{{ route('bantuan-sosial.show', $bantuan) }}'"
                                        title="Lihat Detail">
                                    <i class="fas fa-eye mr-1"></i>
                                    Detail
                                </button>
                                @can('bantuan_sosial.edit')
                                <a href="{{ route('bantuan-sosial.edit', $bantuan) }}"
                                   class="flex items-center px-3 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg transition-colors text-sm font-medium"
                                   onclick="event.stopPropagation()"
                                   title="Edit Data">
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit
                                </a>
                                @endcan
                                @can('bantuan_sosial.delete')
                                <button onclick="event.stopPropagation(); confirmDelete('{{ $bantuan->id }}', '{{ $bantuan->nama_program }}')"
                                        class="flex items-center px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg transition-colors text-sm font-medium"
                                        title="Hapus Data">
                                    <i class="fas fa-trash mr-1"></i>
                                    Hapus
                                </button>
                                @endcan
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Jenis:</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $bantuan->jenis_bantuan }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Periode:</span>
                                    <span class="text-sm text-gray-900">{{ $bantuan->periode }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Nilai:</span>
                                    <span class="text-sm font-bold text-green-600">{{ $bantuan->nilai_bantuan_formatted }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Penerima:</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $bantuan->penerima_count }} orang
                                    </span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Status:</span>
                                    @if($bantuan->status == 'aktif')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $bantuan->status_label }}
                                        </span>
                                    @elseif($bantuan->status == 'selesai')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $bantuan->status_label }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ $bantuan->status_label }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Desktop Table View -->
                <div class="hidden lg:block">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Program</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Bantuan</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Bantuan</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($bantuanSosials as $index => $bantuan)
                            <tr class="hover:bg-gray-50 cursor-pointer transition-colors group" onclick="window.location='{{ route('bantuan-sosial.show', $bantuan) }}'">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $bantuanSosials->firstItem() + $index }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mr-3 group-hover:bg-green-200 transition-colors">
                                            <i class="fas fa-hands-helping text-green-600"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-medium text-gray-900 group-hover:text-green-900 transition-colors truncate">
                                                {{ $bantuan->nama_program }}
                                            </div>
                                            <div class="text-xs text-gray-500 truncate">{{ Str::limit($bantuan->deskripsi, 50) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $bantuan->jenis_bantuan }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $bantuan->periode }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($bantuan->nilai_bantuan)
                                        <div class="font-semibold text-green-600">{{ $bantuan->nilai_bantuan_formatted }}</div>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $bantuan->penerima_count }} orang
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($bantuan->status == 'aktif')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $bantuan->status_label }}
                                        </span>
                                    @elseif($bantuan->status == 'selesai')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $bantuan->status_label }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ $bantuan->status_label }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-1">
                                        <button class="flex items-center px-2 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-md transition-colors text-xs font-medium"
                                                onclick="event.stopPropagation(); window.location='{{ route('bantuan-sosial.show', $bantuan) }}'"
                                                title="Lihat Detail">
                                            <i class="fas fa-eye mr-1"></i>
                                            Detail
                                        </button>
                                        @can('bantuan_sosial.edit')
                                        <a href="{{ route('bantuan-sosial.edit', $bantuan) }}"
                                           class="flex items-center px-2 py-1 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-md transition-colors text-xs font-medium"
                                           onclick="event.stopPropagation()"
                                           title="Edit Data">
                                            <i class="fas fa-edit mr-1"></i>
                                            Edit
                                        </a>
                                        @endcan
                                        @can('bantuan_sosial.delete')
                                        <button onclick="event.stopPropagation(); confirmDelete('{{ $bantuan->id }}', '{{ $bantuan->nama_program }}')"
                                                class="flex items-center px-2 py-1 bg-red-50 hover:bg-red-100 text-red-700 rounded-md transition-colors text-xs font-medium"
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
            @else
                <div class="text-center py-12">
                    <i class="fas fa-hands-helping text-6xl text-gray-400 mb-6"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Program Bantuan Sosial</h3>
                    <p class="text-gray-500 mb-6">Mulai dengan menambahkan program bantuan sosial baru</p>
                    @can('bantuan_sosial.create')
                    <a href="{{ route('bantuan-sosial.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i> Tambah Program Pertama
                    </a>
                    @endcan
                </div>
            @endif
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 flex justify-center">
            {{ $bantuanSosials->links() }}
        </div>
    </div>
</div>

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
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus program bantuan sosial "${name}"?`,
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
            form.action = `/bantuan-sosial/${id}`;

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

