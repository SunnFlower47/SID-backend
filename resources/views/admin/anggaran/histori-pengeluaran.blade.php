@extends('layouts.app')

@section('title', 'Histori Pergerakan Dana')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                @if($apbdes->jenis == 'pendapatan')
                    Histori Penerimaan
                @elseif($apbdes->jenis == 'belanja')
                    Histori Pengeluaran
                @else
                    Histori Pembiayaan
                @endif
            </h1>
            <p class="text-gray-600 mt-1">{{ $apbdes->kode_rekening }} - {{ $apbdes->nama_rekening }}</p>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-2
                {{ $apbdes->jenis == 'pendapatan' ? 'bg-green-100 text-green-800' :
                   ($apbdes->jenis == 'belanja' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                {{ ucfirst($apbdes->jenis) }}
            </span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('transparansi-desa.apbdes', ['tahun' => $apbdes->tahun]) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke APBDes
            </a>
        </div>
    </div>

    <!-- APBDes Info Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Rekening</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-sm text-blue-600 font-medium">Anggaran</div>
                <div class="text-lg font-bold text-blue-900">Rp {{ number_format($apbdes->anggaran, 0, ',', '.') }}</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-sm text-green-600 font-medium">Realisasi</div>
                <div class="text-lg font-bold text-green-900">Rp {{ number_format($apbdes->realisasi, 0, ',', '.') }}</div>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <div class="text-sm text-yellow-600 font-medium">Sisa Anggaran</div>
                <div class="text-lg font-bold text-yellow-900">Rp {{ number_format($apbdes->sisa_anggaran, 0, ',', '.') }}</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <div class="text-sm text-purple-600 font-medium">Persentase</div>
                <div class="text-lg font-bold text-purple-900">{{ $apbdes->persentase_realisasi }}%</div>
            </div>
        </div>
    </div>

    <!-- Histori Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                @if($apbdes->jenis == 'pendapatan')
                    Daftar Penerimaan
                @elseif($apbdes->jenis == 'belanja')
                    Daftar Pengeluaran
                @else
                    Daftar Pembiayaan
                @endif
            </h3>
        </div>

        @if($apbdes->historiPengeluarans->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                @if($apbdes->jenis == 'pendapatan')
                                    Nama Penerimaan
                                @elseif($apbdes->jenis == 'belanja')
                                    Nama Pengeluaran
                                @else
                                    Nama Pembiayaan
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($apbdes->historiPengeluarans as $index => $pengeluaran)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $pengeluaran->nama_pengeluaran }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-red-600">Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $pengeluaran->tanggal_pengeluaran->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $pengeluaran->keterangan ?: '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $pengeluaran->user->name ?? 'Unknown' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('anggaran.edit-pengeluaran', $pengeluaran->id) }}"
                                           class="inline-flex items-center px-3 py-1.5 bg-yellow-600 text-white text-xs font-medium rounded-lg hover:bg-yellow-700 transition-colors"
                                           title="Edit Pengeluaran">
                                            <i class="fas fa-edit mr-1"></i>
                                            Edit
                                        </a>
                                        <button onclick="confirmDelete({{ $pengeluaran->id }}, '{{ $pengeluaran->nama_pengeluaran }}')"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-colors"
                                                title="Hapus Pengeluaran">
                                            <i class="fas fa-trash mr-1"></i>
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-receipt text-4xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    @if($apbdes->jenis == 'pendapatan')
                        Belum Ada Penerimaan
                    @elseif($apbdes->jenis == 'belanja')
                        Belum Ada Pengeluaran
                    @else
                        Belum Ada Pembiayaan
                    @endif
                </h3>
                <p class="text-gray-500 mb-4">
                    @if($apbdes->jenis == 'pendapatan')
                        Belum ada penerimaan yang tercatat untuk rekening ini.
                    @elseif($apbdes->jenis == 'belanja')
                        Belum ada pengeluaran yang tercatat untuk rekening ini.
                    @else
                        Belum ada pembiayaan yang tercatat untuk rekening ini.
                    @endif
                </p>
                <a href="{{ route('anggaran.create-pengeluaran') }}?apbdes_id={{ $apbdes->id }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    @if($apbdes->jenis == 'pendapatan')
                        Tambah Penerimaan
                    @elseif($apbdes->jenis == 'belanja')
                        Tambah Pengeluaran
                    @else
                        Tambah Pembiayaan
                    @endif
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(pengeluaranId, namaPengeluaran) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus pengeluaran "${namaPengeluaran}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('anggaran/delete-pengeluaran') }}/${pengeluaranId}`;

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
