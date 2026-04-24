@extends('layouts.app')

@section('title', 'Edit Surat')
@section('subtitle', 'Edit data surat yang telah dibuat')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .select2-container--default .select2-selection--single {
        @apply h-[42px] border-gray-300 rounded-lg;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        height: 42px;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px;
        padding-left: 12px;
        color: #111827;
    }
    .select2-dropdown {
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-xl border-0 p-6 sm:p-8 mb-8">
        <div class="flex flex-col lg:flex-row items-center justify-between">
            <div class="flex-1 text-center lg:text-left mb-6 lg:mb-0">
                <div class="flex items-center justify-center lg:justify-start mb-4">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mr-4">
                        <i class="fas fa-edit text-yellow-300 text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-2">
                            Edit Surat - {{ $surat->nomor_surat }}
                        </h1>
                        <p class="text-blue-100 text-sm sm:text-base">Ubah data surat dan penandatangan</p>
                    </div>
                </div>
            </div>
            <!-- Back Button -->
            <a href="{{ route('surat.history') }}" class="group flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Section -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-pen-to-square text-blue-500 mr-2"></i>
                        Form Edit Surat
                    </h2>
                </div>

                <div class="p-6 sm:p-8">
                    <form id="editSuratForm" action="{{ route('surat.update', $surat->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-6">

                            <!-- Jenis Surat (Readonly) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jenis Surat
                                </label>
                                <div class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 font-medium">
                                    {{ ucwords(str_replace('-', ' ', $surat->jenis_surat)) }}
                                </div>
                            </div>

                            <!-- Penduduk (Readonly) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Penduduk
                                </label>
                                <div class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
                                    {{ $surat->penduduk->nama }} ({{ $surat->penduduk->nik }})
                                </div>
                            </div>

                            <!-- Tanggal Surat -->
                            <div>
                                <label for="tanggal_surat" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Surat
                                </label>
                                <input type="date" name="tanggal_surat" id="tanggal_surat"
                                       value="{{ $surat->tanggal_surat->format('Y-m-d') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-- Penandatangan -->
                            <div>
                                <label for="penandatangan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanda Tangan Oleh
                                </label>
                                <select name="penandatangan" id="penandatangan"
                                        class="w-full px-3 py-2 border border-blue-300 bg-blue-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-blue-800 font-medium">
                                    <option value="kepala_desa" {{ ($surat->penandatangan ?? 'kepala_desa') == 'kepala_desa' ? 'selected' : '' }}>Kepala Desa</option>
                                    <option value="sekretaris_desa" {{ ($surat->penandatangan ?? 'kepala_desa') == 'sekretaris_desa' ? 'selected' : '' }}>Sekretaris Desa (a.n)</option>
                                </select>
                            </div>

                            <!-- Fields based on Surat Type -->
                            <!-- Keperluan -->
                            @if(in_array($surat->jenis_surat, ['pengantar', 'keterangan-domisili', 'sku', 'sktm_dewasa', 'sktm_anak', 'tidak-mampu-dewasa', 'tidak-mampu-anak']))
                            <div>
                                <label for="keperluan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Keperluan <span class="text-red-500">*</span>
                                </label>
                                <textarea name="keperluan" id="keperluan" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                          placeholder="Contoh: Mengurus KTP, Melamar Pekerjaan">{{ $surat->keperluan }}</textarea>
                            </div>
                            @endif

                            <!-- Tujuan (Only for some letters or generally useful) -->
                             @if(in_array($surat->jenis_surat, ['keterangan-domisili', 'pindah']))
                            <div>
                                <label for="tujuan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tujuan <span class="text-gray-500 text-xs">(Opsional)</span>
                                </label>
                                <input type="text" name="tujuan" id="tujuan" value="{{ $surat->tujuan }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            @endif


                            <!-- Keterangan Tambahan -->
                            <div class="md:col-span-2">
                                <label for="keterangan_tambahan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Keterangan Tambahan <span class="text-gray-500 text-xs">(Opsional)</span>
                                </label>
                                <textarea name="keterangan_tambahan" id="keterangan_tambahan" rows="2"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                          placeholder="Catatan tambahan untuk surat ini... (Akan muncul di bagian bawah surat jika ada)">{{ $surat->keterangan_tambahan }}</textarea>
                            </div>

                            <!-- Data Tambahan (Hidden JSON for simplicity in V1 of edit, or explicit fields if critical) -->
                            <!-- NOTE: For complex data like Kematian details, editing via JSON blob or simplified fields might be risky without full form recreation. -->
                            <!-- Leaving Data Tambahan as is for now, limiting edit to Header/Signer/Keperluan -->
                            <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                <p class="text-sm text-yellow-800">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Saat ini, detail spesifik surat (seperti data kematian/kelahiran) tidak dapat diedit di sini. 
                                    Jika ada kesalahan pada data tersebut, silakan buat surat baru. 
                                    Halaman ini khusus untuk mengubah <strong>Tanggal, Keperluan, dan Penandatangan</strong>.
                                </p>
                            </div>
                            <input type="hidden" name="data_tambahan" value="{{ json_encode($surat->data_tambahan) }}">

                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8">
                            <button type="submit" id="btn-submit"
                                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-lg border-0 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Informasi Surat
                </h3>
                <div class="space-y-4">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Dibuat Oleh</p>
                        <p class="text-gray-900 font-medium">{{ $surat->creator->name ?? 'System' }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tanggal Dibuat</p>
                        <p class="text-gray-900 font-medium">{{ $surat->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                         <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                           {{ ucfirst($surat->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('editSuratForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btnSubmit = document.getElementById('btn-submit');
        const originalText = btnSubmit.innerHTML;
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';

        fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = "{{ route('surat.history') }}";
                });
            } else {
                Swal.fire({
                    title: 'Gagal!',
                    text: data.message || 'Terjadi kesalahan saat menyimpan data.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan sistem.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalText;
        });
    });
</script>
@endpush
