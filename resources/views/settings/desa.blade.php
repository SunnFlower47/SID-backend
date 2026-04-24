@extends('layouts.app')

@section('title', 'Pengaturan Desa')
@section('subtitle', 'Kelola pengaturan desa dan template surat')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Card -->
            <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8 text-white mb-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                    <div class="flex items-center mb-4 sm:mb-0">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-cog text-2xl text-yellow-300"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold">Pengaturan Desa</h1>
                            <p class="text-green-100 text-sm sm:text-base mt-1">Kelola informasi desa, logo, dan template surat</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <button onclick="exportSettings()"
                                class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white font-medium rounded-xl transition-all duration-300 backdrop-blur-sm">
                            <i class="fas fa-download mr-2"></i>
                            Export
                        </button>
                        <button onclick="importSettings()"
                                class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white font-medium rounded-xl transition-all duration-300 backdrop-blur-sm">
                            <i class="fas fa-upload mr-2"></i>
                            Import
                        </button>
                        <button onclick="resetSettings()"
                                class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white font-medium rounded-xl transition-all duration-300 backdrop-blur-sm">
                            <i class="fas fa-undo mr-2"></i>
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <!-- Settings Form -->
                    <form action="{{ route('settings.desa.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @foreach($groups as $groupKey => $groupName)
                            <div class="mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    @if($groupKey === 'general')
                                        <i class="fas fa-building text-blue-600 mr-2"></i>
                                    @elseif($groupKey === 'kepala_desa')
                                        <i class="fas fa-user-tie text-green-600 mr-2"></i>
                                    @elseif($groupKey === 'sekretaris')
                                        <i class="fas fa-user-cog text-purple-600 mr-2"></i>
                                    @elseif($groupKey === 'logo')
                                        <i class="fas fa-image text-yellow-600 mr-2"></i>
                                    @elseif($groupKey === 'surat')
                                        <i class="fas fa-file-alt text-red-600 mr-2"></i>
                                    @elseif($groupKey === 'template')
                                        <i class="fas fa-edit text-indigo-600 mr-2"></i>
                                    @endif
                                    {{ $groupName }}
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach($settings[$groupKey] as $setting)
                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-700">
                                                {{ $setting->description ?: ucfirst(str_replace('_', ' ', $setting->key)) }}
                                            </label>

                                            @if($setting->type === 'image')
                                                <div class="space-y-3">
                                                    @if($setting->value)
                                                        <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                                                            <img src="{{ $setting->value }}" alt="{{ $setting->key }}"
                                                                 class="w-16 h-16 object-cover rounded-xl border-2 border-gray-300">
                                                            <div>
                                                                <p class="text-sm font-medium text-gray-700">Logo saat ini</p>
                                                                <button type="button" onclick="removeImage('{{ $setting->key }}')"
                                                                        class="text-red-600 text-sm hover:text-red-800 flex items-center mt-1">
                                                                    <i class="fas fa-trash mr-1"></i> Hapus
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <input type="file"
                                                           name="files[{{ $setting->key }}]"
                                                           accept="image/*"
                                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition-all duration-200">
                                                </div>
                                            @elseif($setting->type === 'json')
                                                <textarea name="settings[{{ $setting->key }}][value]"
                                                          rows="4"
                                                          class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 resize-none">{{ $setting->value }}</textarea>
                                            @else
                                                <input type="text"
                                                       name="settings[{{ $setting->key }}][value]"
                                                       value="{{ $setting->value }}"
                                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                                            @endif

                                            <input type="hidden" name="settings[{{ $setting->key }}][key]" value="{{ $setting->key }}">
                                            <input type="hidden" name="settings[{{ $setting->key }}][type]" value="{{ $setting->type }}">
                                            <input type="hidden" name="settings[{{ $setting->key }}][group]" value="{{ $setting->group }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('settings.index') }}"
                               class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-6 py-3 rounded-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Kembali
                            </a>
                            <button type="submit"
                                    class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-3 rounded-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-2xl border-0 overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 text-white">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i class="fas fa-upload mr-2"></i>
                            Import Pengaturan
                        </h3>
                        <button onclick="closeImportModal()" class="text-white/80 hover:text-white transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <form id="importForm" action="{{ route('settings.desa.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih File JSON</label>
                            <input type="file" name="file" accept=".json" required
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition-all duration-200">
                            <p class="mt-2 text-xs text-gray-500">File harus berformat JSON dan berisi data pengaturan desa</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="button" onclick="closeImportModal()"
                                    class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-6 py-3 rounded-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl">
                                <i class="fas fa-times mr-2"></i>
                                Batal
                            </button>
                            <button type="submit"
                                    class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-3 rounded-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl">
                                <i class="fas fa-upload mr-2"></i>
                                Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function exportSettings() {
            Swal.fire({
                title: 'Export Pengaturan',
                text: 'Mengunduh file pengaturan desa...',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            window.location.href = '{{ route("settings.desa.export") }}';
        }

        function importSettings() {
            document.getElementById('importModal').classList.remove('hidden');
        }

        function closeImportModal() {
            document.getElementById('importModal').classList.add('hidden');
        }

        function resetSettings() {
            Swal.fire({
                title: 'Reset Pengaturan',
                html: `
                    <div class="text-left">
                        <p class="mb-3">Apakah Anda yakin ingin mereset semua pengaturan ke default?</p>
                        <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                            <p class="text-sm text-red-800 font-medium mb-2">⚠️ Peringatan:</p>
                            <ul class="text-xs text-red-700 space-y-1">
                                <li>• Tindakan ini tidak dapat dibatalkan</li>
                                <li>• Semua pengaturan akan dikembalikan ke nilai default</li>
                                <li>• Logo dan file yang diupload akan dihapus</li>
                            </ul>
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Reset',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'swal-wide'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Mereset Pengaturan...',
                        text: 'Mohon tunggu sebentar',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    window.location.href = '{{ route("settings.desa.reset") }}';
                }
            });
        }

        function removeImage(key) {
            Swal.fire({
                title: 'Hapus Logo',
                text: 'Apakah Anda yakin ingin menghapus logo ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Set value to empty
                    const input = document.querySelector(`input[name="settings[${key}][value]"]`);
                    if (input) {
                        input.value = '';
                    }

                    // Hide the image container
                    const imageContainer = event.target.closest('.flex');
                    if (imageContainer) {
                        imageContainer.style.display = 'none';
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Logo Dihapus!',
                        text: 'Logo telah berhasil dihapus.',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            });
        }
    </script>

    <style>
    .swal-wide {
        width: 500px !important;
    }
    </style>
@endsection

