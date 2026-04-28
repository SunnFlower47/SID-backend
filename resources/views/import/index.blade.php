@extends('layouts.app')

@section('title', 'Import Data')
@section('subtitle', 'Import data dari Excel ke sistem')

@section('content')
<div class="space-y-6">
    <div>
        <!-- Header Card -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white mb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                <div class="flex items-center mb-4 sm:mb-0">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mr-4">
                        <i class="fas fa-file-import text-2xl text-yellow-300"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold">Import Data</h1>
                        <p class="text-green-100 text-sm sm:text-base mt-1">Import data dari file Excel ke sistem</p>
                    </div>
                </div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-medium rounded-xl transition-all duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Import Forms -->
            <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
                <div class="bg-gradient-to-r from-orange-50 to-orange-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-upload text-orange-600 mr-3"></i>
                        Import Data
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-6">

                        <!-- Import Penduduk -->
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-users text-white"></i>
                                </div>
                                <h6 class="text-sm font-semibold text-gray-900">Import Data Penduduk</h6>
                            </div>
                            <form id="pendudukImportForm" action="{{ route('import.penduduk') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label for="penduduk_file" class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel</label>
                                    <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white" id="penduduk_file" name="file" accept=".xlsx,.xls" required>
                                    <p class="text-xs text-gray-500 mt-1">Format: .xlsx atau .xls (Max: 10MB). Preview otomatis saat file dipilih.</p>
                                </div>

                                <div id="penduduk_preview_loading" class="hidden mb-3 text-xs text-blue-700 bg-blue-100 border border-blue-200 rounded-lg px-3 py-2">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>Memproses preview...
                                </div>

                                <div id="penduduk_preview_box" class="hidden mb-4 bg-white border border-blue-200 rounded-lg p-3">
                                    <div class="grid grid-cols-3 gap-2 text-xs mb-2">
                                        <div class="bg-gray-50 rounded p-2">Total: <b id="penduduk_sum_total">0</b></div>
                                        <div class="bg-green-50 text-green-700 rounded p-2">Valid: <b id="penduduk_sum_valid">0</b></div>
                                        <div class="bg-red-50 text-red-700 rounded p-2">Invalid: <b id="penduduk_sum_invalid">0</b></div>
                                    </div>

                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs mb-2">
                                        <div class="bg-red-50 rounded p-2 border border-red-100">Error NIK: <b id="err_nik">0</b></div>
                                        <div class="bg-red-50 rounded p-2 border border-red-100">Error Nama: <b id="err_nama">0</b></div>
                                        <div class="bg-red-50 rounded p-2 border border-red-100">Error No. KK: <b id="err_nkk">0</b></div>
                                        <div class="bg-red-50 rounded p-2 border border-red-100">Error Wilayah: <b id="err_wilayah">0</b></div>
                                    </div>

                                    <p id="penduduk_preview_note" class="text-xs text-gray-500 mb-2"></p>

                                    <div id="penduduk_invalid_wrap" class="hidden mb-4">
                                        <p class="text-xs font-semibold text-red-700 mb-1 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i> Detail baris invalid:
                                        </p>
                                        <div id="invalid-scroll-box" class="border border-red-100 rounded-md bg-red-50/40 p-2" style="max-height:200px; overflow-y:auto;">
                                            <ul id="penduduk_invalid_list" class="list-disc ml-4 text-[10px] text-red-700 space-y-1"></ul>
                                        </div>
                                    </div>

                                    <div id="penduduk_valid_wrap" class="hidden mb-4">
                                        <p class="text-xs font-semibold text-green-700 mb-1 flex items-center">
                                            <i class="fas fa-check-circle mr-1"></i> Detail baris valid (Siap Import):
                                        </p>
                                        <div id="valid-scroll-box" class="border border-green-100 rounded-md bg-green-50/40 p-2" style="max-height:200px; overflow-y:auto;">
                                            <ul id="penduduk_valid_list" class="list-disc ml-4 text-[10px] text-green-700 space-y-1"></ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-2 flex-wrap">
                                    <button type="button" id="penduduk_preview_btn" class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg transition-colors">
                                        <i class="fas fa-arrows-rotate mr-2"></i>Refresh Preview
                                    </button>
                                    <button type="button" id="penduduk_download_invalid_btn" class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors" disabled>
                                        <i class="fas fa-file-circle-xmark mr-2"></i>Download Invalid Rows
                                    </button>
                                    <button type="submit" id="penduduk_import_btn" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors" disabled>
                                        <i class="fas fa-upload mr-2"></i>Import Data Valid
                                    </button>
                                    <a href="{{ route('export-import.template', 'penduduk') }}" class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors">
                                        <i class="fas fa-download mr-2"></i>Template
                                    </a>
                                </div>
                            </form>
                        </div>

                        <!-- Import Bantuan Sosial -->
                        <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-xl p-4 border border-red-200">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-heart text-white"></i>
                                </div>
                                <h6 class="text-sm font-semibold text-gray-900">Import Bantuan Sosial</h6>
                            </div>
                            <form action="{{ route('import.bantuan-sosial') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label for="bantuan_file" class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel</label>
                                    <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500" id="bantuan_file" name="file" accept=".xlsx,.xls" required>
                                    <p class="text-xs text-gray-500 mt-1">Format: .xlsx atau .xls (Max: 10MB)</p>
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors">
                                        <i class="fas fa-upload mr-2"></i>Import
                                    </button>
                                    <a href="{{ route('export-import.template', 'bantuan_sosial') }}" class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors">
                                        <i class="fas fa-download mr-2"></i>Template
                                    </a>
                                </div>
                            </form>
                        </div>

                        <!-- Import UMKM -->
                        <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-store text-white"></i>
                                </div>
                                <h6 class="text-sm font-semibold text-gray-900">Import Data UMKM</h6>
                            </div>
                            <form action="{{ route('import.umkm') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label for="umkm_file" class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel</label>
                                    <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" id="umkm_file" name="file" accept=".xlsx,.xls" required>
                                    <p class="text-xs text-gray-500 mt-1">Format: .xlsx atau .xls (Max: 10MB)</p>
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-medium rounded-lg transition-colors">
                                        <i class="fas fa-upload mr-2"></i>Import
                                    </button>
                                    <a href="{{ route('export-import.template', 'umkm') }}" class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors">
                                        <i class="fas fa-download mr-2"></i>Template
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Excel Structure Guide -->
            <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-table text-indigo-600 mr-3"></i>
                        Struktur Excel untuk Import Data
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-6">

                        <!-- Penduduk Structure -->
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-users text-blue-600 mr-2"></i>
                                Struktur Import Data Penduduk
                            </h4>
                            <div class="text-xs text-gray-600 space-y-1">
                                <p><strong>Kolom yang diperlukan:</strong></p>
                                <ul class="list-disc list-inside ml-4 space-y-1">
                                    <li><code>nik</code> - Nomor Induk Kependudukan (16 digit, wajib unik)</li>
                                    <li><code>nama</code> - Nama lengkap penduduk</li>
                                    <li><code>jenis_kelamin</code> - L/P</li>
                                    <li><code>tanggal_lahir</code> - Format: YYYY-MM-DD</li>
                                    <li><code>tempat_lahir</code> - Tempat lahir</li>
                                    <li><code>agama</code> - Islam, Kristen, Katolik, Hindu, Buddha, Konghucu</li>
                                    <li><code>status_perkawinan</code> - Belum Kawin, Kawin, Cerai Hidup, Cerai Mati</li>
                                    <li><code>pekerjaan</code> - Pekerjaan</li>
                                    <li><code>pendidikan</code> - SD, SMP, SMA, D3, S1, S2, S3</li>
                                    <li><code>nkk</code> - Nomor Kartu Keluarga (16 digit)</li>
                                    <li><code>kedudukan_keluarga</code> - Kepala Keluarga, Istri, Anak, dll</li>
                                    <li><code>alamat</code> - Alamat lengkap</li>
                                    <li><code>rt</code> - RT</li>
                                    <li><code>rw</code> - RW</li>
                                    <li><code>dusun</code> - Dusun (opsional)</li>
                                    <li><code>nama_ayah</code> - Nama ayah (opsional)</li>
                                    <li><code>nama_ibu</code> - Nama ibu (opsional)</li>
                                    <li><code>keterangan</code> - Keterangan tambahan (opsional)</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Bantuan Sosial Structure -->
                        <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-xl p-4 border border-red-200">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-heart text-red-600 mr-2"></i>
                                Struktur Import Bantuan Sosial
                            </h4>
                            <div class="text-xs text-gray-600 space-y-1">
                                <p><strong>Kolom yang diperlukan:</strong></p>
                                <ul class="list-disc list-inside ml-4 space-y-1">
                                    <li><code>nama_program</code> - Nama program bantuan</li>
                                    <li><code>jenis_bantuan</code> - Jenis bantuan (BLT, PKH, BPNT, Bansos Lainnya)</li>
                                    <li><code>deskripsi</code> - Deskripsi program bantuan</li>
                                    <li><code>nilai_bantuan</code> - Nilai bantuan per penerima (angka)</li>
                                    <li><code>periode</code> - Periode pemberian (contoh: 2024, 2024-2025)</li>
                                    <li><code>tanggal_mulai</code> - Format: YYYY-MM-DD</li>
                                    <li><code>tanggal_selesai</code> - Format: YYYY-MM-DD</li>
                                    <li><code>status</code> - aktif, selesai, ditangguhkan</li>
                                    <li><code>kriteria_penerima</code> - Kriteria penerima (JSON format)</li>
                                    <li><code>sumber_dana</code> - Sumber dana (APBN, APBD, Swasta, dll)</li>
                                    <li><code>kuota_penerima</code> - Jumlah target penerima (angka, opsional)</li>
                                </ul>
                            </div>
                        </div>

                        <!-- UMKM Structure -->
                        <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-store text-purple-600 mr-2"></i>
                                Struktur Import Data UMKM
                            </h4>
                            <div class="text-xs text-gray-600 space-y-1">
                                <p><strong>Kolom yang diperlukan:</strong></p>
                                <ul class="list-disc list-inside ml-4 space-y-1">
                                    <li><code>nama_usaha</code> - Nama usaha</li>
                                    <li><code>nama_pemilik</code> - Nama pemilik usaha</li>
                                    <li><code>nik_pemilik</code> - NIK pemilik (16 digit, opsional)</li>
                                    <li><code>alamat_usaha</code> - Alamat usaha</li>
                                    <li><code>rt</code> - RT (opsional)</li>
                                    <li><code>rw</code> - RW (opsional)</li>
                                    <li><code>dusun</code> - Dusun (opsional)</li>
                                    <li><code>no_telepon</code> - Nomor telepon (opsional)</li>
                                    <li><code>email</code> - Email (opsional)</li>
                                    <li><code>jenis_usaha</code> - makanan, minuman, kerajinan, jasa, perdagangan, pertanian, peternakan, lainnya</li>
                                    <li><code>deskripsi_usaha</code> - Deskripsi usaha (opsional)</li>
                                    <li><code>modal_awal</code> - Modal awal (angka, opsional)</li>
                                    <li><code>omset_bulanan</code> - Omset bulanan (angka, opsional)</li>
                                    <li><code>jumlah_karyawan</code> - Jumlah karyawan (angka, default: 0)</li>
                                    <li><code>status_usaha</code> - aktif, tutup, pindah (default: aktif)</li>
                                    <li><code>tanggal_berdiri</code> - Format: YYYY-MM-DD (opsional)</li>
                                    <li><code>produk_unggulan</code> - Array produk (JSON format, opsional)</li>
                                    <li><code>foto_usaha</code> - Array foto (JSON format, opsional)</li>
                                    <li><code>latitude</code> - Koordinat latitude (opsional)</li>
                                    <li><code>longitude</code> - Koordinat longitude (opsional)</li>
                                    <li><code>is_unggulan</code> - true/false (default: false)</li>
                                    <li><code>is_verified</code> - true/false (default: false)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-info-circle text-gray-600 mr-3"></i>
                    Petunjuk Import Data
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <h6 class="text-sm font-semibold text-gray-900 mb-3">Import Data:</h6>
                        <ul class="space-y-2">
                            <li class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Download template terlebih dahulu
                            </li>
                            <li class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Isi data sesuai format template
                            </li>
                            <li class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Upload file yang sudah diisi
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Additional Tips -->
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                    <h6 class="text-sm font-semibold text-yellow-800 mb-2 flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                        Tips Penting:
                    </h6>
                    <ul class="text-xs text-yellow-700 space-y-1">
                        <li>• Pastikan format tanggal menggunakan YYYY-MM-DD (contoh: 2024-01-15)</li>
                        <li>• NIK harus unik dan berjumlah 16 digit</li>
                        <li>• NKK harus berjumlah 16 digit</li>
                        <li>• Gunakan nilai yang sesuai dengan pilihan yang tersedia</li>
                        <li>• Untuk kolom JSON (kriteria_penerima, produk_unggulan, foto_usaha), gunakan format: ["item1", "item2"]</li>
                        <li>• Untuk boolean (is_unggulan, is_verified), gunakan: true atau false</li>
                        <li>• File Excel maksimal 10MB</li>
                        <li>• Format file yang didukung: .xlsx dan .xls</li>
                    </ul>
                </div>
            </div>
        </div>
                </div>
            </div>
        </div>
    </div>

<script nonce="{{ $csp_nonce }}">
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('penduduk_file');
    const previewBtn = document.getElementById('penduduk_preview_btn');
    const downloadInvalidBtn = document.getElementById('penduduk_download_invalid_btn');
    const importBtn = document.getElementById('penduduk_import_btn');
    const loading = document.getElementById('penduduk_preview_loading');
    const box = document.getElementById('penduduk_preview_box');

    if (!fileInput || !previewBtn) return;

    let isPreviewing = false;
    let currentController = null;

    async function runPreview() {
        if (!fileInput.files.length || isPreviewing) return;

        if (currentController) {
            currentController.abort();
        }
        currentController = new AbortController();

        isPreviewing = true;
        previewBtn.disabled = true;
        downloadInvalidBtn.disabled = true;
        importBtn.disabled = true;
        loading.classList.remove('hidden');

        const fd = new FormData();
        fd.append('file', fileInput.files[0]);
        fd.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        try {
            const res = await fetch("{{ route('import.penduduk.preview') }}", {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd,
                signal: currentController.signal,
            });

            // Use cloned response to avoid "body stream already read" issues
            let data;
            try {
                data = await res.clone().json();
            } catch (_) {
                const text = await res.text();
                throw new Error(text?.slice(0, 200) || 'Response tidak valid (bukan JSON)');
            }

            if (!res.ok || !data.success) throw new Error(data.message || 'Preview gagal');

            box.classList.remove('hidden');
            document.getElementById('penduduk_sum_total').textContent = data.summary.total_data_rows;
            document.getElementById('penduduk_sum_valid').textContent = data.summary.valid_rows;
            document.getElementById('penduduk_sum_invalid').textContent = data.summary.invalid_rows;

            document.getElementById('err_nik').textContent = data.summary.column_error_counts?.nik ?? 0;
            document.getElementById('err_nama').textContent = data.summary.column_error_counts?.nama ?? 0;
            document.getElementById('err_nkk').textContent = data.summary.column_error_counts?.nkk ?? 0;
            document.getElementById('err_wilayah').textContent = data.summary.column_error_counts?.wilayah ?? 0;

            const note = document.getElementById('penduduk_preview_note');
            note.textContent = `Menampilkan ${data.preview.invalid_shown} dari ${data.preview.invalid_total} baris invalid, dan ${data.preview.valid_shown} dari ${data.preview.valid_total} baris valid.`;

            const invalidWrap = document.getElementById('penduduk_invalid_wrap');
            const invalidList = document.getElementById('penduduk_invalid_list');
            invalidList.innerHTML = '';

            if (data.preview.invalid.length > 0) {
                invalidWrap.classList.remove('hidden');
                data.preview.invalid.forEach(item => {
                    const li = document.createElement('li');
                    const byCol = item.errors_by_column || {};
                    const parts = [];
                    Object.keys(byCol).forEach(col => {
                        if (col === 'nik_info') return;
                        const msgs = Array.isArray(byCol[col]) ? byCol[col].join(' | ') : byCol[col];
                        parts.push(`${col.toUpperCase()}: ${msgs}`);
                    });
                    const detail = parts.length ? parts.join(' ; ') : (item.errors || []).join(', ');
                    const address = item.alamat ? ` [${item.alamat}]` : '';
                    const wilayah = (item.rt || item.rw) ? ` (RT ${item.rt}/RW ${item.rw})` : '';
                    li.textContent = `Baris ${item.row} (${item.nik || '-'} / ${item.nama || '-'}) ${wilayah}${address}: ${detail}`;
                    invalidList.appendChild(li);
                });
            } else {
                invalidWrap.classList.add('hidden');
            }

            const validWrap = document.getElementById('penduduk_valid_wrap');
            const validList = document.getElementById('penduduk_valid_list');
            validList.innerHTML = '';

            if (data.preview.valid.length > 0) {
                validWrap.classList.remove('hidden');
                data.preview.valid.forEach(item => {
                    const li = document.createElement('li');
                    const info = item.info ? ` <span class="text-blue-600 font-bold">[${item.info}]</span>` : '';
                    const address = item.alamat ? ` [${item.alamat}]` : '';
                    const wilayah = (item.rt || item.rw) ? ` (RT ${item.rt}/RW ${item.rw})` : '';
                    li.innerHTML = `Baris ${item.row} (${item.nik || '-'} / ${item.nama || '-'}) ${wilayah}${address}${info}`;
                    validList.appendChild(li);
                });
            } else {
                validWrap.classList.add('hidden');
            }

            importBtn.disabled = data.summary.valid_rows === 0;
            downloadInvalidBtn.disabled = data.summary.invalid_rows === 0;
        } catch (err) {
            if (err.name === 'AbortError') return;
            if (window.Swal) {
                Swal.fire('Preview gagal', err.message, 'error');
            } else {
                alert('Preview gagal: ' + err.message);
            }
            importBtn.disabled = true;
            downloadInvalidBtn.disabled = true;
        } finally {
            isPreviewing = false;
            previewBtn.disabled = false;
            loading.classList.add('hidden');
        }
    }

    async function downloadInvalidReport() {
        if (!fileInput.files.length) {
            if (window.Swal) Swal.fire('Info', 'Pilih file dulu ya.', 'info');
            return;
        }

        try {
            downloadInvalidBtn.disabled = true;
            const fd = new FormData();
            fd.append('file', fileInput.files[0]);
            fd.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            const res = await fetch("{{ route('import.penduduk.preview-invalid-report') }}", {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd,
            });

            if (!res.ok) {
                const text = await res.text();
                throw new Error(text?.slice(0, 200) || 'Gagal download laporan invalid');
            }

            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `invalid_rows_penduduk_${new Date().toISOString().slice(0,19).replace(/[:T]/g,'-')}.xlsx`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        } catch (err) {
            if (window.Swal) Swal.fire('Gagal', err.message, 'error');
        } finally {
            downloadInvalidBtn.disabled = false;
        }
    }

    fileInput.addEventListener('change', runPreview);
    previewBtn.addEventListener('click', runPreview);
    downloadInvalidBtn.addEventListener('click', downloadInvalidReport);
});
</script>
@endsection

