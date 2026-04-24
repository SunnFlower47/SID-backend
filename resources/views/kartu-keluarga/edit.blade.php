@extends('layouts.app')

@section('title', 'Edit Kartu Keluarga')
@section('subtitle', 'Edit data Kartu Keluarga')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-edit text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Edit Kartu Keluarga</h1>
                    <p class="text-green-100 text-sm sm:text-base">NKK: {{ $nkk }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('kartu-keluarga.show', $nkk) }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-eye mr-2"></i>
                    Lihat Detail
                </a>
                <a href="{{ route('kartu-keluarga.index') }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-lg border-0 p-6 sm:p-8">
        <div class="flex items-center space-x-3 mb-8">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-edit text-green-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Form Edit Kartu Keluarga</h3>
                <p class="text-sm text-gray-600">Ubah data Kartu Keluarga dengan benar</p>
            </div>
        </div>

        <form action="{{ route('kartu-keluarga.update', $nkk) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Data Kepala Keluarga -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-crown text-yellow-500 mr-2"></i>
                        Data Kepala Keluarga
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Kepala Keluarga -->
                    <div>
                        <label for="nama_kepala_keluarga" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_kepala_keluarga" id="nama_kepala_keluarga"
                               value="{{ old('nama_kepala_keluarga', $kartuKeluarga->where('kedudukan_keluarga', 'Kepala Keluarga')->first()->nama ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_kepala_keluarga') border-red-500 @enderror"
                               placeholder="Masukkan nama lengkap" required>
                        @error('nama_kepala_keluarga')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                            Alamat Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="alamat" id="alamat"
                               value="{{ old('alamat', $kartuKeluarga->where('kedudukan_keluarga', 'Kepala Keluarga')->first()->alamat ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('alamat') border-red-500 @enderror"
                               placeholder="Masukkan alamat lengkap" required>
                        @error('alamat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- RT -->
                    <div>
                        <label for="rt" class="block text-sm font-medium text-gray-700 mb-2">
                            RT <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="rt" id="rt"
                               value="{{ old('rt', $kartuKeluarga->where('kedudukan_keluarga', 'Kepala Keluarga')->first()->rt ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('rt') border-red-500 @enderror"
                               placeholder="001" maxlength="3" required>
                        @error('rt')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- RW -->
                    <div>
                        <label for="rw" class="block text-sm font-medium text-gray-700 mb-2">
                            RW <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="rw" id="rw"
                               value="{{ old('rw', $kartuKeluarga->where('kedudukan_keluarga', 'Kepala Keluarga')->first()->rw ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('rw') border-red-500 @enderror"
                               placeholder="001" maxlength="3" required>
                        @error('rw')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dusun -->
                    <div class="md:col-span-2">
                        <label for="dusun" class="block text-sm font-medium text-gray-700 mb-2">
                            Dusun <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="dusun" id="dusun"
                               value="{{ old('dusun', $kartuKeluarga->where('kedudukan_keluarga', 'Kepala Keluarga')->first()->dusun ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('dusun') border-red-500 @enderror"
                               placeholder="Masukkan nama dusun" required>
                        @error('dusun')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('kartu-keluarga.show', $nkk) }}"
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Format RT dan RW
document.getElementById('rt').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\D/g, '');
});

document.getElementById('rw').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\D/g, '');
});
</script>
@endpush
@endsection
