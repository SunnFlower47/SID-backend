@extends('layouts.app')

@section('title', 'Detail Kartu Keluarga')
@section('subtitle', 'Detail anggota keluarga')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-id-card text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Detail Kartu Keluarga</h1>
                    <p class="text-green-100 text-sm sm:text-base">NKK: {{ $nkk }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('kartu-keluarga.index') }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
                @can('kartu-keluarga.edit')
                <a href="{{ route('kartu-keluarga.edit', $nkk) }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-edit mr-2"></i>
                    Edit KK
                </a>
                @endcan
                <button onclick="updateKepalaKeluarga()"
                        class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                        <i class="fas fa-user-crown mr-2"></i>
                        Update Kepala Keluarga
                    </button>
                </div>
            </div>
        </div>

        <!-- Kepala Keluarga Info -->
        @if($kepalaKeluarga)
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-crown text-yellow-500 mr-2"></i>
                    Kepala Keluarga
                </h3>
                @can('penduduk.view')
                <a href="{{ route('penduduk.show', $kepalaKeluarga->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-sm">
                    <i class="fas fa-user-circle mr-2"></i>
                    Detail Penduduk
                </a>
                @endcan
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Nama Lengkap</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $kepalaKeluarga->nama }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">NIK</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $kepalaKeluarga->nik }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Jenis Kelamin</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $kepalaKeluarga->jenis_kelamin }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Tempat, Tanggal Lahir</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $kepalaKeluarga->tempat_lahir }}, {{ \Carbon\Carbon::parse($kepalaKeluarga->tanggal_lahir)->format('d F Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Agama</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $kepalaKeluarga->agama }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status Perkawinan</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $kepalaKeluarga->status_perkawinan }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Pekerjaan</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $kepalaKeluarga->pekerjaan }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Pendidikan</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $kepalaKeluarga->pendidikan }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status</label>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            @if($kepalaKeluarga->status === 'aktif') bg-green-100 text-green-800
                            @elseif($kepalaKeluarga->status === 'meninggal') bg-red-100 text-red-800
                            @elseif($kepalaKeluarga->status === 'pindah') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($kepalaKeluarga->status) }}
                        </span>
                    </div>
                </div>
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-500">Alamat</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $kepalaKeluarga->alamat }}</p>
                    <p class="text-sm text-gray-500">RT {{ $kepalaKeluarga->rt_label }} / RW {{ $kepalaKeluarga->rw_label }}, {{ $kepalaKeluarga->dusun_label }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Anggota Keluarga -->
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-users text-blue-500 mr-2"></i>
                    Anggota Keluarga ({{ $anggotaKeluarga->count() }})
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hubungan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kelamin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Lahir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($anggotaKeluarga as $anggota)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $anggota->nama }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $anggota->nik }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $anggota->kedudukan_keluarga ?: 'Tidak diketahui' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $anggota->jenis_kelamin }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($anggota->tanggal_lahir)->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($anggota->deleted_at)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-trash mr-1"></i>
                                    Terhapus
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Aktif
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @can('penduduk.view')
                                <a href="{{ route('penduduk.show', $anggota->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                                    <i class="fas fa-eye mr-1"></i>
                                    Detail
                                </a>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-user-slash text-4xl mb-4"></i>
                                <p class="text-lg">Tidak ada anggota keluarga</p>
                                <p class="text-sm">Hanya kepala keluarga yang terdaftar</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary -->
        <div class="mt-6 bg-gray-50 rounded-lg p-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Keluarga</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $kartuKeluarga->count() }}</div>
                    <div class="text-sm text-gray-500">Total Anggota</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $kartuKeluarga->whereNull('deleted_at')->count() }}</div>
                    <div class="text-sm text-gray-500">Aktif</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">{{ $kartuKeluarga->whereNotNull('deleted_at')->count() }}</div>
                    <div class="text-sm text-gray-500">Terhapus</div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
@noncescript
function updateKepalaKeluarga() {
    if (confirm('Apakah Anda yakin ingin memperbarui kepala keluarga? Sistem akan mencari kandidat kepala keluarga baru berdasarkan prioritas.')) {
        fetch(`/kartu-keluarga/{{ $nkk }}/update-kepala-keluarga`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    html: `Kepala keluarga berhasil diperbarui!<br><br>Kepala keluarga baru: ${data.data.nama_kepala_keluarga_baru}<br>NIK: ${data.data.nik_kepala_keluarga_baru}<br>Kedudukan sebelumnya: ${data.data.kedudukan_sebelumnya}`,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
                location.reload();
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal memperbarui kepala keluarga: ' + data.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan saat memperbarui kepala keluarga',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    }
}
@endnoncescript
@endpush

