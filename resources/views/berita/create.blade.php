@extends('layouts.app')

@section('title', 'Tambah Berita Baru')
@section('subtitle', 'Buat berita atau pengumuman untuk desa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-newspaper text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Tambah Berita Baru</h1>
                    <p class="text-green-100 mt-1">Buat berita atau pengumuman untuk desa</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('berita.index') }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <form action="{{ route('berita.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6 space-y-6">
                    <!-- Judul -->
                    <div>
                        <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">
                            Judul Berita <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="judul"
                               name="judul"
                               value="{{ old('judul') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('judul') border-red-500 @enderror"
                               placeholder="Masukkan judul berita"
                               required>
                        @error('judul')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kategori dan Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select id="kategori"
                                    name="kategori"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('kategori') border-red-500 @enderror"
                                    required>
                                <option value="">Pilih Kategori</option>
                                <option value="berita" {{ old('kategori') == 'berita' ? 'selected' : '' }}>Berita</option>
                                <option value="pengumuman" {{ old('kategori') == 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                                <option value="agenda" {{ old('kategori') == 'agenda' ? 'selected' : '' }}>Agenda</option>
                            </select>
                            @error('kategori')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status
                            </label>
                            <select id="status"
                                    name="status"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('status') border-red-500 @enderror">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                            </select>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Konten -->
                    <div>
                        <label for="konten" class="block text-sm font-medium text-gray-700 mb-2">
                            Konten Berita <span class="text-red-500">*</span>
                        </label>
                        <textarea id="konten"
                                  name="konten"
                                  rows="10"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('konten') border-red-500 @enderror"
                                  placeholder="Tulis konten berita di sini..."
                                  required>{{ old('konten') }}</textarea>
                        @error('konten')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gambar -->
                    <div>
                        <label for="gambar" class="block text-sm font-medium text-gray-700 mb-2">
                            Gambar Berita
                        </label>
                        <input type="file"
                               id="gambar"
                               name="gambar"
                               accept="image/*"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('gambar') border-red-500 @enderror"
                               onchange="validateFileSize(this)">
                        <p class="mt-2 text-sm text-gray-500">Format: JPG, PNG, GIF. Maksimal 5MB</p>
                        @error('gambar')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tombol Submit -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('berita.index') }}"
                           class="px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-6 py-3 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Berita
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@noncescript
function validateFileSize(input) {
    const maxSize = 5 * 1024 * 1024; // 5MB in bytes
    const file = input.files[0];

    if (file && file.size > maxSize) {
        alert('Ukuran file terlalu besar! Maksimal 5MB.');
        input.value = ''; // Clear the input
        return false;
    }

    // Show file size info
    if (file) {
        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
        const infoElement = input.parentNode.querySelector('.file-info');
        if (infoElement) {
            infoElement.remove();
        }

        const info = document.createElement('p');
        info.className = 'file-info mt-1 text-sm text-blue-600';
        info.textContent = `Ukuran file: ${fileSizeMB} MB`;
        input.parentNode.appendChild(info);
    }

    return true;
}

// Add form validation before submit
document.querySelector('form').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('gambar');
    if (fileInput.files.length > 0) {
        if (!validateFileSize(fileInput)) {
            e.preventDefault();
            return false;
        }
    }
});
@endnoncescript
@endpush

