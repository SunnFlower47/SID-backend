@extends('layouts.app')

@section('title', 'Laporan Mutasi')
@section('subtitle', 'Laporan data mutasi penduduk')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Laporan Mutasi</h2>
                            <p class="text-gray-600 mt-1">Laporan data mutasi penduduk desa Cibatu</p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('laporan.mutasi.export.excel') }}"
                               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                                <i class="fas fa-file-excel mr-2"></i>
                                Export Excel
                            </a>
                        </div>
                    </div>

                    <!-- Filter Form -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <form method="GET" action="{{ route('laporan.mutasi') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="jenis_mutasi" class="block text-sm font-medium text-gray-700 mb-1">Jenis Mutasi</label>
                                <select id="jenis_mutasi" name="jenis_mutasi" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Semua Jenis</option>
                                    @foreach($jenisMutasiOptions as $jenis)
                                        <option value="{{ $jenis }}" {{ request('jenis_mutasi') == $jenis ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $jenis)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="kategori_mutasi" class="block text-sm font-medium text-gray-700 mb-1">Kategori Mutasi</label>
                                <select id="kategori_mutasi" name="kategori_mutasi" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Semua Kategori</option>
                                    @foreach($kategoriMutasiOptions as $kategori)
                                        <option value="{{ $kategori }}" {{ request('kategori_mutasi') == $kategori ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $kategori)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="tanggal_dari" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dari</label>
                                <input type="date" id="tanggal_dari" name="tanggal_dari"
                                       value="{{ request('tanggal_dari') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="tanggal_sampai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Sampai</label>
                                <input type="date" id="tanggal_sampai" name="tanggal_sampai"
                                       value="{{ request('tanggal_sampai') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                    <i class="fas fa-search mr-2"></i>
                                    Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-6">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exchange-alt text-blue-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-600">Total Mutasi</p>
                                    <p class="text-2xl font-bold text-blue-900">{{ $totalMutasi }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-cross text-red-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-red-600">Kematian</p>
                                    <p class="text-2xl font-bold text-red-900">{{ $totalKematian }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-baby text-green-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-600">Kelahiran</p>
                                    <p class="text-2xl font-bold text-green-900">{{ $totalKelahiran }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-arrow-left text-yellow-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-yellow-600">Pindah Keluar</p>
                                    <p class="text-2xl font-bold text-yellow-900">{{ $totalPindahKeluar }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-arrow-right text-blue-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-600">Pindah Masuk</p>
                                    <p class="text-2xl font-bold text-blue-900">{{ $totalPindahMasuk }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-home text-purple-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-purple-600">Pindah RT/RW</p>
                                    <p class="text-2xl font-bold text-purple-900">{{ $totalPindahRTRW }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-pink-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-users text-pink-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-pink-600">Pisah KK</p>
                                    <p class="text-2xl font-bold text-pink-900">{{ $totalPisahKK }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Info -->
                    <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                        <p class="text-sm text-gray-600">
                            Menampilkan {{ $mutasis->firstItem() ?? 0 }} - {{ $mutasis->lastItem() ?? 0 }}
                            dari {{ number_format($totalFiltered) }} data mutasi
                            (Total: {{ number_format($totalMutasi) }} mutasi)
                        </p>
                    </div>

                    <!-- Mutasi Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Penduduk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Mutasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($mutasis as $index => $mutasi)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $mutasis->firstItem() + $index }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $mutasi->penduduk->nama ?? 'Data tidak tersedia' }}
                                            @if($mutasi->penduduk && $mutasi->penduduk->trashed())
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i class="fas fa-trash mr-1"></i>
                                                    Terhapus
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($mutasi->jenis_mutasi == 'kematian') bg-red-100 text-red-800
                                                @elseif($mutasi->jenis_mutasi == 'pindah_keluar') bg-yellow-100 text-yellow-800
                                                @elseif($mutasi->jenis_mutasi == 'pindah_rt_rw') bg-green-100 text-green-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $mutasi->jenis_mutasi)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ ucfirst(str_replace('_', ' ', $mutasi->kategori_mutasi)) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $mutasi->tanggal_mutasi ? \Carbon\Carbon::parse($mutasi->tanggal_mutasi)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ Str::limit($mutasi->keterangan, 50) ?: '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('mutasi.data.show', $mutasi) }}"
                                               class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye mr-1"></i>
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                            <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                            <p>Tidak ada data mutasi</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($mutasis->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $mutasis->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection


