@extends('layouts.app')

@section('title', 'Edit Struktur Desa')

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
                    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">Edit Struktur Desa</h1>
                    <p class="text-green-100 text-sm sm:text-base">Ubah data struktur organisasi dan kepemimpinan desa</p>
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
        <div class="px-6 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-edit text-green-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Form Edit Struktur Desa</h3>
                    <p class="text-sm text-gray-600">Ubah data struktur organisasi desa</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <form action="{{ route('struktur-desa.update', $strukturDesa) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                <!-- Nama dan Jabatan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="nama"
                               name="nama"
                               value="{{ old('nama', $strukturDesa->nama) }}"
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
                               value="{{ old('jabatan', $strukturDesa->jabatan) }}"
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
                                <option value="kepala_desa" {{ old('kategori', $strukturDesa->kategori) == 'kepala_desa' ? 'selected' : '' }}>Kepala Desa</option>
                                <option value="sekretaris" {{ old('kategori', $strukturDesa->kategori) == 'sekretaris' ? 'selected' : '' }}>Sekretaris Desa</option>
                                <option value="bendahara" {{ old('kategori', $strukturDesa->kategori) == 'bendahara' ? 'selected' : '' }}>Bendahara Desa</option>
                                <option value="kasi_pemerintahan" {{ old('kategori', $strukturDesa->kategori) == 'kasi_pemerintahan' ? 'selected' : '' }}>Kasi Pemerintahan</option>
                                <option value="kasi_kesejahteraan" {{ old('kategori', $strukturDesa->kategori) == 'kasi_kesejahteraan' ? 'selected' : '' }}>Kasi Kesejahteraan</option>
                                <option value="kasi_pelayanan" {{ old('kategori', $strukturDesa->kategori) == 'kasi_pelayanan' ? 'selected' : '' }}>Kasi Pelayanan</option>
                                <option value="kepala_dusun" {{ old('kategori', $strukturDesa->kategori) == 'kepala_dusun' ? 'selected' : '' }}>Kepala Dusun</option>
                                <option value="ketua_rt" {{ old('kategori', $strukturDesa->kategori) == 'ketua_rt' ? 'selected' : '' }}>Ketua RT</option>
                                <option value="ketua_rw" {{ old('kategori', $strukturDesa->kategori) == 'ketua_rw' ? 'selected' : '' }}>Ketua RW</option>
                                <option value="ketua_bumdes" {{ old('kategori', $strukturDesa->kategori) == 'ketua_bumdes' ? 'selected' : '' }}>Ketua BUMDes</option>
                                <option value="staf_kaur" {{ old('kategori', $strukturDesa->kategori) == 'staf_kaur' ? 'selected' : '' }}>Staf KAUR</option>
                                <option value="lainnya" {{ old('kategori', $strukturDesa->kategori) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
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
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="1" {{ old('status_aktif', $strukturDesa->status_aktif) == '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('status_aktif', $strukturDesa->status_aktif) == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                    </div>

                    <!-- Cari Penduduk (NIK) -->
                    <div class="mb-6">
                        <label for="nik_search" class="block text-sm font-medium text-gray-700 mb-2">
                            Cari Penduduk (NIK) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text"
                                   id="nik_search"
                                   name="nik_search"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Ketik NIK penduduk untuk mencari..."
                                   autocomplete="off">
                            <input type="hidden" name="nik" id="nik" value="{{ old('nik', $strukturDesa->nik) }}">

                            <!-- Loading indicator -->
                            <div id="search_loading" class="absolute right-3 top-3 hidden">
                                <i class="fas fa-spinner fa-spin text-gray-400"></i>
                            </div>

                            <!-- Search results -->
                            <div id="search_results" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                        </div>

                        <!-- Selected penduduk display -->
                        <div id="selected_penduduk" class="mt-3 hidden">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-green-900" id="selected_penduduk_name"></p>
                                        <p class="text-sm text-green-700" id="selected_penduduk_info"></p>
                                    </div>
                                    <button type="button" onclick="clearPendudukSelection()" class="text-green-400 hover:text-green-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        @error('nik')
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
                                   value="{{ old('no_hp', $strukturDesa->no_hp) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('no_hp') border-red-500 @enderror"
                                   placeholder="Masukkan nomor HP">
                            @error('no_hp')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Email dan Alamat -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $strukturDesa->email) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror"
                                   placeholder="Masukkan email">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat
                            </label>
                            <input type="text"
                                   id="alamat"
                                   name="alamat"
                                   value="{{ old('alamat', $strukturDesa->alamat) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('alamat') border-red-500 @enderror"
                                   placeholder="Masukkan alamat">
                            @error('alamat')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
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
                                    <option value="{{ $rw['id'] }}" {{ old('rw_id', $strukturDesa->rw_id) == $rw['id'] ? 'selected' : '' }}>RW {{ $rw['kode'] }} - {{ $rw['nama'] }}</option>
                                @endforeach
                            </select>
                            @error('rw_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="rt_id" class="block text-sm font-medium text-gray-700 mb-2">
                                RT Master <span class="text-red-500">*</span>
                                @if(!$strukturDesa->rt_id)
                                    <span class="text-red-600 text-xs font-bold animate-pulse">(DATA TIDAK VALID)</span>
                                @endif
                            </label>
                            <select id="rt_id" name="rt_id" onchange="syncDusunFromRt()" required
                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @if(!$strukturDesa->rt_id) border-red-500 bg-red-50 @else border-gray-300 @endif @error('rt_id') border-red-500 @enderror">
                                <option value="">Pilih RT</option>
                            </select>
                            @error('rt_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="dusun_display" class="block text-sm font-medium text-gray-700 mb-2">Dusun</label>
                            <input type="text" id="dusun_display" value="{{ $strukturDesa->dusun_label }}" disabled
                                   class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-lg text-gray-500"
                                   placeholder="Otomatis dari RT">
                            <input type="hidden" name="dusun_id" id="dusun_id" value="{{ old('dusun_id', $strukturDesa->dusun_id) }}">
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
                                  placeholder="Jelaskan tugas dan wewenang">{{ old('tugas_wewenang', $strukturDesa->tugas_wewenang) }}</textarea>
                        @error('tugas_wewenang')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Foto -->
                    <div>
                        <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">
                            Foto
                        </label>
                        @if($strukturDesa->foto)
                        <div class="mb-4">
                            <img src="{{ Storage::url($strukturDesa->foto) }}" alt="Foto saat ini" class="w-32 h-32 object-cover rounded-lg">
                            <p class="text-sm text-gray-500 mt-2">Foto saat ini</p>
                        </div>
                        @endif
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-camera text-gray-400 text-4xl mb-4"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="foto" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                                        <span>Upload foto baru</span>
                                        <input id="foto" name="foto" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">atau drag & drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF hingga 2MB</p>
                            </div>
                        </div>
                        @error('foto')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Urutan -->
                    <div>
                        <label for="urutan" class="block text-sm font-medium text-gray-700 mb-2">
                            Urutan Tampil
                        </label>
                        <input type="number"
                               id="urutan"
                               name="urutan"
                               value="{{ old('urutan', $strukturDesa->urutan) }}"
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
                    Update Data
                </button>
            </div>
        </div>
        </form>
        </div>
    </div>
</div>

@noncescript
    const masterRwOptions = @json($masterRwOptions);
    const currentRtId = "{{ $strukturDesa->rt_id }}";
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
                if (initial && String(rt.id) === String(targetRtId || currentRtId)) {
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
        const loading = document.getElementById('search_loading');
        if (loading) {
            if (show) {
                loading.classList.remove('hidden');
            } else {
                loading.classList.add('hidden');
            }
        }
    }

    function displaySearchResults(results) {
        const resultsContainer = document.getElementById('search_results');
        if (!resultsContainer) return;

        resultsContainer.innerHTML = '';

        results.forEach(penduduk => {
            const resultItem = document.createElement('div');
            resultItem.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0';
            resultItem.innerHTML = `
                <div class="font-medium text-gray-900">${penduduk.nama}</div>
                <div class="text-sm text-gray-600">NIK: ${penduduk.nik}</div>
                <div class="text-sm text-gray-500">${penduduk.alamat} - RT ${penduduk.rt_label}/RW ${penduduk.rw_label}</div>
            `;

            resultItem.addEventListener('click', () => selectPenduduk(penduduk));
            resultsContainer.appendChild(resultItem);
        });

        resultsContainer.classList.remove('hidden');
    }

    function hideSearchResults() {
        const resultsContainer = document.getElementById('search_results');
        if (resultsContainer) {
            resultsContainer.classList.add('hidden');
        }
    }

    function selectPenduduk(penduduk) {
        // Set hidden input values
        document.getElementById('nik').value = penduduk.nik;

        // Auto-fill form fields
        document.getElementById('nama').value = penduduk.nama;
        document.getElementById('alamat').value = penduduk.alamat;
        
        // Handle Wilayah IDs
        if (penduduk.rw_id && penduduk.rw_id !== 'null') {
            document.getElementById('rw_id').value = penduduk.rw_id;
            populateRtByRw(true, penduduk.rt_id);
            if (penduduk.rt_id && penduduk.rt_id !== 'null') {
                syncDusunFromRt();
            }
        }

        // Show selected penduduk info
        document.getElementById('selected_penduduk_name').textContent = penduduk.nama;
        document.getElementById('selected_penduduk_info').textContent = `NIK: ${penduduk.nik} - ${penduduk.alamat}`;
        document.getElementById('selected_penduduk').classList.remove('hidden');

        // Hide search results
        hideSearchResults();

        // Clear search input
        document.getElementById('nik_search').value = '';
    }

    function clearPendudukSelection() {
        // Clear hidden input
        document.getElementById('nik').value = '';

        // Clear form fields
        document.getElementById('nama').value = '';
        document.getElementById('alamat').value = '';
        document.getElementById('rw_id').value = '';
        document.getElementById('rt_id').innerHTML = '<option value="">Pilih RT</option>';
        document.getElementById('dusun_display').value = '';
        document.getElementById('dusun_id').value = '';

        // Hide selected penduduk display
        document.getElementById('selected_penduduk').classList.add('hidden');

        // Clear search input
        document.getElementById('nik_search').value = '';
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Initial populate
        if (document.getElementById('rw_id').value) {
            populateRtByRw(true);
        }

        const searchInput = document.getElementById('nik_search');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();

                clearTimeout(searchTimeout);

                if (query.length >= 3) {
                    searchTimeout = setTimeout(() => {
                        searchPenduduk(query);
                    }, 300);
                } else {
                    hideSearchResults();
                }
            });

            // Hide results when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#nik_search') && !e.target.closest('#search_results')) {
                    hideSearchResults();
                }
            });
        }
    });
@endnoncescript
@endnoncescript
@endsection

