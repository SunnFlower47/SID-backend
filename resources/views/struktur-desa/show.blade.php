@extends('layouts.app')

@section('title', 'Detail Struktur Desa')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center mb-4 sm:mb-0">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-sitemap text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">Detail Struktur Desa</h1>
                    <p class="text-green-100 text-sm sm:text-base">Informasi lengkap data struktur organisasi</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('struktur-desa.edit', $strukturDesa) }}" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                <a href="{{ route('struktur-desa.index') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Detail Card -->
    <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
        <!-- Header Info -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $strukturDesa->kategori == 'kepala_desa' ? 'bg-blue-100 text-blue-800' : ($strukturDesa->kategori == 'sekretaris' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                        {{ ucfirst(str_replace('_', ' ', $strukturDesa->kategori)) }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $strukturDesa->status_aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $strukturDesa->status_aktif ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
                <div class="text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    {{ $strukturDesa->created_at->format('d M Y, H:i') }}
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Profile Section -->
            <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-6 mb-8">
                @if($strukturDesa->foto)
                <img src="{{ Storage::url($strukturDesa->foto) }}" alt="{{ $strukturDesa->nama }}" class="w-32 h-32 rounded-xl object-cover shadow-lg">
                @else
                <div class="w-32 h-32 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-4xl">{{ strtoupper(substr($strukturDesa->nama, 0, 1)) }}</span>
                </div>
                @endif
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $strukturDesa->nama }}</h1>
                    <p class="text-xl text-gray-600 mb-2">{{ $strukturDesa->jabatan }}</p>
                    @if($strukturDesa->nik)
                    <p class="text-sm text-gray-500">NIK: {{ $strukturDesa->nik }}</p>
                    @endif
                </div>
            </div>

            <!-- Contact Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kontak</h3>

                    @if($strukturDesa->no_hp)
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-phone text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">No. HP</p>
                            <p class="text-gray-900">{{ $strukturDesa->no_hp }}</p>
                        </div>
                    </div>
                    @endif

                    @if($strukturDesa->email)
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-envelope text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="text-gray-900">{{ $strukturDesa->email }}</p>
                        </div>
                    </div>
                    @endif

                    @if($strukturDesa->alamat)
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-map-marker-alt text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Alamat</p>
                            <p class="text-gray-900">{{ $strukturDesa->alamat }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Lokasi</h3>

                    @if($strukturDesa->rt)
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-home text-orange-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">RT</p>
                            <p class="text-gray-900">{{ $strukturDesa->rt }}</p>
                        </div>
                    </div>
                    @endif

                    @if($strukturDesa->rw)
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-building text-indigo-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">RW</p>
                            <p class="text-gray-900">{{ $strukturDesa->rw }}</p>
                        </div>
                    </div>
                    @endif

                    @if($strukturDesa->dusun)
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-map text-teal-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Dusun</p>
                            <p class="text-gray-900">{{ $strukturDesa->dusun }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Tugas dan Wewenang -->
            @if($strukturDesa->tugas_wewenang)
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tugas dan Wewenang</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700 leading-relaxed">{{ $strukturDesa->tugas_wewenang }}</p>
                </div>
            </div>
            @endif

            <!-- Meta Information -->
            <div class="border-t border-gray-200 pt-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-gray-500">
                    <div>
                        <p class="font-medium text-gray-900">Urutan Tampil</p>
                        <p>{{ $strukturDesa->urutan ?? 'Tidak diatur' }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Dibuat</p>
                        <p>{{ $strukturDesa->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Diupdate</p>
                        <p>{{ $strukturDesa->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-sm text-gray-500">
                    <span>Terakhir diupdate {{ $strukturDesa->updated_at->diffForHumans() }}</span>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    @can('struktur-desa.edit')
                    <a href="{{ route('struktur-desa.edit', $strukturDesa) }}" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Data
                    </a>
                    @endcan
                    @can('struktur-desa.delete')
                    <button onclick="confirmDelete({{ $strukturDesa->id }}, '{{ addslashes($strukturDesa->nama) }}')" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus Data
                    </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@noncescript
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Yakin ingin menghapus data "${name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/struktur-desa/${id}`;

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
@endsection
