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

                    <!-- Pilih Rekening APBDes -->
                    <div>
                        <label for="apbdes_id" class="block text-sm font-medium text-gray-700 mb-2">Rekening APBDes <span class="text-red-500">*</span></label>
                        <select name="apbdes_id" id="apbdes_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('apbdes_id') border-red-300 @enderror" required>
                            <option value="">Pilih Rekening APBDes</option>
                            @forelse($apbdesList as $apbdes)
                                <option value="{{ $apbdes->id }}" {{ old('apbdes_id') == $apbdes->id ? 'selected' : '' }}>
                                    {{ $apbdes->kode_rekening }} - {{ $apbdes->nama_rekening }}
                                    (Sisa: Rp {{ number_format($apbdes->anggaran - $apbdes->realisasi, 0, ',', '.') }})
                                </option>
                            @empty
                                <option value="" disabled>Tidak ada rekening APBDes tersedia</option>
                            @endforelse
                        </select>
                        @error('apbdes_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($apbdesList->isEmpty())
                            <p class="mt-1 text-sm text-yellow-600">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Tidak ada rekening APBDes dengan sisa anggaran tersedia.
                                <a href="{{ route('anggaran.create-tahunan') }}" class="text-blue-600 hover:text-blue-800 underline">Tambah rekening APBDes terlebih dahulu</a>
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Info Box -->
                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Integrasi APBDes</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li><strong>Pilih Rekening:</strong> Proyek akan menggunakan anggaran dari rekening APBDes yang dipilih</li>
                                    <li><strong>Pengurangan Otomatis:</strong> Anggaran proyek akan mengurangi sisa anggaran rekening APBDes</li>
                                    <li><strong>Sinkronisasi:</strong> Realisasi proyek akan mempengaruhi realisasi rekening APBDes</li>
                                    <li><strong>Validasi:</strong> Anggaran proyek tidak boleh melebihi sisa anggaran rekening yang tersedia</li>
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

// Validate project budget against selected APBDes
document.getElementById('apbdes_id').addEventListener('change', function(e) {
    const selectedOption = e.target.options[e.target.selectedIndex];
    const anggaranInput = document.getElementById('anggaran');

    if (selectedOption.value && selectedOption.text.includes('Sisa:')) {
        // Extract remaining budget from option text
        const sisaText = selectedOption.text.match(/Sisa: Rp ([\d.,]+)/);
        if (sisaText) {
            const sisaAnggaran = parseInt(sisaText[1].replace(/[.,]/g, ''));
            anggaranInput.max = sisaAnggaran;
            anggaranInput.title = `Maksimal anggaran: Rp ${sisaAnggaran.toLocaleString('id-ID')}`;

            // Show warning if current budget exceeds remaining
            if (parseInt(anggaranInput.value.replace(/[.,]/g, '')) > sisaAnggaran) {
                anggaranInput.style.borderColor = '#ef4444';
                anggaranInput.style.backgroundColor = '#fef2f2';
            } else {
                anggaranInput.style.borderColor = '';
                anggaranInput.style.backgroundColor = '';
            }
        }
    }
});

// Validate budget on input
document.getElementById('anggaran').addEventListener('input', function(e) {
    const apbdesSelect = document.getElementById('apbdes_id');
    const selectedOption = apbdesSelect.options[apbdesSelect.selectedIndex];

    if (selectedOption.value && selectedOption.text.includes('Sisa:')) {
        const sisaText = selectedOption.text.match(/Sisa: Rp ([\d.,]+)/);
        if (sisaText) {
            const sisaAnggaran = parseInt(sisaText[1].replace(/[.,]/g, ''));
            const currentAnggaran = parseInt(e.target.value.replace(/[.,]/g, ''));

            if (currentAnggaran > sisaAnggaran) {
                e.target.style.borderColor = '#ef4444';
                e.target.style.backgroundColor = '#fef2f2';
            } else {
                e.target.style.borderColor = '';
                e.target.style.backgroundColor = '';
            }
        }
    }
});
</script>
@endpush
