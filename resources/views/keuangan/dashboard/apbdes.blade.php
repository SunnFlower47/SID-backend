@extends('layouts.app')

@section('title', 'APBDes')
@section('subtitle', 'Data anggaran dan realisasi keuangan desa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-pie text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">APBDes (Anggaran Pendapatan dan Belanja Desa)</h1>
                    <p class="text-blue-100 mt-1">Data anggaran dan realisasi keuangan desa</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                @can('keuangan')
                <a href="{{ route('anggaran.create-tahunan') }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Anggaran
                </a>
                <a href="{{ route('anggaran.create-pengeluaran') }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-minus mr-2"></i>
                    Tambah Pengeluaran
                </a>
                @endcan
                <a href="{{ route('transparansi-desa.index') }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg border-0 p-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100 -mx-6 -mt-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Filter & Pencarian</h2>
        </div>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                <select name="tahun" id="tahun" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    @foreach($tahunList as $tahunOption)
                        <option value="{{ $tahunOption }}" {{ $tahun == $tahunOption ? 'selected' : '' }}>
                            {{ $tahunOption }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="jenis" class="block text-sm font-medium text-gray-700 mb-2">Jenis</label>
                <select name="jenis" id="jenis" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">Semua Jenis</option>
                    <option value="pendapatan" {{ $jenis == 'pendapatan' ? 'selected' : '' }}>Pendapatan</option>
                    <option value="belanja" {{ $jenis == 'belanja' ? 'selected' : '' }}>Belanja</option>
                    <option value="pembiayaan" {{ $jenis == 'pembiayaan' ? 'selected' : '' }}>Pembiayaan</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all duration-200">
                    <i class="fas fa-filter mr-2"></i>
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <i class="fas fa-wallet text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Anggaran</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['total_anggaran'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Realisasi</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['total_realisasi'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100">
                    <i class="fas fa-percentage text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Persentase Realisasi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['persentase_realisasi'] }}%</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100">
                    <i class="fas fa-balance-scale text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Sisa Anggaran</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['total_anggaran'] - $stats['total_realisasi'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
        <div class="px-6 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Data APBDes Tahun {{ $tahun }}</h2>
        </div>
        <!-- Mobile Card View -->
        <div class="block lg:hidden p-6 space-y-4">
            @forelse($apbdes as $index => $item)
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="text-sm font-medium text-gray-500">#{{ $apbdes->firstItem() + $index }}</span>
                            <span class="text-sm font-mono text-gray-600">{{ $item->kode_rekening }}</span>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">{{ $item->nama_rekening }}</h3>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $item->jenis == 'pendapatan' ? 'bg-green-100 text-green-800' :
                                   ($item->jenis == 'belanja' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($item->jenis) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Sumber Dana:</span>
                        <span class="text-xs text-gray-900">{{ $item->sumber_dana_label ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Anggaran:</span>
                        <span class="text-xs font-medium text-gray-900">Rp {{ number_format($item->anggaran, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Realisasi:</span>
                        <span class="text-xs font-medium text-gray-900">Rp {{ number_format($item->realisasi, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Sisa:</span>
                        <span class="text-xs font-medium text-gray-900">Rp {{ number_format($item->anggaran - $item->realisasi, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                        <span class="text-xs text-gray-500">Persentase:</span>
                        <span class="text-xs font-bold text-gray-900">
                            @if($item->anggaran > 0)
                                {{ round(($item->realisasi / $item->anggaran) * 100, 2) }}%
                            @else
                                0%
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">Tidak ada data APBDes untuk tahun {{ $tahun }}</p>
            </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="dataTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Rekening</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uraian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sumber Dana</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Anggaran (Rp)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Realisasi (Rp)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa (Rp)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase (%)</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($apbdes as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $apbdes->firstItem() + $index }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->kode_rekening }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item->nama_rekening }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $item->jenis == 'pendapatan' ? 'bg-green-100 text-green-800' :
                                   ($item->jenis == 'belanja' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($item->jenis) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->sumber_dana_label ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item->anggaran, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item->realisasi, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item->anggaran - $item->realisasi, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                            @if($item->anggaran > 0)
                                {{ round(($item->realisasi / $item->anggaran) * 100, 2) }}%
                            @else
                                0%
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('anggaran.histori-pengeluaran', $item->id) }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors"
                                   title="Lihat Histori Pergerakan Dana">
                                    <i class="fas fa-receipt mr-1"></i>
                                    Histori
                                </a>

                                @can('keuangan')
                                    <a href="{{ route('anggaran.edit-apbdes', $item->id) }}"
                                       class="inline-flex items-center px-3 py-1.5 bg-yellow-600 text-white text-xs font-medium rounded-lg hover:bg-yellow-700 transition-colors"
                                       title="Edit APBDes">
                                        <i class="fas fa-edit mr-1"></i>
                                        Edit
                                    </a>
                                @endcan

                                @can('keuangan')
                                    <button onclick="confirmDeleteApbdes({{ $item->id }}, '{{ $item->nama_rekening }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-colors"
                                            title="Hapus APBDes">
                                        <i class="fas fa-trash mr-1"></i>
                                        Hapus
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data APBDes untuk tahun {{ $tahun }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex justify-center">
                {{ $apbdes->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteApbdesForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@noncescript
function confirmDeleteApbdes(apbdesId, namaRekening) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus rekening APBDes "${namaRekening}"?`,
        html: `
            <div class="text-left">
                <p class="mb-2">Rekening yang akan dihapus:</p>
                <p class="font-semibold">${namaRekening}</p>
                <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded">
                    <p class="text-sm text-red-800 font-semibold">?? Peringatan:</p>
                    <ul class="text-sm text-red-700 mt-1 list-disc list-inside">
                        <li>Rekening yang sudah memiliki histori pengeluaran tidak dapat dihapus</li>
                        <li>Rekening yang sudah terhubung dengan proyek desa tidak dapat dihapus</li>
                        <li>Tindakan ini tidak dapat dibatalkan</li>
                    </ul>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        width: '500px'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('anggaran/delete-apbdes') }}/${apbdesId}`;

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

@push('scripts')
@noncescript
$(document).ready(function() {
    $('#dataTable').DataTable({
        "paging": false,
        "searching": true,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "responsive": true
    });
});
@endnoncescript
@endpush

