@extends('layouts.app')

@section('title', 'Buat Pengajuan Surat')
@section('subtitle', 'Buat pengajuan surat baru untuk warga')

@section('content')
<div class="space-y-6" x-data="suratPengajuanForm()">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-file-signature text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Buat Surat Baru</h1>
                    <p class="text-green-100 mt-1">Isi formulir di bawah untuk membuat surat administrasi desa</p>
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
        <form action="{{ route('admin.surat-pengajuan.store') }}" method="POST" class="p-6 sm:p-8 space-y-8">
            @csrf

            <!-- SECTION 1: DATA PEMOHON -->
            <div>
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Data Pemohon</h3>
                        <p class="text-sm text-gray-500">Cari data penduduk yang akan mengajukan surat</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 relative">
                    <!-- Search Input -->
                    <div class="space-y-2 relative" x-on:click.away="showResults = false">
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
                             class="absolute z-50 w-full bg-white mt-1 border border-gray-200 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                            
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
                        <input type="hidden" name="penduduk_id" id="penduduk_id" required>
                        @error('penduduk_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

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
                            <i class="fas fa-times"></i>
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
                        <p class="text-sm text-gray-500">Pilih jenis dan isi kelengkapan surat</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Jenis Surat -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Jenis Surat <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_surat" x-model="jenisSurat" @change="updateFormJson()" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors bg-white">
                            <option value="">-- Pilih Jenis Surat --</option>
                            @foreach($suratTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->nama }}</option>
                            @endforeach
                        </select>
                        @error('jenis_surat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Tanggal Surat -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Tanggal Surat <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_surat" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <!-- Penandatangan -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Tanda Tangan Oleh <span class="text-red-500">*</span>
                        </label>
                        <select name="penandatangan" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors bg-white">
                            <option value="kepala_desa">Kepala Desa</option>
                            <option value="sekretaris_desa">Sekretaris Desa (a.n)</option>
                        </select>
                    </div>

                    <!-- Keperluan -->
                    <div class="col-span-1 md:col-span-2 space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Keperluan
                        </label>
                        <textarea name="keperluan" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" placeholder="Contoh: Persyaratan melamar pekerjaan, Pendaftaran Sekolah, dll..."></textarea>
                    </div>

                     <!-- Keterangan Tambahan -->
                     <div class="col-span-1 md:col-span-2 space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Keterangan Lain (Opsional)
                        </label>
                        <textarea name="keterangan_tambahan" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" placeholder="Tambahkan catatan khusus jika ada..."></textarea>
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
                    <!-- Dynamic Form Rendering -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <template x-for="(field, index) in formJson" :key="index">
                            <div :class="field.type === 'textarea' ? 'md:col-span-2' : ''" class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">
                                    <span x-text="field.label"></span>
                                    <template x-if="field.required">
                                        <span class="text-red-500">*</span>
                                    </template>
                                </label>

                                <!-- Text/Number/Date Input -->
                                <template x-if="['text', 'number', 'date', 'email'].includes(field.type)">
                                    <input :type="field.type"
                                           :name="'data_tambahan[' + field.name + ']'"
                                           :required="field.required"
                                           :placeholder="field.placeholder || ''"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors bg-white">
                                </template>

                                <!-- Textarea -->
                                <template x-if="field.type === 'textarea'">
                                    <textarea :name="'data_tambahan[' + field.name + ']'"
                                              :required="field.required"
                                              :placeholder="field.placeholder || ''"
                                              rows="3"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors bg-white"></textarea>
                                </template>

                                <!-- Select -->
                                <template x-if="field.type === 'select'">
                                    <select :name="'data_tambahan[' + field.name + ']'"
                                            :required="field.required"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors bg-white">
                                        <option value="">-- Pilih --</option>
                                        <template x-for="option in field.options" :key="option">
                                            <option :value="option" x-text="option"></option>
                                        </template>
                                    </select>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- Empty State for Dynamic Fields -->
                    <template x-if="formJson.length === 0">
                        <div class="text-center py-4 text-gray-500 italic">
                            Tidak ada formulir tambahan untuk jenis surat ini.
                        </div>
                    </template>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.surat-pengajuan.index') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-save mr-2"></i>
                    Buat Surat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script nonce="{{ $csp_nonce }}">
    document.addEventListener('alpine:init', () => {
        Alpine.data('suratPengajuanForm', () => ({
            searchQuery: '',
            isLoading: false,
            results: [],
            selectedPenduduk: null,
            showResults: false,
            hasSearched: false,
            jenisSurat: '{{ old('jenis_surat', '') }}',
            suratTypes: @json($suratTypes),
            dynamicTypes: @json($dynamicTypes),
            formJson: [],

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

            init() {
                this.updateFormJson();
            },

            resetSelection() {
                this.selectedPenduduk = null;
                document.getElementById('penduduk_id').value = '';
                this.searchQuery = '';
            },

            updateFormJson() {
                if (!this.jenisSurat) {
                    this.formJson = [];
                    return;
                }
                const selected = this.suratTypes.find(t => t.id.toString() === this.jenisSurat.toString());
                if (selected && selected.form_json) {
                    this.formJson = typeof selected.form_json === 'string' ? JSON.parse(selected.form_json) : selected.form_json;
                } else {
                    this.formJson = [];
                }
            },
            
            hasDynamicFields() {
                return this.formJson && this.formJson.length > 0;
            }
        }));
    });
</script>
@endpush

