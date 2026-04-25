@extends('layouts.app')

@section('title', 'Edit Pengeluaran')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Edit Pengeluaran</h1>
            <p class="text-gray-600 mt-1">{{ $pengeluaran->apbdes->kode_rekening }} - {{ $pengeluaran->apbdes->nama_rekening }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('anggaran.histori-pengeluaran', $pengeluaran->apbdes->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="{{ route('anggaran.update-pengeluaran', $pengeluaran->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Nama Pengeluaran -->
            <div>
                <label for="nama_pengeluaran" class="block text-sm font-medium text-gray-700 mb-2">Nama Pengeluaran <span class="text-red-500">*</span></label>
                <input type="text" name="nama_pengeluaran" id="nama_pengeluaran" value="{{ old('nama_pengeluaran', $pengeluaran->nama_pengeluaran) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('nama_pengeluaran') border-red-300 @enderror"
                       placeholder="Contoh: Pembelian Material Bangunan" required>
                @error('nama_pengeluaran')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Jumlah Pengeluaran -->
                <div>
                    <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Pengeluaran (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah', $pengeluaran->jumlah) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('jumlah') border-red-300 @enderror"
                           placeholder="0" min="0" step="0.01" required>
                    @error('jumlah')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Pengeluaran -->
                <div>
                    <label for="tanggal_pengeluaran" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengeluaran <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_pengeluaran" id="tanggal_pengeluaran" value="{{ old('tanggal_pengeluaran', $pengeluaran->tanggal_pengeluaran->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('tanggal_pengeluaran') border-red-300 @enderror"
                           required>
                    @error('tanggal_pengeluaran')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Keterangan -->
            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan Pengeluaran</label>
                <textarea name="keterangan" id="keterangan" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('keterangan') border-red-300 @enderror"
                          placeholder="Deskripsi pengeluaran (opsional)">{{ old('keterangan', $pengeluaran->keterangan) }}</textarea>
                @error('keterangan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Informasi</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Perubahan jumlah pengeluaran akan mempengaruhi realisasi APBDes</li>
                                <li>Pastikan jumlah pengeluaran tidak melebihi sisa anggaran yang tersedia</li>
                                <li>Realisasi akan otomatis diperbarui setelah pengeluaran disimpan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('anggaran.histori-pengeluaran', $pengeluaran->apbdes->id) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@noncescript
// Format number input
document.getElementById('jumlah').addEventListener('input', function(e) {
    let value = e.target.value.replace(/[^\d]/g, '');
    if (value) {
        value = parseInt(value).toLocaleString('id-ID');
        e.target.value = value;
    }
});
@endnoncescript
@endpush
