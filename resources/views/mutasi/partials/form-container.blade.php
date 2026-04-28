<!-- Form Container -->
<div class="bg-white rounded-2xl shadow-lg border-0 p-6 sm:p-8">

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Terjadi kesalahan validasi:
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="flex items-center space-x-3 mb-8">
        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
            <i class="fas fa-edit text-green-600"></i>
        </div>
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Form Data Mutasi</h3>
            <p class="text-sm text-gray-600">Lengkapi data mutasi penduduk dengan benar</p>
        </div>
    </div>

    <form action="{{ route('mutasi.data.store') }}" method="POST" class="space-y-8" id="mutasiForm">
        @csrf

        <!-- Pilih Jenis Mutasi -->
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6 sm:p-8 border border-green-200">
            <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-exchange-alt text-green-600"></i>
                </div>
                Pilih Jenis Mutasi
            </h3>
            <div>
                <label for="jenis_mutasi" class="block text-sm font-medium text-gray-700 mb-3">
                    <i class="fas fa-list-alt text-green-600 mr-2"></i>Jenis Mutasi
                </label>
                <select name="jenis_mutasi" id="jenis_mutasi"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition-colors" required>
                    <option value="">Pilih jenis mutasi...</option>
                    <option value="kelahiran">Kelahiran</option>
                    <option value="kematian">Kematian</option>
                    <option value="pindah_masuk">Pindah Masuk</option>
                    <option value="pindah_keluar">Pindah Keluar</option>
                    <option value="pindah_rt_rw">Pindah RT/RW/Dusun</option>
                    <option value="pisah_kk">Pisah KK</option>
                </select>
                @error('jenis_mutasi') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Form Dinamis Berdasarkan Jenis Mutasi -->
        <div id="formContent" class="space-y-6">
            <div id="defaultMessage" class="text-center py-12 text-gray-500">
                <i class="fas fa-info-circle text-4xl mb-4"></i>
                <p class="text-lg">Pilih jenis mutasi untuk menampilkan form</p>
            </div>

            @include('mutasi.forms.kelahiran')
            @include('mutasi.forms.kematian')
            @include('mutasi.forms.pindah_masuk')
            @include('mutasi.forms.pindah_keluar')
            @include('mutasi.forms.pindah_rt_rw')
            @include('mutasi.forms.pisah_kk')
        </div>

        <!-- Submit Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
            <button type="submit" id="submitBtn" class="flex-1 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                <i class="fas fa-save mr-2"></i>
                Simpan Mutasi
            </button>
            <a href="{{ route('mutasi.data.index') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 text-center">
                <i class="fas fa-times mr-2"></i>
                Batal
            </a>
        </div>
    </form>
</div>

<script nonce="{{ $csp_nonce ?? '' }}">
    const masterRwOptions = @json($masterRwOptions ?? []);

    function populateRtByRwMutasi(rwId, rtSelectId, callback) {
        const rtSelect = document.getElementById(rtSelectId);
        if (!rtSelect) return;

        rtSelect.innerHTML = '<option value="">Pilih RT...</option>';
        const rwObj = masterRwOptions.find(r => String(r.id) === String(rwId));
        if (rwObj && rwObj.rts) {
            rwObj.rts.forEach(rt => {
                const opt = document.createElement('option');
                opt.value = rt.id;
                opt.textContent = `RT ${rt.kode}${rt.dusun ? ` - ${rt.dusun}` : ''}`;
                rtSelect.appendChild(opt);
            });
        }
        if (callback) callback();
    }

    function syncDusunByRtMutasi(rtId, rwId, baseId) {
        const dusunLabel = document.getElementById(baseId + '_label');
        const dusunHidden = document.getElementById(baseId);
        
        const rwObj = masterRwOptions.find(r => String(r.id) === String(rwId));
        const rtObj = rwObj?.rts?.find(r => String(r.id) === String(rtId));
        
        const dusunName = rtObj?.dusun || '';
        
        if (dusunLabel) dusunLabel.value = dusunName;
        if (dusunHidden) dusunHidden.value = rtObj?.id ? (rtObj.dusun_id || '') : ''; // wait, rtObj has dusun name, but we need dusun_id too
        
        // Let's refine the masterRwOptions structure in the controller to include dusun_id
    }
</script>

