@extends('layouts.app')

@section('title', 'Backup & Restore')
@section('subtitle', 'Kelola backup dan restore data sistem')

@section('content')
    <!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Backup & Restore</h2>
                            <p class="text-gray-600 mt-1">Kelola backup dan restore data sistem</p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('backup.statistics') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                                <i class="fas fa-chart-bar mr-2"></i>
                                Statistik
                            </a>
                        </div>
                    </div>

                    <!-- Backup Form -->
                    <div class="bg-gradient-to-r from-blue-50 to-green-50 rounded-xl p-6 mb-6 border border-blue-200">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-database text-blue-600 text-2xl mr-3"></i>
                            <h3 class="text-xl font-semibold text-gray-900">Buat Backup Database</h3>
                        </div>
                        <p class="text-gray-600 mb-4">Backup otomatis akan mencakup seluruh database sistem desa</p>
                        <form action="{{ route('backup.create') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nama Backup
                                    </label>
                                    <input type="text"
                                           id="name"
                                           name="name"
                                           value="backup-{{ date('Y-m-d-H-i-s') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Masukkan nama backup">
                                </div>
                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                        Jenis Backup
                                    </label>
                                    <select id="type" name="type"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="database">Database Only (Recommended)</option>
                                        <option value="full">Full Backup (Database + Files)</option>
                                        <option value="files">Files Only</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit"
                                        class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors shadow-lg hover:shadow-xl">
                                    <i class="fas fa-save mr-2"></i>
                                    Buat Backup Sekarang
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Backup List -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-blue-50">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <i class="fas fa-archive text-blue-600 mr-2"></i>
                                    Daftar Backup
                                </h3>
                                <span class="text-sm text-gray-500">
                                    Total: {{ $backupFiles->count() }} file
                                </span>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama File</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($backupFiles as $backup)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <div class="flex items-center">
                                                    <i class="fas fa-file-archive text-gray-400 mr-2"></i>
                                                    {{ $backup['name'] }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @php
                                                    $type = 'database';
                                                    if (str_contains($backup['name'], 'full')) $type = 'full';
                                                    elseif (str_contains($backup['name'], 'files')) $type = 'files';
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($type == 'full') bg-blue-100 text-blue-800
                                                    @elseif($type == 'database') bg-green-100 text-green-800
                                                    @else bg-yellow-100 text-yellow-800
                                                    @endif">
                                                    {{ ucfirst($type) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ number_format($backup['size'] / 1024, 2) }} KB
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $backup['created_at'] ? $backup['created_at']->format('d M Y, H:i') : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('backup.download', $backup['name']) }}"
                                                       class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 transition-colors text-xs font-medium"
                                                       title="Download Backup">
                                                        <i class="fas fa-download mr-1"></i>
                                                        Download
                                                    </a>
                                                    <button onclick="deleteBackup('{{ $backup['name'] }}')"
                                                            class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition-colors text-xs font-medium"
                                                            title="Hapus Backup">
                                                        <i class="fas fa-trash mr-1"></i>
                                                        Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center">
                                                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-archive text-gray-400 text-2xl"></i>
                                                </div>
                                                <h4 class="text-lg font-semibold text-gray-700 mb-2">Belum ada backup</h4>
                                                <p class="text-sm text-gray-500">Buat backup pertama untuk melindungi data sistem</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Restore Modal -->
    <div id="restoreModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Restore Backup</h3>
                    <button onclick="closeRestoreModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="text-sm text-gray-600 mb-4">
                    Apakah Anda yakin ingin melakukan restore? Data saat ini akan diganti dengan data dari backup.
                </p>
                <div class="flex justify-end space-x-3">
                    <button onclick="closeRestoreModal()"
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">
                        Batal
                    </button>
                    <form id="restoreForm" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                            Ya, Restore
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function createBackup() {
            // This would typically show a loading state
            console.log('Creating backup...');
        }

        function restoreBackup(filename) {
            document.getElementById('restoreForm').action = '{{ route("backup.restore") }}';
            document.getElementById('restoreModal').classList.remove('hidden');
        }

        function closeRestoreModal() {
            document.getElementById('restoreModal').classList.add('hidden');
        }

        function deleteBackup(filename) {
            if (confirm('Apakah Anda yakin ingin menghapus backup ini?')) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("backup.delete", ":filename") }}'.replace(':filename', filename);

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@noncescript
// SweetAlert untuk notifikasi sukses
@if(session('success'))
    Swal.fire({
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        icon: 'success',
        confirmButtonText: 'OK'
    });
@endif

// SweetAlert untuk notifikasi error
@if(session('error'))
    Swal.fire({
        title: 'Error!',
        text: '{{ session('error') }}',
        icon: 'error',
        confirmButtonText: 'OK'
    });
@endif

// SweetAlert untuk notifikasi warning
@if(session('warning'))
    Swal.fire({
        title: 'Peringatan!',
        text: '{{ session('warning') }}',
        icon: 'warning',
        confirmButtonText: 'OK'
    });
@endif

// SweetAlert untuk notifikasi info
@if(session('info'))
    Swal.fire({
        title: 'Informasi!',
        text: '{{ session('info') }}',
        icon: 'info',
        confirmButtonText: 'OK'
    });
@endif
@endnoncescript
@endsection


