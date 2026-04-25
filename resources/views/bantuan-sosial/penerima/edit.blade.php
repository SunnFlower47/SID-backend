@extends('layouts.app')

@section('title', 'Bantuan Sosial')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8 mb-8">
        <div class="flex flex-col lg:flex-row items-center justify-between">
            <div class="flex-1 text-center lg:text-left mb-6 lg:mb-0">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-2 flex items-center justify-center lg:justify-start">
                    <i class="fas fa-edit mr-3 text-yellow-300"></i>
                    Edit Penerima
                </h1>
                <p class="text-green-100 text-sm sm:text-base">{{ $bantuanSosial->nama_program }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('bantuan-sosial.penerima.index', $bantuanSosial) }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg">
                <div class="px-4 py-4 lg:px-8 lg:py-6 border-b border-gray-200">
                    <h6 class="text-lg lg:text-xl font-semibold text-gray-900">Form Edit Penerima</h6>
                </div>
                <div class="p-4 lg:p-8">
                    @php
                        $dataTambahan = is_string($penerima->data_tambahan) ? json_decode($penerima->data_tambahan, true) : $penerima->data_tambahan;
                        $sistemPembayaran = $dataTambahan['sistem_pembayaran'] ?? 'sekali';
                    @endphp

                    <form action="{{ route('bantuan-sosial.penerima.update', [$bantuanSosial, $penerima]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Info Penerima (Read Only) -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Penerima Bantuan
                                </label>
                                <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                                            <i class="fas fa-user text-green-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-900">{{ $penerima->penduduk->nama }}</div>
                                            <div class="text-sm text-gray-600">NIK: {{ $penerima->penduduk->nik }}</div>
                                            <div class="text-sm text-gray-500">{{ $penerima->penduduk->alamat }}</div>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Untuk mengubah penerima, hapus data ini dan buat penerima baru.
                                </p>

                                <!-- Hidden input for penduduk_id -->
                                <input type="hidden" name="penduduk_id" id="penduduk_id" value="{{ $penerima->penduduk_id }}" required>
                            </div>

                            <!-- Sistem Pembayaran -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Pembayaran Bantuan <span class="text-red-500">*</span>
                                </label>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="radio" name="sistem_pembayaran" value="sekali"
                                               {{ old('sistem_pembayaran', $sistemPembayaran) == 'sekali' ? 'checked' : '' }}
                                               class="mr-2" onchange="togglePembayaranSistem()">
                                        <span>Sekali Bayar</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="sistem_pembayaran" value="triwulanan"
                                               {{ old('sistem_pembayaran', $sistemPembayaran) == 'triwulanan' ? 'checked' : '' }}
                                               class="mr-2" onchange="togglePembayaranSistem()">
                                        <span>Triwulanan (3x dalam 1 tahun)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Nilai Diterima -->
                            <div id="nilai_sekali_container">
                                <label for="nilai_diterima" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nilai Diterima (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="nilai_diterima" id="nilai_diterima"
                                       value="{{ old('nilai_diterima', $penerima->nilai_diterima) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('nilai_diterima') border-red-500 @enderror"
                                       placeholder="Masukkan nilai bantuan">
                                @error('nilai_diterima')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nilai Total untuk Triwulanan -->
                            <div id="nilai_triwulanan_container" class="hidden">
                                <label for="nilai_total_triwulanan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nilai Total Bantuan (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="nilai_total_triwulanan" id="nilai_total_triwulanan"
                                       value="{{ old('nilai_total_triwulanan', $penerima->nilai_diterima) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                       placeholder="Masukkan nilai total bantuan" onchange="calculateTriwulanan()">

                                <!-- Display pembagian triwulanan -->
                                <div id="triwulanan_breakdown" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
                                    <h4 class="font-medium text-blue-900 mb-2">Pembagian Triwulanan:</h4>
                                    <div class="space-y-1 text-sm text-blue-800">
                                        <div>Triwulan 1: <span id="triwulan_1_amount">Rp 0</span></div>
                                        <div>Triwulan 2: <span id="triwulan_2_amount">Rp 0</span></div>
                                        <div>Triwulan 3: <span id="triwulan_3_amount">Rp 0</span></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tanggal Penerimaan -->
                            <div id="tanggal_sekali_container">
                                <label for="tanggal_penerimaan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Penerimaan <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tanggal_penerimaan" id="tanggal_penerimaan"
                                       value="{{ old('tanggal_penerimaan', $penerima->tanggal_penerimaan->format('Y-m-d')) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('tanggal_penerimaan') border-red-500 @enderror">
                                @error('tanggal_penerimaan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal Triwulanan -->
                            <div id="tanggal_triwulanan_container" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Pembayaran Triwulanan <span class="text-red-500">*</span>
                                </label>
                                <div class="space-y-3">
                                    <div>
                                        <label for="tanggal_triwulan_1" class="block text-sm text-gray-600 mb-1">Triwulan 1</label>
                                        <input type="date" name="tanggal_triwulan_1" id="tanggal_triwulan_1"
                                               value="{{ old('tanggal_triwulan_1', isset($dataTambahan['triwulan_1']['tanggal']) ? $dataTambahan['triwulan_1']['tanggal'] : date('Y-m-d')) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="tanggal_triwulan_2" class="block text-sm text-gray-600 mb-1">Triwulan 2</label>
                                        <input type="date" name="tanggal_triwulan_2" id="tanggal_triwulan_2"
                                               value="{{ old('tanggal_triwulan_2', isset($dataTambahan['triwulan_2']['tanggal']) ? $dataTambahan['triwulan_2']['tanggal'] : date('Y-m-d', strtotime('+3 months'))) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="tanggal_triwulan_3" class="block text-sm text-gray-600 mb-1">Triwulan 3</label>
                                        <input type="date" name="tanggal_triwulan_3" id="tanggal_triwulan_3"
                                               value="{{ old('tanggal_triwulan_3', isset($dataTambahan['triwulan_3']['tanggal']) ? $dataTambahan['triwulan_3']['tanggal'] : date('Y-m-d', strtotime('+6 months'))) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Status Penerimaan -->
                            <div>
                                <label for="status_penerimaan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status Penerimaan <span class="text-red-500">*</span>
                                </label>
                                <select name="status_penerimaan" id="status_penerimaan" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('status_penerimaan') border-red-500 @enderror" required>
                                    <option value="aktif" {{ old('status_penerimaan', $penerima->status_penerimaan) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="ditangguhkan" {{ old('status_penerimaan', $penerima->status_penerimaan) == 'ditangguhkan' ? 'selected' : '' }}>Ditangguhkan</option>
                                    <option value="berhenti" {{ old('status_penerimaan', $penerima->status_penerimaan) == 'berhenti' ? 'selected' : '' }}>Berhenti</option>
                                </select>
                                @error('status_penerimaan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Keterangan -->
                            <div>
                                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Keterangan
                                </label>
                                <textarea name="keterangan" id="keterangan" rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('keterangan') border-red-500 @enderror"
                                          placeholder="Masukkan keterangan tambahan">{{ old('keterangan', $penerima->keterangan) }}</textarea>
                                @error('keterangan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                            <button type="submit" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                                <i class="fas fa-save mr-2"></i> Update Penerima
                            </button>
                            <a href="{{ route('bantuan-sosial.penerima.index', $bantuanSosial) }}" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                                <i class="fas fa-times mr-2"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Penerima -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg">
                <div class="px-4 py-4 lg:px-6 lg:py-4 border-b border-gray-200">
                    <h6 class="text-lg font-semibold text-gray-900">Informasi Penerima</h6>
                </div>
                <div class="p-4 lg:p-6">
                    <div class="space-y-4">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Nama Penerima</span>
                            <p class="text-sm text-gray-900 font-semibold">{{ $penerima->penduduk->nama }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">NIK</span>
                            <p class="text-sm text-gray-900 font-mono">{{ $penerima->penduduk->nik }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Alamat</span>
                            <p class="text-sm text-gray-900">{{ $penerima->penduduk->alamat }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Nilai Diterima</span>
                            <p class="text-sm text-gray-900 font-semibold text-green-600">{{ $penerima->nilai_diterima_formatted }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Tanggal Penerimaan</span>
                            <p class="text-sm text-gray-900">{{ $penerima->tanggal_penerimaan->format('d F Y') }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Status Saat Ini</span>
                            @if($penerima->status_penerimaan == 'aktif')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $penerima->status_penerimaan_label }}
                                </span>
                            @elseif($penerima->status_penerimaan == 'ditangguhkan')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ $penerima->status_penerimaan_label }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $penerima->status_penerimaan_label }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@noncescript
    function togglePembayaranSistem() {
        const sistemPembayaran = document.querySelector('input[name="sistem_pembayaran"]:checked').value;
        const nilaiSekaliContainer = document.getElementById('nilai_sekali_container');
        const nilaiTriwulananContainer = document.getElementById('nilai_triwulanan_container');
        const tanggalSekaliContainer = document.getElementById('tanggal_sekali_container');
        const tanggalTriwulananContainer = document.getElementById('tanggal_triwulanan_container');

        if (sistemPembayaran === 'sekali') {
            nilaiSekaliContainer.classList.remove('hidden');
            nilaiTriwulananContainer.classList.add('hidden');
            tanggalSekaliContainer.classList.remove('hidden');
            tanggalTriwulananContainer.classList.add('hidden');

            // Set required attributes
            document.getElementById('nilai_diterima').setAttribute('required', 'required');
            document.getElementById('tanggal_penerimaan').setAttribute('required', 'required');
            document.getElementById('nilai_total_triwulanan').removeAttribute('required');
        } else {
            nilaiSekaliContainer.classList.add('hidden');
            nilaiTriwulananContainer.classList.remove('hidden');
            tanggalSekaliContainer.classList.add('hidden');
            tanggalTriwulananContainer.classList.remove('hidden');

            // Set required attributes
            document.getElementById('nilai_diterima').removeAttribute('required');
            document.getElementById('tanggal_penerimaan').removeAttribute('required');
            document.getElementById('nilai_total_triwulanan').setAttribute('required', 'required');

            // Calculate triwulanan on load
            calculateTriwulanan();
        }
    }

    function calculateTriwulanan() {
        const totalAmount = parseFloat(document.getElementById('nilai_total_triwulanan').value) || 0;
        const perTriwulan = Math.floor(totalAmount / 3);
        const remainder = totalAmount % 3;

        const triwulan1 = perTriwulan + (remainder >= 1 ? 1 : 0);
        const triwulan2 = perTriwulan + (remainder >= 2 ? 1 : 0);
        const triwulan3 = perTriwulan;

        document.getElementById('triwulan_1_amount').textContent = 'Rp ' + triwulan1.toLocaleString('id-ID');
        document.getElementById('triwulan_2_amount').textContent = 'Rp ' + triwulan2.toLocaleString('id-ID');
        document.getElementById('triwulan_3_amount').textContent = 'Rp ' + triwulan3.toLocaleString('id-ID');

        document.getElementById('triwulanan_breakdown').classList.remove('hidden');
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        togglePembayaranSistem();
    });
@endnoncescript
@endsection
