@extends('layouts.app')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-600 via-orange-700 to-orange-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-star text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Testimoni Warga</h1>
                    <p class="text-orange-100 mt-1">Kelola testimoni dan rating dari warga desa</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                @can('pelayanan_informasi')
                <a href="{{ route('testimoni.create') }}" class="group flex items-center px-4 py-2.5 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Testimoni
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wide">Total Testimoni</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-comments text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium uppercase tracking-wide">Disetujui</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['approved'] }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl shadow-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium uppercase tracking-wide">Menunggu</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium uppercase tracking-wide">Rating Rata-rata</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($stats['avg_rating'], 1) }} ?</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-star text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="bg-white rounded-2xl shadow-xl border-0 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-filter mr-2 text-orange-500"></i>
            Filter & Pencarian
        </h3>
        <form method="GET" action="{{ route('testimoni.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300"
                           id="search" name="search" value="{{ request('search') }}" placeholder="Cari nama, testimoni, atau RT/RW...">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300" id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div>
                    <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300" id="rating" name="rating">
                        <option value="">Semua Rating</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 ?</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 ?</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 ?</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 ?</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 ?</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl">
                        <i class="fas fa-search mr-2"></i>Cari
                    </button>
                    <a href="{{ route('testimoni.index') }}" class="px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl transition-all duration-300">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Testimonials Table -->
    <div class="bg-white rounded-2xl shadow-xl border-0 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <i class="fas fa-list mr-2 text-orange-500"></i>
                Daftar Testimoni
            </h3>
        </div>
        <div class="p-6">
            @if(isset($testimonis) && $testimonis->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">No</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Nama</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">RT/RW</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Testimoni</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Rating</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Tanggal</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($testimonis as $testimoni)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200">
                                    <td class="py-4 px-4 text-gray-600">{{ $loop->iteration + ($testimonis->currentPage() - 1) * $testimonis->perPage() }}</td>
                                    <td class="py-4 px-4">
                                        <div class="font-semibold text-gray-800">{{ $testimoni->nama }}</div>
                                        <small class="text-gray-500">{{ $testimoni->ip_address }}</small>
                                    </td>
                                    <td class="py-4 px-4 text-gray-600">
                                        @if($testimoni->rt_label || $testimoni->rw_label)
                                            RT {{ $testimoni->rt_label ?? '-' }} / RW {{ $testimoni->rw_label ?? '-' }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="text-gray-700 max-w-xs truncate" title="{{ $testimoni->testimoni }}">
                                            {{ $testimoni->testimoni }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $testimoni->rating ? 'text-yellow-400' : 'text-gray-300' }} text-sm"></i>
                                            @endfor
                                            <span class="ml-2 font-semibold text-gray-700">{{ $testimoni->rating }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        @if($testimoni->status == 'approved')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>Disetujui
                                            </span>
                                        @elseif($testimoni->status == 'pending')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>Menunggu
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i>Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-gray-600">{{ $testimoni->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center space-x-2">
                                            @can('pelayanan_informasi')
                                            <a href="{{ route('testimoni.show', $testimoni) }}" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('pelayanan_informasi')
                                            <a href="{{ route('testimoni.edit', $testimoni) }}" class="p-2 text-yellow-600 hover:bg-yellow-100 rounded-lg transition-colors" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @if($testimoni->status == 'pending')
                                                @can('pelayanan_informasi')
                                                <form action="{{ route('testimoni.approve', $testimoni) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors" title="Setujui" onclick="return confirm('Setujui testimoni ini?')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                                @can('pelayanan_informasi')
                                                <form action="{{ route('testimoni.reject', $testimoni) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="Tolak" onclick="return confirm('Tolak testimoni ini?')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                            @endif
                                            @can('pelayanan_informasi')
                                            <form action="{{ route('testimoni.destroy', $testimoni) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="Hapus" onclick="return confirm('Hapus testimoni ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(isset($testimonis) && $testimonis->hasPages())
                    <div class="mt-6 flex justify-center">
                        {{ $testimonis->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-comments text-2xl text-gray-400"></i>
                    </div>
                    <h5 class="text-lg font-semibold text-gray-600 mb-2">Belum ada testimoni</h5>
                    <p class="text-gray-500">Testimoni dari warga akan muncul di sini setelah disetujui.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@noncescript
// Session messages handled by global component
@endnoncescript
@endsection


