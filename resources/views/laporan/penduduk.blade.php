@extends('layouts.app')

@section('title', 'Laporan Data Penduduk')
@section('subtitle', 'Laporan lengkap data penduduk desa Cibatu')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Laporan Data Penduduk</h1>
            <p class="text-gray-600 mt-1">Filter dan ekspor data penduduk desa Cibatu</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="exportToExcel()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg flex items-center transition-colors shadow-md">
                <i class="fas fa-file-excel mr-2"></i>
                Export Excel
            </button>
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center transition-colors shadow-md">
                <i class="fas fa-print mr-2"></i>
                Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Data</h3>
        <form method="GET" action="{{ route('laporan.penduduk') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Nama/NIK</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Masukkan nama atau NIK">
            </div>

            <!-- Dusun -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dusun</label>
                <select name="dusun" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Dusun</option>
                    @foreach($dusunOptions as $dusun)
                        <option value="{{ $dusun }}" {{ request('dusun') == $dusun ? 'selected' : '' }}>
                            {{ $dusun }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- RT -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">RT</label>
                <select name="rt" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua RT</option>
                    @foreach($rtOptions as $rt)
                        <option value="{{ $rt }}" {{ request('rt') == $rt ? 'selected' : '' }}>
                            RT {{ $rt }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Jenis Kelamin -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Jenis Kelamin</option>
                    @foreach($jenisKelaminOptions as $jk)
                        <option value="{{ $jk }}" {{ request('jenis_kelamin') == $jk ? 'selected' : '' }}>
                            {{ $jk }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Usia Min -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Usia Minimum</label>
                <input type="number" name="usia_min" value="{{ request('usia_min') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="0" min="0" max="120">
            </div>

            <!-- Usia Max -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Usia Maksimum</label>
                <input type="number" name="usia_max" value="{{ request('usia_max') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="100" min="0" max="120">
            </div>

            <!-- Status Perkawinan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Perkawinan</label>
                <select name="status_perkawinan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    @foreach($statusPerkawinanOptions as $status)
                        <option value="{{ $status }}" {{ request('status_perkawinan') == $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Pendidikan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pendidikan</label>
                <input type="text" name="pendidikan" value="{{ request('pendidikan') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Masukkan pendidikan">
            </div>

            <!-- Pekerjaan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan</label>
                <input type="text" name="pekerjaan" value="{{ request('pekerjaan') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Masukkan pekerjaan">
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center transition-colors">
                    <i class="fas fa-search mr-2"></i>
                    Filter
                </button>
                <a href="{{ route('laporan.penduduk') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg flex items-center transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-blue-50 p-6 rounded-xl border border-blue-200">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-blue-600">Total Penduduk</p>
                    <p class="text-2xl font-bold text-blue-900">{{ number_format($totalPenduduk) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 p-6 rounded-xl border border-green-200">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-filter text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-green-600">Data Tersaring</p>
                    <p class="text-2xl font-bold text-green-900">{{ number_format($totalFiltered) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 p-6 rounded-xl border border-purple-200">
            <div class="flex items-center">
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-percentage text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-purple-600">Persentase</p>
                    <p class="text-2xl font-bold text-purple-900">
                        {{ $totalPenduduk > 0 ? number_format(($totalFiltered / $totalPenduduk) * 100, 1) : 0 }}%
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Data Penduduk</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kelamin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dusun</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pekerjaan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($penduduks as $index => $penduduk)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $penduduks->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $penduduk->nik }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $penduduk->nama }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $penduduk->jenis_kelamin == 'LAKI-LAKI' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                    {{ $penduduk->jenis_kelamin }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $penduduk->usia }} tahun
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $penduduk->dusun ?: '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                RT {{ $penduduk->rt }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $penduduk->status_perkawinan ?: '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $penduduk->pekerjaan ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                                <p class="text-lg font-medium">Tidak ada data penduduk</p>
                                <p class="text-sm">Coba ubah filter atau tambah data penduduk baru</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($penduduks->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $penduduks->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

@noncescript
function exportToExcel() {
    // Get current filter parameters
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');

    // Redirect to export URL
    window.location.href = '{{ route("laporan.penduduk.export.excel") }}?' + params.toString();
}
@endnoncescript

<style>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
@endsection
