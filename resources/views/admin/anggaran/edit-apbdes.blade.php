@extends('layouts.app')

@section('title', 'Edit APBDes')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Edit APBDes</h1>
            <p class="text-gray-600 mt-1">Tahun {{ $apbdes->tahun }} - {{ $apbdes->kode_rekening }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('transparansi-desa.apbdes', ['tahun' => $apbdes->tahun]) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="{{ route('anggaran.update-apbdes', $apbdes->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kode Rekening -->
                <div>
                    <label for="kode_rekening" class="block text-sm font-medium text-gray-700 mb-2">Kode Rekening <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_rekening" id="kode_rekening" value="{{ old('kode_rekening', $apbdes->kode_rekening) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('kode_rekening') border-red-300 @enderror"
                           placeholder="Contoh: 5.2.1.01" required>
                    @error('kode_rekening')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Rekening -->
                <div>
                    <label for="nama_rekening" class="block text-sm font-medium text-gray-700 mb-2">Nama Rekening <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_rekening" id="nama_rekening" value="{{ old('nama_rekening', $apbdes->nama_rekening) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('nama_rekening') border-red-300 @enderror"
                           placeholder="Contoh: Pembangunan Jembatan" required>
                    @error('nama_rekening')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Jenis -->
                <div>
                    <label for="jenis" class="block text-sm font-medium text-gray-700 mb-2">Jenis <span class="text-red-500">*</span></label>
                    <select name="jenis" id="jenis" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('jenis') border-red-300 @enderror" required>
                        <option value="">Pilih Jenis</option>
                        <option value="pendapatan" {{ old('jenis', $apbdes->jenis) == 'pendapatan' ? 'selected' : '' }}>Pendapatan</option>
                        <option value="belanja" {{ old('jenis', $apbdes->jenis) == 'belanja' ? 'selected' : '' }}>Belanja</option>
                        <option value="pembiayaan" {{ old('jenis', $apbdes->jenis) == 'pembiayaan' ? 'selected' : '' }}>Pembiayaan</option>
                    </select>
                    @error('jenis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sumber Dana -->
                <div>
                    <label for="sumber_dana" class="block text-sm font-medium text-gray-700 mb-2">Sumber Dana <span class="text-red-500">*</span></label>
                    <select name="sumber_dana" id="sumber_dana" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('sumber_dana') border-red-300 @enderror" required>
                        <option value="">Pilih Sumber Dana</option>
                        <option value="Dana Desa" {{ old('sumber_dana', $apbdes->sumber_dana) == 'Dana Desa' ? 'selected' : '' }}>Dana Desa</option>
                        <option value="Alokasi Dana Desa" {{ old('sumber_dana', $apbdes->sumber_dana) == 'Alokasi Dana Desa' ? 'selected' : '' }}>Alokasi Dana Desa</option>
                        <option value="Bantuan Keuangan" {{ old('sumber_dana', $apbdes->sumber_dana) == 'Bantuan Keuangan' ? 'selected' : '' }}>Bantuan Keuangan</option>
                        <option value="Pendapatan Asli Desa" {{ old('sumber_dana', $apbdes->sumber_dana) == 'Pendapatan Asli Desa' ? 'selected' : '' }}>Pendapatan Asli Desa</option>
                        <option value="Lainnya" {{ old('sumber_dana', $apbdes->sumber_dana) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('sumber_dana')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Anggaran -->
            <div>
                <label for="anggaran" class="block text-sm font-medium text-gray-700 mb-2">Anggaran (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="anggaran" id="anggaran" value="{{ old('anggaran', $apbdes->anggaran) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('anggaran') border-red-300 @enderror"
                       placeholder="0" min="0" step="0.01" required>
                @error('anggaran')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @if($apbdes->realisasi > 0)
                    <p class="mt-1 text-sm text-yellow-600">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Realisasi saat ini: Rp {{ number_format($apbdes->realisasi, 0, ',', '.') }}.
                        Anggaran tidak boleh kurang dari realisasi yang sudah ada.
                    </p>
                @endif
            </div>

            <!-- Keterangan -->
            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('keterangan') border-red-300 @enderror"
                          placeholder="Keterangan tambahan (opsional)">{{ old('keterangan', $apbdes->keterangan) }}</textarea>
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
                                <li>Perubahan anggaran akan mempengaruhi sisa anggaran yang tersedia</li>
                                <li>Anggaran tidak boleh kurang dari realisasi yang sudah ada</li>
                                <li>Jika ada histori pengeluaran atau proyek terhubung, perubahan akan mempengaruhi data terkait</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('transparansi-desa.apbdes', ['tahun' => $apbdes->tahun]) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
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
<script>
// Format number input
document.getElementById('anggaran').addEventListener('input', function(e) {
    let value = e.target.value.replace(/[^\d]/g, '');
    if (value) {
        value = parseInt(value).toLocaleString('id-ID');
        e.target.value = value;
    }
});
</script>
@endpush


