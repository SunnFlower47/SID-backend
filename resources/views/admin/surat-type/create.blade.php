@extends('layouts.app')

@section('title', 'Tambah Jenis Surat')
@section('subtitle', 'Buat master jenis surat baru')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-2xl shadow-xl border-0 p-6 sm:p-8">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                <i class="fas fa-plus-circle text-yellow-300 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Tambah Jenis Surat</h1>
                <p class="text-purple-100 mt-1">Tentukan nama, syarat, dan metode pemrosesan surat baru.</p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
        <form action="{{ route('admin.surat-type.store') }}" method="POST" class="p-6 sm:p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Kode Surat -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ID / Kode Surat <span class="text-red-500">*</span></label>
                    <input type="text" name="id" value="{{ old('id') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('id') border-red-500 @enderror"
                           placeholder="Contoh: surat-ahli-waris">
                    <p class="mt-1 text-xs text-gray-500">Gunakan huruf kecil dan tanda hubung (slug).</p>
                    @error('id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Surat -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Surat <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('nama') border-red-500 @enderror"
                           placeholder="Contoh: Surat Ahli Waris">
                    @error('nama')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Singkat</label>
                <input type="text" name="deskripsi" value="{{ old('deskripsi') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                       placeholder="Contoh: Surat keterangan untuk pembagian hak waris">
            </div>

            <!-- Persyaratan -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Persyaratan Dokumen (Untuk Warga)</label>
                <textarea name="persyaratan" rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                          placeholder="Contoh:&#10;1. Fotokopi KTP&#10;2. Fotokopi KK&#10;3. Pengantar RT/RW&#10;Semua dokumen dijadikan 1 file PDF.">{{ old('persyaratan') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Keterangan ini akan muncul di Web Desa saat warga memilih jenis surat ini.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 bg-gray-50 p-5 rounded-xl border border-gray-200">
                <!-- Metode Pemrosesan -->
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-3">Metode Pemrosesan</label>
                    <label class="flex items-center cursor-pointer group">
                        <div class="relative">
                            <input type="hidden" name="has_template" value="0">
                            <input type="checkbox" name="has_template" value="1" class="sr-only peer" id="hasTemplate" {{ old('has_template') ? 'checked' : '' }}>
                            <div class="block bg-gray-300 w-14 h-8 rounded-full transition-colors group-hover:bg-gray-400 peer-checked:bg-purple-600"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform peer-checked:translate-x-6"></div>
                        </div>
                        <div class="ml-3">
                            <span class="block text-sm font-medium text-gray-700">Gunakan Template Otomatis</span>
                            <span class="block text-xs text-gray-500">Jika mati, admin proses manual via Microsoft Word</span>
                        </div>
                    </label>
                </div>

                <!-- Status Aktif -->
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-3">Status Publikasi</label>
                    <label class="flex items-center cursor-pointer group">
                        <div class="relative">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" id="isActive" checked>
                            <div class="block bg-gray-300 w-14 h-8 rounded-full transition-colors group-hover:bg-gray-400 peer-checked:bg-green-500"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform peer-checked:translate-x-6"></div>
                        </div>
                        <div class="ml-3">
                            <span class="block text-sm font-medium text-gray-700">Tampilkan di Web Desa</span>
                            <span class="block text-xs text-gray-500">Warga bisa mengajukan surat ini</span>
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Tambahan CSS untuk Switch -->
            <style>
                input:checked ~ .dot {
                    transform: translateX(100%);
                }
            </style>

            <!-- Actions -->
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.surat-type.index') }}" 
                   class="w-full sm:w-auto px-6 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 font-medium text-center transition-all duration-300">
                    Batal
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-medium rounded-xl hover:from-purple-700 hover:to-purple-800 shadow-md hover:shadow-lg transition-all duration-300">
                    <i class="fas fa-save mr-2"></i>Simpan Jenis Surat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
