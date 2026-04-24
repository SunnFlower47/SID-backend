@extends('layouts.app')

@section('title', $berita->judul)
@section('subtitle', 'Detail berita')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-blue-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-xl mr-4">
                        <i class="fas fa-newspaper text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Detail Berita</h2>
                        <p class="text-gray-600">Informasi lengkap berita</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('berita.edit', $berita) }}"
                       class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
                    </a>
                    <a href="{{ route('berita.index') }}"
                       class="px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Status Badges -->
            <div class="flex items-center space-x-4 mb-6">
                @if($berita->status === 'published')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-2"></i>
                        Diterbitkan
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        <i class="fas fa-edit mr-2"></i>
                        Draft
                    </span>
                @endif

                @if($berita->featured)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                        <i class="fas fa-star mr-2"></i>
                        Featured
                    </span>
                @endif
            </div>

            <!-- Judul -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $berita->judul }}</h1>

            <!-- Meta Info -->
            <div class="flex items-center text-sm text-gray-500 mb-6 space-x-6">
                <div class="flex items-center">
                    <i class="fas fa-user mr-2"></i>
                    <span>{{ $berita->user->name ?? 'Admin' }}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-calendar mr-2"></i>
                    <span>{{ $berita->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-link mr-2"></i>
                    <span>Slug: {{ $berita->slug }}</span>
                </div>
            </div>

            <!-- Gambar -->
            @if($berita->gambar)
                <div class="mb-8">
                    <img src="{{ Storage::url($berita->gambar) }}"
                         alt="{{ $berita->judul }}"
                         class="w-full h-64 object-cover rounded-xl shadow-lg">
                </div>
            @endif

            <!-- Konten -->
            <div class="prose prose-lg max-w-none">
                <div class="whitespace-pre-wrap text-gray-700 leading-relaxed">
                    {{ $berita->konten }}
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-8 mt-8 border-t border-gray-200">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('berita.edit', $berita) }}"
                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Berita
                    </a>

                    <form action="{{ route('web-desa.berita.destroy', $berita) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berita ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                            <i class="fas fa-trash mr-2"></i>
                            Hapus Berita
                        </button>
                    </form>
                </div>

                <a href="{{ route('web-desa.berita.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
