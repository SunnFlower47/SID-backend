@extends('layouts.app')

@section('title', 'Bantuan Sosial')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8 mb-8">
        <div class="flex flex-col lg:flex-row items-center justify-between">
            <div class="flex-1 text-center lg:text-left mb-6 lg:mb-0">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-2 flex items-center justify-center lg:justify-start">
                    <i class="fas fa-user mr-3 text-yellow-300"></i>
                    Detail Penerima
                </h1>
                <p class="text-green-100 text-sm sm:text-base">{{ $bantuanSosial->nama_program }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                @can('bantuan_sosial.manage_penerima')
                <a href="{{ route('bantuan-sosial.penerima.edit', [$bantuanSosial, $penerima]) }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                @endcan
                <a href="{{ route('bantuan-sosial.penerima.index', $bantuanSosial) }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6 lg:space-y-8">
            <!-- Info Penerima -->
            <div class="bg-white rounded-2xl shadow-lg">
                <div class="px-4 py-4 lg:px-8 lg:py-6 border-b border-gray-200">
                    <h6 class="text-lg lg:text-xl font-semibold text-gray-900">Informasi Penerima</h6>
                </div>
                <div class="p-4 lg:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Nama Lengkap</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $penerima->penduduk->nama }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">NIK</label>
                            <p class="text-lg font-semibold text-gray-900 font-mono">{{ $penerima->penduduk->nik }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tempat, Tanggal Lahir</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $penerima->penduduk->tempat_lahir }}, {{ $penerima->penduduk->tanggal_lahir->format('d F Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Jenis Kelamin</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $penerima->penduduk->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Alamat</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $penerima->penduduk->alamat }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">RT/RW</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $penerima->penduduk->rt }}/{{ $penerima->penduduk->rw }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Bantuan -->
            <div class="bg-white rounded-2xl shadow-lg">
                <div class="px-4 py-4 lg:px-8 lg:py-6 border-b border-gray-200">
                    <h6 class="text-lg lg:text-xl font-semibold text-gray-900">Informasi Bantuan</h6>
                </div>
                <div class="p-4 lg:p-8">
                    @php
                        $dataTambahan = is_string($penerima->data_tambahan) ? json_decode($penerima->data_tambahan, true) : $penerima->data_tambahan;
                        $sistemPembayaran = $dataTambahan['sistem_pembayaran'] ?? 'sekali';
                    @endphp

                    @if($sistemPembayaran === 'triwulanan')
                        <!-- Sistem Triwulanan -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-500 mb-2">Sistem Pembayaran</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                Triwulanan (3x dalam 1 tahun)
                            </span>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Total Nilai Bantuan</label>
                            <p class="text-2xl font-bold text-green-600">{{ $penerima->nilai_diterima_formatted }}</p>
                        </div>

                        <!-- Jadwal Pembayaran Triwulanan -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-500 mb-3">Jadwal Pembayaran</label>
                            <div class="space-y-3">
                                @if(isset($dataTambahan['triwulan_1']))
                                <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">1</div>
                                        <div>
                                            <div class="font-medium text-gray-900">Triwulan 1</div>
                                            <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($dataTambahan['triwulan_1']['tanggal'])->format('d F Y') }}</div>
                                        </div>
                                    </div>
                                    <div class="text-lg font-bold text-green-600">Rp {{ number_format($dataTambahan['triwulan_1']['jumlah'], 0, ',', '.') }}</div>
                                </div>
                                @endif

                                @if(isset($dataTambahan['triwulan_2']))
                                <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">2</div>
                                        <div>
                                            <div class="font-medium text-gray-900">Triwulan 2</div>
                                            <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($dataTambahan['triwulan_2']['tanggal'])->format('d F Y') }}</div>
                                        </div>
                                    </div>
                                    <div class="text-lg font-bold text-green-600">Rp {{ number_format($dataTambahan['triwulan_2']['jumlah'], 0, ',', '.') }}</div>
                                </div>
                                @endif

                                @if(isset($dataTambahan['triwulan_3']))
                                <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">3</div>
                                        <div>
                                            <div class="font-medium text-gray-900">Triwulan 3</div>
                                            <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($dataTambahan['triwulan_3']['tanggal'])->format('d F Y') }}</div>
                                        </div>
                                    </div>
                                    <div class="text-lg font-bold text-green-600">Rp {{ number_format($dataTambahan['triwulan_3']['jumlah'], 0, ',', '.') }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <!-- Sistem Sekali Bayar -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-500 mb-2">Sistem Pembayaran</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-money-bill-wave mr-2"></i>
                                Sekali Bayar
                            </span>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Nilai Diterima</label>
                            <p class="text-2xl font-bold text-green-600">{{ $penerima->nilai_diterima_formatted }}</p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Penerimaan</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $penerima->tanggal_penerimaan->format('d F Y') }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Status Penerimaan</label>
                            @if($penerima->status_penerimaan == 'aktif')
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    {{ $penerima->status_penerimaan_label }}
                                </span>
                            @elseif($penerima->status_penerimaan == 'ditangguhkan')
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-pause-circle mr-2"></i>
                                    {{ $penerima->status_penerimaan_label }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-2"></i>
                                    {{ $penerima->status_penerimaan_label }}
                                </span>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Dibuat</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $penerima->created_at->format('d F Y, H:i') }}</p>
                        </div>
                    </div>

                    @if($penerima->keterangan)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Keterangan</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $penerima->keterangan }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-4 lg:space-y-6">
            <!-- Aksi -->
            <div class="bg-white rounded-2xl shadow-lg">
                <div class="px-4 py-4 lg:px-6 lg:py-4 border-b border-gray-200">
                    <h6 class="text-lg font-semibold text-gray-900">Aksi</h6>
                </div>
                <div class="p-4 lg:p-6">
                    <div class="space-y-3">
                        @can('bantuan_sosial.manage_penerima')
                        <a href="{{ route('bantuan-sosial.penerima.edit', [$bantuanSosial, $penerima]) }}" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i> Edit Penerima
                        </a>
                        @endcan

                        @can('bantuan_sosial.manage_penerima')
                        <button onclick="confirmDeletePenerima('{{ $penerima->id }}', '{{ $penerima->penduduk->nama ?? 'Penerima' }}')"
                                class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-trash mr-2"></i> Hapus Penerima
                        </button>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Info Program -->
            <div class="bg-white rounded-2xl shadow-lg">
                <div class="px-4 py-4 lg:px-6 lg:py-4 border-b border-gray-200">
                    <h6 class="text-lg font-semibold text-gray-900">Program Bantuan</h6>
                </div>
                <div class="p-4 lg:p-6">
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Nama Program</span>
                            <p class="text-sm text-gray-900 font-semibold">{{ $bantuanSosial->nama_program }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Jenis Bantuan</span>
                            <p class="text-sm text-gray-900">{{ $bantuanSosial->jenis_bantuan }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Periode</span>
                            <p class="text-sm text-gray-900">{{ $bantuanSosial->periode }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Status Program</span>
                            @if($bantuanSosial->status == 'aktif')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $bantuanSosial->status_label }}
                                </span>
                            @elseif($bantuanSosial->status == 'selesai')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $bantuanSosial->status_label }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ $bantuanSosial->status_label }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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
</script>
@endpush
@endsection
