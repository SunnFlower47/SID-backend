@extends('layouts.app')

@section('title', 'Statistik Backup')
@section('subtitle', 'Statistik backup dan restore sistem')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Statistik Backup</h2>
                            <p class="text-gray-600 mt-1">Statistik backup dan restore sistem</p>
                        </div>
                        <a href="{{ route('backup.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Backup
                        </a>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-blue-50 rounded-lg p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-archive text-blue-600 text-3xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-600">Total Backup</p>
                                    <p class="text-3xl font-bold text-blue-900">{{ $statistics['total_backups'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-database text-green-600 text-3xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-600">Database Backup</p>
                                    <p class="text-3xl font-bold text-green-900">{{ $statistics['database_backups'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-folder text-yellow-600 text-3xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-yellow-600">File Backup</p>
                                    <p class="text-3xl font-bold text-yellow-900">{{ $statistics['file_backups'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-hdd text-purple-600 text-3xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-purple-600">Total Ukuran</p>
                                    <p class="text-3xl font-bold text-purple-900">{{ $statistics['total_size'] ?? '0 MB' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Backup by Type -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Backup Berdasarkan Jenis</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                        <span class="text-sm text-gray-900">Full Backup</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $statistics['full_backups'] ?? 0 }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                        <span class="text-sm text-gray-900">Database Only</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $statistics['database_backups'] ?? 0 }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                                        <span class="text-sm text-gray-900">Files Only</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $statistics['file_backups'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Backups -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Backup Terbaru</h3>
                            <div class="space-y-3">
                                @forelse($recentBackups as $backup)
                                    <div class="flex items-center justify-between p-3 bg-white rounded-lg border">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $backup['name'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $backup['date'] }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-900">{{ $backup['size'] }}</p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                @if($backup['type'] == 'full') bg-blue-100 text-blue-800
                                                @elseif($backup['type'] == 'database') bg-green-100 text-green-800
                                                @else bg-yellow-100 text-yellow-800
                                                @endif">
                                                {{ ucfirst($backup['type']) }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-sm">Tidak ada backup terbaru</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Storage Usage -->
                    <div class="mt-8">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Penggunaan Storage</h3>
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                                        <span>Storage Terpakai</span>
                                        <span>{{ $statistics['used_storage'] ?? '0 MB' }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $statistics['storage_percentage'] ?? 0 }}%"></div>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <p>Total Storage: {{ $statistics['total_storage'] ?? 'Tidak diketahui' }}</p>
                                    <p>Storage Tersisa: {{ $statistics['free_storage'] ?? 'Tidak diketahui' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


