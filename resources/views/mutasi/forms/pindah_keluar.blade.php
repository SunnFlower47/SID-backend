<!-- Form Pindah Keluar -->
<div id="formPindahKeluar" class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl p-6 border border-orange-200" style="display: none;">
    <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
        <i class="fas fa-user-minus text-orange-600 mr-3"></i>
        Data Pindah Keluar
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="penduduk_id_pindah_keluar" class="block text-sm font-medium text-gray-700 mb-2">Penduduk yang Pindah</label>
            <div class="relative">
                <input type="text" id="penduduk_search_pindah_keluar"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500"
                       placeholder="Cari penduduk (NIK, nama, atau No KK)..."
                       autocomplete="off">
                <input type="hidden" name="penduduk_id" id="penduduk_id_pindah_keluar">
                <div id="penduduk_search_results_pindah_keluar" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                <div id="penduduk_search_loading_pindah_keluar" class="absolute right-3 top-3 hidden">
                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                </div>
            </div>
            <div id="selected_penduduk_pindah_keluar" class="mt-2 hidden">
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-orange-900" id="selected_penduduk_name_pindah_keluar"></p>
                            <p class="text-sm text-orange-700" id="selected_penduduk_info_pindah_keluar"></p>
                        </div>
                        <button type="button" onclick="clearPendudukSelection('pindah_keluar')" class="text-orange-400 hover:text-orange-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Container for Anggota Keluarga Lainnya (Pindah Kolektif) -->
            <div id="anggota_keluarga_container_pindah_keluar" class="mt-4 hidden">
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Anggota Keluarga Lainnya</span>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="check_all_anggota_pindah" class="rounded border-gray-300 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                            <span class="ml-2 text-xs text-gray-600">Pilih Semua (Pindah 1 Keluarga)</span>
                        </label>
                    </div>
                    <div id="anggota_keluarga_list_pindah_keluar" class="divide-y divide-gray-100 max-h-48 overflow-y-auto">
                        <!-- Checkboxes will be rendered here by JS -->
                    </div>
                    <div id="anggota_keluarga_loading_pindah_keluar" class="p-4 text-center hidden">
                        <i class="fas fa-spinner fa-spin text-orange-500 mr-2"></i><span class="text-sm text-gray-500">Memuat anggota keluarga...</span>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <label for="kategori_mutasi_pindah_keluar" class="block text-sm font-medium text-gray-700 mb-2">Kategori Tujuan (Pindah Ke)</label>
            <select name="kategori_mutasi" id="kategori_mutasi_pindah_keluar"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500" required>
                <option value="">Pilih kategori...</option>
                <option value="dalam_kota">Dalam Kabupaten / Kota</option>
                <option value="luar_kota">Luar Kabupaten / Kota</option>
                <option value="luar_negeri">Luar Negeri</option>
            </select>
        </div>
        <div class="md:col-span-2 bg-orange-50/50 p-4 rounded-lg border border-orange-100">
            <label class="block text-sm font-medium text-gray-700 mb-2">Detail Tujuan Pindah</label>
            <!-- Container Dalam Negeri -->
            <div id="container_tujuan_dalam_negeri" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <input type="text" id="pk_alamat_jalan" 
                           class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm"
                           placeholder="Jalan / Gang / Blok / No. Rumah">
                </div>
                <div>
                    <div class="flex space-x-2">
                        <input type="text" id="pk_rt" placeholder="RT" class="block w-1/2 border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm">
                        <input type="text" id="pk_rw" placeholder="RW" class="block w-1/2 border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm">
                    </div>
                </div>
                <div>
                    <input type="text" id="pk_desa" placeholder="Desa / Kelurahan" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm">
                </div>
                <div>
                    <input type="text" id="pk_kecamatan" placeholder="Kecamatan" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm">
                </div>
                <div>
                    <input type="text" id="pk_kabupaten" placeholder="Kabupaten / Kota" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm">
                </div>
                <div class="md:col-span-2">
                    <input type="text" id="pk_provinsi" placeholder="Provinsi" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm">
                </div>
            </div>

            <!-- Container Luar Negeri -->
            <div id="container_tujuan_luar_negeri" class="grid grid-cols-1 gap-4 hidden">
                <div>
                    <label for="pk_negara_tujuan" class="block text-xs font-medium text-gray-500 mb-1">Negara Tujuan</label>
                    <input type="text" id="pk_negara_tujuan" 
                           class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm"
                           placeholder="Contoh: Malaysia, Arab Saudi, Taiwan">
                </div>
                <div>
                    <label for="pk_alamat_luar_negeri" class="block text-xs font-medium text-gray-500 mb-1">Detail Alamat (Opsional)</label>
                    <input type="text" id="pk_alamat_luar_negeri" 
                           class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm"
                           placeholder="Kota / Distrik / Info Tambahan">
                </div>
            </div>
            
            <!-- Hidden Input for Database -->
            <input type="hidden" name="asal_tujuan" id="asal_tujuan_pindah_keluar" required>
            
            @noncescript
                document.addEventListener('DOMContentLoaded', function() {
                    const pkFields = ['pk_alamat_jalan', 'pk_rt', 'pk_rw', 'pk_desa', 'pk_kecamatan', 'pk_kabupaten', 'pk_provinsi', 'pk_negara_tujuan', 'pk_alamat_luar_negeri'];
                    const targetField = document.getElementById('asal_tujuan_pindah_keluar');
                    const kategoriSelect = document.getElementById('kategori_mutasi_pindah_keluar');
                    const containerDalamNegeri = document.getElementById('container_tujuan_dalam_negeri');
                    const containerLuarNegeri = document.getElementById('container_tujuan_luar_negeri');
                    
                    // Handle dropdown change
                    if(kategoriSelect) {
                        kategoriSelect.addEventListener('change', function() {
                            if (this.value === 'luar_negeri') {
                                containerDalamNegeri.classList.add('hidden');
                                containerLuarNegeri.classList.remove('hidden');
                            } else {
                                containerLuarNegeri.classList.add('hidden');
                                containerDalamNegeri.classList.remove('hidden');
                            }
                            // Re-calculate tujuan whenever kategori changes
                            updateTujuan();
                        });
                    }

                    function updateTujuan() {
                        const parts = [];
                        const isLuarNegeri = kategoriSelect && kategoriSelect.value === 'luar_negeri';

                        if (isLuarNegeri) {
                            const negara = document.getElementById('pk_negara_tujuan').value.trim();
                            const alamatLn = document.getElementById('pk_alamat_luar_negeri').value.trim();
                            
                            if (negara) parts.push(`Negara: ${negara}`);
                            if (alamatLn) parts.push(alamatLn);
                            
                        } else {
                            const jalan = document.getElementById('pk_alamat_jalan').value.trim();
                            if(jalan) parts.push(jalan);
                            
                            const rt = document.getElementById('pk_rt').value.trim();
                            const rw = document.getElementById('pk_rw').value.trim();
                            if(rt || rw) parts.push(`RT ${rt}/RW ${rw}`);
                            
                            const desa = document.getElementById('pk_desa').value.trim();
                            if(desa) parts.push(`Desa ${desa}`);
                            
                            const kec = document.getElementById('pk_kecamatan').value.trim();
                            if(kec) parts.push(`Kec. ${kec}`);
                            
                            const kab = document.getElementById('pk_kabupaten').value.trim();
                            if(kab) parts.push(`Kab. ${kab}`);
                            
                            const prov = document.getElementById('pk_provinsi').value.trim();
                            if(prov) parts.push(`Prov. ${prov}`);
                        }
                        
                        targetField.value = parts.join(', ');
                    }
                    
                    pkFields.forEach(id => {
                        document.getElementById(id)?.addEventListener('input', updateTujuan);
                    });
                });
            @endnoncescript
        </div>
        <div>
            <label for="tanggal_mutasi_pindah_keluar" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pindah</label>
            <input type="date" name="tanggal_mutasi" id="tanggal_mutasi_pindah_keluar"
                   value="{{ now()->format('Y-m-d') }}"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500" required>
        </div>
        <div class="md:col-span-2">
            <label for="alasan_pindah_keluar" class="block text-sm font-medium text-gray-700 mb-2">Alasan Pindah</label>
            <textarea name="alasan" id="alasan_pindah_keluar"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500"
                   placeholder="Alasan pindah keluar" rows="3" required></textarea>
        </div>
    </div>
</div>

