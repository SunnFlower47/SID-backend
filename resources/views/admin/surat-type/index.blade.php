@extends('layouts.app')

@section('title', 'Master Jenis Surat')
@section('subtitle', 'Kelola daftar jenis surat dan persyaratannya')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-2xl shadow-xl border-0 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-list-ul text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Master Jenis Surat</h1>
                    <p class="text-purple-100 mt-1">Kelola daftar surat yang bisa diajukan oleh warga</p>
                </div>
            </div>
            @can('settings.edit')
            <div class="mt-6 sm:mt-0 flex flex-wrap gap-3">
                <a href="{{ route('admin.surat-type.create') }}" class="group flex items-center px-4 py-2.5 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Jenis Surat
                </a>
            </div>
            @endcan
        </div>
    </div>

    <!-- Table Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 sm:p-6 mb-6 flex items-start">
        <div class="flex-shrink-0 mt-0.5">
            <i class="fas fa-info-circle text-blue-500 text-lg"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-blue-800">
                Di sini Anda dapat menentukan syarat dokumen (berupa PDF) dan mengatur apakah surat tersebut menggunakan template sistem (otomatis) atau akan diproses manual menggunakan Microsoft Word.
            </p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kode / Nama Surat
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Persyaratan
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Metode
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($suratTypes as $type)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-900">{{ $type->nama }}</div>
                            <div class="text-xs text-gray-500 font-mono mt-1">{{ $type->id }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600 truncate max-w-xs" title="{{ $type->persyaratan }}">
                                {{ $type->persyaratan ?: 'Belum ada persyaratan' }}
                            </p>
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            @if($type->has_template)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-magic mr-1"></i> Otomatis
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-keyboard mr-1"></i> Manual (Word)
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            @if($type->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Non-Aktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            @can('settings.edit')
                            <div class="flex items-center justify-center space-x-3">
                                <a href="{{ route('admin.surat-type.edit', $type->id) }}" class="text-orange-600 hover:text-orange-900 transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.surat-type.destroy', $type->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis surat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada Master Surat</h3>
                                <p class="text-gray-500 mb-6">Silakan tambah jenis surat baru</p>
                                @can('settings.edit')
                                <a href="{{ route('admin.surat-type.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white text-sm font-medium rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all duration-200">
                                    <i class="fas fa-plus mr-2"></i>
                                    Tambah Surat Pertama
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
