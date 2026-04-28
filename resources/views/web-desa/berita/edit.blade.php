@extends('layouts.app')

@section('title', 'Edit Berita')
@section('subtitle', 'Edit berita untuk website desa')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-blue-50">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-xl mr-4">
                    <i class="fas fa-edit text-green-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Edit Berita</h2>
                    <p class="text-gray-600">Edit berita atau pengumuman untuk website desa</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('web-desa.berita.update', $berita) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Judul -->
                <div>
                    <label for="judul" class="block text-sm font-semibold text-gray-700 mb-2">Judul Berita *</label>
                    <input type="text"
                           id="judul"
                           name="judul"
                           value="{{ old('judul', $berita->judul) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('judul') border-red-500 @enderror"
                           placeholder="Masukkan judul berita"
                           required>
                    @error('judul')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Konten -->
                <div>
                    <label for="konten" class="block text-sm font-semibold text-gray-700 mb-2">Konten Berita *</label>
                    <textarea id="konten"
                              name="konten"
                              rows="10"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('konten') border-red-500 @enderror"
                              placeholder="Tulis konten berita di sini..."
                              required>{{ old('konten', $berita->konten) }}</textarea>
                    @error('konten')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gambar -->
                <div>
                    <label for="gambar" class="block text-sm font-semibold text-gray-700 mb-2">Gambar Berita</label>

                    @if($berita->gambar)
                        <div class="mb-4">
                            <img src="{{ Storage::url($berita->gambar) }}" alt="Gambar saat ini" class="w-32 h-32 object-cover rounded-lg">
                            <p class="text-sm text-gray-500 mt-2">Gambar saat ini</p>
                        </div>
                    @endif

                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-green-400 transition-colors duration-200">
                        <div class="space-y-1 text-center">
                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                            <div class="flex text-sm text-gray-600">
                                <label for="gambar" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                                    <span>Upload gambar baru</span>
                                    <input id="gambar" name="gambar" type="file" class="sr-only" accept="image/*">
                                </label>
                                <p class="pl-1">atau drag & drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF hingga 2MB</p>
                        </div>
                    </div>
                    @error('gambar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status dan Featured -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status *</label>
                        <select id="status"
                                name="status"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('status') border-red-500 @enderror">
                            <option value="draft" {{ old('status', $berita->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $berita->status) == 'published' ? 'selected' : '' }}>Diterbitkan</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <div class="flex items-center h-5">
                            <input id="featured"
                                   name="featured"
                                   type="checkbox"
                                   value="1"
                                   {{ old('featured', $berita->featured) ? 'checked' : '' }}
                                   class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="featured" class="font-medium text-gray-700">Berita Unggulan</label>
                            <p class="text-gray-500">Tampilkan di halaman utama website</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100 mt-8">
                <a href="{{ route('berita.index') }}"
                   class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors duration-200">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Update Berita
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

