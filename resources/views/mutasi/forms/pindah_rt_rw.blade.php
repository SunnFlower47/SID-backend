<!-- Form Pindah RT/RW -->
<div id="formPindahRTRW" class="bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl p-6 border border-purple-200" style="display: none;">
    <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
        <i class="fas fa-home text-purple-600 mr-3"></i>
        Data Pindah RT/RW/Dusun
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="nkk_pindah_rt_rw" class="block text-sm font-medium text-gray-700 mb-2">No KK yang Akan Pindah</label>
            <div class="relative">
                <input type="text" id="nkk_search_pindah_rt_rw" name="nkk_search_pindah_rt_rw"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500"
                       placeholder="Cari No KK (NKK atau nama kepala keluarga)..."
                       maxlength="16"
                       autocomplete="off">
                <input type="hidden" name="nkk" id="nkk_pindah_rt_rw">
                <div id="nkk_search_results_pindah_rt_rw" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                <div id="nkk_search_loading_pindah_rt_rw" class="absolute right-3 top-3 hidden">
                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                </div>
            </div>
            <div id="selected_nkk_pindah_rt_rw" class="mt-2 hidden">
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-purple-900" id="selected_nkk_name_pindah_rt_rw"></p>
                            <p class="text-sm text-purple-700" id="selected_nkk_info_pindah_rt_rw"></p>
                        </div>
                        <button type="button" onclick="clearNKKSelection('pindah_rt_rw')" class="text-purple-400 hover:text-purple-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <label for="rw_id_tujuan" class="block text-sm font-medium text-gray-700 mb-2">RW Tujuan</label>
            <select name="rw_id_tujuan" id="rw_id_tujuan" onchange="populateRtByRwMutasi(this.value, 'rt_id_tujuan')"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500" required>
                <option value="">Pilih RW...</option>
                @foreach(($masterRwOptions ?? []) as $rw)
                    <option value="{{ $rw['id'] }}">RW {{ $rw['kode'] }} - {{ $rw['nama'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="rt_id_tujuan" class="block text-sm font-medium text-gray-700 mb-2">RT Tujuan</label>
            <select name="rt_id_tujuan" id="rt_id_tujuan" onchange="syncDusunByRtMutasi(this.value, document.getElementById('rw_id_tujuan').value, 'dusun_id_tujuan')"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500" required>
                <option value="">Pilih RT...</option>
            </select>
        </div>
        <div>
            <label for="dusun_id_tujuan_label" class="block text-sm font-medium text-gray-700 mb-2">Dusun Tujuan</label>
            <input type="text" id="dusun_id_tujuan_label"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 bg-gray-50"
                   placeholder="Dusun" readonly>
            <input type="hidden" name="dusun_id_tujuan" id="dusun_id_tujuan">
        </div>
        <div>
            <label for="alamat_tujuan" class="block text-sm font-medium text-gray-700 mb-2">Alamat Tujuan</label>
            <input type="text" name="alamat_tujuan" id="alamat_tujuan"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500"
                   placeholder="Alamat lengkap tujuan">
        </div>
        <div>
            <label for="tanggal_mutasi_pindah_rt_rw" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pindah</label>
            <input type="date" name="tanggal_mutasi" id="tanggal_mutasi_pindah_rt_rw"
                   value="{{ old('tanggal_mutasi', now()->format('Y-m-d')) }}"
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500" required>
        </div>
        <!-- Info Pindah Satu KK -->
        <div class="md:col-span-2">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-green-600 mr-2 mt-1"></i>
                    <div>
                        <h4 class="font-medium text-green-900">Pindah RT/RW Satu KK</h4>
                        <p class="text-sm text-green-700 mt-1">
                            <strong>Seluruh anggota keluarga</strong> akan ikut pindah ke RT/RW/Dusun baru.
                            No KK tetap sama, hanya alamat yang berubah.
                        </p>
                        <p class="text-sm text-blue-700 mt-2">
                            <i class="fas fa-lightbulb mr-1"></i>
                            <strong>Catatan:</strong> Untuk memisahkan anggota keluarga (buat KK baru), gunakan menu <strong>"Pisah KK"</strong>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

