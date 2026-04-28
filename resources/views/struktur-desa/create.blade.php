@extends('layouts.app')

@section('title', 'Tambah Struktur Desa')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center mb-4 sm:mb-0">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-sitemap text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">Tambah Struktur Desa</h1>
                    <p class="text-green-100 text-sm sm:text-base">Tambah data struktur organisasi dan kepemimpinan desa</p>
                </div>
            </div>
            <a href="{{ route('struktur-desa.index') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
        <form action="{{ route('struktur-desa.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-6">
                <!-- Cari Penduduk (NIK) -->
                <div>
                    <label for="nik_search" class="block text-sm font-medium text-gray-700 mb-2">
                        Cari Penduduk (NIK) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" id="nik_search" name="nik_search"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nik') border-red-500 @enderror"
                               placeholder="Ketik NIK penduduk..."
                               value="{{ old('nik_search') }}"
                               autocomplete="off">
                        <div id="nik_search_loading" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                            <i class="fas fa-spinner fa-spin text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Search Results -->
                    <div id="nik_search_results" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
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

                    <!-- Hidden input for nik -->
                    <input type="hidden" name="nik" id="nik" value="{{ old('nik') }}" required>

                    @error('nik')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama dan Jabatan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="nama"
                               name="nama"
                               value="{{ old('nama') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nama') border-red-500 @enderror"
                               placeholder="Masukkan nama lengkap"
                               required>
                        @error('nama')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="jabatan" class="block text-sm font-medium text-gray-700 mb-2">
                            Jabatan <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="jabatan"
                               name="jabatan"
                               value="{{ old('jabatan') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('jabatan') border-red-500 @enderror"
                               placeholder="Masukkan jabatan"
                               required>
                        @error('jabatan')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Kategori dan Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select id="kategori"
                                name="kategori"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('kategori') border-red-500 @enderror"
                                required>
                            <option value="">Pilih Kategori</option>
                            <option value="kepala_desa" {{ old('kategori') == 'kepala_desa' ? 'selected' : '' }}>Kepala Desa</option>
                            <option value="sekretaris" {{ old('kategori') == 'sekretaris' ? 'selected' : '' }}>Sekretaris</option>
                            <option value="bendahara" {{ old('kategori') == 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                            <option value="kasi_pemerintahan" {{ old('kategori') == 'kasi_pemerintahan' ? 'selected' : '' }}>Kasi Pemerintahan</option>
                            <option value="kasi_kesejahteraan" {{ old('kategori') == 'kasi_kesejahteraan' ? 'selected' : '' }}>Kasi Kesejahteraan</option>
                            <option value="kasi_pelayanan" {{ old('kategori') == 'kasi_pelayanan' ? 'selected' : '' }}>Kasi Pelayanan</option>
                            <option value="kepala_dusun" {{ old('kategori') == 'kepala_dusun' ? 'selected' : '' }}>Kepala Dusun</option>
                            <option value="ketua_rt" {{ old('kategori') == 'ketua_rt' ? 'selected' : '' }}>Ketua RT</option>
                            <option value="ketua_rw" {{ old('kategori') == 'ketua_rw' ? 'selected' : '' }}>Ketua RW</option>
                            <option value="ketua_bumdes" {{ old('kategori') == 'ketua_bumdes' ? 'selected' : '' }}>Ketua BUMDes</option>
                            <option value="staf_kaur" {{ old('kategori') == 'staf_kaur' ? 'selected' : '' }}>Staf KAUR</option>
                            <option value="lainnya" {{ old('kategori') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('kategori')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status_aktif" class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>
                        <select id="status_aktif"
                                name="status_aktif"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('status_aktif') border-red-500 @enderror">
                            <option value="1" {{ old('status_aktif', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('status_aktif') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        @error('status_aktif')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Foto -->
                <div>
                    <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">
                        Foto
                    </label>
                    <input type="file"
                           id="foto"
                           name="foto"
                           accept="image/*"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('foto') border-red-500 @enderror">
                    @error('foto')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kontak -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-2">
                            No. HP
                        </label>
                        <input type="text"
                               id="no_hp"
                               name="no_hp"
                               value="{{ old('no_hp') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('no_hp') border-red-500 @enderror"
                               placeholder="Masukkan nomor HP">
                        @error('no_hp')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror"
                               placeholder="Masukkan email">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Alamat -->
                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat
                    </label>
                    <textarea id="alamat"
                              name="alamat"
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('alamat') border-red-500 @enderror"
                              placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- RT, RW, Dusun -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="rw_id" class="block text-sm font-medium text-gray-700 mb-2">
                            RW Master <span class="text-red-500">*</span>
                        </label>
                        <select id="rw_id" name="rw_id" onchange="populateRtByRw()" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('rw_id') border-red-500 @enderror">
                            <option value="">Pilih RW</option>
                            @foreach($masterRwOptions as $rw)
                                <option value="{{ $rw['id'] }}" {{ old('rw_id') == $rw['id'] ? 'selected' : '' }}>RW {{ $rw['kode'] }} - {{ $rw['nama'] }}</option>
                            @endforeach
                        </select>
                        @error('rw_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="rt_id" class="block text-sm font-medium text-gray-700 mb-2">
                            RT Master <span class="text-red-500">*</span>
                        </label>
                        <select id="rt_id" name="rt_id" onchange="syncDusunFromRt()" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('rt_id') border-red-500 @enderror">
                            <option value="">Pilih RT</option>
                        </select>
                        @error('rt_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="dusun_display" class="block text-sm font-medium text-gray-700 mb-2">Dusun</label>
                        <input type="text" id="dusun_display" disabled
                               class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-lg text-gray-500"
                               placeholder="Otomatis dari RT">
                        <input type="hidden" name="dusun_id" id="dusun_id" value="{{ old('dusun_id') }}">
                    </div>
                </div>

                <!-- Tugas dan Wewenang -->
                <div>
                    <label for="tugas_wewenang" class="block text-sm font-medium text-gray-700 mb-2">
                        Tugas dan Wewenang
                    </label>
                    <textarea id="tugas_wewenang"
                              name="tugas_wewenang"
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('tugas_wewenang') border-red-500 @enderror"
                              placeholder="Masukkan tugas dan wewenang">{{ old('tugas_wewenang') }}</textarea>
                    @error('tugas_wewenang')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Urutan Tampil -->
                <div>
                    <label for="urutan" class="block text-sm font-medium text-gray-700 mb-2">
                        Urutan Tampil
                    </label>
                    <input type="number"
                           id="urutan"
                           name="urutan"
                           value="{{ old('urutan') }}"
                           min="1"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('urutan') border-red-500 @enderror"
                           placeholder="Masukkan urutan tampil">
                    @error('urutan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
                    <a href="{{ route('struktur-desa.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    <button type="submit" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Data
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@noncescript
    const masterRwOptions = @json($masterRwOptions);
    let searchTimeout;

    function populateRtByRw(initial = false, targetRtId = null) {
        const rwId = document.getElementById('rw_id').value;
        const rtSelect = document.getElementById('rt_id');
        rtSelect.innerHTML = '<option value="">Pilih RT</option>';

        const rwObj = masterRwOptions.find(r => String(r.id) === String(rwId));
        if (rwObj) {
            rwObj.rts.forEach(rt => {
                const opt = document.createElement('option');
                opt.value = rt.id;
                opt.textContent = `RT ${rt.kode}${rt.dusun ? ' - ' + rt.dusun : ''}`;
                if (targetRtId && String(rt.id) === String(targetRtId)) {
                    opt.selected = true;
                }
                rtSelect.appendChild(opt);
            });
        }
        if (!initial) syncDusunFromRt();
    }

    function syncDusunFromRt() {
        const rwId = document.getElementById('rw_id').value;
        const rtId = document.getElementById('rt_id').value;
        const dusunDisplay = document.getElementById('dusun_display');
        const dusunHidden = document.getElementById('dusun_id');

        const rwObj = masterRwOptions.find(r => String(r.id) === String(rwId));
        const rtObj = rwObj?.rts?.find(r => String(r.id) === String(rtId));

        if (rtObj) {
            dusunDisplay.value = rtObj.dusun || 'N/A';
            dusunHidden.value = rtObj.dusun_id || '';
        } else {
            dusunDisplay.value = '';
            dusunHidden.value = '';
        }
    }

    // Penduduk search functions
    function searchPenduduk(query) {
        if (query.length < 3) {
            hideSearchResults();
            return;
        }

        showLoading(true);

        fetch(`/mutasi/search-penduduk?query=${encodeURIComponent(query)}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                showLoading(false);
                if (data && data.length > 0) {
                    displaySearchResults(data);
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
        const loading = document.getElementById('nik_search_loading');
        if (show) {
            loading.classList.remove('hidden');
        } else {
            loading.classList.add('hidden');
        }
    }

    function displaySearchResults(penduduks) {
        const resultsContainer = document.getElementById('nik_search_results');

        if (penduduks.length === 0) {
            resultsContainer.innerHTML = '<div class="p-3 text-gray-500 text-center">Tidak ada penduduk ditemukan</div>';
        } else {
            resultsContainer.innerHTML = penduduks.map(penduduk => `
                <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                     onclick="selectPenduduk('${penduduk.nik}', '${penduduk.nama}', '${penduduk.alamat}', '${penduduk.rt_id}', '${penduduk.rw_id}', '${penduduk.dusun_id}', '${penduduk.dusun_label}')">
                    <div class="font-medium text-gray-900">${penduduk.nama}</div>
                    <div class="text-sm text-gray-600">NIK: ${penduduk.nik}</div>
                    <div class="text-sm text-gray-500">${penduduk.alamat}</div>
                </div>
            `).join('');
        }

        resultsContainer.classList.remove('hidden');
    }

    function hideSearchResults() {
        document.getElementById('nik_search_results').classList.add('hidden');
    }

    function selectPenduduk(nik, nama, alamat, rt_id, rw_id, dusun_id, dusun_label) {
        // Update hidden input
        document.getElementById('nik').value = nik;

        // Update display
        document.getElementById('selected_nama').textContent = nama;
        document.getElementById('selected_nik').textContent = nik;
        document.getElementById('selected_alamat').textContent = alamat;
        document.getElementById('selected_penduduk_display').classList.remove('hidden');

        // Auto-fill form fields
        document.getElementById('nama').value = nama;
        document.getElementById('alamat').value = alamat;
        
        // Handle Wilayah IDs
        if (rw_id && rw_id !== 'null') {
            document.getElementById('rw_id').value = rw_id;
            populateRtByRw(true, rt_id);
            if (rt_id && rt_id !== 'null') {
                syncDusunFromRt();
            }
        }

        // Clear search input
        document.getElementById('nik_search').value = '';

        // Hide results
        hideSearchResults();
    }

    function clearPendudukSelection() {
        document.getElementById('nik').value = '';
        document.getElementById('nik_search').value = '';
        document.getElementById('selected_penduduk_display').classList.add('hidden');

        // Clear auto-filled fields
        document.getElementById('nama').value = '';
        document.getElementById('alamat').value = '';
        document.getElementById('rw_id').value = '';
        document.getElementById('rt_id').innerHTML = '<option value="">Pilih RT</option>';
        document.getElementById('dusun_display').value = '';
        document.getElementById('dusun_id').value = '';
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Setup search event listener
        const searchInput = document.getElementById('nik_search');
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchPenduduk(this.value);
            }, 300);
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#nik_search') && !e.target.closest('#nik_search_results')) {
                hideSearchResults();
            }
        });
    });
@endnoncescript
@endnoncescript
@endsection

