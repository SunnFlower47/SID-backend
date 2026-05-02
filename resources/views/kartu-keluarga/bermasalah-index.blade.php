@extends('layouts.app')

@section('title', 'KK Bermasalah')
@section('subtitle', 'Pantau dan selesaikan Kartu Keluarga yang kehilangan Kepala Keluarga')

@section('content')
<div class="space-y-6">

    {{-- ===== HEADER ===== --}}
    <div class="bg-gradient-to-r from-red-600 to-orange-600 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">KK Bermasalah</h1>
                    <p class="text-red-100 text-sm sm:text-base">Kartu Keluarga yang kehilangan Kepala Keluarga</p>
                    @if($stats['pending_total'] > 0)
                    <p class="text-yellow-200 text-sm font-semibold mt-1 animate-pulse">
                        <i class="fas fa-bell mr-1"></i>{{ $stats['pending_total'] }} KK perlu ditangani
                    </p>
                    @else
                    <p class="text-green-200 text-sm font-medium mt-1">
                        <i class="fas fa-check-circle mr-1"></i>Semua KK dalam kondisi normal
                    </p>
                    @endif
                </div>
            </div>
            <a href="{{ route('kk.index') }}"
               class="flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white rounded-xl transition-all duration-200 self-start sm:self-auto">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Menu KK
            </a>
        </div>
    </div>

    {{-- ===== STAT CARDS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-lg p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Belum Ditangani</p>
                <p class="text-2xl font-bold text-red-700">{{ $stats['bermasalah'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-clock text-orange-600 text-lg"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">KK Sementara Aktif</p>
                <p class="text-2xl font-bold text-orange-600">{{ $stats['bermasalah_sementara'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-green-600 text-lg"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Sudah Diselesaikan</p>
                <p class="text-2xl font-bold text-green-700">{{ $stats['resolved'] }}</p>
            </div>
        </div>
    </div>

    {{-- ===== TABS + SEARCH ===== --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        {{-- Tab Navigation --}}
        <div class="flex border-b border-gray-200">
            <a href="{{ route('kk.bermasalah.index', array_merge(request()->query(), ['tab' => 'pending'])) }}"
               class="flex-1 py-4 text-center text-sm font-semibold transition-all duration-200 border-b-2
                      {{ $tab === 'pending'
                         ? 'border-red-500 text-red-600 bg-red-50'
                         : 'border-transparent text-gray-500 hover:text-red-600 hover:bg-red-50' }}">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Perlu Ditangani
                @if($stats['pending_total'] > 0)
                <span class="ml-1 bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $stats['pending_total'] }}</span>
                @endif
            </a>
            <a href="{{ route('kk.bermasalah.index', array_merge(request()->query(), ['tab' => 'resolved'])) }}"
               class="flex-1 py-4 text-center text-sm font-semibold transition-all duration-200 border-b-2
                      {{ $tab === 'resolved'
                         ? 'border-green-500 text-green-600 bg-green-50'
                         : 'border-transparent text-gray-500 hover:text-green-600 hover:bg-green-50' }}">
                <i class="fas fa-history mr-2"></i>
                Riwayat Diselesaikan
                <span class="ml-1 bg-gray-200 text-gray-600 text-xs rounded-full px-2 py-0.5">{{ $stats['resolved'] }}</span>
            </a>
        </div>

        {{-- Search Bar --}}
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex gap-2">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 text-sm"></i>
                    </div>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Cari NKK atau nama kepala keluarga..."
                           class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
                <button type="submit" class="px-4 py-2.5 bg-red-600 text-white rounded-xl text-sm font-medium hover:bg-red-700 transition-colors">
                    Cari
                </button>
                @if($search)
                <a href="{{ route('kk.bermasalah.index', ['tab' => $tab]) }}"
                   class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-300 transition-colors">
                    Reset
                </a>
                @endif
            </form>
        </div>

        {{-- ===== TAB CONTENT: PENDING ===== --}}
        @if($tab === 'pending')
        @if($kkList->isEmpty())
        <div class="py-16 text-center text-gray-400">
            <i class="fas fa-check-circle text-5xl text-green-300 mb-4"></i>
            <p class="text-lg font-semibold text-green-600">Tidak ada KK yang perlu ditangani</p>
            <p class="text-sm mt-1">Semua Kartu Keluarga dalam kondisi normal</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">NKK</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">KK Lama / Catatan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Bermasalah Sejak</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($kkList as $kk)
                    <tr class="hover:bg-red-50 transition-colors">
                        <td class="px-5 py-4">
                            <span class="font-mono text-sm font-semibold text-gray-800">{{ $kk->nkk }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-medium text-gray-800">{{ $kk->nama_kepala_keluarga ?: '-' }}</p>
                            @if($kk->catatan_bermasalah)
                            <p class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ $kk->catatan_bermasalah }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($kk->status_kk === 'bermasalah')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Bermasalah
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
                                <i class="fas fa-clock mr-1"></i> Sementara
                            </span>
                            @if($kk->kkSementara)
                            <p class="text-xs text-gray-500 mt-1">KK sementara: {{ $kk->kkSementara->nama }}</p>
                            @endif
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-500">
                            @if($kk->kk_bermasalah_sejak)
                            <p>{{ $kk->kk_bermasalah_sejak->format('d M Y') }}</p>
                            <p class="text-xs text-red-400 font-semibold">{{ $kk->harisBermasalah() }} hari</p>
                            @else
                            <span class="text-gray-300">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <a href="{{ route('kk.bermasalah', $kk->nkk) }}"
                               class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg text-xs font-semibold transition-all duration-200 hover:scale-105 shadow-sm">
                                <i class="fas fa-tools mr-1.5"></i> Selesaikan
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($kkList->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $kkList->appends(request()->query())->links() }}
        </div>
        @endif
        @endif

        {{-- ===== TAB CONTENT: RESOLVED (AUDIT) ===== --}}
        @elseif($tab === 'resolved')
        @if($kkList->isEmpty())
        <div class="py-16 text-center text-gray-400">
            <i class="fas fa-history text-5xl text-gray-200 mb-4"></i>
            <p class="text-lg font-semibold">Belum ada riwayat penyelesaian</p>
            <p class="text-sm mt-1">Data KK yang diselesaikan permanen akan muncul di sini</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">NKK Lama</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kepala KK Lama</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">NKK Baru</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Bermasalah Sejak</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Diselesaikan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Audit</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($kkList as $kk)
                    @php
                        $nkkBaru = $kk->mutasiPenyebab?->detail_tambahan['nkk_baru'] ?? null;
                    @endphp
                    <tr class="hover:bg-green-50 transition-colors">
                        <td class="px-5 py-4">
                            <span class="font-mono text-sm text-gray-500 line-through">{{ $kk->nkk }}</span>
                            <span class="ml-2 text-xs bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">Diarsip</span>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-700">{{ $kk->nama_kepala_keluarga ?: '-' }}</td>
                        <td class="px-5 py-4">
                            @if($nkkBaru)
                            <a href="{{ route('kk.show', $nkkBaru) }}"
                               class="font-mono text-sm font-semibold text-green-700 hover:underline">
                                {{ $nkkBaru }}
                            </a>
                            @else
                            <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-500">
                            {{ $kk->kk_bermasalah_sejak?->format('d M Y') ?? '-' }}
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-500">
                            {{ $kk->updated_at->format('d M Y') }}
                            <p class="text-xs text-gray-400">{{ $kk->updated_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-5 py-4">
                            @if($kk->mutasi_penyebab_id)
                            <a href="{{ route('mutasi.data.show', $kk->mutasi_penyebab_id) }}"
                               class="inline-flex items-center px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium transition-colors">
                                <i class="fas fa-file-alt mr-1"></i> Lihat Mutasi
                            </a>
                            @else
                            <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($kkList->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $kkList->appends(request()->query())->links() }}
        </div>
        @endif
        @endif
        @endif
    </div>

</div>
@endsection

