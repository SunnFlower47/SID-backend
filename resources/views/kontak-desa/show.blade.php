@extends('layouts.app')

@section('title', 'Detail Kontak Desa')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center mb-4 sm:mb-0">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-phone text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">Detail Kontak Desa</h1>
                    <p class="text-green-100 text-sm sm:text-base">Informasi lengkap data kontak dan komunikasi desa</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('kontak-desa.edit', $kontakDesa) }}" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                <a href="{{ route('kontak-desa.index') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Detail Card -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Header Info -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $kontakDesa->jenis == 'kantor_desa' ? 'bg-blue-100 text-blue-800' : ($kontakDesa->jenis == 'kepala_desa' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                        {{ $kontakDesa->jenis_label }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $kontakDesa->status_aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $kontakDesa->status_aktif ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
                <div class="text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    {{ $kontakDesa->created_at->format('d M Y, H:i') }}
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Profile Section -->
            <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-6 mb-8">
                @if($kontakDesa->foto)
                <img src="{{ Storage::url($kontakDesa->foto) }}" alt="{{ $kontakDesa->nama }}" class="w-32 h-32 rounded-xl object-cover shadow-lg">
                @else
                <div class="w-32 h-32 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-4xl">{{ strtoupper(substr($kontakDesa->nama, 0, 1)) }}</span>
                </div>
                @endif
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $kontakDesa->nama }}</h1>
                    @if($kontakDesa->jabatan)
                    <p class="text-xl text-gray-600 mb-2">{{ $kontakDesa->jabatan }}</p>
                    @endif
                    <p class="text-sm text-gray-500">{{ $kontakDesa->jenis_label }}</p>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kontak</h3>

                    @if($kontakDesa->no_telepon)
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-phone text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">No. Telepon</p>
                            <p class="text-gray-900">{{ $kontakDesa->no_telepon }}</p>
                        </div>
                    </div>
                    @endif

                    @if($kontakDesa->no_hp)
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-mobile-alt text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">No. HP</p>
                            <p class="text-gray-900">{{ $kontakDesa->no_hp }}</p>
                        </div>
                    </div>
                    @endif

                    @if($kontakDesa->email)
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-envelope text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="text-gray-900">{{ $kontakDesa->email }}</p>
                        </div>
                    </div>
                    @endif

                    @if($kontakDesa->website)
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-globe text-indigo-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Website</p>
                            <a href="{{ $kontakDesa->website }}" target="_blank" class="text-blue-600 hover:text-blue-800">{{ $kontakDesa->website }}</a>
                        </div>
                    </div>
                    @endif

                    @if($kontakDesa->whatsapp)
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fab fa-whatsapp text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">WhatsApp</p>
                            <a href="https://wa.me/{{ $kontakDesa->whatsapp }}" target="_blank" class="text-green-600 hover:text-green-800">{{ $kontakDesa->whatsapp }}</a>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Lokasi</h3>

                    @if($kontakDesa->alamat)
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-map-marker-alt text-orange-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Alamat</p>
                            <p class="text-gray-900">{{ $kontakDesa->alamat }}</p>
                        </div>
                    </div>
                    @endif

                    @if($kontakDesa->rt_label)
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-home text-teal-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">RT</p>
                            <p class="text-gray-900">{{ $kontakDesa->rt_label }}</p>
                        </div>
                    </div>
                    @endif

                    @if($kontakDesa->rw_label)
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-building text-cyan-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">RW</p>
                            <p class="text-gray-900">{{ $kontakDesa->rw_label }}</p>
                        </div>
                    </div>
                    @endif

                    @if($kontakDesa->dusun_label)
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-map text-emerald-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Dusun</p>
                            <p class="text-gray-900">{{ $kontakDesa->dusun_label }}</p>
                        </div>
                    </div>
                    @endif

                    @if($kontakDesa->jam_operasional)
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Jam Operasional</p>
                            <p class="text-gray-900">{{ $kontakDesa->jam_operasional }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Social Media -->
            @if($kontakDesa->facebook || $kontakDesa->instagram || $kontakDesa->youtube)
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Media Sosial</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @if($kontakDesa->facebook)
                    <div class="flex items-center p-4 bg-blue-50 rounded-lg">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fab fa-facebook text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Facebook</p>
                            <a href="{{ $kontakDesa->facebook }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">Lihat Profil</a>
                        </div>
                    </div>
                    @endif

                    @if($kontakDesa->instagram)
                    <div class="flex items-center p-4 bg-pink-50 rounded-lg">
                        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fab fa-instagram text-pink-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Instagram</p>
                            <a href="{{ $kontakDesa->instagram }}" target="_blank" class="text-pink-600 hover:text-pink-800 text-sm">Lihat Profil</a>
                        </div>
                    </div>
                    @endif

                    @if($kontakDesa->youtube)
                    <div class="flex items-center p-4 bg-red-50 rounded-lg">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fab fa-youtube text-red-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">YouTube</p>
                            <a href="{{ $kontakDesa->youtube }}" target="_blank" class="text-red-600 hover:text-red-800 text-sm">Lihat Channel</a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Deskripsi -->
            @if($kontakDesa->deskripsi)
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Deskripsi</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700 leading-relaxed">{{ $kontakDesa->deskripsi }}</p>
                </div>
            </div>
            @endif

            <!-- Meta Information -->
            <div class="border-t border-gray-200 pt-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-gray-500">
                    <div>
                        <p class="font-medium text-gray-900">Urutan Tampil</p>
                        <p>{{ $kontakDesa->urutan ?? 'Tidak diatur' }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Dibuat</p>
                        <p>{{ $kontakDesa->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Diupdate</p>
                        <p>{{ $kontakDesa->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-sm text-gray-500">
                    <span>Terakhir diupdate {{ $kontakDesa->updated_at->diffForHumans() }}</span>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('kontak-desa.edit', $kontakDesa) }}" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Data
                    </a>
                    <button onclick="confirmDelete({{ $kontakDesa->id }}, '{{ addslashes($kontakDesa->nama) }}')" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@noncescript
// SweetAlert untuk notifikasi success
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

// SweetAlert untuk konfirmasi delete
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus kontak "${name}"?`,
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
            form.action = `/kontak-desa/${id}`;

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

