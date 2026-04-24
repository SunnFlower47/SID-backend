@extends('layouts.app')

@section('title', 'Manajemen Surat Pengajuan')
@section('subtitle', 'Kelola pengajuan surat dari warga')

@section('content')
<div class="space-y-6" x-data="{
    showStatusModal: false,
    statusUrl: '',
    currentStatus: '',
    
    openStatusModal(id, status) {
        this.statusUrl = '/admin/surat-pengajuan/' + id + '/status';
        this.currentStatus = status;
        this.showStatusModal = true;
    },
    
    closeStatusModal() {
        this.showStatusModal = false;
        this.statusUrl = '';
        this.currentStatus = '';
    }
}">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl border-0 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-file-alt text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Manajemen Surat Pengajuan</h1>
                    <p class="text-green-100 mt-1">Kelola dan proses pengajuan surat dari warga</p>
                </div>
            </div>
        </div>
        <!-- Action Buttons -->
        <div class="mt-6 flex flex-wrap gap-3">
            @can('surat.create')
            <a href="{{ route('admin.surat-pengajuan.create') }}" class="group flex items-center px-4 py-2.5 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                <i class="fas fa-plus mr-2"></i>
                Buat Pengajuan Baru
            </a>
            @endcan
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        @php
            $stats = [
                ['label' => 'Total Pengajuan', 'count' => $pengajuans->total(), 'color' => 'blue', 'icon' => 'fas fa-file-alt'],
                ['label' => 'Menunggu', 'count' => $pengajuans->where('status', 'pending')->count(), 'color' => 'yellow', 'icon' => 'fas fa-clock'],
                ['label' => 'Disetujui', 'count' => $pengajuans->where('status', 'approved')->count(), 'color' => 'green', 'icon' => 'fas fa-check'],
                ['label' => 'Selesai', 'count' => $pengajuans->where('status', 'completed')->count(), 'color' => 'purple', 'icon' => 'fas fa-check-double'],
            ];
        @endphp

        @foreach($stats as $stat)
        <div class="bg-white rounded-2xl shadow-lg border-0 p-4 sm:p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-{{ $stat['color'] }}-500 to-{{ $stat['color'] }}-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="{{ $stat['icon'] }} text-white text-lg sm:text-xl"></i>
                    </div>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-500">{{ $stat['label'] }}</p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ $stat['count'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg border-0 p-4 sm:p-6">
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-filter text-green-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Filter & Pencarian</h3>
                <p class="text-sm text-gray-600">Saring data pengajuan surat berdasarkan kriteria</p>
            </div>
        </div>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">Semua Status</option>
                    @foreach($statusList as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Surat</label>
                <select name="jenis_surat" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">Semua Jenis</option>
                    @foreach($suratTypes as $key => $label)
                        <option value="{{ $key }}" {{ request('jenis_surat') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nomor surat, nama, atau NIK..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-search mr-2"></i>
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nomor Surat
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jenis Surat
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Penduduk
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal Pengajuan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pengajuans as $pengajuan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $pengajuan->nomor_surat }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $pengajuan->surat_type_name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $pengajuan->penduduk->nama }}
                            </div>
                            <div class="text-sm text-gray-500">
                                NIK: {{ $pengajuan->penduduk->nik }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $pengajuan->status_color }}-100 text-{{ $pengajuan->status_color }}-800">
                                {{ $pengajuan->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $pengajuan->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.surat-pengajuan.show', $pengajuan) }}"
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @can('surat.edit')
                                <a href="{{ route('admin.surat-pengajuan.edit', $pengajuan) }}" 
                                   class="text-orange-600 hover:text-orange-900" title="Edit Isi Surat">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>

                                <button @click="openStatusModal({{ $pengajuan->id }}, '{{ $pengajuan->status }}')"
                                        class="text-yellow-600 hover:text-yellow-900" title="Update Status">
                                    <i class="fas fa-tasks"></i>
                                </button>
                                @endcan

                                @can('surat.view')
                                <a href="{{ route('admin.surat-pengajuan.preview', $pengajuan) }}"
                                   target="_blank" class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('admin.surat-pengajuan.pdf', $pengajuan) }}"
                                   target="_blank" class="text-gray-600 hover:text-gray-900" title="Cetak PDF">
                                    <i class="fas fa-print"></i>
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4"></i>
                                <p>Tidak ada pengajuan surat ditemukan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        @if($pengajuans->count() > 0)
        <div class="lg:hidden px-1 sm:px-2 py-4 space-y-3">
            @foreach($pengajuans as $pengajuan)
            <div class="bg-white rounded-2xl shadow-lg border-0 p-4 sm:p-6 hover:shadow-xl transition-all duration-300 w-full">
                <!-- Header with Nomor Surat and Status -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-file-alt text-white text-lg sm:text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0 overflow-hidden">
                            <h3 class="text-base sm:text-lg font-bold text-gray-900 truncate" title="{{ $pengajuan->nomor_surat }}">{{ $pengajuan->nomor_surat }}</h3>
                            <div class="flex items-center space-x-2 mt-1">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $pengajuan->status_color }}-100 text-{{ $pengajuan->status_color }}-800">
                                    {{ $pengajuan->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Information Grid - Mobile Optimized -->
                <div class="space-y-3 mb-4">
                    <!-- Penduduk Info -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-3">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-user text-blue-500 mr-2 text-sm"></i>
                            <span class="text-xs sm:text-sm font-medium text-gray-700">Penduduk</span>
                        </div>
                        <p class="text-sm text-gray-900 font-medium">{{ $pengajuan->penduduk->nama }}</p>
                        <p class="text-xs text-gray-600">{{ $pengajuan->penduduk->nik }}</p>
                    </div>

                    <!-- Jenis Surat dan Tanggal -->
                    <div class="grid grid-cols-2 gap-2">
                        <!-- Jenis Surat -->
                        <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-2.5">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-file-alt text-purple-500 mr-1 text-xs"></i>
                                <span class="text-xs font-medium text-gray-700">Jenis Surat</span>
                            </div>
                            <p class="text-xs text-gray-900">{{ $pengajuan->surat_type_name }}</p>
                        </div>

                        <!-- Tanggal -->
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-2.5">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-calendar text-gray-500 mr-1 text-xs"></i>
                                <span class="text-xs font-medium text-gray-700">Tanggal</span>
                            </div>
                            <p class="text-xs text-gray-900">{{ $pengajuan->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons - Mobile Optimized -->
                <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:gap-2">
                    <!-- Detail & Edit Row -->
                    <div class="flex gap-2">
                        <a href="{{ route('admin.surat-pengajuan.show', $pengajuan) }}" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                            <i class="fas fa-eye mr-2 text-sm"></i>
                            <span class="text-sm font-medium">Detail</span>
                        </a>
                        @can('surat.edit')
                        <a href="{{ route('admin.surat-pengajuan.edit', $pengajuan) }}" class="flex-1 bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                            <i class="fas fa-pencil-alt mr-2 text-sm"></i>
                            <span class="text-sm font-medium">Edit Data</span>
                        </a>
                        <button @click="openStatusModal({{ $pengajuan->id }}, '{{ $pengajuan->status }}')" class="flex-1 bg-gradient-to-r from-yellow-600 to-yellow-700 hover:from-yellow-700 hover:to-yellow-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                            <i class="fas fa-tasks mr-2 text-sm"></i>
                            <span class="text-sm font-medium">Status</span>
                        </button>
                        @endcan
                    </div>

                    <!-- Preview & PDF Row -->
                    <div class="flex gap-2">
                        @can('surat.view')
                        <a href="{{ route('admin.surat-pengajuan.preview', $pengajuan) }}" target="_blank" class="flex-1 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                            <i class="fas fa-eye mr-2 text-sm"></i>
                            <span class="text-sm font-medium">Preview</span>
                        </a>
                        <a href="{{ route('admin.surat-pengajuan.pdf', $pengajuan) }}" target="_blank" class="flex-1 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-3 py-2.5 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-md">
                            <i class="fas fa-print mr-2 text-sm"></i>
                            <span class="text-sm font-medium">Cetak</span>
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- Empty State for Mobile -->
        <div class="lg:hidden bg-white rounded-2xl shadow-lg border-0 p-8 text-center mx-1 sm:mx-2 my-4">
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada pengajuan surat</h3>
                <p class="text-gray-500 mb-6">Belum ada pengajuan surat ditemukan</p>
                @can('surat.create')
                <a href="{{ route('admin.surat-pengajuan.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white text-sm font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Buat Pengajuan Pertama
                </a>
                @endcan
            </div>
        </div>
        @endif

        <!-- Pagination -->
        @if($pengajuans->hasPages())
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
            {{ $pengajuans->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
    
    <!-- Update Status Modal -->
    <div x-show="showStatusModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         style="display: none;">
         
        <div x-show="showStatusModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="relative p-5 border w-96 shadow-xl rounded-2xl bg-white"
             @click.away="closeStatusModal()">
             
            <div class="mt-3">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-edit text-yellow-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Update Status Surat</h3>
                </div>

                <form :action="statusUrl" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" required x-model="currentStatus">
                            <option value="pending">Menunggu Persetujuan</option>
                            <option value="approved">Disetujui</option>
                            <option value="rejected">Ditolak</option>
                            <option value="completed">Selesai</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea name="keterangan" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                  placeholder="Keterangan (opsional)"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="closeStatusModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


