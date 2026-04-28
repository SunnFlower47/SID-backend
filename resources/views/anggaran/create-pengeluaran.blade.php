@extends('layouts.app')

@section('title', 'Tambah Pengeluaran Anggaran')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Tambah Pengeluaran Anggaran</h1>
            <p class="text-gray-600 mt-1">Tambah realisasi pengeluaran dari anggaran yang sudah ada</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('transparansi-desa.apbdes') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="{{ route('anggaran.store-pengeluaran') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tahun -->
                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">Tahun Anggaran</label>
                    <select name="tahun" id="tahun" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" onchange="loadApbdesList()">
                        @foreach($tahunList as $tahunOption)
                            <option value="{{ $tahunOption }}" {{ $tahun == $tahunOption ? 'selected' : '' }}>{{ $tahunOption }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Jenis -->
                <div>
                    <label for="jenis" class="block text-sm font-medium text-gray-700 mb-2">Jenis Anggaran</label>
                    <select name="jenis" id="jenis" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" onchange="loadApbdesList()">
                        <option value="belanja" {{ $jenis == 'belanja' ? 'selected' : '' }}>Belanja</option>
                        <option value="pendapatan" {{ $jenis == 'pendapatan' ? 'selected' : '' }}>Pendapatan</option>
                        <option value="pembiayaan" {{ $jenis == 'pembiayaan' ? 'selected' : '' }}>Pembiayaan</option>
                    </select>
                </div>
            </div>

            <!-- APBDes Selection -->
            <div>
                <label for="apbdes_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Rekening Anggaran <span class="text-red-500">*</span></label>
                <select name="apbdes_id" id="apbdes_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('apbdes_id') border-red-300 @enderror" required>
                    <option value="">Pilih Rekening</option>
                    @foreach($apbdesList as $apbdes)
                        <option value="{{ $apbdes->id }}" {{ old('apbdes_id') == $apbdes->id ? 'selected' : '' }}>
                            {{ $apbdes->kode_rekening }} - {{ $apbdes->nama_rekening }}
                            (Anggaran: Rp {{ number_format($apbdes->anggaran, 0, ',', '.') }},
                            Sisa: Rp {{ number_format($apbdes->anggaran - $apbdes->realisasi, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
                @error('apbdes_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Jumlah Pengeluaran -->
                <div>
                    <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Pengeluaran (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('jumlah') border-red-300 @enderror"
                           placeholder="0" min="0" step="0.01" required>
                    @error('jumlah')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Pengeluaran -->
                <div>
                    <label for="tanggal_pengeluaran" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengeluaran <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_pengeluaran" id="tanggal_pengeluaran" value="{{ old('tanggal_pengeluaran', date('Y-m-d')) }}"
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
                          placeholder="Deskripsi pengeluaran (opsional)">{{ old('keterangan') }}</textarea>
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
                                <li>Pengeluaran akan mengurangi sisa anggaran yang tersedia</li>
                                <li>Pastikan jumlah pengeluaran tidak melebihi sisa anggaran</li>
                                <li>Realisasi akan otomatis diperbarui setelah pengeluaran ditambahkan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('transparansi-desa.apbdes') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i> Tambah Pengeluaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@noncescript
function loadApbdesList() {
    const tahun = document.getElementById('tahun').value;
    const jenis = document.getElementById('jenis').value;

    // Reload page with new parameters
    window.location.href = `{{ route('anggaran.create-pengeluaran') }}?tahun=${tahun}&jenis=${jenis}`;
}

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

