@extends('layouts.app')

@section('title', 'Tambah Anggaran Tahunan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Tambah Anggaran Tahunan</h1>
            <p class="text-gray-600 mt-1">Tambah data anggaran pendapatan dan belanja desa</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('transparansi-desa.apbdes') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="{{ route('anggaran.store-tahunan') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tahun -->
                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">Tahun Anggaran <span class="text-red-500">*</span></label>
                    <select name="tahun" id="tahun" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('tahun') border-red-300 @enderror" required>
                        @for($i = $currentYear; $i <= $currentYear + 5; $i++)
                            <option value="{{ $i }}" {{ old('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                    @error('tahun')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis -->
                <div>
                    <label for="jenis" class="block text-sm font-medium text-gray-700 mb-2">Jenis Anggaran <span class="text-red-500">*</span></label>
                    <select name="jenis" id="jenis" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('jenis') border-red-300 @enderror" required>
                        <option value="">Pilih Jenis</option>
                        <option value="pendapatan" {{ old('jenis') == 'pendapatan' ? 'selected' : '' }}>Pendapatan</option>
                        <option value="belanja" {{ old('jenis') == 'belanja' ? 'selected' : '' }}>Belanja</option>
                        <option value="pembiayaan" {{ old('jenis') == 'pembiayaan' ? 'selected' : '' }}>Pembiayaan</option>
                    </select>
                    @error('jenis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Sumber Dana -->
                <div>
                    <label for="sumber_dana" class="block text-sm font-medium text-gray-700 mb-2">Sumber Dana <span class="text-red-500">*</span></label>
                    <select name="sumber_dana" id="sumber_dana" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('sumber_dana') border-red-300 @enderror" required>
                        <option value="">Pilih Sumber Dana</option>
                        <optgroup label="Dana Desa (Pemerintah Pusat)">
                            <option value="dana_desa_ad" {{ old('sumber_dana') == 'dana_desa_ad' ? 'selected' : '' }}>Dana Desa - Alokasi Dasar (AD)</option>
                            <option value="dana_desa_af" {{ old('sumber_dana') == 'dana_desa_af' ? 'selected' : '' }}>Dana Desa - Alokasi Formula (AF)</option>
                            <option value="dana_desa_ak" {{ old('sumber_dana') == 'dana_desa_ak' ? 'selected' : '' }}>Dana Desa - Alokasi Kinerja (AK)</option>
                        </optgroup>
                        <optgroup label="Dana Transfer Umum (Pusat)">
                            <option value="dau" {{ old('sumber_dana') == 'dau' ? 'selected' : '' }}>Dana Alokasi Umum (DAU)</option>
                            <option value="dak" {{ old('sumber_dana') == 'dak' ? 'selected' : '' }}>Dana Alokasi Khusus (DAK)</option>
                        </optgroup>
                        <optgroup label="Dana Transfer Khusus (Provinsi)">
                            <option value="dbh" {{ old('sumber_dana') == 'dbh' ? 'selected' : '' }}>Dana Bagi Hasil (DBH)</option>
                            <option value="did" {{ old('sumber_dana') == 'did' ? 'selected' : '' }}>Dana Insentif Daerah (DID)</option>
                        </optgroup>
                        <optgroup label="Pendapatan Asli Desa">
                            <option value="pad" {{ old('sumber_dana') == 'pad' ? 'selected' : '' }}>Pendapatan Asli Desa (PAD)</option>
                        </optgroup>
                    </select>
                    @error('sumber_dana')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kode Rekening -->
                <div>
                    <label for="kode_rekening" class="block text-sm font-medium text-gray-700 mb-2">Kode Rekening <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_rekening" id="kode_rekening" value="{{ old('kode_rekening') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('kode_rekening') border-red-300 @enderror"
                           placeholder="Contoh: 4.1.1.01" required>
                    @error('kode_rekening')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Rekening -->
                <div>
                    <label for="nama_rekening" class="block text-sm font-medium text-gray-700 mb-2">Uraian <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_rekening" id="nama_rekening" value="{{ old('nama_rekening') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('nama_rekening') border-red-300 @enderror"
                           placeholder="Contoh: Pendapatan Asli Desa" required>
                    @error('nama_rekening')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Anggaran -->
                <div>
                    <label for="anggaran" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Anggaran (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="anggaran" id="anggaran" value="{{ old('anggaran') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('anggaran') border-red-300 @enderror"
                           placeholder="0" min="0" step="0.01" required>
                    @error('anggaran')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Keterangan -->
            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('keterangan') border-red-300 @enderror"
                          placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('transparansi-desa.apbdes') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-save mr-2"></i> Simpan Anggaran
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
