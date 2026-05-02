@extends('layouts.app')

@section('title', 'Bantuan Sosial')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8 mb-8">
        <div class="flex flex-col lg:flex-row items-center justify-between">
            <div class="flex-1 text-center lg:text-left mb-6 lg:mb-0">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-2 flex items-center justify-center lg:justify-start">
                    <i class="fas fa-users mr-3 text-yellow-300"></i>
                    Kelola Penerima
                </h1>
                <p class="text-green-100 text-sm sm:text-base">{{ $bantuanSosial->nama_program }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                @can('pelayanan_informasi')
                <a href="{{ route('bantuan-sosial.penerima.create', $bantuanSosial) }}" class="group flex items-center px-4 py-2.5 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Penerima
                </a>
                @endcan
                <a href="{{ route('bantuan-sosial.show', $bantuanSosial) }}" class="group flex items-center px-4 py-2.5 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6 mb-6 lg:mb-8">
        <div class="bg-white rounded-2xl shadow-lg p-4 lg:p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Penerima</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $penerima->total() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-4 lg:p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Penerima Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $penerima->where('status_penerimaan', 'aktif')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-4 lg:p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <i class="fas fa-pause-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Ditangguhkan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $penerima->where('status_penerimaan', 'ditangguhkan')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-lg">
        <div class="px-4 py-4 lg:px-8 lg:py-6 border-b border-gray-200">
            <h6 class="text-lg lg:text-xl font-semibold text-gray-900">Daftar Penerima Bantuan</h6>
        </div>
        <div class="p-4 lg:p-8">
            @if($penerima->count() > 0)
                <!-- Mobile Card View -->
                <div class="block lg:hidden">
                    <div class="space-y-4">
                        @foreach($penerima as $index => $item)
                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow cursor-pointer group" onclick="window.location='{{ route('bantuan-sosial.penerima.show', [$bantuanSosial, $item]) }}'">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3 flex-1 min-w-0">
                                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition-colors flex-shrink-0">
                                        <i class="fas fa-user text-green-600"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-semibold text-gray-900 truncate group-hover:text-green-900 transition-colors">
                                            {{ $item->penduduk->nama }}
                                        </h4>
                                        <p class="text-xs text-gray-500 font-mono">{{ $item->penduduk->nik }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons - Always Visible -->
                            <div class="flex items-center justify-end space-x-2 mb-4">
                                <button class="flex items-center px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors text-sm font-medium"
                                        onclick="event.stopPropagation(); window.location='{{ route('bantuan-sosial.penerima.show', [$bantuanSosial, $item]) }}'"
                                        title="Lihat Detail">
                                    <i class="fas fa-eye mr-1"></i>
                                    Detail
                                </button>
                                @can('pelayanan_informasi')
                                <a href="{{ route('bantuan-sosial.penerima.edit', [$bantuanSosial, $item]) }}"
                                   class="flex items-center px-3 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg transition-colors text-sm font-medium"
                                   onclick="event.stopPropagation()"
                                   title="Edit Data">
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit
                                </a>
                                <button onclick="event.stopPropagation(); confirmDeletePenerima('{{ $item->id }}', '{{ $item->penduduk->nama ?? 'Penerima' }}')"
                                        class="flex items-center px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg transition-colors text-sm font-medium"
                                        title="Hapus Data">
                                    <i class="fas fa-trash mr-1"></i>
                                    Hapus
                                </button>
                                @endcan
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Nilai Diterima:</span>
                                    <span class="text-sm font-bold text-green-600">{{ $item->nilai_diterima_formatted }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Tanggal:</span>
                                    <span class="text-sm text-gray-900">{{ $item->tanggal_penerimaan->format('d/m/Y') }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Status:</span>
                                    @if($item->status_penerimaan == 'aktif')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            {{ $item->status_penerimaan_label }}
                                        </span>
                                    @elseif($item->status_penerimaan == 'ditangguhkan')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-pause-circle mr-1"></i>
                                            {{ $item->status_penerimaan_label }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            {{ $item->status_penerimaan_label }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64">Nama Penerima</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">NIK</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Nilai Diterima</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($penerima as $index => $item)
                            <tr class="hover:bg-gray-50 cursor-pointer transition-colors group" onclick="window.location='{{ route('bantuan-sosial.penerima.show', [$bantuanSosial, $item]) }}'">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $penerima->firstItem() + $index }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mr-3 group-hover:bg-green-200 transition-colors">
                                            <i class="fas fa-user text-green-600"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-medium text-gray-900 group-hover:text-green-900 transition-colors truncate">
                                                {{ $item->penduduk->nama }}
                                            </div>
                                            <div class="text-xs text-gray-500 truncate">{{ Str::limit($item->penduduk->alamat, 30) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">{{ $item->penduduk->nik }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">{{ $item->nilai_diterima_formatted }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->tanggal_penerimaan->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->status_penerimaan == 'aktif')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            {{ $item->status_penerimaan_label }}
                                        </span>
                                    @elseif($item->status_penerimaan == 'ditangguhkan')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-pause-circle mr-1"></i>
                                            {{ $item->status_penerimaan_label }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            {{ $item->status_penerimaan_label }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-1">
                                        <button class="flex items-center px-2 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-md transition-colors text-xs font-medium"
                                                onclick="event.stopPropagation(); window.location='{{ route('bantuan-sosial.penerima.show', [$bantuanSosial, $item]) }}'"
                                                title="Lihat Detail">
                                            <i class="fas fa-eye mr-1"></i>
                                            Detail
                                        </button>
                                        @can('pelayanan_informasi')
                                        <a href="{{ route('bantuan-sosial.penerima.edit', [$bantuanSosial, $item]) }}"
                                           class="flex items-center px-2 py-1 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-md transition-colors text-xs font-medium"
                                           onclick="event.stopPropagation()"
                                           title="Edit Data">
                                            <i class="fas fa-edit mr-1"></i>
                                            Edit
                                        </a>
                                        <button onclick="event.stopPropagation(); confirmDeletePenerima('{{ $item->id }}', '{{ $item->penduduk->nama ?? 'Penerima' }}')"
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

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $penerima->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-users text-6xl text-gray-400 mb-6"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Penerima Bantuan</h3>
                    <p class="text-gray-500 mb-6">Belum ada penerima bantuan untuk program ini</p>
                    @can('pelayanan_informasi')
                    <a href="{{ route('bantuan-sosial.penerima.create', $bantuanSosial) }}" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i> Tambah Penerima Pertama
                    </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@noncescript
// SweetAlert untuk konfirmasi delete penerima
function confirmDeletePenerima(id, name) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus penerima "${name}"?`,
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
            form.action = `/bantuan-sosial/{{ $bantuanSosial->id }}/penerima/${id}`;

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
@endpush
@endsection


