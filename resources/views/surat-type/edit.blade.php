@extends('layouts.app')

@section('title', 'Edit Jenis Surat')
@section('subtitle', 'Ubah data master surat')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-2xl shadow-xl border-0 p-6 sm:p-8">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                <i class="fas fa-edit text-yellow-300 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Edit Jenis Surat</h1>
                <p class="text-purple-100 mt-1">Ubah nama, persyaratan, atau status surat: {{ $suratType->nama }}</p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
        <form action="{{ route('admin.surat-type.update', $suratType->id) }}" method="POST" class="p-6 sm:p-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Kode Surat -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ID / Kode Surat</label>
                    <input type="text" value="{{ $suratType->id }}" disabled
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl bg-gray-100 text-gray-500 cursor-not-allowed">
                    <p class="mt-1 text-xs text-gray-500">ID surat tidak dapat diubah setelah dibuat.</p>
                </div>

                <!-- Nama Surat -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Surat <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $suratType->nama) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('nama') border-red-500 @enderror">
                    @error('nama')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kode Inisial Surat -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Inisial Surat <span class="text-red-500">*</span></label>
                    <input type="text" name="kode" value="{{ old('kode', $suratType->kode) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('kode') border-red-500 @enderror"
                           placeholder="Contoh: SKU, SKD, SKM">
                    <p class="mt-1 text-xs text-gray-500">Kode ini akan muncul di nomor surat (misal: SKU/001/...).</p>
                    @error('kode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Singkat</label>
                <input type="text" name="deskripsi" value="{{ old('deskripsi', $suratType->deskripsi) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
            </div>

            <!-- Persyaratan -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Persyaratan Dokumen (Untuk Warga)</label>
                <textarea name="persyaratan" rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">{{ old('persyaratan', $suratType->persyaratan) }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Keterangan ini akan muncul di Web Desa saat warga memilih jenis surat ini.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Template Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Template Code (Nama File Blade)</label>
                    <input type="text" name="template_code" value="{{ old('template_code', $suratType->template_code) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                           placeholder="Contoh: tidak-mampu-anak">
                    <p class="mt-1 text-xs text-gray-500">Isi sesuai nama file di folder <code>surat/templates/</code> (tanpa .blade.php). Jika kosong, akan menggunakan ID.</p>
                </div>

                <!-- Icon & Color -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Icon (Visual)</label>
                        <div class="flex space-x-2">
                            <div id="iconPreview" class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center border border-purple-200">
                                <i class="{{ $suratType->icon }}"></i>
                            </div>
                            <select name="icon" id="iconSelect" class="flex-1 px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                                <option value="fas fa-file-alt" {{ old('icon', $suratType->icon) == 'fas fa-file-alt' ? 'selected' : '' }}>Standar</option>
                                <option value="fas fa-building" {{ old('icon', $suratType->icon) == 'fas fa-building' ? 'selected' : '' }}>Usaha (SKU)</option>
                                <option value="fas fa-home" {{ old('icon', $suratType->icon) == 'fas fa-home' ? 'selected' : '' }}>Domisili</option>
                                <option value="fas fa-walking" {{ old('icon', $suratType->icon) == 'fas fa-walking' ? 'selected' : '' }}>Pindah</option>
                                <option value="fas fa-skull" {{ old('icon', $suratType->icon) == 'fas fa-skull' ? 'selected' : '' }}>Kematian</option>
                                <option value="fas fa-baby" {{ old('icon', $suratType->icon) == 'fas fa-baby' ? 'selected' : '' }}>Kelahiran</option>
                                <option value="fas fa-hand-holding-heart" {{ old('icon', $suratType->icon) == 'fas fa-hand-holding-heart' ? 'selected' : '' }}>SKTM</option>
                                <option value="fas fa-briefcase" {{ old('icon', $suratType->icon) == 'fas fa-briefcase' ? 'selected' : '' }}>Pekerjaan</option>
                                <option value="fas fa-heart" {{ old('icon', $suratType->icon) == 'fas fa-heart' ? 'selected' : '' }}>Nikah/Sosial</option>
                                <option value="fas fa-users" {{ old('icon', $suratType->icon) == 'fas fa-users' ? 'selected' : '' }}>Keluarga</option>
                                <option value="fas fa-id-card" {{ old('icon', $suratType->icon) == 'fas fa-id-card' ? 'selected' : '' }}>Identitas</option>
                                <option value="fas fa-graduation-cap" {{ old('icon', $suratType->icon) == 'fas fa-graduation-cap' ? 'selected' : '' }}>Pendidikan</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna (Tailwind)</label>
                        <select name="color" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                            <option value="blue" {{ old('color', $suratType->color) == 'blue' ? 'selected' : '' }}>Biru</option>
                            <option value="green" {{ old('color', $suratType->color) == 'green' ? 'selected' : '' }}>Hijau</option>
                            <option value="red" {{ old('color', $suratType->color) == 'red' ? 'selected' : '' }}>Merah</option>
                            <option value="yellow" {{ old('color', $suratType->color) == 'yellow' ? 'selected' : '' }}>Kuning</option>
                            <option value="purple" {{ old('color', $suratType->color) == 'purple' ? 'selected' : '' }}>Ungu</option>
                            <option value="indigo" {{ old('color', $suratType->color) == 'indigo' ? 'selected' : '' }}>Indigo</option>
                            <option value="gray" {{ old('color', $suratType->color) == 'gray' ? 'selected' : '' }}>Abu-abu</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Form Builder (JSON) -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-3">
                    <label class="block text-sm font-bold text-gray-900">Konfigurasi Form (Resep Pertanyaan Warga)</label>
                    <div class="flex space-x-2">
                        <button type="button" data-example="sku" class="btn-example text-xs bg-green-100 text-green-700 px-3 py-1 rounded-lg hover:bg-green-200 transition-colors border border-green-200 font-bold">
                            <i class="fas fa-building mr-1"></i> Contoh SKU
                        </button>
                        <button type="button" data-example="sktm" class="btn-example text-xs bg-indigo-100 text-indigo-700 px-3 py-1 rounded-lg hover:bg-indigo-200 transition-colors border border-indigo-200 font-bold">
                            <i class="fas fa-hand-holding-heart mr-1"></i> Contoh SKTM
                        </button>
                        <button type="button" data-example="kematian" class="btn-example text-xs bg-gray-100 text-gray-700 px-3 py-1 rounded-lg hover:bg-gray-200 transition-colors border border-gray-200 font-bold">
                            <i class="fas fa-skull mr-1"></i> Contoh Kematian
                        </button>
                    </div>
                </div>
                <div class="relative">
                    <textarea name="form_json" id="formJsonArea" rows="8"
                              class="w-full px-4 py-3 border border-gray-300 rounded-2xl font-mono text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all bg-gray-900 text-green-400"
                              placeholder='[{"name": "nama_usaha", "label": "Nama Usaha", "type": "text"}]'>{{ old('form_json', $suratType->form_json_raw) }}</textarea>
                </div>
                <p class="mt-2 text-xs text-gray-500 flex items-center italic">
                    <i class="fas fa-info-circle mr-1 text-purple-600"></i>
                    Gunakan format JSON untuk menentukan pertanyaan apa saja yang harus dijawab warga.
                </p>
            </div>

@push('scripts')
<script nonce="{{ $csp_nonce }}">
    document.addEventListener('DOMContentLoaded', function() {
        const examples = {
            'sku': [
                {"name": "nama_usaha", "label": "Nama Usaha", "type": "text"},
                {"name": "alamat_usaha", "label": "Alamat Usaha", "type": "textarea"},
                {"name": "jenis_usaha", "label": "Jenis Usaha", "type": "text"}
            ],
            'sktm': [
                {"name": "pekerjaan", "label": "Pekerjaan", "type": "text"},
                {"name": "penghasilan", "label": "Penghasilan Per Bulan (Rp)", "type": "number"},
                {"name": "alasan_tidak_mampu", "label": "Alasan Pengajuan", "type": "textarea"}
            ],
            'kematian': [
                {"name": "tanggal_meninggal", "label": "Tanggal Meninggal", "type": "date"},
                {"name": "tempat_meninggal", "label": "Tempat Meninggal", "type": "text"},
                {"name": "penyebab_kematian", "label": "Penyebab Kematian", "type": "text"}
            ]
        };

        document.querySelectorAll('.btn-example').forEach(button => {
            button.addEventListener('click', function() {
                const type = this.getAttribute('data-example');
                
                Swal.fire({
                    title: 'Muat Contoh?',
                    text: 'Konten di kotak JSON akan tertimpa dengan format contoh baru.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#7c3aed',
                    cancelButtonColor: '#9ca3af',
                    confirmButtonText: 'Ya, Muat Contoh',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('formJsonArea').value = JSON.stringify(examples[type], null, 4);
                        
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Format contoh berhasil dimuat.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            });
        });
        // Handle Icon Preview
        const iconSelect = document.getElementById('iconSelect');
        const iconPreview = document.getElementById('iconPreview');
        if (iconSelect && iconPreview) {
            iconSelect.addEventListener('change', function() {
                iconPreview.innerHTML = `<i class="${this.value}"></i>`;
            });
            // Initial preview
            iconPreview.innerHTML = `<i class="${iconSelect.value}"></i>`;
        }
    });
</script>
@endpush

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 bg-gray-50 p-5 rounded-xl border border-gray-200">
                <!-- Metode Pemrosesan -->
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-3">Metode Pemrosesan</label>
                    <label class="flex items-center cursor-pointer group">
                        <div class="relative">
                            <input type="hidden" name="has_template" value="0">
                            <input type="checkbox" name="has_template" value="1" class="sr-only peer" id="hasTemplate" {{ old('has_template', $suratType->has_template) ? 'checked' : '' }}>
                            <div class="block bg-gray-300 w-14 h-8 rounded-full transition-colors group-hover:bg-gray-400 peer-checked:bg-purple-600"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform peer-checked:translate-x-6"></div>
                        </div>
                        <div class="ml-3">
                            <span class="block text-sm font-medium text-gray-700">Gunakan Template Otomatis</span>
                            <span class="block text-xs text-gray-500">Jika mati, admin proses manual via Microsoft Word</span>
                        </div>
                    </label>
                </div>

                <!-- Status Aktif -->
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-3">Status Publikasi</label>
                    <label class="flex items-center cursor-pointer group">
                        <div class="relative">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" id="isActive" {{ old('is_active', $suratType->is_active) ? 'checked' : '' }}>
                            <div class="block bg-gray-300 w-14 h-8 rounded-full transition-colors group-hover:bg-gray-400 peer-checked:bg-green-500"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform peer-checked:translate-x-6"></div>
                        </div>
                        <div class="ml-3">
                            <span class="block text-sm font-medium text-gray-700">Tampilkan di Web Desa</span>
                            <span class="block text-xs text-gray-500">Warga bisa mengajukan surat ini</span>
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Tambahan CSS untuk Switch -->
            <style>
                input:checked ~ .dot {
                    transform: translateX(100%);
                }
            </style>

            <!-- Actions -->
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.surat-type.index') }}" 
                   class="w-full sm:w-auto px-6 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 font-medium text-center transition-all duration-300">
                    Batal
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-medium rounded-xl hover:from-purple-700 hover:to-purple-800 shadow-md hover:shadow-lg transition-all duration-300">
                    <i class="fas fa-save mr-2"></i>Update Jenis Surat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
