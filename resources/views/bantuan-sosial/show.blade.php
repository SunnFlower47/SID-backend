@extends('layouts.app')

@section('title', 'Bantuan Sosial')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-hands-helping text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Detail Program Bantuan Sosial</h1>
                    <p class="text-green-100 text-sm sm:text-base">Informasi lengkap bantuan sosial</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                @can('bantuan_sosial.edit')
                <a href="{{ route('bantuan-sosial.edit', $bantuanSosial) }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                @endcan
                <a href="{{ route('bantuan-sosial.index') }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 lg:gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-6 lg:space-y-8">
            <!-- Program Info -->
            <div class="bg-white rounded-2xl shadow-lg">
                <div class="px-4 py-4 lg:px-8 lg:py-6 border-b border-gray-200">
                    <h6 class="text-lg lg:text-xl font-semibold text-gray-900">Informasi Program</h6>
                </div>
                <div class="p-4 lg:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Nama Program</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $bantuanSosial->nama_program }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Jenis Bantuan</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $bantuanSosial->jenis_bantuan }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                            @if($bantuanSosial->status == 'aktif')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    {{ $bantuanSosial->status_label }}
                                </span>
                            @elseif($bantuanSosial->status == 'selesai')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    {{ $bantuanSosial->status_label }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    {{ $bantuanSosial->status_label }}
                                </span>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Periode</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $bantuanSosial->periode }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Nilai Bantuan</label>
                            <p class="text-lg font-semibold text-green-600">{{ $bantuanSosial->nilai_bantuan_formatted }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Sumber Dana</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $bantuanSosial->sumber_dana }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Mulai</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $bantuanSosial->tanggal_mulai->format('d F Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Selesai</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $bantuanSosial->tanggal_selesai->format('d F Y') }}</p>
                        </div>
                        @if($bantuanSosial->kuota_penerima)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Kuota Penerima</label>
                            <p class="text-lg font-semibold text-gray-900">{{ number_format($bantuanSosial->kuota_penerima) }} orang</p>
                        </div>
                        @endif
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-500 mb-2">Deskripsi Program</label>
                        <p class="text-gray-900 leading-relaxed">{{ $bantuanSosial->deskripsi }}</p>
                    </div>

                    @if($bantuanSosial->kriteria_penerima)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-500 mb-2">Kriteria Penerima</label>
                        <div class="bg-gray-50 rounded-xl p-4">
                            @if(is_array($bantuanSosial->kriteria_penerima))
                                <ul class="list-disc list-inside space-y-2">
                                    @foreach($bantuanSosial->kriteria_penerima as $kriteria)
                                        <li class="text-gray-900">{{ $kriteria }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-gray-900">{{ $bantuanSosial->kriteria_penerima }}</p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Penerima Bantuan -->
            <div class="bg-white rounded-2xl shadow-lg">
                <div class="px-4 py-4 lg:px-8 lg:py-6 border-b border-gray-200">
                    <h6 class="text-lg lg:text-xl font-semibold text-gray-900">Daftar Penerima Bantuan</h6>
                </div>
                <div class="p-4 lg:p-8">
                    @if($bantuanSosial->penerima->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-3 lg:px-6 lg:py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                        <th class="px-3 py-3 lg:px-6 lg:py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Penerima</th>
                                        <th class="px-3 py-3 lg:px-6 lg:py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                                        <th class="px-3 py-3 lg:px-6 lg:py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Diterima</th>
                                        <th class="px-3 py-3 lg:px-6 lg:py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Penerimaan</th>
                                        <th class="px-3 py-3 lg:px-6 lg:py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($bantuanSosial->penerima as $index => $penerima)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-3 py-4 lg:px-6 lg:py-5 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $index + 1 }}</td>
                                        <td class="px-3 py-4 lg:px-6 lg:py-5 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900">{{ $penerima->penduduk->nama }}</div>
                                            <div class="text-sm text-gray-500 hidden lg:block">{{ $penerima->penduduk->alamat }}</div>
                                        </td>
                                        <td class="px-3 py-4 lg:px-6 lg:py-5 whitespace-nowrap text-sm text-gray-900 font-mono">{{ $penerima->penduduk->nik }}</td>
                                        <td class="px-3 py-4 lg:px-6 lg:py-5 whitespace-nowrap text-sm font-bold text-green-600">{{ $penerima->nilai_diterima_formatted }}</td>
                                        <td class="px-3 py-4 lg:px-6 lg:py-5 whitespace-nowrap text-sm text-gray-900">{{ $penerima->tanggal_penerimaan->format('d/m/Y') }}</td>
                                        <td class="px-3 py-4 lg:px-6 lg:py-5 whitespace-nowrap">
                                            @if($penerima->status_penerimaan == 'aktif')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    {{ $penerima->status_penerimaan_label }}
                                                </span>
                                            @elseif($penerima->status_penerimaan == 'ditangguhkan')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-pause-circle mr-1"></i>
                                                    {{ $penerima->status_penerimaan_label }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i class="fas fa-times-circle mr-1"></i>
                                                    {{ $penerima->status_penerimaan_label }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-users text-6xl text-gray-400 mb-6"></i>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Penerima Bantuan</h3>
                            <p class="text-gray-500">Belum ada penerima bantuan untuk program ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-4 lg:space-y-6">
            <!-- Statistics -->
            <div class="bg-white rounded-2xl shadow-lg">
                <div class="px-4 py-4 lg:px-6 lg:py-4 border-b border-gray-200">
                    <h6 class="text-lg font-semibold text-gray-900">Statistik</h6>
                </div>
                <div class="p-4 lg:p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Total Penerima</span>
                            <span class="text-2xl font-bold text-blue-600">{{ $bantuanSosial->penerima_count }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Penerima Aktif</span>
                            <span class="text-2xl font-bold text-green-600">{{ $bantuanSosial->penerima->where('status_penerimaan', 'aktif')->count() }}</span>
                        </div>
                        @if($bantuanSosial->kuota_penerima)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Kuota Tersisa</span>
                            <span class="text-2xl font-bold text-orange-600">{{ $bantuanSosial->kuota_penerima - $bantuanSosial->penerima_count }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-2xl shadow-lg">
                <div class="px-4 py-4 lg:px-6 lg:py-4 border-b border-gray-200">
                    <h6 class="text-lg font-semibold text-gray-900">Aksi</h6>
                </div>
                <div class="p-4 lg:p-6">
                    <div class="space-y-3">
                        @can('bantuan_sosial.manage_penerima')
                        <a href="{{ route('bantuan-sosial.penerima.index', $bantuanSosial) }}" class="w-full bg-orange-500 hover:bg-orange-600 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-users mr-2"></i> Kelola Penerima
                        </a>
                        @endcan

                        @can('bantuan_sosial.delete')
                        <button onclick="confirmDelete('{{ $bantuanSosial->id }}', '{{ $bantuanSosial->nama_program }}')"
                                class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-trash mr-2"></i> Hapus Program
                        </button>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Info -->
            <div class="bg-white rounded-2xl shadow-lg">
                <div class="px-4 py-4 lg:px-6 lg:py-4 border-b border-gray-200">
                    <h6 class="text-lg font-semibold text-gray-900">Informasi</h6>
                </div>
                <div class="p-4 lg:p-6">
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Dibuat</span>
                            <p class="text-sm text-gray-900">{{ $bantuanSosial->created_at->format('d F Y, H:i') }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Terakhir Diupdate</span>
                            <p class="text-sm text-gray-900">{{ $bantuanSosial->updated_at->format('d F Y, H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
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
