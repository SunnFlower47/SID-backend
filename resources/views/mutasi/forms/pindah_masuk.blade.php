<!-- Form Pindah Masuk -->
<div id="formPindahMasuk" class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl p-6 border border-blue-200" style="display: none;">
    <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
        <i class="fas fa-user-plus text-blue-600 mr-3"></i>
        Data Pindah Masuk
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="nama_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
            <input type="text" name="nama" id="nama_pindah_masuk"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Nama lengkap penduduk" required>
        </div>
        <div>
            <label for="nik_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">NIK</label>
            <div class="relative">
                <input type="text" name="nik" id="nik_pindah_masuk"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="16 digit NIK" maxlength="16" required>
                <div id="nik_check_loading_pindah_masuk" class="absolute right-3 top-3 hidden">
                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                </div>
            </div>
            <div id="nikStatusInfoPindahMasuk" class="mt-2 text-sm" style="display: none;">
                <div id="nikNewInfoPindahMasuk" class="text-green-700 bg-green-50 p-2 rounded-lg border border-green-200" style="display: none;">
                    <i class="fas fa-check-circle mr-1"></i>
                    <strong>NIK Tersedia:</strong> NIK ini belum terdaftar di database
                </div>
                <div id="nikExistingInfoPindahMasuk" class="text-red-700 bg-red-50 p-2 rounded-lg border border-red-200" style="display: none;">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <strong>NIK Sudah Ada:</strong> <span id="existingNIKDetailsPindahMasuk"></span>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-1">Format: 16 digit angka</p>
        </div>
        <div>
            <label for="jenis_kelamin_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
            <select name="jenis_kelamin" id="jenis_kelamin_pindah_masuk"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">Pilih jenis kelamin...</option>
                <option value="LAKI-LAKI">Laki-laki</option>
                <option value="PEREMPUAN">Perempuan</option>
            </select>
        </div>
        <div>
            <label for="agama_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Agama</label>
            <select name="agama" id="agama_pindah_masuk"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">Pilih agama...</option>
                <option value="Islam">Islam</option>
                <option value="Kristen">Kristen</option>
                <option value="Katolik">Katolik</option>
                <option value="Hindu">Hindu</option>
                <option value="Buddha">Buddha</option>
                <option value="Konghucu">Konghucu</option>
            </select>
        </div>
        <div>
            <label for="status_perkawinan_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Status Perkawinan</label>
            <select name="status_perkawinan" id="status_perkawinan_pindah_masuk"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">Pilih status perkawinan...</option>
                <option value="Belum Kawin">Belum Kawin</option>
                <option value="Kawin">Kawin</option>
                <option value="Cerai Hidup">Cerai Hidup</option>
                <option value="Cerai Mati">Cerai Mati</option>
            </select>
        </div>
        <div>
            <label for="tempat_lahir_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" id="tempat_lahir_pindah_masuk"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Tempat lahir" required>
        </div>
        <div>
            <label for="tanggal_lahir_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" id="tanggal_lahir_pindah_masuk"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div>
            <label for="kedudukan_keluarga_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Kedudukan dalam Keluarga</label>
            <select name="kedudukan_keluarga" id="kedudukan_keluarga_pindah_masuk"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
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
        </div>
        <div>
            <label for="pendidikan_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Pendidikan</label>
            <input type="text" name="pendidikan" id="pendidikan_pindah_masuk"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Contoh: SD, SMP, SMA, S1, dll" required>
        </div>
        <div>
            <label for="pekerjaan_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan</label>
            <input type="text" name="pekerjaan" id="pekerjaan_pindah_masuk"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Pekerjaan (opsional)">
        </div>
        <div>
            <label for="nama_ayah_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Nama Ayah</label>
            <input type="text" name="nama_ayah" id="nama_ayah_pindah_masuk"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Masukkan nama ayah">
        </div>
        <div>
            <label for="nama_ibu_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Nama Ibu</label>
            <input type="text" name="nama_ibu" id="nama_ibu_pindah_masuk"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Masukkan nama ibu">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilihan KK</label>
            <div class="space-y-3">
                <div class="flex items-center">
                    <input type="radio" id="kk_existing_pindah_masuk" name="kk_option_pindah_masuk" value="existing" class="mr-2">
                    <label for="kk_existing_pindah_masuk" class="text-sm font-medium text-gray-700">Gabung ke KK yang sudah ada</label>
                </div>
                <div class="flex items-center">
                    <input type="radio" id="kk_new_pindah_masuk" name="kk_option_pindah_masuk" value="new" class="mr-2">
                    <label for="kk_new_pindah_masuk" class="text-sm font-medium text-gray-700">Buat KK baru</label>
                </div>
            </div>
        </div>

        <!-- Existing KK Selection -->
        <div id="existingKKContainerPindahMasuk" class="md:col-span-2" style="display: none;">
            <label for="nkk_existing_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Pilih KK yang sudah ada</label>
            <div class="relative">
                <input type="text" id="nkk_search_pindah_masuk"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Cari No KK (NKK atau nama kepala keluarga)..."
                       autocomplete="off">
                <input type="hidden" name="nkk" id="nkk_existing_pindah_masuk">
                <div id="nkk_search_results_pindah_masuk" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                <div id="nkk_search_loading_pindah_masuk" class="absolute right-3 top-3 hidden">
                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                </div>
            </div>
            <div id="selected_nkk_pindah_masuk" class="mt-2 hidden">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-blue-900" id="selected_nkk_name_pindah_masuk"></p>
                            <p class="text-sm text-blue-700" id="selected_nkk_info_pindah_masuk"></p>
                        </div>
                        <button type="button" onclick="clearNKKSelection('pindah_masuk')" class="text-blue-400 hover:text-blue-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- New KK Input -->
        <div id="newKKContainerPindahMasuk" class="md:col-span-2" style="display: none;">
            <label for="nkk_new_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">No KK Baru</label>
            <div class="relative">
                <input type="text" name="nkk_new" id="nkk_new_pindah_masuk"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Masukkan No KK baru (16 digit)"
                       maxlength="16">
                <div id="nkk_check_loading_pindah_masuk" class="absolute right-3 top-3 hidden">
                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                </div>
            </div>
            <div id="nkkStatusInfoPindahMasuk" class="mt-2 text-sm" style="display: none;">
                <div id="nkkNewInfoPindahMasuk" class="text-blue-700 bg-blue-50 p-2 rounded-lg border border-blue-200" style="display: none;">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>KK Baru:</strong> Akan membuat keluarga baru dengan NKK ini
                </div>
                <div id="nkkExistingInfoPindahMasuk" class="text-orange-700 bg-orange-50 p-2 rounded-lg border border-orange-200" style="display: none;">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <strong>KK Sudah Ada:</strong> <span id="existingKKDetailsPindahMasuk"></span>
                    <div class="mt-2">
                        <button type="button" id="joinExistingKKPindahMasuk" class="text-sm bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded">
                            <i class="fas fa-plus mr-1"></i>Gabung ke KK ini
                        </button>
                    </div>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-1">Format: 16 digit angka</p>
        </div>
        <div>
            <label for="alamat_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
            <textarea name="alamat" id="alamat_pindah_masuk"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Alamat lengkap" rows="3" required></textarea>
        </div>
        <div>
            <label for="rw_id_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">RW</label>
            <select name="rw_id" id="rw_id_pindah_masuk" onchange="populateRtByRwMutasi(this.value, 'rt_id_pindah_masuk')"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">Pilih RW...</option>
                @foreach(($masterRwOptions ?? []) as $rw)
                    <option value="{{ $rw['id'] }}">RW {{ $rw['kode'] }} - {{ $rw['nama'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="rt_id_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">RT</label>
            <select name="rt_id" id="rt_id_pindah_masuk" onchange="syncDusunByRtMutasi(this.value, document.getElementById('rw_id_pindah_masuk').value, 'dusun_id_pindah_masuk')"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">Pilih RT...</option>
            </select>
        </div>
        <div>
            <label for="dusun_id_pindah_masuk_label" class="block text-sm font-medium text-gray-700 mb-2">Dusun</label>
            <input type="text" id="dusun_id_pindah_masuk_label"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
                   placeholder="Dusun" readonly>
            <input type="hidden" name="dusun_id" id="dusun_id_pindah_masuk">
        </div>
        <div>
            <label for="kategori_mutasi_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Kategori Asal (Pindah Dari)</label>
            <select name="kategori_mutasi" id="kategori_mutasi_pindah_masuk"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">Pilih kategori...</option>
                <option value="dalam_kota">Dalam Kabupaten / Kota</option>
                <option value="luar_kota">Luar Kabupaten / Kota</option>
                <option value="luar_negeri">Luar Negeri</option>
            </select>
        </div>
        <div>
            <label for="asal_tujuan_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Asal Tempat</label>
            <input type="text" name="asal_tujuan" id="asal_tujuan_pindah_masuk"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Asal daerah/kota" required>
        </div>
        <div>
            <label for="tanggal_mutasi_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pindah</label>
            <input type="date" name="tanggal_mutasi" id="tanggal_mutasi_pindah_masuk"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   value="{{ now()->format('Y-m-d') }}" required>
        </div>
        <div class="md:col-span-2">
            <label for="alasan_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Alasan Pindah (Opsional)</label>
            <textarea name="alasan" id="alasan_pindah_masuk"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Alasan pindah masuk ke desa" rows="3"></textarea>
        </div>
        <div class="md:col-span-2">
            <label for="keterangan_pindah_masuk" class="block text-sm font-medium text-gray-700 mb-2">Keterangan Tambahan (Opsional)</label>
            <textarea name="keterangan" id="keterangan_pindah_masuk"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Keterangan tambahan" rows="3"></textarea>
        </div>

        <!-- Section: Family Members (Batch Input) -->
        <div class="md:col-span-2 border-t border-gray-200 pt-6 mt-4">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-md font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-users-cog text-blue-600 mr-2"></i>
                    Anggota Keluarga yang Ikut Pindah (Opsional)
                </h4>
                <button type="button" id="btnAddMemberPindah"
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                    <i class="fas fa-plus mr-1"></i> Tambah Anggota
                </button>
            </div>
            
            <div id="familyMembersContainerPindah" class="space-y-4">
                <!-- Dynamic rows will be added here -->
            </div>
            
            <div id="noFamilyMemberMsg" class="text-center py-8 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                <p class="text-sm text-gray-500 italic">Belum ada anggota keluarga tambahan.</p>
                <p class="text-xs text-gray-400 mt-1">Klik tombol "+ Tambah Anggota" jika ada keluarga lain yang ikut pindah masuk.</p>
            </div>
        </div>
    </div>
</div>


