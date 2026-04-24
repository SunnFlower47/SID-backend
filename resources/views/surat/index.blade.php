@extends('layouts.app')

@section('title', 'Pembuatan Surat')
@section('subtitle', 'Buat surat administrasi desa dengan mudah')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl border-0 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-file-alt text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Pembuatan Surat Administrasi Desa</h1>
                    <p class="text-green-100 mt-1">Pilih jenis surat dan isi data untuk membuat surat</p>
                </div>
            </div>
        </div>
        <!-- Action Buttons -->
        <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('surat.history') }}" class="group flex items-center px-4 py-2.5 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                    <i class="fas fa-history mr-2"></i>
                    History Surat
                </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Form Pembuatan Surat -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
                <div class="px-4 sm:px-6 py-4 sm:py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-edit text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Form Pembuatan Surat</h3>
                            <p class="text-sm text-gray-600">Pilih jenis surat dan lengkapi data yang diperlukan</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 sm:p-6 lg:p-8">

                    <form id="suratForm" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
                            <!-- Pilih Jenis Surat -->
                            <div class="md:col-span-2">
                                <label for="surat_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jenis Surat <span class="text-red-500">*</span>
                                </label>
                                <select name="surat_type" id="surat_type" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Jenis Surat --</option>
                                    @foreach($suratTypes as $surat)
                                        <option value="{{ $surat['id'] }}" data-color="{{ $surat['color'] }}">
                                            {{ $surat['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Pilih Penduduk -->
                            <div class="md:col-span-2" id="penduduk-field">
                                <label for="penduduk_search" class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Penduduk <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text"
                                           id="penduduk_search"
                                           name="penduduk_search"
                                           placeholder="Cari penduduk (nama atau NIK)..."
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           autocomplete="off">

                                    <!-- Loading indicator -->
                                    <div id="penduduk_search_loading" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500"></div>
                                    </div>

                                    <!-- Search results -->
                                    <div id="penduduk_search_results" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                                        <!-- Results will be populated here -->
                                    </div>
                                </div>

                                <!-- Hidden input untuk menyimpan ID penduduk yang dipilih -->
                                <input type="hidden" id="penduduk_id" name="penduduk_id" required>

                                <!-- Display selected penduduk -->
                                <div id="selected_penduduk_display" class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-medium text-blue-900" id="selected_penduduk_nama"></div>
                                            <div class="text-sm text-blue-700">
                                                <span id="selected_penduduk_nik"></span> |
                                                <span id="selected_penduduk_alamat"></span>
                                            </div>
                                        </div>
                                        <button type="button" id="clear_penduduk_selection" class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Info untuk Surat Kelahiran -->
                            <div id="kelahiran-info" class="md:col-span-2 hidden">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-info-circle text-blue-400"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-blue-800">Surat Keterangan Kelahiran</h3>
                                            <div class="mt-2 text-sm text-blue-700">
                                                <p>Surat ini untuk bayi yang baru lahir. Data bayi akan diisi di form di bawah ini.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Fields yang Dinamis -->
                            <div id="dynamicFields" class="md:col-span-2">
                                <!-- Fields akan dimuat berdasarkan jenis surat -->
                            </div>

                            <!-- Keperluan -->
                            <div>
                                <label for="keperluan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Keperluan
                                </label>
                                <input type="text" name="keperluan" id="keperluan"
                                       placeholder="Contoh: Keperluan administrasi, dll"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-- Tujuan -->
                            <div>
                                <label for="tujuan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tujuan
                                </label>
                                <input type="text" name="tujuan" id="tujuan"
                                       placeholder="Contoh: Bank, Kantor, dll"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-- Tanggal Surat -->
                            <div>
                                <label for="tanggal_surat" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Surat
                                </label>
                                <input type="date" name="tanggal_surat" id="tanggal_surat"
                                       value="{{ date('Y-m-d') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-- Penandatangan -->
                            <div>
                                <label for="penandatangan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanda Tangan Oleh
                                </label>
                                <select name="penandatangan" id="penandatangan"
                                        class="w-full px-3 py-2 border border-blue-300 bg-blue-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-blue-800 font-medium">
                                    <option value="kepala_desa">Kepala Desa</option>
                                    <option value="sekretaris_desa">Sekretaris Desa (a.n)</option>
                                </select>
                            </div>

                            <!-- Keterangan Tambahan -->
                            <div class="md:col-span-2">
                                <label for="keterangan_tambahan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Keterangan Tambahan
                                </label>
                                <textarea name="keterangan_tambahan" id="keterangan_tambahan" rows="3"
                                          placeholder="Keterangan tambahan jika diperlukan"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>
                        </div>

                                <!-- Preview Section -->
                                <div id="previewSection" class="mt-8 hidden">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-3">Preview Data Penduduk</h4>
                                        <div id="previewContent" class="text-sm text-gray-600">
                                            <!-- Preview content will be loaded here -->
                                        </div>
                                    </div>
                                </div>

                            <!-- Instruksi Print -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4 mt-8">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                                    <div>
                                        <h4 class="text-sm font-semibold text-blue-900 mb-2">Tips Print Bersih:</h4>
                                        <ul class="text-xs text-blue-800 space-y-1">
                                            <li>• <strong>Chrome/Edge:</strong> Uncheck "Headers and footers" di pengaturan print</li>
                                            <li>• <strong>Firefox:</strong> Uncheck "Print headers and footers" di More Settings</li>
                                            <li>• <strong>Safari:</strong> Uncheck "Print headers and footers" di Show Details</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons - Mobile Responsive -->
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3 mt-8 lg:mt-12">
                                <button type="button" onclick="previewSurat()"
                                        class="group flex flex-col items-center justify-center px-4 py-4 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-eye text-white text-sm"></i>
                                    </div>
                                    <div class="text-center">
                                        <p class="font-bold text-sm">Preview</p>
                                        <p class="text-yellow-200 text-xs">Lihat</p>
                                    </div>
                                </button>

                                <button type="button" onclick="printSurat()"
                                        class="group flex flex-col items-center justify-center px-4 py-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-print text-white text-sm"></i>
                                    </div>
                                    <div class="text-center">
                                        <p class="font-bold text-sm">Print</p>
                                        <p class="text-green-200 text-xs">Langsung</p>
                                    </div>
                                </button>

                                <button type="button" onclick="saveSurat()"
                                        class="group flex flex-col items-center justify-center px-4 py-4 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-save text-white text-sm"></i>
                                    </div>
                                    <div class="text-center">
                                        <p class="font-bold text-sm">Save</p>
                                        <p class="text-purple-200 text-xs">Surat</p>
                                    </div>
                                </button>

                                <button type="submit"
                                        class="group flex flex-col items-center justify-center px-4 py-4 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-download text-white text-sm"></i>
                                    </div>
                                    <div class="text-center">
                                        <p class="font-bold text-sm">Download</p>
                                        <p class="text-blue-200 text-xs">PDF</p>
                                    </div>
                                </button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

        <!-- Jenis Surat yang Tersedia -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
                <div class="px-4 sm:px-6 py-4 sm:py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-list text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Jenis Surat Tersedia</h3>
                            <p class="text-sm text-gray-600">Pilih jenis surat yang ingin dibuat</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 sm:p-6">

                    <div class="space-y-3">
                        @foreach($suratTypes as $surat)
                            <div class="p-4 border border-gray-200 rounded-xl hover:bg-gray-50 hover:shadow-md transition-all duration-200 cursor-pointer group"
                                 onclick="selectSuratType('{{ $surat['id'] }}')">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-{{ $surat['color'] }}-100 rounded-xl flex items-center justify-center group-hover:bg-{{ $surat['color'] }}-200 transition-colors">
                                            <i class="{{ $surat['icon'] }} text-{{ $surat['color'] }}-600 text-lg"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1">
                                                <h4 class="text-sm font-medium text-gray-900">{{ $surat['name'] }}</h4>
                                                <p class="text-xs text-gray-500">{{ $surat['description'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="mt-6 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-file-alt text-blue-600 text-lg sm:text-xl"></i>
                        </div>
                        <div class="ml-2 sm:ml-3">
                            <p class="text-xs sm:text-sm font-medium text-gray-500">Total Surat</p>
                            <p class="text-lg sm:text-xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-day text-green-600 text-lg sm:text-xl"></i>
                        </div>
                        <div class="ml-2 sm:ml-3">
                            <p class="text-xs sm:text-sm font-medium text-gray-500">Hari Ini</p>
                            <p class="text-lg sm:text-xl font-bold text-gray-900">{{ number_format($stats['hari_ini']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-week text-yellow-600 text-lg sm:text-xl"></i>
                        </div>
                        <div class="ml-2 sm:ml-3">
                            <p class="text-xs sm:text-sm font-medium text-gray-500">Minggu Ini</p>
                            <p class="text-lg sm:text-xl font-bold text-gray-900">{{ number_format($stats['minggu_ini']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar text-purple-600 text-lg sm:text-xl"></i>
                        </div>
                        <div class="ml-2 sm:ml-3">
                            <p class="text-xs sm:text-sm font-medium text-gray-500">Bulan Ini</p>
                            <p class="text-lg sm:text-xl font-bold text-gray-900">{{ number_format($stats['bulan_ini']) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // SweetAlert functions - Define first
        function showSuccess(message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: message,
                    confirmButtonColor: '#10b981',
                    confirmButtonText: 'OK'
                });
            } else {
                alert('Berhasil: ' + message);
            }
        }

        function showError(message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: message,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK'
                });
            } else {
                alert('Error: ' + message);
            }
        }

        function showWarning(message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: message,
                    confirmButtonColor: '#f59e0b',
                    confirmButtonText: 'OK'
                });
            } else {
                alert('Peringatan: ' + message);
            }
        }

        // Form fields configuration untuk setiap jenis surat
        const suratFields = {
            'keterangan-domisili': [],
            'pengantar': [],
            'pindah': [
                { name: 'alamat_tujuan', label: 'Alamat Tujuan', type: 'text', placeholder: 'Alamat tempat pindah' },
                { name: 'rt_rw_tujuan', label: 'RT/RW Tujuan', type: 'text', placeholder: 'RT/RW tempat pindah' },
                { name: 'kelurahan_tujuan', label: 'Kelurahan/Desa Tujuan', type: 'text', placeholder: 'Kelurahan/Desa tempat pindah' },
                { name: 'kecamatan_tujuan', label: 'Kecamatan Tujuan', type: 'text', placeholder: 'Kecamatan tempat pindah' },
                { name: 'kabupaten_tujuan', label: 'Kabupaten/Kota Tujuan', type: 'text', placeholder: 'Kabupaten/Kota tempat pindah' }
            ],
            'kematian': [
                { name: 'tanggal_meninggal', label: 'Tanggal Meninggal', type: 'date' },
                { name: 'penyebab_kematian', label: 'Penyebab Kematian', type: 'text', placeholder: 'Penyebab kematian' },
                { name: 'tempat_meninggal', label: 'Tempat Meninggal', type: 'text', placeholder: 'Rumah sakit, rumah, dll' }
            ],
            'kelahiran': [
                { name: 'nama_bayi', label: 'Nama Bayi', type: 'text', placeholder: 'Nama lengkap bayi' },
                { name: 'tempat_lahir', label: 'Tempat Lahir', type: 'text', placeholder: 'Tempat lahir bayi' },
                { name: 'tanggal_lahir', label: 'Tanggal Lahir', type: 'date' },
                { name: 'jenis_kelamin', label: 'Jenis Kelamin', type: 'select', options: [
                    { value: 'L', text: 'Laki-laki' },
                    { value: 'P', text: 'Perempuan' }
                ]},
                { name: 'nama_ayah', label: 'Nama Ayah', type: 'text', placeholder: 'Nama lengkap ayah' },
                { name: 'nama_ibu', label: 'Nama Ibu', type: 'text', placeholder: 'Nama lengkap ibu' },
                { name: 'berat_badan', label: 'Berat Badan (kg)', type: 'number', placeholder: 'Berat badan bayi' },
                { name: 'panjang_badan', label: 'Panjang Badan (cm)', type: 'number', placeholder: 'Panjang badan bayi' }
            ],
            'tidak-mampu-dewasa': [
                { name: 'pekerjaan', label: 'Pekerjaan', type: 'text', placeholder: 'Pekerjaan saat ini' },
                { name: 'penghasilan', label: 'Penghasilan per Bulan', type: 'text', placeholder: 'Jumlah penghasilan' },
                { name: 'jumlah_tanggungan', label: 'Jumlah Tanggungan', type: 'number', placeholder: 'Jumlah anggota keluarga yang ditanggung' },
                { name: 'alasan_tidak_mampu', label: 'Alasan Tidak Mampu', type: 'textarea', placeholder: 'Alasan mengapa tidak mampu' }
            ],
            'tidak-mampu-anak': [
                { name: 'pekerjaan', label: 'Pekerjaan', type: 'text', placeholder: 'Pekerjaan saat ini' },
                { name: 'penghasilan', label: 'Penghasilan per Bulan', type: 'text', placeholder: 'Jumlah penghasilan' },
                { name: 'jumlah_tanggungan', label: 'Jumlah Tanggungan', type: 'number', placeholder: 'Jumlah anggota keluarga yang ditanggung' },
                { name: 'alasan_tidak_mampu', label: 'Alasan Tidak Mampu', type: 'textarea', placeholder: 'Alasan mengapa tidak mampu' }
            ]
        };

        // Function untuk membuat form field
        function createFormField(field) {
            let html = `<div class="md:col-span-2">
                <label for="${field.name}" class="block text-sm font-medium text-gray-700 mb-2">
                    ${field.label} ${field.required ? '<span class="text-red-500">*</span>' : ''}
                </label>`;

            if (field.type === 'select') {
                html += `<select name="${field.name}" id="${field.name}" ${field.required ? 'required' : ''}
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih ${field.label} --</option>`;
                field.options.forEach(option => {
                    html += `<option value="${option.value}">${option.text}</option>`;
                });
                html += `</select>`;
            } else if (field.type === 'textarea') {
                html += `<textarea name="${field.name}" id="${field.name}" rows="3" ${field.required ? 'required' : ''}
                        placeholder="${field.placeholder || ''}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>`;
            } else {
                html += `<input type="${field.type}" name="${field.name}" id="${field.name}" ${field.required ? 'required' : ''}
                        placeholder="${field.placeholder || ''}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">`;
            }

            html += `</div>`;
            return html;
        }

        // Function untuk memuat form fields berdasarkan jenis surat
        function loadFormFields(suratType) {
            const dynamicFields = document.getElementById('dynamicFields');
            const fields = suratFields[suratType] || [];

            let html = '';
            fields.forEach(field => {
                html += createFormField(field);
            });

            dynamicFields.innerHTML = html;
        }

        // Event listener untuk perubahan jenis surat
        document.getElementById('surat_type').addEventListener('change', function() {
            const suratType = this.value;
            loadFormFields(suratType);

            // Handle surat kelahiran - sembunyikan field penduduk
            const pendudukField = document.getElementById('penduduk-field');
            const kelahiranInfo = document.getElementById('kelahiran-info');
            const pendudukIdInput = document.getElementById('penduduk_id');

            if (suratType === 'kelahiran') {
                pendudukField.classList.add('hidden');
                kelahiranInfo.classList.remove('hidden');
                pendudukIdInput.removeAttribute('required');
                pendudukIdInput.value = ''; // Clear selection
                clearPendudukSelection();
            } else {
                pendudukField.classList.remove('hidden');
                kelahiranInfo.classList.add('hidden');
                pendudukIdInput.setAttribute('required', 'required');
            }
        });

        // Penduduk search functionality
        let searchTimeout;
        document.getElementById('penduduk_search').addEventListener('input', function() {
            const query = this.value.trim();

            // Clear previous timeout
            clearTimeout(searchTimeout);

            if (query.length < 3) {
                hidePendudukSearchResults();
                return;
            }

            // Debounce search
            searchTimeout = setTimeout(() => {
                searchPenduduk(query);
            }, 300);
        });

        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            const searchContainer = document.getElementById('penduduk_search_results');
            const searchInput = document.getElementById('penduduk_search');

            if (!searchContainer.contains(e.target) && !searchInput.contains(e.target)) {
                hidePendudukSearchResults();
            }
        });

        // Clear penduduk selection
        document.getElementById('clear_penduduk_selection').addEventListener('click', function() {
            clearPendudukSelection();
        });

        function searchPenduduk(query) {
            const loading = document.getElementById('penduduk_search_loading');
            const results = document.getElementById('penduduk_search_results');

            loading.classList.remove('hidden');
            results.classList.add('hidden');

            fetch(`{{ route('mutasi.search-penduduk') }}?q=${encodeURIComponent(query)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin',
                cache: 'no-cache'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                loading.classList.add('hidden');
                displayPendudukSearchResults(data);
            })
            .catch(error => {
                console.error('Search error:', error);
                loading.classList.add('hidden');
                results.innerHTML = '<div class="p-3 text-red-600">Error saat mencari penduduk: ' + error.message + '</div>';
                results.classList.remove('hidden');
            });
        }

        function displayPendudukSearchResults(results) {
            const resultsContainer = document.getElementById('penduduk_search_results');
            resultsContainer.innerHTML = '';

            if (results.length > 0) {
                results.forEach(penduduk => {
                    const option = document.createElement('div');
                    option.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100';
                    option.innerHTML = `
                        <div class="font-medium text-gray-900">${penduduk.nama}</div>
                        <div class="text-sm text-gray-600">
                            <div class="flex items-center space-x-4">
                                <span><i class="fas fa-id-card text-blue-500 mr-1"></i>NIK: ${penduduk.nik}</span>
                                <span><i class="fas fa-users text-green-500 mr-1"></i>KK: ${penduduk.nkk}</span>
                            </div>
                            <div class="mt-1">
                                <span><i class="fas fa-map-marker-alt text-red-500 mr-1"></i>${penduduk.alamat || 'Alamat tidak tersedia'}</span>
                            </div>
                        </div>
                    `;
                    option.addEventListener('click', () => selectPenduduk(penduduk));
                    resultsContainer.appendChild(option);
                });
                resultsContainer.classList.remove('hidden');
            } else {
                resultsContainer.innerHTML = '<div class="p-3 text-gray-500 text-center">Tidak ada penduduk ditemukan</div>';
                resultsContainer.classList.remove('hidden');
            }
        }

        function selectPenduduk(penduduk) {
            // Set hidden input value
            document.getElementById('penduduk_id').value = penduduk.id;

            // Display selected penduduk
            document.getElementById('selected_penduduk_nama').textContent = penduduk.nama;
            document.getElementById('selected_penduduk_nik').textContent = `NIK: ${penduduk.nik}`;
            document.getElementById('selected_penduduk_alamat').textContent = penduduk.alamat || 'Alamat tidak tersedia';

            // Show selected display and hide search
            document.getElementById('selected_penduduk_display').classList.remove('hidden');
            document.getElementById('penduduk_search').value = '';
            hidePendudukSearchResults();
        }

        function clearPendudukSelection() {
            document.getElementById('penduduk_id').value = '';
            document.getElementById('selected_penduduk_display').classList.add('hidden');
            document.getElementById('penduduk_search').value = '';
            hidePendudukSearchResults();
        }

        function hidePendudukSearchResults() {
            const results = document.getElementById('penduduk_search_results');
            if (results) {
                results.classList.add('hidden');
            }
        }

        // Form submission
        document.getElementById('suratForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const suratType = document.getElementById('surat_type').value;
            const pendudukId = document.getElementById('penduduk_id').value;

            // Validasi khusus untuk surat kelahiran
            if (suratType === 'kelahiran') {
                if (!suratType) {
                    showWarning('Pilih jenis surat terlebih dahulu!');
                    return;
                }
            } else {
                if (!suratType || !pendudukId) {
                    showWarning('Pilih jenis surat dan penduduk terlebih dahulu!');
                    return;
                }
            }

            // Set form action and submit
            this.action = `{{ url('surat') }}/${suratType}/generate`;
            this.submit();
        });

        // Select surat type from sidebar
        function selectSuratType(type) {
            document.getElementById('surat_type').value = type;
            document.getElementById('surat_type').dispatchEvent(new Event('change'));
        }

        // Preview surat
        function previewSurat() {
            const suratType = document.getElementById('surat_type').value;
            const pendudukId = document.getElementById('penduduk_id').value;

            // Validasi khusus untuk surat kelahiran
            if (suratType === 'kelahiran') {
                if (!suratType) {
                    showWarning('Pilih jenis surat terlebih dahulu!');
                    return;
                }
            } else {
                if (!suratType || !pendudukId) {
                    showWarning('Pilih jenis surat dan penduduk terlebih dahulu!');
                    return;
                }
            }

            // Get form data
            const formData = new FormData(document.getElementById('suratForm'));
            const params = new URLSearchParams(formData);

            // Open preview in new window
            window.open(`{{ url('surat') }}/${suratType}/preview?${params.toString()}`, '_blank');
        }

        // Print surat
        function printSurat() {
            const suratType = document.getElementById('surat_type').value;
            const pendudukId = document.getElementById('penduduk_id').value;

            // Validasi khusus untuk surat kelahiran
            if (suratType === 'kelahiran') {
                if (!suratType) {
                    showWarning('Pilih jenis surat terlebih dahulu!');
                    return;
                }
            } else {
                if (!suratType || !pendudukId) {
                    showWarning('Pilih jenis surat dan penduduk terlebih dahulu!');
                    return;
                }
            }

            // Get form data
            const formData = new FormData(document.getElementById('suratForm'));
            const params = new URLSearchParams(formData);

            // Open print window and trigger print
            const printUrl = `{{ url('surat') }}/${suratType}/preview?${params.toString()}`;
            const printWindow = window.open(printUrl, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');

            if (printWindow) {
                printWindow.onload = function() {
                    printWindow.print();
                };
            }
        }

        // Save surat to history
        function saveSurat() {
            const suratType = document.getElementById('surat_type').value;
            const pendudukId = document.getElementById('penduduk_id').value;

            if (!suratType || !pendudukId) {
                showWarning('Pilih jenis surat dan penduduk terlebih dahulu!');
                return;
            }

            // Show loading
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Sedang menyimpan surat ke histori',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }

            // Get form data
            const formData = new FormData(document.getElementById('suratForm'));

            // Collect dynamic form data
            const dynamicFields = suratFields[suratType] || [];
            const dataTambahan = {};

            dynamicFields.forEach(field => {
                const element = document.getElementById(field.name);
                if (element && element.value) {
                    dataTambahan[field.name] = element.value;
                }
            });

            // Add data_tambahan to form data
            formData.append('data_tambahan', JSON.stringify(dataTambahan));

            // Get CSRF token safely
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const token = csrfToken ? csrfToken.getAttribute('content') : '';

            // Submit form to store route to save history
            fetch(`{{ url('surat') }}/${suratType}/store`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': token
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (typeof Swal !== 'undefined') {
                    Swal.close();
                }
                if (data.success) {
                    showSuccess('Surat berhasil disimpan ke histori!');
                    // Update statistics after successful save
                    updateStatistics();
                } else {
                    showError('Terjadi kesalahan saat menyimpan surat!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.close();
                }
                showError('Terjadi kesalahan saat menyimpan surat: ' + error.message);
            });
        }

        // Update statistics function
        function updateStatistics() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token not found');
                return;
            }

            fetch('{{ url("surat/statistics") }}', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Update the statistics cards safely
                const totalCard = document.querySelector('.grid .bg-white:nth-child(1) .text-2xl');
                const todayCard = document.querySelector('.grid .bg-white:nth-child(2) .text-2xl');
                const weekCard = document.querySelector('.grid .bg-white:nth-child(3) .text-2xl');
                const monthCard = document.querySelector('.grid .bg-white:nth-child(4) .text-2xl');

                if (totalCard) totalCard.textContent = data.total || 0;
                if (todayCard) todayCard.textContent = data.hari_ini || 0;
                if (weekCard) weekCard.textContent = data.minggu_ini || 0;
                if (monthCard) monthCard.textContent = data.bulan_ini || 0;
            })
            .catch(error => {
                console.error('Error updating statistics:', error);
            });
        }

        // Preview penduduk data
        document.getElementById('penduduk_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const previewSection = document.getElementById('previewSection');
            const previewContent = document.getElementById('previewContent');

            if (this.value) {
                const nama = selectedOption.getAttribute('data-nama');
                const nik = selectedOption.getAttribute('data-nik');
                const alamat = selectedOption.getAttribute('data-alamat');

                previewContent.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p><strong>Nama:</strong> ${nama}</p>
                            <p><strong>NIK:</strong> ${nik}</p>
                        </div>
                        <div>
                            <p><strong>Alamat:</strong> ${alamat || 'Tidak tersedia'}</p>
                        </div>
                    </div>
                `;

                previewSection.classList.remove('hidden');
            } else {
                previewSection.classList.add('hidden');
            }
        });
    </script>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endpush
@endsection


