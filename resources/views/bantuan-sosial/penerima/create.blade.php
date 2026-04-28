@extends('layouts.app')

@section('title', 'Bantuan Sosial')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8 mb-8">
        <div class="flex flex-col lg:flex-row items-center justify-between">
            <div class="flex-1 text-center lg:text-left mb-6 lg:mb-0">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-2 flex items-center justify-center lg:justify-start">
                    <i class="fas fa-plus mr-3 text-yellow-300"></i>
                    Tambah Penerima
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
                    <h6 class="text-lg lg:text-xl font-semibold text-gray-900">Form Tambah Penerima</h6>
                </div>
                <div class="p-4 lg:p-8">
                    <form action="{{ route('bantuan-sosial.penerima.store', $bantuanSosial) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Pilih Penduduk -->
                            <div class="md:col-span-2">
                                <label for="penduduk_search" class="block text-sm font-medium text-gray-700 mb-2">
                                    Cari Penduduk <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="penduduk_search" name="penduduk_search"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('penduduk_id') border-red-500 @enderror"
                                           placeholder="Ketik nama atau NIK penduduk..."
                                           value="{{ old('penduduk_search') }}"
                                           autocomplete="off">
                                    <div id="penduduk_search_loading" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                                        <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                    </div>
                                </div>

                                <!-- Search Results -->
                                <div id="penduduk_search_results" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                    <!-- Results will be populated here -->
                                </div>

                                <!-- Selected Penduduk Display -->
                                <div id="selected_penduduk_display" class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg hidden">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-medium text-green-900" id="selected_nama"></div>
                                            <div class="text-sm text-green-700">NIK: <span id="selected_nik"></span></div>
                                            <div class="text-sm text-green-600" id="selected_alamat"></div>
                                        </div>
                                        <button type="button" onclick="clearPendudukSelection()" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Hidden input for penduduk_id -->
                                <input type="hidden" name="penduduk_id" id="penduduk_id" value="{{ old('penduduk_id') }}" required>

                                @error('penduduk_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Sistem Pembayaran -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                   pembayaran bantuan<span class="text-red-500">*</span>
                                </label>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="radio" name="sistem_pembayaran" value="sekali"
                                               {{ old('sistem_pembayaran', 'sekali') == 'sekali' ? 'checked' : '' }}
                                               class="mr-2" onchange="togglePembayaranSistem()">
                                        <span>Sekali Bayar</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="sistem_pembayaran" value="triwulanan"
                                               {{ old('sistem_pembayaran') == 'triwulanan' ? 'checked' : '' }}
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
                                       value="{{ old('nilai_diterima', $bantuanSosial->nilai_bantuan) }}"
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
                                       value="{{ old('nilai_total_triwulanan', $bantuanSosial->nilai_bantuan) }}"
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
                                       value="{{ old('tanggal_penerimaan', date('Y-m-d')) }}"
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
                                               value="{{ old('tanggal_triwulan_1', date('Y-m-d')) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="tanggal_triwulan_2" class="block text-sm text-gray-600 mb-1">Triwulan 2</label>
                                        <input type="date" name="tanggal_triwulan_2" id="tanggal_triwulan_2"
                                               value="{{ old('tanggal_triwulan_2', date('Y-m-d', strtotime('+3 months'))) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="tanggal_triwulan_3" class="block text-sm text-gray-600 mb-1">Triwulan 3</label>
                                        <input type="date" name="tanggal_triwulan_3" id="tanggal_triwulan_3"
                                               value="{{ old('tanggal_triwulan_3', date('Y-m-d', strtotime('+6 months'))) }}"
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
                                    <option value="aktif" {{ old('status_penerimaan', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="ditangguhkan" {{ old('status_penerimaan') == 'ditangguhkan' ? 'selected' : '' }}>Ditangguhkan</option>
                                    <option value="berhenti" {{ old('status_penerimaan') == 'berhenti' ? 'selected' : '' }}>Berhenti</option>
                                </select>
                                @error('status_penerimaan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                            <button type="submit" class="w-full sm:w-auto bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                                <i class="fas fa-save mr-2"></i> Simpan Penerima
                            </button>
                            <a href="{{ route('bantuan-sosial.penerima.index', $bantuanSosial) }}" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                                <i class="fas fa-times mr-2"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Program -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg">
                <div class="px-4 py-4 lg:px-6 lg:py-4 border-b border-gray-200">
                    <h6 class="text-lg font-semibold text-gray-900">Informasi Program</h6>
                </div>
                <div class="p-4 lg:p-6">
                    <div class="space-y-4">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Nama Program</span>
                            <p class="text-sm text-gray-900 font-semibold">{{ $bantuanSosial->nama_program }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Jenis Bantuan</span>
                            <p class="text-sm text-gray-900">{{ $bantuanSosial->jenis_bantuan }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Nilai Bantuan</span>
                            <p class="text-sm text-gray-900 font-semibold text-green-600">{{ $bantuanSosial->nilai_bantuan_formatted }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Periode</span>
                            <p class="text-sm text-gray-900">{{ $bantuanSosial->periode }}</p>
                        </div>
                        @if($bantuanSosial->kuota_penerima)
                        <div>
                            <span class="text-sm font-medium text-gray-500">Kuota Penerima</span>
                            <p class="text-sm text-gray-900">{{ number_format($bantuanSosial->kuota_penerima) }} orang</p>
                        </div>
                        @endif
                        <div>
                            <span class="text-sm font-medium text-gray-500">Status</span>
                            @if($bantuanSosial->status == 'aktif')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $bantuanSosial->status_label }}
                                </span>
                            @elseif($bantuanSosial->status == 'selesai')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $bantuanSosial->status_label }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ $bantuanSosial->status_label }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

    <script>
        let searchTimeout;

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

        // Penduduk search functions
        function searchPenduduk(query) {
            if (query.length < 2) {
                hideSearchResults();
                return;
            }

            showLoading(true);

            fetch(`/api/penduduk/search?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    showLoading(false);
                    if (data.success) {
                        displaySearchResults(data.data);
                    } else {
                        hideSearchResults();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showLoading(false);
                    hideSearchResults();
                });
        }

        function showLoading(show) {
            const loading = document.getElementById('penduduk_search_loading');
            if (show) {
                loading.classList.remove('hidden');
            } else {
                loading.classList.add('hidden');
            }
        }

        function displaySearchResults(penduduks) {
            const resultsContainer = document.getElementById('penduduk_search_results');

            if (penduduks.length === 0) {
                resultsContainer.innerHTML = '<div class="p-3 text-gray-500 text-center">Tidak ada penduduk ditemukan</div>';
            } else {
                resultsContainer.innerHTML = penduduks.map(penduduk => `
                    <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                         onclick="selectPenduduk(${penduduk.id}, '${penduduk.nama}', '${penduduk.nik}', '${penduduk.alamat}')">
                        <div class="font-medium text-gray-900">${penduduk.nama}</div>
                        <div class="text-sm text-gray-600">NIK: ${penduduk.nik}</div>
                        <div class="text-sm text-gray-500">${penduduk.alamat}</div>
                    </div>
                `).join('');
            }

            resultsContainer.classList.remove('hidden');
        }

        function hideSearchResults() {
            document.getElementById('penduduk_search_results').classList.add('hidden');
        }

        function selectPenduduk(id, nama, nik, alamat) {
            // Update hidden input
            document.getElementById('penduduk_id').value = id;

            // Update display
            document.getElementById('selected_nama').textContent = nama;
            document.getElementById('selected_nik').textContent = nik;
            document.getElementById('selected_alamat').textContent = alamat;
            document.getElementById('selected_penduduk_display').classList.remove('hidden');

            // Clear search input
            document.getElementById('penduduk_search').value = '';

            // Hide results
            hideSearchResults();
        }

        function clearPendudukSelection() {
            document.getElementById('penduduk_id').value = '';
            document.getElementById('penduduk_search').value = '';
            document.getElementById('selected_penduduk_display').classList.add('hidden');
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            togglePembayaranSistem();

            // Setup search event listener
            const searchInput = document.getElementById('penduduk_search');
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchPenduduk(this.value);
                }, 300);
            });

            // Hide results when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#penduduk_search') && !e.target.closest('#penduduk_search_results')) {
                    hideSearchResults();
                }
            });
        });
    </script>
@endsection

