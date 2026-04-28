<!-- Form Kelahiran -->
<div id="formKelahiran" class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6 sm:p-8 border border-green-200" style="display: none;">
    <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mr-4">
            <i class="fas fa-baby text-green-600"></i>
        </div>
        Data Kelahiran
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="nik_bayi" class="block text-sm font-medium text-gray-700 mb-2">NIK Bayi</label>
            <input type="text" name="nik_bayi" id="nik_bayi"
                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors"
                   placeholder="16 digit NIK bayi" maxlength="16" required>
            <div id="nik_check_loading_kelahiran" class="mt-2 hidden text-xs text-gray-500">
                <i class="fas fa-spinner fa-spin mr-1"></i> Memeriksa NIK...
            </div>
            <div id="nikStatusInfoKelahiran" class="mt-2 hidden">
                <div id="nikNewInfoKelahiran" class="hidden text-xs text-green-600 bg-green-50 border border-green-200 rounded-lg px-3 py-2">
                    <i class="fas fa-check-circle mr-1"></i> NIK belum terdaftar, bisa digunakan.
                </div>
                <div id="nikExistingInfoKelahiran" class="hidden text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <strong>NIK sudah terdaftar:</strong> <span id="existingNIKDetailsKelahiran"></span>
                </div>
            </div>
        </div>
        <div>
            <label for="nama_bayi" class="block text-sm font-medium text-gray-700 mb-2">Nama Bayi</label>
            <input type="text" name="nama_bayi" id="nama_bayi"
                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors"
                   placeholder="Nama lengkap bayi" required>
        </div>
        <div>
            <label for="jenis_kelamin_bayi" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
            <select name="jenis_kelamin_bayi" id="jenis_kelamin_bayi"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors" required>
                <option value="">Pilih jenis kelamin...</option>
                <option value="LAKI-LAKI">Laki-laki</option>
                <option value="PEREMPUAN">Perempuan</option>
            </select>
        </div>
        <div>
            <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" id="tempat_lahir"
                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors"
                   placeholder="Tempat lahir bayi" required>
        </div>
        <div>
            <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors" required>
        </div>
        <div>
            <label for="agama_bayi" class="block text-sm font-medium text-gray-700 mb-2">Agama</label>
            <select name="agama_bayi" id="agama_bayi"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors" required>
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
            <label for="status_perkawinan_bayi" class="block text-sm font-medium text-gray-700 mb-2">Status Perkawinan</label>
            <select name="status_perkawinan_bayi" id="status_perkawinan_bayi"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors" required>
                <option value="">Pilih status...</option>
                <option value="Belum Kawin" selected>Belum Kawin</option>
                <option value="Kawin">Kawin</option>
                <option value="Cerai Hidup">Cerai Hidup</option>
                <option value="Cerai Mati">Cerai Mati</option>
            </select>
        </div>
        <div>
            <label for="kedudukan_keluarga_bayi" class="block text-sm font-medium text-gray-700 mb-2">Kedudukan Keluarga</label>
            <select name="kedudukan_keluarga_bayi" id="kedudukan_keluarga_bayi"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors" required>
                <option value="">Pilih kedudukan...</option>
                <option value="Anak" selected>Anak</option>
                <option value="Kepala Keluarga">Kepala Keluarga</option>
                <option value="Istri">Istri</option>
                <option value="Suami">Suami</option>
                <option value="Menantu">Menantu</option>
                <option value="Cucu">Cucu</option>
                <option value="Orang Tua">Orang Tua</option>
                <option value="Mertua">Mertua</option>
                <option value="Famili Lain">Famili Lain</option>
                <option value="Pembantu">Pembantu</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>
        <div>
            <label for="pendidikan_bayi" class="block text-sm font-medium text-gray-700 mb-2">Pendidikan</label>
            <select name="pendidikan_bayi" id="pendidikan_bayi"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors">
                <option value="">Pilih pendidikan...</option>
                <option value="Tidak/Belum Sekolah" selected>Tidak/Belum Sekolah</option>
                <option value="Tidak Tamat SD/Sederajat">Tidak Tamat SD/Sederajat</option>
                <option value="Tamat SD/Sederajat">Tamat SD/Sederajat</option>
                <option value="SLTP/Sederajat">SLTP/Sederajat</option>
                <option value="SLTA/Sederajat">SLTA/Sederajat</option>
                <option value="Diploma I/II">Diploma I/II</option>
                <option value="Akademi/Diploma III/S.Muda">Akademi/Diploma III/S.Muda</option>
                <option value="Diploma IV/Strata I">Diploma IV/Strata I</option>
                <option value="Strata II">Strata II</option>
                <option value="Strata III">Strata III</option>
            </select>
        </div>
        <div>
            <label for="pekerjaan_bayi" class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan</label>
            <input type="text" name="pekerjaan_bayi" id="pekerjaan_bayi"
                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors"
                   placeholder="Pekerjaan bayi" value="Belum Bekerja" required>
        </div>
        <div>
            <label for="nkk_kelahiran" class="block text-sm font-medium text-gray-700 mb-2">No Kartu Keluarga</label>
            <div class="relative">
                <input type="text" id="nkk_search_kelahiran" name="nkk_search_kelahiran"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors"
                       placeholder="Cari No KK (NKK atau nama kepala keluarga)..."
                       autocomplete="off">
                <input type="hidden" name="nkk" id="nkk_kelahiran">
                <div id="nkk_search_results_kelahiran" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                <div id="nkk_search_loading_kelahiran" class="absolute right-3 top-3 hidden">
                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                </div>
            </div>
            <div id="selected_nkk_kelahiran" class="mt-2 hidden">
                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-green-900" id="selected_nkk_name_kelahiran"></p>
                            <p class="text-sm text-green-700" id="selected_nkk_info_kelahiran"></p>
                        </div>
                        <button type="button" onclick="clearNKKSelection('kelahiran')" class="text-green-400 hover:text-green-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <label for="alamat_bayi" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
            <textarea name="alamat_bayi" id="alamat_bayi" rows="3"
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors"
                      placeholder="Alamat lengkap bayi" required></textarea>
        </div>
        <div>
            <label for="rw_id_bayi" class="block text-sm font-medium text-gray-700 mb-2">RW</label>
            <select name="rw_id_bayi" id="rw_id_bayi" onchange="populateRtByRwMutasi(this.value, 'rt_id_bayi')"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors" required>
                <option value="">Pilih RW...</option>
                @foreach(($masterRwOptions ?? []) as $rw)
                    <option value="{{ $rw['id'] }}">RW {{ $rw['kode'] }} - {{ $rw['nama'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="rt_id_bayi" class="block text-sm font-medium text-gray-700 mb-2">RT</label>
            <select name="rt_id_bayi" id="rt_id_bayi" onchange="syncDusunByRtMutasi(this.value, document.getElementById('rw_id_bayi').value, 'dusun_id_bayi')"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors" required>
                <option value="">Pilih RT...</option>
            </select>
        </div>
        <div>
            <label for="dusun_id_bayi_label" class="block text-sm font-medium text-gray-700 mb-2">Dusun</label>
            <input type="text" id="dusun_id_bayi_label"
                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors bg-gray-50"
                   placeholder="Dusun" readonly>
            <input type="hidden" name="dusun_id_bayi" id="dusun_id_bayi">
        </div>
        <div>
            <label for="keterangan_bayi" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
            <textarea name="keterangan_bayi" id="keterangan_bayi" rows="2"
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors"
                      placeholder="Keterangan tambahan (opsional)"></textarea>
        </div>
        <div>
            <label for="nama_ayah" class="block text-sm font-medium text-gray-700 mb-2">Nama Ayah</label>
            <input type="text" name="nama_ayah" id="nama_ayah"
                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors"
                   placeholder="Nama ayah bayi" required>
        </div>
        <div>
            <label for="nama_ibu" class="block text-sm font-medium text-gray-700 mb-2">Nama Ibu</label>
            <input type="text" name="nama_ibu" id="nama_ibu"
                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors"
                   placeholder="Nama ibu bayi" required>
        </div>
        <div>
            <label for="tanggal_mutasi_kelahiran" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mutasi</label>
            <input type="date" name="tanggal_mutasi" id="tanggal_mutasi_kelahiran"
                   value="{{ old('tanggal_mutasi', now()->format('Y-m-d')) }}"
                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors" required>
        </div>
    </div>
</div>

