@extends('layouts.app')

@section('title', 'Edit Pengajuan Surat')
@section('subtitle', 'Edit data pengajuan surat')

@section('content')
<div class="space-y-6" x-data="{
    searchQuery: '',
    isLoading: false,
    results: [],
    selectedPenduduk: {{ $suratPengajuan->penduduk->toJson() }}, // Pre-fill penduduk
    showResults: false,
    hasSearched: false,
    jenisSurat: '{{ $suratPengajuan->jenis_surat }}', // Pre-fill jenis surat

    searchPenduduk() {
        if (this.searchQuery.length < 3) {
            this.results = [];
            this.showResults = false;
            return;
        }

        this.isLoading = true;
        this.hasSearched = false;

        fetch(`/penduduk/search?q=${this.searchQuery}`)
            .then(res => res.json())
            .then(data => {
                this.results = data;
                this.isLoading = false;
                this.showResults = true;
                this.hasSearched = true;
            })
            .catch(() => {
                this.isLoading = false;
            });
    },

    selectPenduduk(penduduk) {
        this.selectedPenduduk = penduduk;
        document.getElementById('penduduk_id').value = penduduk.id;
        this.searchQuery = ''; 
        this.showResults = false;
    },

    resetSelection() {
        this.selectedPenduduk = null;
        document.getElementById('penduduk_id').value = '';
        this.searchQuery = '';
    },
    
    hasDynamicFields() {
        return ['sku', 'sktm_dewasa', 'sktm_anak', 'domisili', 'keterangan-domisili', 'kematian', 'kelahiran', 'pindah', 'pengantar'].includes(this.jenisSurat);
    }
}">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-edit text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Edit Pengajuan Surat</h1>
                    <p class="text-blue-100 mt-1">Nomor Surat: {{ $suratPengajuan->nomor_surat }}</p>
                </div>
            </div>
            <a href="{{ route('admin.surat-pengajuan.index') }}" class="group flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white rounded-xl transition-all duration-300">
                <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Main Form -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <form action="{{ route('admin.surat-pengajuan.update', $suratPengajuan->id) }}" method="POST" class="p-6 sm:p-8 space-y-8">
            @csrf
            @method('PUT')

            <!-- SECTION 1: DATA PEMOHON -->
            <div>
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Data Pemohon</h3>
                        <p class="text-sm text-gray-500">Data penduduk yang mengajukan surat</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 relative">
                    <!-- Search Input (Hidden if selected) -->
                    <div class="space-y-2 relative" x-on:click.away="showResults = false" x-show="!selectedPenduduk">
                        <label class="block text-sm font-semibold text-gray-700">
                            Cari Penduduk <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text"
                                   x-model="searchQuery"
                                   x-on:input.debounce.300ms="searchPenduduk()"
                                   x-on:focus="showResults = true"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                   placeholder="Ketik Nama atau NIK..."
                                   autocomplete="off">
                            
                            <!-- Loading Indicator -->
                            <div x-show="isLoading" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-circle-notch fa-spin text-green-600"></i>
                            </div>
                        </div>

                        <!-- Dropdown Results -->
                        <div x-show="showResults && (results.length > 0 || hasSearched)"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translateY-2"
                             x-transition:enter-end="opacity-100 translateY-0"
                             class="absolute z-10 w-full bg-white mt-1 border border-gray-200 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                            
                            <template x-for="p in results" :key="p.id">
                                <button type="button"
                                        x-on:click="selectPenduduk(p)"
                                        class="w-full text-left px-4 py-3 hover:bg-green-50 transition-colors border-b border-gray-50 last:border-0">
                                    <div class="font-bold text-gray-900" x-text="p.nama"></div>
                                    <div class="text-xs text-gray-500 flex items-center mt-1 space-x-2">
                                        <span class="bg-gray-100 px-2 py-0.5 rounded" x-text="p.nik"></span>
                                        <template x-if="p.deleted_at">
                                            <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded font-bold text-[10px] uppercase">
                                                Non-Aktif / Meninggal
                                            </span>
                                        </template>
                                        <span x-text="p.alamat"></span>
                                    </div>
                                </button>
                            </template>
                            
                            <div x-show="results.length === 0 && hasSearched && !isLoading" class="px-4 py-3 text-center text-gray-500 text-sm">
                                Data tidak ditemukan
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="penduduk_id" id="penduduk_id" value="{{ $suratPengajuan->penduduk_id }}" required>
                    @error('penduduk_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    <!-- Selected Preview -->
                    <div x-show="selectedPenduduk" x-transition class="bg-green-50 border border-green-100 rounded-xl p-4 flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-green-700 text-lg"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900" x-text="selectedPenduduk?.nama"></h4>
                            <p class="text-sm text-green-800 font-mono mb-1" x-text="selectedPenduduk?.nik"></p>
                            <p class="text-xs text-gray-600 flex items-center">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                <span x-text="selectedPenduduk?.alamat || 'Alamat tidak lengkap'"></span>
                            </p>
                        </div>
                        <button type="button" x-on:click="resetSelection()" class="ml-auto text-gray-400 hover:text-red-500 transition-colors">
                            <i class="fas fa-times"></i> Ubah
                        </button>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            <!-- SECTION 2: DETAIL SURAT -->
            <div>
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-alt text-purple-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Detail Surat</h3>
                        <p class="text-sm text-gray-500">Edit isi surat</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Jenis Surat -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Jenis Surat <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_surat" x-model="jenisSurat" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors bg-white">
                            <option value="">-- Pilih Jenis Surat --</option>
                            @foreach($suratTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('jenis_surat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Tanggal Surat -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Tanggal Surat <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_surat" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" value="{{ $suratPengajuan->tanggal_surat->format('Y-m-d') }}" required>
                    </div>

                    <!-- Penandatangan -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Tanda Tangan Oleh <span class="text-red-500">*</span>
                        </label>
                        <select name="penandatangan" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors bg-white">
                            <option value="kepala_desa" {{ ($suratPengajuan->penandatangan ?? 'kepala_desa') == 'kepala_desa' ? 'selected' : '' }}>Kepala Desa</option>
                            <option value="sekretaris_desa" {{ ($suratPengajuan->penandatangan ?? 'kepala_desa') == 'sekretaris_desa' ? 'selected' : '' }}>Sekretaris Desa (a.n)</option>
                        </select>
                    </div>

                    <!-- Keperluan -->
                    <div class="col-span-1 md:col-span-2 space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Keperluan
                        </label>
                        <textarea name="keperluan" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" placeholder="Contoh: Persyaratan melamar pekerjaan">{{ $suratPengajuan->keperluan }}</textarea>
                    </div>

                     <!-- Keterangan Tambahan -->
                     <div class="col-span-1 md:col-span-2 space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Keterangan Lain (Opsional)
                        </label>
                        <textarea name="keterangan_tambahan" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">{{ $suratPengajuan->keterangan_tambahan }}</textarea>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: DATA KHUSUS (DYNAMIC) -->
            <div x-show="jenisSurat && hasDynamicFields()" x-transition>
                <hr class="border-gray-100 mb-8">
                
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-sliders-h text-orange-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Data Khusus</h3>
                        <p class="text-sm text-gray-500">Formulir tambahan sesuai jenis surat</p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200">
                    
                    <!-- Form SKU -->
                    <div x-show="jenisSurat === 'sku'" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Usaha</label>
                                <input type="text" name="data_tambahan[nama_usaha]" value="{{ $suratPengajuan->data_tambahan['nama_usaha'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Usaha</label>
                                <input type="text" name="data_tambahan[jenis_usaha]" value="{{ $suratPengajuan->data_tambahan['jenis_usaha'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Usaha</label>
                                <input type="text" name="data_tambahan[alamat_usaha]" value="{{ $suratPengajuan->data_tambahan['alamat_usaha'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Mulai Usaha (Tahun/Bulan)</label>
                                <input type="text" name="data_tambahan[mulai_usaha]" value="{{ $suratPengajuan->data_tambahan['mulai_usaha'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                    </div>

                    <!-- Form SKTM -->
                    <div x-show="['sktm_dewasa', 'sktm_anak'].includes(jenisSurat)" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Orang Tua/Wali</label>
                                <input type="text" name="data_tambahan[nama_wali]" value="{{ $suratPengajuan->data_tambahan['nama_wali'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Pekerjaan Wali</label>
                                <input type="text" name="data_tambahan[pekerjaan_wali]" value="{{ $suratPengajuan->data_tambahan['pekerjaan_wali'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Wali</label>
                                <input type="text" name="data_tambahan[alamat_wali]" value="{{ $suratPengajuan->data_tambahan['alamat_wali'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Penghasilan Rata-rata</label>
                                <input type="number" name="data_tambahan[penghasilan]" value="{{ $suratPengajuan->data_tambahan['penghasilan'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div x-show="jenisSurat === 'sktm_anak'">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Sekolah / Tujuan</label>
                                <input type="text" name="data_tambahan[sekolah]" value="{{ $suratPengajuan->data_tambahan['sekolah'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                    </div>

                     <!-- Form Domisili -->
                     <div x-show="['domisili', 'keterangan-domisili'].includes(jenisSurat)" class="space-y-4">
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Status Tempat Tinggal</label>
                                <select name="data_tambahan[status_tempat_tinggal]" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    @php $stt = $suratPengajuan->data_tambahan['status_tempat_tinggal'] ?? ''; @endphp
                                    <option value="Milik Sendiri" {{ $stt == 'Milik Sendiri' ? 'selected' : '' }}>Milik Sendiri</option>
                                    <option value="Sewa/Kontrak" {{ $stt == 'Sewa/Kontrak' ? 'selected' : '' }}>Sewa/Kontrak</option>
                                    <option value="Menumpang" {{ $stt == 'Menumpang' ? 'selected' : '' }}>Menumpang</option>
                                    <option value="Rumah Dinas" {{ $stt == 'Rumah Dinas' ? 'selected' : '' }}>Rumah Dinas</option>
                                </select>
                            </div>
                        </div>
                    </div>

                     <!-- Form Kematian -->
                     <div x-show="jenisSurat === 'kematian'" class="space-y-6">
                        <!-- Detail Kematian -->
                        <div>
                            <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">Detail Kematian</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Hari Meninggal</label>
                                    <select name="data_tambahan[kematian][hari]" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <option value="">-- Pilih Hari --</option>
                                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                            <option {{ ($suratPengajuan->data_tambahan['kematian']['hari'] ?? '') == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Meninggal</label>
                                    <input type="date" name="data_tambahan[kematian][tanggal]" value="{{ $suratPengajuan->data_tambahan['kematian']['tanggal'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jam Meninggal</label>
                                    <input type="time" name="data_tambahan[kematian][jam]" value="{{ $suratPengajuan->data_tambahan['kematian']['jam'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tempat Meninggal</label>
                                    <input type="text" name="data_tambahan[kematian][bertempat_di]" value="{{ $suratPengajuan->data_tambahan['kematian']['bertempat_di'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Penyebab Kematian</label>
                                    <input type="text" name="data_tambahan[alasan]" value="{{ $suratPengajuan->data_tambahan['alasan'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                        </div>

                        <!-- Detail Pemakaman -->
                        <div>
                            <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">Detail Pemakaman</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Dimakamkan Hari</label>
                                    <select name="data_tambahan[pemakaman][hari]" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <option value="">-- Pilih Hari --</option>
                                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                            <option {{ ($suratPengajuan->data_tambahan['pemakaman']['hari'] ?? '') == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pemakaman</label>
                                    <input type="date" name="data_tambahan[pemakaman][tanggal]" value="{{ $suratPengajuan->data_tambahan['pemakaman']['tanggal'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jam Pemakaman</label>
                                    <input type="time" name="data_tambahan[pemakaman][jam]" value="{{ $suratPengajuan->data_tambahan['pemakaman']['jam'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Lokasi Pemakaman</label>
                                    <input type="text" name="data_tambahan[pemakaman][lokasi]" value="{{ $suratPengajuan->data_tambahan['pemakaman']['lokasi'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                        </div>

                        <!-- Data Pelapor -->
                        <div>
                            <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">Data Pelapor</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Pelapor</label>
                                    <input type="text" name="data_tambahan[pelapor_nama]" value="{{ $suratPengajuan->data_tambahan['pelapor_nama'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Hubungan dengan Jenazah</label>
                                    <input type="text" name="data_tambahan[pelapor_hubungan]" value="{{ $suratPengajuan->data_tambahan['pelapor_hubungan'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Umur Pelapor</label>
                                    <input type="number" name="data_tambahan[pelapor_umur]" value="{{ $suratPengajuan->data_tambahan['pelapor_umur'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Pekerjaan Pelapor</label>
                                    <input type="text" name="data_tambahan[pelapor_pekerjaan]" value="{{ $suratPengajuan->data_tambahan['pelapor_pekerjaan'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Pelapor</label>
                                    <input type="text" name="data_tambahan[pelapor_alamat]" value="{{ $suratPengajuan->data_tambahan['pelapor_alamat'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Form Kelahiran -->
                    <div x-show="jenisSurat === 'kelahiran'" class="space-y-4">
                        <div class="bg-purple-50 text-purple-800 p-4 rounded-xl text-sm mb-4 flex items-center">
                            <i class="fas fa-baby mr-2"></i>
                            Silakan lengkapi data kelahiran bayi di bawah ini.
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Bayi</label>
                                <input type="text" name="data_tambahan[nama_bayi]" value="{{ $suratPengajuan->data_tambahan['nama_bayi'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Nama lengkap bayi">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Tempat Lahir</label>
                                <input type="text" name="data_tambahan[tempat_lahir]" value="{{ $suratPengajuan->data_tambahan['tempat_lahir'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Kota/Kabupaten">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Lahir</label>
                                <input type="date" name="data_tambahan[tanggal_lahir]" value="{{ $suratPengajuan->data_tambahan['tanggal_lahir'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kelamin</label>
                                <select name="data_tambahan[jenis_kelamin]" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    @php $jk = $suratPengajuan->data_tambahan['jenis_kelamin'] ?? ''; @endphp
                                    <option value="LAKI-LAKI" {{ $jk == 'LAKI-LAKI' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="PEREMPUAN" {{ $jk == 'PEREMPUAN' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Ayah</label>
                                <input type="text" name="data_tambahan[nama_ayah]" value="{{ $suratPengajuan->data_tambahan['nama_ayah'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Ibu</label>
                                <input type="text" name="data_tambahan[nama_ibu]" value="{{ $suratPengajuan->data_tambahan['nama_ibu'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Berat Badan (kg)</label>
                                <input type="text" name="data_tambahan[berat_badan]" value="{{ $suratPengajuan->data_tambahan['berat_badan'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Contoh: 3.2">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Panjang Badan (cm)</label>
                                <input type="text" name="data_tambahan[panjang_badan]" value="{{ $suratPengajuan->data_tambahan['panjang_badan'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Contoh: 50">
                            </div>
                        </div>
                    </div>

                    <!-- Form Pindah -->
                    <div x-show="jenisSurat === 'pindah'" class="space-y-4">
                        <div class="bg-yellow-50 text-yellow-800 p-4 rounded-xl text-sm mb-4 flex items-center">
                            <i class="fas fa-map-marked-alt mr-2"></i>
                            Silakan lengkapi alamat tujuan pindah di bawah ini.
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Tujuan</label>
                                <input type="text" name="data_tambahan[alamat_tujuan]" value="{{ $suratPengajuan->data_tambahan['alamat_tujuan'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Jalan / Kp / Dusun">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">RT/RW Tujuan</label>
                                <input type="text" name="data_tambahan[rt_rw_tujuan]" value="{{ $suratPengajuan->data_tambahan['rt_rw_tujuan'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="001/002">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Kelurahan/Desa Tujuan</label>
                                <input type="text" name="data_tambahan[kelurahan_tujuan]" value="{{ $suratPengajuan->data_tambahan['kelurahan_tujuan'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Kecamatan Tujuan</label>
                                <input type="text" name="data_tambahan[kecamatan_tujuan]" value="{{ $suratPengajuan->data_tambahan['kecamatan_tujuan'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Kabupaten/Kota Tujuan</label>
                                <input type="text" name="data_tambahan[kabupaten_tujuan]" value="{{ $suratPengajuan->data_tambahan['kabupaten_tujuan'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Provinsi Tujuan</label>
                                <input type="text" name="data_tambahan[provinsi_tujuan]" value="{{ $suratPengajuan->data_tambahan['provinsi_tujuan'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                    </div>

                    <!-- Form Pengantar / Lainnya -->
                    <div x-show="jenisSurat === 'pengantar'" class="space-y-4">
                         <div class="bg-green-50 text-green-800 p-4 rounded-xl text-sm flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Surat pengantar standar. Gunakan kolom Keperluan di atas untuk detail tambahan.
                        </div>
                    </div>

                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.surat-pengajuan.index') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

