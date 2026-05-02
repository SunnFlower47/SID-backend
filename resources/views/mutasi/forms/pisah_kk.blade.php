<!-- Form Pisah KK -->
<div id="formPisahKK" class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200" style="display: none;">
    <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
        <i class="fas fa-users text-green-600 mr-3"></i>
        Data Pisah KK
    </h3>

    <!-- Person Selection -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <label for="penduduk_id_pisah" class="block text-sm font-medium text-gray-700 mb-2">Penduduk yang Akan Pisah KK</label>
            <div class="relative">
                <input type="text" id="penduduk_search_pisah" name="penduduk_search_pisah"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                       placeholder="Cari penduduk (NIK, nama, atau No KK)..."
                       autocomplete="off">
                <input type="hidden" name="penduduk_id" id="penduduk_id_pisah">
                <div id="penduduk_search_results_pisah" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                <div id="penduduk_search_loading_pisah" class="absolute right-3 top-3 hidden">
                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                </div>
            </div>
            <div id="selected_penduduk_pisah" class="mt-2 hidden">
                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-green-900" id="selected_penduduk_name_pisah"></p>
                            <p class="text-sm text-green-700" id="selected_penduduk_info_pisah"></p>
                        </div>
                        <button type="button" onclick="clearPendudukSelection('pisah_kk')" class="text-green-400 hover:text-green-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <label for="kategori_mutasi_pisah" class="block text-sm font-medium text-gray-700 mb-2">Kategori Pisah KK</label>
            <select name="kategori_mutasi" id="kategori_mutasi_pisah"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500" required>
                <option value="">Pilih kategori...</option>
                <option value="dalam_desa">Dalam Desa (Tetap di Desa Cibatu)</option>
                <option value="dalam_kota">Dalam Kota (Pindah ke desa/kota lain)</option>
                <option value="luar_kota">Luar Kota (Pindah ke kota lain)</option>
                <option value="luar_negeri">Luar Negeri (Pindah ke luar negeri)</option>
            </select>
        </div>
    </div>

    <!-- Current Family Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h4 class="font-medium text-blue-900 mb-3">Info Keluarga Saat Ini</h4>
        <div id="currentFamilyInfo">
            <p class="text-sm text-blue-700">Pilih penduduk terlebih dahulu untuk melihat info keluarga.</p>
        </div>
    </div>

    <!-- KK Options (only for dalam_desa) -->
    <div id="kkOptionsContainer" class="mb-6" style="display: none;">
        <h4 class="font-medium text-gray-900 mb-3">Opsi Kartu Keluarga</h4>
        <div class="space-y-4">
            <div class="flex items-center space-x-4">
                <input type="radio" id="kk_new" name="kk_option" value="new" class="text-green-600 focus:ring-green-500">
                <label for="kk_new" class="text-sm font-medium text-gray-700">Buat KK Baru (Input NKK Manual)</label>
            </div>
            <div class="flex items-center space-x-4">
                <input type="radio" id="kk_existing" name="kk_option" value="existing" class="text-green-600 focus:ring-green-500">
                <label for="kk_existing" class="text-sm font-medium text-gray-700">Gabung ke KK yang Sudah Ada</label>
            </div>
        </div>
    </div>

    <!-- Status Kependudukan -->
    <div class="mb-6">
        <h4 class="font-medium text-gray-900 mb-3">Status Kependudukan Setelah Pisah KK</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="kedudukan_keluarga_pisah" class="block text-sm font-medium text-gray-700 mb-2">
                    Kedudukan dalam Keluarga <span class="text-red-500">*</span>
                </label>
                <select name="kedudukan_keluarga_pisah" id="kedudukan_keluarga_pisah"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500" required>
                    <option value="">Pilih kedudukan...</option>
                    <option value="Kepala Keluarga">Kepala Keluarga</option>
                    <option value="Istri">Istri</option>
                    <option value="Anak">Anak</option>
                    <option value="Menantu">Menantu</option>
                    <option value="Cucu">Cucu</option>
                    <option value="Orangtua">Orangtua</option>
                    <option value="Mertua">Mertua</option>
                    <option value="Famili Lain">Famili Lain</option>
                    <option value="Pembantu">Pembantu</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    <span id="kedudukanInfo">Pilih opsi KK terlebih dahulu untuk melihat info kedudukan</span>
                </p>
            </div>
            <div>
                <label for="status_perkawinan_pisah" class="block text-sm font-medium text-gray-700 mb-2">
                    Status Perkawinan
                </label>
                <select name="status_perkawinan_pisah" id="status_perkawinan_pisah"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">Pilih status...</option>
                    <option value="Belum Kawin">Belum Kawin</option>
                    <option value="Kawin">Kawin</option>
                    <option value="Cerai Hidup">Cerai Hidup</option>
                    <option value="Cerai Mati">Cerai Mati</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Opsi KK untuk Dalam Desa -->
    <div id="kkOptionsContainerPisah" class="mb-6" style="display: none;">
        <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Opsi KK</label>
        <div class="space-y-3">
            <label class="flex items-center">
                <input type="radio" name="kk_option" value="existing" class="mr-3 text-green-600 focus:ring-green-500">
                <span class="text-sm text-gray-700">Gabung ke KK yang sudah ada</span>
            </label>
            <label class="flex items-center">
                <input type="radio" name="kk_option" value="new" class="mr-3 text-green-600 focus:ring-green-500">
                <span class="text-sm text-gray-700">Buat KK baru</span>
            </label>
        </div>
    </div>

    <!-- Input NKK Baru (only for dalam_desa + new) -->
    <div id="newKKContainer" class="mb-6" style="display: none;">
        <label for="nkk_baru_pisah" class="block text-sm font-medium text-gray-700 mb-2">No KK Baru</label>
        <div class="relative">
            <input type="text" name="nkk_baru" id="nkk_baru_pisah"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                   placeholder="Masukkan No KK baru (16 digit)"
                   maxlength="16">
            <div id="nkk_check_loading" class="absolute right-3 top-3 hidden">
                <i class="fas fa-spinner fa-spin text-gray-400"></i>
            </div>
        </div>
        <div id="nkkStatusInfo" class="mt-2 text-sm" style="display: none;">
            <div id="nkkNewInfo" class="text-blue-700 bg-blue-50 p-2 rounded-lg border border-blue-200" style="display: none;">
                <i class="fas fa-info-circle mr-1"></i>
                <strong>KK Baru:</strong> Akan membuat keluarga baru dengan NKK ini
            </div>
            <div id="nkkExistingInfo" class="text-orange-700 bg-orange-50 p-2 rounded-lg border border-orange-200" style="display: none;">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                <strong>KK Sudah Ada:</strong> <span id="existingKKDetails"></span>
                <div class="mt-2">
                    <button type="button" id="joinExistingKK" class="text-sm bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded">
                        <i class="fas fa-plus mr-1"></i>Gabung ke KK ini
                    </button>
                </div>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-1">Format: 16 digit angka</p>

        <!-- Alamat untuk KK Baru (hanya untuk dalam_desa + new) -->
        <div class="mt-4">
            <h5 class="text-sm font-medium text-gray-700 mb-3">Alamat KK Baru</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="rw_id_pisah" class="block text-sm font-medium text-gray-700 mb-2">RW</label>
                    <select name="rw_id" id="rw_id_pisah" onchange="populateRtByRwMutasi(this.value, 'rt_id_pisah')"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
                        <option value="">Pilih RW...</option>
                        @foreach(($masterRwOptions ?? []) as $rw)
                            <option value="{{ $rw['id'] }}">RW {{ $rw['kode'] }} - {{ $rw['nama'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="rt_id_pisah" class="block text-sm font-medium text-gray-700 mb-2">RT</label>
                    <select name="rt_id" id="rt_id_pisah" onchange="syncDusunByRtMutasi(this.value, document.getElementById('rw_id_pisah').value, 'dusun_id_pisah')"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
                        <option value="">Pilih RT...</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="dusun_id_pisah_label" class="block text-sm font-medium text-gray-700 mb-2">Dusun</label>
                    <input type="text" id="dusun_id_pisah_label"
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 bg-gray-50"
                           placeholder="Dusun" readonly>
                    <input type="hidden" name="dusun_id" id="dusun_id_pisah">
                </div>
            </div>
            <div class="mt-4">
                <label for="alamat_baru_pisah" class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                <textarea name="alamat" id="alamat_baru_pisah" rows="3"
                          class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                          placeholder="Masukkan alamat lengkap untuk KK baru"></textarea>
            </div>
        </div>
    </div>

    <!-- Select Existing KK (only for dalam_desa + existing) -->
    <div id="existingKKContainer" class="mb-6" style="display: none;">
        <label for="nkk_existing_pisah" class="block text-sm font-medium text-gray-700 mb-2">Pilih No KK yang Sudah Ada</label>
        <div class="relative">
            <input type="text" name="nkk_existing" id="nkk_existing_pisah"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                   placeholder="Ketik No KK atau nama kepala keluarga..."
                   maxlength="16"
                   autocomplete="off">
            <input type="hidden" name="nkk_existing_id" id="nkk_existing_id">

            <!-- Search Results Dropdown -->
            <div id="kkSearchResults" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                <!-- Results will be loaded here via AJAX -->
            </div>

            <!-- Loading indicator -->
            <div id="kkSearchLoading" class="absolute right-3 top-3 hidden">
                <i class="fas fa-spinner fa-spin text-gray-400"></i>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-1">Ketik minimal 3 karakter untuk mencari KK</p>
    </div>

    <!-- Alamat Tujuan (only for luar kota/desa/negeri) -->
    <div id="addressContainer" class="mb-6" style="display: none;">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="nkk_tujuan_pisah" class="block text-sm font-medium text-gray-700 mb-2">No KK Tujuan</label>
                <input type="text" name="nkk_tujuan" id="nkk_tujuan_pisah"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                       placeholder="Masukkan No KK tujuan (16 digit)"
                       maxlength="16">
                <p class="text-xs text-gray-500 mt-1">Format: 16 digit angka</p>
            </div>
            <div>
                <label for="alamat_pisah" class="block text-sm font-medium text-gray-700 mb-2">Alamat Tujuan</label>
                <textarea name="alamat" id="alamat_pisah" rows="3"
                          class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                          placeholder="Masukkan alamat lengkap tujuan pindah (contoh: Jl. Merdeka No. 123, Kelurahan ABC, Kecamatan XYZ, Kota/Kabupaten DEF)"></textarea>
            </div>
        </div>
        <p class="text-xs text-gray-500">Contoh alamat: Jl. Merdeka No. 123, Kelurahan ABC, Kecamatan XYZ, Kota/Kabupaten DEF</p>
    </div>

    <!-- Info for joining existing KK -->
    <div id="existingKKInfo" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6" style="display: none;">
        <h4 class="font-medium text-yellow-900 mb-3">Info KK yang Dipilih</h4>
        <div id="existingKKDetails">
            <p class="text-sm text-yellow-700">Pilih KK terlebih dahulu untuk melihat info.</p>
        </div>
    </div>

    <!-- Mutation Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="tanggal_mutasi_pisah" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pisah KK</label>
            <input type="date" name="tanggal_mutasi" id="tanggal_mutasi_pisah"
                   value="{{ old('tanggal_mutasi', now()->format('Y-m-d')) }}"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500" required>
        </div>
        <div>
            <label for="alasan_pisah" class="block text-sm font-medium text-gray-700 mb-2">Alasan Pisah KK</label>
            <input type="text" name="alasan" id="alasan_pisah"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                   placeholder="Contoh: Menikah, mandiri, dll">
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">
                    Informasi Penting
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Jika kategori "Dalam Desa", penduduk akan tetap aktif dan hanya berpindah KK</li>
                        <li>Jika kategori "Dalam Kota", "Luar Kota", atau "Luar Negeri", penduduk akan dihapus dari sistem (soft delete)</li>
                        <li>Anggota keluarga yang ikut pindah juga akan dihapus jika kategori bukan "Dalam Desa"</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

