@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-trash-alt text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Sampah Penduduk</h1>
                    <p class="text-green-100 mt-1">Manajemen data penduduk yang terhapus tanpa mutasi resmi</p>
                    <p class="text-green-200 text-sm mt-1">
                        <i class="fas fa-database mr-1"></i>
                        Total: {{ $penduduks->total() }} data sampah
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('penduduk.index') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl transition-all text-sm font-bold border border-white/20">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Penduduk
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl shadow-lg border-0 p-4 sm:p-6">
        <div class="flex items-center gap-3 mb-4">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <i class="fas fa-search text-green-500 mr-2"></i>
                Cari Data Sampah
            </h3>
        </div>
        <form action="{{ route('settings.trash.penduduk.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="flex-[3] w-full">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Cari berdasarkan NIK, Nama, atau No. KK penduduk yang terhapus..." 
                        class="w-full pl-12 pr-4 py-3.5 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm bg-gray-50/50">
                </div>
            </div>
            <div class="flex-1 flex gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-6 py-3.5 bg-green-600 hover:bg-green-700 text-white rounded-2xl shadow-md transition-all text-sm font-bold">
                    <i class="fas fa-filter mr-2"></i> Terapkan
                </button>
                @if(request('search'))
                    <a href="{{ route('settings.trash.penduduk.index') }}" class="inline-flex items-center justify-center px-5 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-2xl transition-all text-sm font-bold">
                        <i class="fas fa-redo"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
        <div class="px-6 py-6 border-b border-gray-100 bg-gradient-to-r from-green-50 to-blue-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-list text-green-500 mr-3"></i>
                        Daftar Antrean Penghapusan
                    </h3>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penduduk</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat & Wilayah</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Hapus</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($penduduks as $p)
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center shadow-md">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900">{{ $p->nama }}</div>
                                    <div class="text-xs font-mono text-gray-500 mt-0.5">NIK: {{ $p->nik }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 font-medium">{{ Str::limit($p->alamat, 40) }}</div>
                            <div class="text-xs text-gray-600">RT {{ $p->rt_label }} / RW {{ $p->rw_label }} ({{ $p->dusun_label }})</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                {{ $p->deleted_at->format('d M Y H:i') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <form action="{{ route('settings.trash.penduduk.restore', $p->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="group flex items-center px-3 py-2 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                                        <i class="fas fa-undo text-xs mr-2"></i>
                                        <span class="text-xs font-bold uppercase tracking-wider">Pulihkan</span>
                                    </button>
                                </form>
                                
                                <button type="button" 
                                        onclick="confirmDelete('{{ $p->id }}', '{{ $p->nama }}')"
                                        class="group flex items-center px-3 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                                    <i class="fas fa-trash-alt text-xs mr-2"></i>
                                    <span class="text-xs font-bold uppercase tracking-wider">Hapus Permanen</span>
                                </button>

                                <form id="delete-form-{{ $p->id }}" action="{{ route('settings.trash.penduduk.force-delete', $p->id) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-box-open text-gray-300 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Tempat Sampah Kosong</h3>
                            <p class="text-sm text-gray-500">Tidak ada data penduduk terhapus yang ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($penduduks->hasPages())
        <div class="px-6 py-6 border-t border-gray-100 bg-gray-50">
            {{ $penduduks->links('vendor.pagination.tailwind') }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script nonce="{{ $csp_nonce }}">
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Konfirmasi Hapus Permanen',
            text: `Tindakan ini akan menghapus data ${name} selamanya dari database. NIK tersebut akan bisa digunakan kembali. Yakin?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus Selamanya!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "{{ session('error') }}",
            timer: 5000,
            showConfirmButton: false
        });
    @endif
</script>
@endpush
@endsection
