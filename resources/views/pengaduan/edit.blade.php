@extends('layouts.app')

@section('title', 'Edit Pengaduan')
@section('subtitle', 'Ubah status dan prioritas pengaduan')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Pengaduan</h1>
            <p class="text-gray-600 mt-1">Ubah status dan prioritas pengaduan warga</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('pengaduan.show', $pengaduan) }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg flex items-center transition-colors shadow-md">
                <i class="fas fa-eye mr-2"></i>
                Lihat Detail
            </a>
            <a href="{{ route('pengaduan.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg flex items-center transition-colors shadow-md">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <form action="{{ route('pengaduan.update', $pengaduan) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Status -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Pengaduan</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="status" value="baru"
                                   {{ $pengaduan->status === 'baru' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition-all">
                                <div class="text-center">
                                    <i class="fas fa-exclamation-circle text-yellow-600 text-2xl mb-2"></i>
                                    <p class="font-medium text-gray-900">Baru</p>
                                    <p class="text-xs text-gray-500">Belum diproses</p>
                                </div>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input type="radio" name="status" value="diproses"
                                   {{ $pengaduan->status === 'diproses' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                <div class="text-center">
                                    <i class="fas fa-cog text-blue-600 text-2xl mb-2"></i>
                                    <p class="font-medium text-gray-900">Diproses</p>
                                    <p class="text-xs text-gray-500">Sedang ditangani</p>
                                </div>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input type="radio" name="status" value="selesai"
                                   {{ $pengaduan->status === 'selesai' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-green-500 peer-checked:bg-green-50 transition-all">
                                <div class="text-center">
                                    <i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i>
                                    <p class="font-medium text-gray-900">Selesai</p>
                                    <p class="text-xs text-gray-500">Sudah selesai</p>
                                </div>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input type="radio" name="status" value="ditolak"
                                   {{ $pengaduan->status === 'ditolak' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-red-500 peer-checked:bg-red-50 transition-all">
                                <div class="text-center">
                                    <i class="fas fa-times-circle text-red-600 text-2xl mb-2"></i>
                                    <p class="font-medium text-gray-900">Ditolak</p>
                                    <p class="text-xs text-gray-500">Tidak dapat diproses</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Prioritas -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Prioritas Pengaduan</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="prioritas" value="rendah"
                                   {{ $pengaduan->prioritas === 'rendah' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-green-500 peer-checked:bg-green-50 transition-all">
                                <div class="text-center">
                                    <i class="fas fa-arrow-down text-green-600 text-2xl mb-2"></i>
                                    <p class="font-medium text-gray-900">Rendah</p>
                                    <p class="text-xs text-gray-500">Tidak mendesak</p>
                                </div>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input type="radio" name="prioritas" value="sedang"
                                   {{ $pengaduan->prioritas === 'sedang' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition-all">
                                <div class="text-center">
                                    <i class="fas fa-minus text-yellow-600 text-2xl mb-2"></i>
                                    <p class="font-medium text-gray-900">Sedang</p>
                                    <p class="text-xs text-gray-500">Normal</p>
                                </div>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input type="radio" name="prioritas" value="tinggi"
                                   {{ $pengaduan->prioritas === 'tinggi' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-all">
                                <div class="text-center">
                                    <i class="fas fa-arrow-up text-orange-600 text-2xl mb-2"></i>
                                    <p class="font-medium text-gray-900">Tinggi</p>
                                    <p class="text-xs text-gray-500">Penting</p>
                                </div>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input type="radio" name="prioritas" value="darurat"
                                   {{ $pengaduan->prioritas === 'darurat' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-red-500 peer-checked:bg-red-50 transition-all">
                                <div class="text-center">
                                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl mb-2"></i>
                                    <p class="font-medium text-gray-900">Darurat</p>
                                    <p class="text-xs text-gray-500">Sangat mendesak</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Tanggapan -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tanggapan Admin</h3>
                    <div>
                        <label for="tanggapan" class="block text-sm font-medium text-gray-700 mb-2">
                            Berikan tanggapan atau catatan untuk pengaduan ini
                        </label>
                        <textarea name="tanggapan" id="tanggapan" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none"
                                  placeholder="Masukkan tanggapan atau catatan...">{{ old('tanggapan', $pengaduan->tanggapan) }}</textarea>
                        <p class="text-sm text-gray-500 mt-1">
                            Tanggapan akan ditampilkan kepada pelapor dan dapat membantu dalam komunikasi.
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center justify-center transition-colors shadow-md">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('pengaduan.show', $pengaduan) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg flex items-center justify-center transition-colors shadow-md">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Info Pengaduan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pengaduan</h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Judul</label>
                        <p class="text-gray-900">{{ $pengaduan->judul }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Kategori</label>
                        <p class="text-gray-900">{{ ucfirst($pengaduan->kategori) }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Pelapor</label>
                        <p class="text-gray-900">{{ $pengaduan->nama_pelapor }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Dibuat</label>
                        <p class="text-gray-900">{{ $pengaduan->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Deskripsi Singkat -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Deskripsi</h3>
                <p class="text-gray-700 text-sm leading-relaxed">{{ Str::limit($pengaduan->deskripsi, 150) }}</p>
                @if(strlen($pengaduan->deskripsi) > 150)
                <a href="{{ route('pengaduan.show', $pengaduan) }}" class="text-green-600 text-sm hover:text-green-700 mt-2 inline-block">
                    Baca selengkapnya →
                </a>
                @endif
            </div>

            <!-- Foto Pendukung -->
            @if($pengaduan->foto && count($pengaduan->foto) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Foto Pendukung</h3>
                <div class="grid grid-cols-2 gap-2">
                    @foreach(array_slice($pengaduan->foto, 0, 4) as $foto)
                    <img src="{{ Storage::url($foto) }}"
                         alt="Foto pengaduan"
                         class="w-full h-20 object-cover rounded-lg">
                    @endforeach
                </div>
                @if(count($pengaduan->foto) > 4)
                <p class="text-sm text-gray-500 mt-2">+{{ count($pengaduan->foto) - 4 }} foto lainnya</p>
                @endif
            </div>
            @endif

            <!-- Tips -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-3">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Tips
                </h3>
                <ul class="text-sm text-blue-800 space-y-2">
                    <li>• Pilih status yang sesuai dengan kondisi pengaduan</li>
                    <li>• Prioritas darurat untuk masalah yang sangat mendesak</li>
                    <li>• Berikan tanggapan yang jelas dan informatif</li>
                    <li>• Status "Selesai" atau "Ditolak" akan mencatat tanggal tanggapan</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@noncescript
// Auto-save form data
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input, textarea');

    // Load saved data
    inputs.forEach(input => {
        const saved = localStorage.getItem('pengaduan_edit_' + input.name);
        if (saved && !input.value) {
            input.value = saved;
        }
    });

    // Save data on change
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            localStorage.setItem('pengaduan_edit_' + this.name, this.value);
        });
    });

    // Clear saved data on submit
    form.addEventListener('submit', function() {
        inputs.forEach(input => {
            localStorage.removeItem('pengaduan_edit_' + input.name);
        });
    });
});
@endnoncescript

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@noncescript
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
@endnoncescript
@endsection
