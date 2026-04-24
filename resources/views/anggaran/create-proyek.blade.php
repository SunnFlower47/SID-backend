@extends('layouts.app')

@section('title', 'Tambah Proyek Desa')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Tambah Proyek Desa</h1>
            <p class="text-gray-600 mt-1">Tambah proyek pembangunan desa dan hubungkan dengan anggaran</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('transparansi-desa.proyek') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="{{ route('anggaran.store-proyek') }}" class="space-y-6">
            @csrf

            <!-- Informasi Proyek -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Proyek</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Proyek -->
                    <div class="md:col-span-2">
                        <label for="nama_proyek" class="block text-sm font-medium text-gray-700 mb-2">Nama Proyek <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_proyek" id="nama_proyek" value="{{ old('nama_proyek') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('nama_proyek') border-red-300 @enderror"
                               placeholder="Contoh: Pembangunan Jembatan Desa" required>
                        @error('nama_proyek')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Proyek -->
                    <div>
                        <label for="jenis" class="block text-sm font-medium text-gray-700 mb-2">Jenis Proyek <span class="text-red-500">*</span></label>
                        <select name="jenis" id="jenis" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('jenis') border-red-300 @enderror" required>
                            <option value="">Pilih Jenis</option>
                            <option value="infrastruktur" {{ old('jenis') == 'infrastruktur' ? 'selected' : '' }}>Infrastruktur</option>
                            <option value="sosial" {{ old('jenis') == 'sosial' ? 'selected' : '' }}>Sosial</option>
                            <option value="ekonomi" {{ old('jenis') == 'ekonomi' ? 'selected' : '' }}>Ekonomi</option>
                            <option value="lingkungan" {{ old('jenis') == 'lingkungan' ? 'selected' : '' }}>Lingkungan</option>
                            <option value="lainnya" {{ old('jenis') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('jenis')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Lokasi -->
                    <div>
                        <label for="lokasi" class="block text-sm font-medium text-gray-700 mb-2">Lokasi Proyek <span class="text-red-500">*</span></label>
                        <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('lokasi') border-red-300 @enderror"
                               placeholder="Contoh: Dusun Cibatu" required>
                        @error('lokasi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Penanggung Jawab -->
                    <div>
                        <label for="penanggung_jawab" class="block text-sm font-medium text-gray-700 mb-2">Penanggung Jawab <span class="text-red-500">*</span></label>
                        <input type="text" name="penanggung_jawab" id="penanggung_jawab" value="{{ old('penanggung_jawab') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('penanggung_jawab') border-red-300 @enderror"
                               placeholder="Nama penanggung jawab" required>
                        @error('penanggung_jawab')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kontraktor -->
                    <div>
                        <label for="kontraktor" class="block text-sm font-medium text-gray-700 mb-2">Kontraktor</label>
                        <input type="text" name="kontraktor" id="kontraktor" value="{{ old('kontraktor') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('kontraktor') border-red-300 @enderror"
                               placeholder="Nama kontraktor (opsional)">
                        @error('kontraktor')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="mt-6">
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Proyek</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('deskripsi') border-red-300 @enderror"
                              placeholder="Deskripsi detail proyek (opsional)">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Anggaran dan Jadwal -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Anggaran dan Jadwal</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Anggaran -->
                    <div>
                        <label for="anggaran" class="block text-sm font-medium text-gray-700 mb-2">Anggaran Proyek (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="anggaran" id="anggaran" value="{{ old('anggaran') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('anggaran') border-red-300 @enderror"
                               placeholder="0" min="0" step="0.01" required>
                        @error('anggaran')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Mulai -->
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('tanggal_mulai') border-red-300 @enderror"
                               required>
                        @error('tanggal_mulai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Selesai -->
                    <div>
                        <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('tanggal_selesai') border-red-300 @enderror">
                        @error('tanggal_selesai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Integrasi dengan APBDes -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Integrasi dengan APBDes</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tahun Anggaran -->
                    <div>
                        <label for="tahun_anggaran" class="block text-sm font-medium text-gray-700 mb-2">Tahun Anggaran <span class="text-red-500">*</span></label>
                        <select name="tahun_anggaran" id="tahun_anggaran" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('tahun_anggaran') border-red-300 @enderror" required>
                            @for($i = $currentYear; $i <= $currentYear + 5; $i++)
                                <option value="{{ $i }}" {{ old('tahun_anggaran') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        @error('tahun_anggaran')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Link to APBDes -->
                    <div class="flex items-center">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="link_to_apbdes" id="link_to_apbdes" value="1" {{ old('link_to_apbdes') ? 'checked' : '' }}
                                   class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 focus:ring-2">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="link_to_apbdes" class="font-medium text-gray-700">Hubungkan dengan APBDes</label>
                            <p class="text-gray-500">Otomatis membuat/memperbarui entry APBDes untuk proyek ini</p>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">Integrasi APBDes</h3>
                            <div class="mt-2 text-sm text-green-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Jika dicentang, proyek akan otomatis terhubung dengan APBDes</li>
                                    <li>Realisasi proyek akan mempengaruhi realisasi APBDes</li>
                                    <li>Progress proyek akan sinkron dengan persentase realisasi APBDes</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('transparansi-desa.proyek') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-save mr-2"></i> Simpan Proyek
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

// Set minimum end date based on start date
document.getElementById('tanggal_mulai').addEventListener('change', function(e) {
    const endDateInput = document.getElementById('tanggal_selesai');
    endDateInput.min = e.target.value;
});
</script>
@endpush
