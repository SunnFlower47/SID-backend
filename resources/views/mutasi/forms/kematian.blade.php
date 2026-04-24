<!-- Form Kematian -->
<div id="formKematian" class="bg-gradient-to-r from-red-50 to-rose-50 rounded-xl p-6 border border-red-200" style="display: none;">
    <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
        <i class="fas fa-user-times text-red-600 mr-3"></i>
        Data Kematian
    </h3>

    <!-- Data Penduduk yang Meninggal -->
    <div class="bg-white rounded-lg p-4 mb-6 border border-red-200">
        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-user text-red-600 mr-2"></i>
            Data Penduduk yang Meninggal
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="penduduk_id_kematian" class="block text-sm font-medium text-gray-700 mb-2">Penduduk yang Meninggal</label>
                <div class="relative">
                    <input type="text" id="penduduk_search_kematian"
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                           placeholder="Cari penduduk (NIK, nama, atau No KK)..."
                           autocomplete="off">
                    <input type="hidden" name="penduduk_id" id="penduduk_id_kematian" value="">
                    <div id="penduduk_search_results_kematian" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                    <div id="penduduk_search_loading_kematian" class="absolute right-3 top-3 hidden">
                        <i class="fas fa-spinner fa-spin text-gray-400"></i>
                    </div>
                </div>
                <div id="selected_penduduk_kematian" class="mt-2 hidden">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-red-900" id="selected_penduduk_name_kematian"></p>
                                <p class="text-sm text-red-700" id="selected_penduduk_info_kematian"></p>
                            </div>
                            <button type="button" onclick="clearPendudukSelection('kematian')" class="text-red-400 hover:text-red-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Data Penduduk</label>
                <div id="penduduk_data_display_kematian" class="mt-1 p-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600">
                    <p class="mb-1"><strong>Nama:</strong> <span id="display_nama_kematian">-</span></p>
                    <p class="mb-1"><strong>Jenis Kelamin:</strong> <span id="display_jenis_kelamin_kematian">-</span></p>
                    <p class="mb-1"><strong>Umur:</strong> <span id="display_umur_kematian">-</span></p>
                    <p class="mb-1"><strong>Agama:</strong> <span id="display_agama_kematian">-</span></p>
                    <p class="mb-0"><strong>Alamat:</strong> <span id="display_alamat_kematian">-</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Kematian -->
    <div class="bg-white rounded-lg p-4 border border-red-200">
        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-calendar-alt text-red-600 mr-2"></i>
            Detail Kematian
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label for="hari_meninggal" class="block text-sm font-medium text-gray-700 mb-2">Hari Meninggal</label>
                <select name="hari_meninggal" id="hari_meninggal"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required>
                    <option value="">Pilih hari...</option>
                    <option value="Senin">Senin</option>
                    <option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option>
                    <option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option>
                    <option value="Sabtu">Sabtu</option>
                    <option value="Minggu">Minggu</option>
                </select>
                @error('hari_meninggal') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="tanggal_mutasi_kematian" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Meninggal</label>
                <input type="date" name="tanggal_mutasi" id="tanggal_mutasi_kematian"
                       value="{{ old('tanggal_mutasi', now()->format('Y-m-d')) }}"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required>
                @error('tanggal_mutasi') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="jam_meninggal" class="block text-sm font-medium text-gray-700 mb-2">Jam Meninggal</label>
                <input type="time" name="jam_meninggal" id="jam_meninggal"
                       value="{{ old('jam_meninggal', '21:00') }}"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required>
                @error('jam_meninggal') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="bertempat_di" class="block text-sm font-medium text-gray-700 mb-2">Bertempat di</label>
                <input type="text" name="bertempat_di" id="bertempat_di"
                       value="{{ old('bertempat_di', 'Rumah Duka') }}"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required
                       placeholder="Rumah Duka, Rumah Sakit, dll">
                @error('bertempat_di') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label for="alasan" class="block text-sm font-medium text-gray-700 mb-2">Penyebab Kematian</label>
                <input type="text" name="alasan" id="alasan"
                       value="{{ old('alasan', 'Sakit') }}"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required
                       placeholder="Penyebab kematian">
                @error('alasan') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <!-- Detail Pemakaman -->
    <div class="bg-white rounded-lg p-4 border border-red-200">
        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>
            Dimakamkan pada
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="hari_pemakaman" class="block text-sm font-medium text-gray-700 mb-2">Hari</label>
                <select name="hari_pemakaman" id="hari_pemakaman"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required>
                    <option value="">Pilih hari...</option>
                    <option value="Senin">Senin</option>
                    <option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option>
                    <option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option>
                    <option value="Sabtu">Sabtu</option>
                    <option value="Minggu">Minggu</option>
                </select>
                @error('hari_pemakaman') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="tanggal_pemakaman" class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <input type="date" name="tanggal_pemakaman" id="tanggal_pemakaman"
                       value="{{ old('tanggal_pemakaman', now()->format('Y-m-d')) }}"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required>
                @error('tanggal_pemakaman') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="jam_pemakaman" class="block text-sm font-medium text-gray-700 mb-2">Jam</label>
                <input type="time" name="jam_pemakaman" id="jam_pemakaman"
                       value="{{ old('jam_pemakaman', '16:00') }}"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required>
                @error('jam_pemakaman') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="lokasi_pemakaman" class="block text-sm font-medium text-gray-700 mb-2">Dimakamkan di</label>
                <input type="text" name="lokasi_pemakaman" id="lokasi_pemakaman"
                       value="{{ old('lokasi_pemakaman', 'TPU Desa Cibatu') }}"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required
                       placeholder="TPU Desa Cibatu">
                @error('lokasi_pemakaman') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <!-- Data Pelapor (Optional) -->
    <div class="bg-white rounded-lg p-4 mt-6 border border-red-200">
        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-bullhorn text-red-600 mr-2"></i>
            Data Pelapor (Opsional)
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="pelapor_nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Pelapor</label>
                <input type="text" name="pelapor_nama" id="pelapor_nama"
                       value="{{ old('pelapor_nama') }}"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                       placeholder="Nama lengkap pelapor">
            </div>
            <div>
                <label for="pelapor_hubungan" class="block text-sm font-medium text-gray-700 mb-2">Hubungan dengan Jenazah</label>
                <input type="text" name="pelapor_hubungan" id="pelapor_hubungan"
                       value="{{ old('pelapor_hubungan') }}"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                       placeholder="Contoh: Anak / Suami / Istri">
            </div>
            <div>
                <label for="pelapor_umur" class="block text-sm font-medium text-gray-700 mb-2">Umur Pelapor</label>
                <input type="number" name="pelapor_umur" id="pelapor_umur"
                       value="{{ old('pelapor_umur') }}"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                       placehoder="Tahun">
            </div>
            <div>
                <label for="pelapor_pekerjaan" class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan Pelapor</label>
                <input type="text" name="pelapor_pekerjaan" id="pelapor_pekerjaan"
                       value="{{ old('pelapor_pekerjaan') }}"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
            </div>
            <div class="md:col-span-2">
                <label for="pelapor_alamat" class="block text-sm font-medium text-gray-700 mb-2">Alamat Pelapor</label>
                <input type="text" name="pelapor_alamat" id="pelapor_alamat"
                       value="{{ old('pelapor_alamat') }}"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                       placeholder="Alamat lengkap pelapor">
            </div>
        </div>
    </div>
</div>
