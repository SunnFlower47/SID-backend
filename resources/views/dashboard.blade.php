@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6 animate-fade-in pb-10">
    <!-- Green Welcome Header -->
    <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-3xl p-6 sm:p-8 shadow-lg shadow-green-200/50 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center space-x-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full">
                    <span class="w-2 h-2 bg-green-300 rounded-full animate-pulse"></span>
                    <span class="text-[10px] font-bold text-green-50 uppercase tracking-widest">Sistem Informasi Desa</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">
                    Selamat Datang, <span class="text-emerald-300">{{ explode(' ', auth()->user()->name)[0] }}</span>
                </h1>
                <p class="text-green-50/80 text-sm max-w-xl">
                    Ringkasan administrasi dan data kependudukan Desa Cibatu hari ini.
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-right hidden sm:block text-white">
                    <p class="text-xs font-medium text-green-200 uppercase tracking-tight">{{ now()->format('l') }}</p>
                    <p class="text-sm font-bold">{{ now()->format('d F Y') }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20">
                    <i class="fas fa-calendar-check text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Minimal Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $statsData = [
                ['label' => 'Total Penduduk', 'value' => $stats['total_penduduk'], 'icon' => 'fa-users', 'color' => 'blue'],
                ['label' => 'Total Keluarga', 'value' => $stats['total_kk'], 'icon' => 'fa-home', 'color' => 'emerald'],
                ['label' => 'Laki-laki', 'value' => $stats['laki_laki'], 'icon' => 'fa-male', 'color' => 'indigo'],
                ['label' => 'Perempuan', 'value' => $stats['perempuan'], 'icon' => 'fa-female', 'color' => 'pink'],
            ];
        @endphp

        @foreach($statsData as $item)
        <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100 hover:border-{{ $item['color'] }}-200 transition-colors group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-400 mb-1">{{ $item['label'] }}</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($item['value']) }}</h3>
                </div>
                <div class="w-10 h-10 bg-{{ $item['color'] }}-50 text-{{ $item['color'] }}-500 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-110">
                    <i class="fas {{ $item['icon'] }} text-sm"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Charts & Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Letter Status Distribution -->
        <div class="lg:col-span-4 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-gray-900">Layanan Surat</h3>
                <div class="w-8 h-8 bg-orange-50 text-orange-500 rounded-xl flex items-center justify-center text-xs">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <div class="h-48 relative mb-6">
                <canvas id="letterStatusChart"></canvas>
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl">
                    <span class="text-xs text-gray-500 flex items-center">
                        <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></span> Menunggu
                    </span>
                    <span class="text-sm font-bold text-gray-900">{{ $suratStats['pending'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl">
                    <span class="text-xs text-gray-500 flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span> Selesai
                    </span>
                    <span class="text-sm font-bold text-gray-900">{{ $suratStats['selesai'] }}</span>
                </div>
            </div>
        </div>

        <!-- Mutation Trends -->
        <div class="lg:col-span-8 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-gray-900">Grafik Mutasi Penduduk</h3>
                <div class="flex space-x-4">
                    <div class="flex items-center text-[10px] font-bold text-gray-400 uppercase">
                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-1.5"></span> Masuk
                    </div>
                    <div class="flex items-center text-[10px] font-bold text-gray-400 uppercase">
                        <span class="w-2 h-2 bg-rose-500 rounded-full mr-1.5"></span> Keluar
                    </div>
                </div>
            </div>
            <div class="h-64">
                <canvas id="mutationTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions (Modern & Light) -->
    <div class="bg-indigo-50/50 p-6 rounded-3xl border border-indigo-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-bold text-indigo-900">Aksi & Layanan Cepat</h3>
            @can('admin_sistem')
            <form action="{{ route('dashboard.refresh') }}" method="POST">
                @csrf
                <button type="submit" class="text-[10px] font-bold text-indigo-600 bg-white px-3 py-1.5 rounded-xl border border-indigo-100 hover:bg-indigo-600 hover:text-white transition-all shadow-sm uppercase tracking-wider">
                    <i class="fas fa-sync-alt mr-1.5"></i> Sinkronisasi
                </button>
            </form>
            @endcan
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @php
                $quickActions = [
                    ['can' => 'kependudukan', 'route' => 'penduduk.create', 'label' => 'Tambah Warga', 'icon' => 'fa-plus', 'color' => 'blue'],
                    ['can' => 'kependudukan', 'route' => 'penduduk.index', 'label' => 'Data Penduduk', 'icon' => 'fa-users', 'color' => 'green'],
                    ['can' => 'pelayanan_informasi', 'route' => 'admin.surat-pengajuan.index', 'label' => 'Proses Surat', 'icon' => 'fa-envelope', 'color' => 'orange'],
                    ['can' => 'keuangan', 'route' => 'anggaran.create-tahunan', 'label' => 'Input APBDes', 'icon' => 'fa-calculator', 'color' => 'indigo'],
                ];
            @endphp
            @foreach($quickActions as $action)
            @can($action['can'])
            <a href="{{ route($action['route']) }}" class="flex items-center space-x-3 p-4 bg-white rounded-2xl border border-white hover:border-indigo-200 transition-all shadow-sm group">
                <div class="w-10 h-10 bg-{{ $action['color'] }}-50 text-{{ $action['color'] }}-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas {{ $action['icon'] }} text-sm"></i>
                </div>
                <span class="text-xs font-bold text-gray-700">{{ $action['label'] }}</span>
            </a>
            @endcan
            @endforeach
        </div>
    </div>

    <!-- Lower Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Age Distribution -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-900 mb-6">Komposisi Usia</h3>
            <div class="h-56">
                <canvas id="ageGroupChart"></canvas>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-gray-900">Aktivitas Terakhir</h3>
                <a href="{{ route('mutasi.data.index') }}" class="text-[10px] font-bold text-green-600 uppercase tracking-widest">Detail <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
            <div class="space-y-3">
                @forelse($recentMutasi as $mutasi)
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-2xl transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gray-100 text-gray-500 rounded-lg flex items-center justify-center text-[10px]">
                            <i class="fas {{ ['kelahiran' => 'fa-baby', 'kematian' => 'fa-skull', 'pindah_masuk' => 'fa-sign-in-alt', 'pindah_keluar' => 'fa-sign-out-alt'][$mutasi->jenis_mutasi] ?? 'fa-exchange-alt' }}"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-900">{{ $mutasi->penduduk->nama ?? 'N/A' }}</p>
                            <p class="text-[9px] text-gray-400 uppercase">{{ str_replace('_', ' ', $mutasi->jenis_mutasi) }}</p>
                        </div>
                    </div>
                    <span class="text-[9px] font-medium text-gray-400">{{ $mutasi->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <div class="text-center py-6">
                    <p class="text-xs text-gray-400">Tidak ada aktivitas mutasi terbaru</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script nonce="{{ $csp_nonce }}">
document.addEventListener('DOMContentLoaded', function() {
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } }
    };

    // 1. Letter Status
    new Chart(document.getElementById('letterStatusChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Menunggu', 'Diproses', 'Selesai', 'Ditolak'],
            datasets: [{
                data: [{{ $suratStats['pending'] }}, {{ $suratStats['diproses'] }}, {{ $suratStats['selesai'] }}, {{ $suratStats['ditolak'] }}],
                backgroundColor: ['#facc15', '#10b981', '#059669', '#f43f5e'],
                borderWidth: 0,
                cutout: '80%'
            }]
        },
        options: commonOptions
    });

    // 2. Mutation Trends
    new Chart(document.getElementById('mutationTrendChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($mutationTrends['labels']),
            datasets: [
                { label: 'Masuk', data: @json($mutationTrends['masuk']), backgroundColor: '#3b82f6', borderRadius: 8, barThickness: 12 },
                { label: 'Keluar', data: @json($mutationTrends['keluar']), backgroundColor: '#f43f5e', borderRadius: 8, barThickness: 12 }
            ]
        },
        options: {
            ...commonOptions,
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [5, 5], drawBorder: false } },
                x: { grid: { display: false } }
            }
        }
    });

    // 3. Age Groups
    new Chart(document.getElementById('ageGroupChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: ['Balita', 'Anak', 'Remaja', 'Dewasa', 'Lansia'],
            datasets: [{
                data: [{{ $ageGroups['balita'] }}, {{ $ageGroups['anak'] }}, {{ $ageGroups['remaja'] }}, {{ $ageGroups['dewasa'] }}, {{ $ageGroups['lansia'] }}],
                backgroundColor: ['#60a5fa', '#34d399', '#fbbf24', '#818cf8', '#f87171'],
                borderWidth: 4,
                borderColor: '#ffffff'
            }]
        },
        options: {
            ...commonOptions,
            plugins: {
                legend: {
                    display: true,
                    position: 'right',
                    labels: { usePointStyle: true, padding: 15, font: { size: 10, weight: '600' } }
                }
            }
        }
    });
});
</script>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.6s ease-out forwards;
    }
</style>
@endpush
@endsection
