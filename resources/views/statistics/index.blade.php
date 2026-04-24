@extends('layouts.app')

@section('title', 'Statistik Kependudukan')
@section('subtitle', 'Analisis data kependudukan desa Cibatu')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .gradient-blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .gradient-emerald { background: linear-gradient(135deg, #10b981, #047857); }
    .gradient-purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .gradient-orange { background: linear-gradient(135deg, #f97316, #ea580c); }
</style>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Statistik Kependudukan</h1>
            <p class="text-gray-600 mt-1">Analisis data kependudukan desa Cibatu</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="refreshStatistics()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg flex items-center transition-colors shadow-md">
                <i class="fas fa-sync-alt mr-2"></i>
                Refresh Data
            </button>
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center transition-colors shadow-md">
                <i class="fas fa-print mr-2"></i>
                Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="gradient-blue p-6 rounded-xl shadow-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Penduduk</p>
                    <p class="text-4xl font-bold">{{ number_format($totalPenduduk) }}</p>
                    <p class="text-blue-100 text-sm mt-1">Warga Aktif</p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-xl">
                    <i class="fas fa-users text-3xl"></i>
                </div>
            </div>
        </div>

        <div class="gradient-emerald p-6 rounded-xl shadow-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-sm font-medium">Kartu Keluarga</p>
                    <p class="text-4xl font-bold">{{ number_format($totalKK) }}</p>
                    <p class="text-emerald-100 text-sm mt-1">Kepala Keluarga</p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-xl">
                    <i class="fas fa-home text-3xl"></i>
                </div>
            </div>
        </div>

        <div class="gradient-purple p-6 rounded-xl shadow-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Mutasi</p>
                    <p class="text-4xl font-bold">{{ number_format($totalMutasi) }}</p>
                    <p class="text-purple-100 text-sm mt-1">Perubahan Data</p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-xl">
                    <i class="fas fa-exchange-alt text-3xl"></i>
                </div>
            </div>
        </div>

        <div class="gradient-orange p-6 rounded-xl shadow-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Rata-rata per KK</p>
                    <p class="text-4xl font-bold">{{ $totalKK > 0 ? number_format($totalPenduduk / $totalKK, 1) : '0' }}</p>
                    <p class="text-orange-100 text-sm mt-1">Anggota Keluarga</p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-xl">
                    <i class="fas fa-chart-line text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gender Distribution -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Distribusi Jenis Kelamin</h3>
                <div class="bg-blue-100 p-2 rounded-lg">
                    <i class="fas fa-venus-mars text-blue-600"></i>
                </div>
            </div>
            <div class="space-y-4">
                @foreach($genderStats as $gender => $total)
                    @php
                        $percentage = $totalPenduduk > 0 ? round(($total / $totalPenduduk) * 100, 1) : 0;
                        $color = $gender == 'LAKI-LAKI' ? 'blue' : 'pink';
                    @endphp
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="bg-{{ $color }}-100 p-2 rounded-lg">
                                <i class="fas {{ $gender == 'LAKI-LAKI' ? 'fa-male' : 'fa-female' }} text-{{ $color }}-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ $gender == 'LAKI-LAKI' ? 'Laki-laki' : 'Perempuan' }}
                                </p>
                                <p class="text-sm text-gray-500">{{ $percentage }}% dari total</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($total) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Dusun Distribution -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Distribusi per Dusun</h3>
                <div class="bg-green-100 p-2 rounded-lg">
                    <i class="fas fa-map-marker-alt text-green-600"></i>
                </div>
            </div>
            <div class="space-y-4">
                @foreach($dusunStats as $dusun)
                    @php
                        $percentage = $totalPenduduk > 0 ? round(($dusun->total / $totalPenduduk) * 100, 1) : 0;
                        $dusunName = $dusun->dusun ?: 'Belum Ditentukan';
                    @endphp
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-100 p-2 rounded-lg">
                                <i class="fas fa-home text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $dusunName }}</p>
                                <p class="text-sm text-gray-500">{{ $percentage }}% dari total</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($dusun->total) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Age Groups & Marital Status -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Age Groups -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Distribusi Kelompok Usia</h3>
                <div class="bg-purple-100 p-2 rounded-lg">
                    <i class="fas fa-chart-bar text-purple-600"></i>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($ageGroups as $group)
                    @php
                        $percentage = $totalPenduduk > 0 ? round(($group->total / $totalPenduduk) * 100, 1) : 0;
                    @endphp
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg text-center border border-purple-200">
                        <p class="text-sm font-semibold text-purple-700">{{ $group->age_group }}</p>
                        <p class="text-2xl font-bold text-purple-900">{{ number_format($group->total) }}</p>
                        <p class="text-xs text-purple-600">{{ $percentage }}%</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Marital Status -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Status Perkawinan</h3>
                <div class="bg-pink-100 p-2 rounded-lg">
                    <i class="fas fa-heart text-pink-600"></i>
                </div>
            </div>
            <div class="space-y-3">
                @foreach($maritalStats as $marital)
                    @php
                        $percentage = $totalPenduduk > 0 ? round(($marital->total / $totalPenduduk) * 100, 1) : 0;
                        $maritalName = $marital->status_perkawinan ?: 'Belum Diketahui';
                    @endphp
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-pink-50 to-pink-100 rounded-lg border border-pink-200">
                        <div class="flex items-center space-x-3">
                            <div class="bg-pink-200 p-2 rounded-lg">
                                <i class="fas fa-ring text-pink-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $maritalName }}</p>
                                <p class="text-sm text-gray-500">{{ $percentage }}%</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-bold text-gray-900">{{ number_format($marital->total) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- RT Distribution & Family Position -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- RT Distribution -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Distribusi per RT</h3>
                <div class="bg-indigo-100 p-2 rounded-lg">
                    <i class="fas fa-map text-indigo-600"></i>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">
                @foreach($rtStats as $rt)
                    @php
                        $percentage = $totalPenduduk > 0 ? round(($rt->total / $totalPenduduk) * 100, 1) : 0;
                    @endphp
                    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-4 rounded-lg text-center border border-indigo-200">
                        <p class="text-sm font-semibold text-indigo-700">RT {{ $rt->rt }}</p>
                        <p class="text-xl font-bold text-indigo-900">{{ number_format($rt->total) }}</p>
                        <p class="text-xs text-indigo-600">{{ $percentage }}%</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Family Position -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Kedudukan Keluarga</h3>
                <div class="bg-teal-100 p-2 rounded-lg">
                    <i class="fas fa-users-cog text-teal-600"></i>
                </div>
            </div>
            <div class="space-y-3">
                @foreach($familyPositionStats as $position)
                    @php
                        $percentage = $totalPenduduk > 0 ? round(($position->total / $totalPenduduk) * 100, 1) : 0;
                        $positionName = $position->kedudukan_keluarga ?: 'Belum Diketahui';
                    @endphp
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-teal-50 to-teal-100 rounded-lg border border-teal-200">
                        <div class="flex items-center space-x-3">
                            <div class="bg-teal-200 p-2 rounded-lg">
                                <i class="fas fa-user text-teal-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $positionName }}</p>
                                <p class="text-sm text-gray-500">{{ $percentage }}%</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-bold text-gray-900">{{ number_format($position->total) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Religion, Education & Job Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Religion Distribution -->
        @if($religionStats->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Distribusi Agama</h3>
                <div class="bg-yellow-100 p-2 rounded-lg">
                    <i class="fas fa-pray text-yellow-600"></i>
                </div>
            </div>
            <div class="space-y-3">
                @foreach($religionStats->take(5) as $religion)
                    @php
                        $percentage = $totalPenduduk > 0 ? round(($religion->total / $totalPenduduk) * 100, 1) : 0;
                    @endphp
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg border border-yellow-200">
                        <div>
                            <p class="font-medium text-gray-900">{{ $religion->agama }}</p>
                            <p class="text-sm text-gray-500">{{ $percentage }}%</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-gray-900">{{ number_format($religion->total) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Education Distribution -->
        @if($educationStats->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Distribusi Pendidikan</h3>
                <div class="bg-red-100 p-2 rounded-lg">
                    <i class="fas fa-graduation-cap text-red-600"></i>
                </div>
            </div>
            <div class="space-y-3">
                @foreach($educationStats->take(5) as $education)
                    @php
                        $percentage = $totalPenduduk > 0 ? round(($education->total / $totalPenduduk) * 100, 1) : 0;
                    @endphp
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-red-50 to-red-100 rounded-lg border border-red-200">
                        <div>
                            <p class="font-medium text-gray-900">{{ $education->pendidikan }}</p>
                            <p class="text-sm text-gray-500">{{ $percentage }}%</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-gray-900">{{ number_format($education->total) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Job Distribution -->
        @if($jobStats->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Distribusi Pekerjaan</h3>
                <div class="bg-cyan-100 p-2 rounded-lg">
                    <i class="fas fa-briefcase text-cyan-600"></i>
                </div>
            </div>
            <div class="space-y-3">
                @foreach($jobStats->take(5) as $job)
                    @php
                        $percentage = $totalPenduduk > 0 ? round(($job->total / $totalPenduduk) * 100, 1) : 0;
                    @endphp
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-cyan-50 to-cyan-100 rounded-lg border border-cyan-200">
                        <div>
                            <p class="font-medium text-gray-900">{{ $job->pekerjaan }}</p>
                            <p class="text-sm text-gray-500">{{ $percentage }}%</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-gray-900">{{ number_format($job->total) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Mutation Statistics & Recent Mutations -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Mutation Statistics -->
        @if($mutationStats->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Statistik Mutasi</h3>
                <div class="bg-violet-100 p-2 rounded-lg">
                    <i class="fas fa-chart-pie text-violet-600"></i>
                </div>
            </div>
            <div class="space-y-4">
                @foreach($mutationStats as $mutation)
                    @php
                        $percentage = $totalMutasi > 0 ? round(($mutation->total / $totalMutasi) * 100, 1) : 0;
                        $mutationName = ucfirst(str_replace('_', ' ', $mutation->jenis_mutasi));
                        $colors = [
                            'kelahiran' => 'green',
                            'kematian' => 'red',
                            'pindah masuk' => 'blue',
                            'pindah keluar' => 'orange',
                            'pindah rt rw' => 'purple'
                        ];
                        $color = $colors[strtolower($mutationName)] ?? 'gray';
                    @endphp
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-{{ $color }}-50 to-{{ $color }}-100 rounded-lg border border-{{ $color }}-200">
                        <div class="flex items-center space-x-3">
                            <div class="bg-{{ $color }}-200 p-2 rounded-lg">
                                <i class="fas fa-exchange-alt text-{{ $color }}-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $mutationName }}</p>
                                <p class="text-sm text-gray-500">{{ $percentage }}% dari total mutasi</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-bold text-gray-900">{{ number_format($mutation->total) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent Mutations -->
        @if($recentMutations->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Mutasi Terbaru</h3>
                <div class="bg-amber-100 p-2 rounded-lg">
                    <i class="fas fa-clock text-amber-600"></i>
                </div>
            </div>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @foreach($recentMutations as $mutation)
                    @php
                        $mutationName = ucfirst(str_replace('_', ' ', $mutation->jenis_mutasi));
                        $colors = [
                            'kelahiran' => 'green',
                            'kematian' => 'red',
                            'pindah masuk' => 'blue',
                            'pindah keluar' => 'orange',
                            'pindah rt rw' => 'purple'
                        ];
                        $color = $colors[strtolower($mutationName)] ?? 'gray';
                    @endphp
                    <div class="flex items-center space-x-4 p-4 bg-gradient-to-r from-{{ $color }}-50 to-{{ $color }}-100 rounded-lg border border-{{ $color }}-200">
                        <div class="bg-{{ $color }}-200 p-2 rounded-lg">
                            <i class="fas fa-exchange-alt text-{{ $color }}-600 text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">
                                {{ $mutation->penduduk ? $mutation->penduduk->nama : 'Data tidak tersedia' }}
                            </p>
                            <p class="text-sm text-gray-500">{{ $mutationName }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $mutation->tanggal_mutasi ? $mutation->tanggal_mutasi->format('d/m/Y') : '-' }}
                                @if($mutation->alasan)
                                    • {{ Str::limit($mutation->alasan, 30) }}
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>

<script>
function refreshStatistics() {
    if (confirm('Apakah Anda yakin ingin me-refresh data statistik? Data akan diperbarui dari database terbaru.')) {
        // Show loading
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memperbarui...';
        button.disabled = true;

        // Clear cache and reload
        fetch('/admin/statistics/refresh', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload page to show updated data
                window.location.reload();
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal me-refresh data: ' + (data.message || 'Terjadi kesalahan'),
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                button.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan saat me-refresh data',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}
</script>

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
</script>
@endsection
