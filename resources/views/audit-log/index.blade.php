@extends('layouts.app')

@section('title', 'Audit Log')
@section('subtitle', 'Riwayat aktivitas sistem')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Card -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white mb-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                    <div class="flex items-center mb-4 sm:mb-0">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-history text-2xl text-yellow-300"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold">Audit Log</h1>
                            <p class="text-red-100 text-sm sm:text-base mt-1">Riwayat aktivitas dan perubahan data dalam sistem</p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('audit-log.export.excel') }}"
                           class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white font-medium rounded-xl transition-all duration-300 backdrop-blur-sm">
                            <i class="fas fa-file-excel mr-2"></i>
                            Export Excel
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <!-- Filter Form -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <form method="GET" action="{{ route('audit-log.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="causer_id" class="block text-sm font-medium text-gray-700 mb-1">User</label>
                                <select id="causer_id" name="causer_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Semua User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('causer_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="event" class="block text-sm font-medium text-gray-700 mb-1">Event</label>
                                <select id="event" name="event" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Semua Event</option>
                                    <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                                    <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                                    <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                                </select>
                            </div>
                            <div>
                                <label for="subject_type" class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                                <select id="subject_type" name="subject_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Semua Model</option>
                                    <option value="App\Models\Penduduk" {{ request('subject_type') == 'App\Models\Penduduk' ? 'selected' : '' }}>Penduduk</option>
                                    <option value="App\Models\Mutasi" {{ request('subject_type') == 'App\Models\Mutasi' ? 'selected' : '' }}>Mutasi</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                    <i class="fas fa-search mr-2"></i>
                                    Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Audit Log Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Waktu</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Event</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Model</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">IP Address</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Method</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($activities as $activity)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                            <div class="flex flex-col">
                                                <span class="font-medium">{{ $activity->created_at->format('d/m/Y') }}</span>
                                                <span class="text-gray-500">{{ $activity->created_at->format('H:i:s') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-100 to-blue-200 flex items-center justify-center mr-2">
                                                    <i class="fas fa-user text-blue-600 text-xs"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium">{{ $activity->causer->name ?? 'System' }}</div>
                                                    @if($activity->causer)
                                                        <div class="text-xs text-gray-500">{{ $activity->causer->email }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-medium
                                                @if($activity->event == 'created') bg-green-100 text-green-800
                                                @elseif($activity->event == 'updated') bg-yellow-100 text-yellow-800
                                                @elseif($activity->event == 'deleted') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                <i class="fas fa-{{ $activity->event == 'created' ? 'plus' : ($activity->event == 'updated' ? 'edit' : 'trash') }} mr-1"></i>
                                                {{ ucfirst($activity->event) }}
                                            </span>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                            <div class="flex items-center">
                                                <i class="fas fa-database text-gray-400 mr-2"></i>
                                                {{ class_basename($activity->subject_type) }}
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                            <div class="flex items-center">
                                                <i class="fas fa-globe text-gray-400 mr-2"></i>
                                                <span class="font-mono">{{ $activity->ip_address ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                            @if($activity->method)
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                                    @if($activity->method == 'GET') bg-blue-100 text-blue-800
                                                    @elseif($activity->method == 'POST') bg-green-100 text-green-800
                                                    @elseif($activity->method == 'PUT' || $activity->method == 'PATCH') bg-yellow-100 text-yellow-800
                                                    @elseif($activity->method == 'DELETE') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ $activity->method }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                                            <a href="{{ route('audit-log.show', $activity) }}"
                                               class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-colors">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                                            <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                            <p>Tidak ada data audit log</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($activities->hasPages())
                        <div class="mt-6">
                            {{ $activities->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
