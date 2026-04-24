@extends('layouts.app')

@section('title', 'Detail Audit Log')
@section('subtitle', 'Detail aktivitas sistem')

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
                            <h1 class="text-2xl sm:text-3xl font-bold">Detail Audit Log</h1>
                            <p class="text-red-100 text-sm sm:text-base mt-1">Detail aktivitas dan perubahan data</p>
                        </div>
                    </div>
                    <a href="{{ route('audit-log.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white font-medium rounded-xl transition-all duration-300 backdrop-blur-sm">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <!-- Activity Details -->
                    <div class="space-y-6">
                        <!-- Basic Information -->
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                Informasi Dasar
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">ID Aktivitas</label>
                                    <p class="text-sm text-gray-900 font-mono">{{ $activity->id }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Waktu</label>
                                    <p class="text-sm text-gray-900">{{ $activity->created_at->format('d F Y H:i:s') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Event</label>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                        @if($activity->event == 'created') bg-green-100 text-green-800
                                        @elseif($activity->event == 'updated') bg-yellow-100 text-yellow-800
                                        @elseif($activity->event == 'deleted') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        <i class="fas fa-{{ $activity->event == 'created' ? 'plus' : ($activity->event == 'updated' ? 'edit' : 'trash') }} mr-1"></i>
                                        {{ ucfirst($activity->event) }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">User</label>
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-100 to-blue-200 flex items-center justify-center mr-2">
                                            <i class="fas fa-user text-blue-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-900 font-medium">{{ $activity->causer->name ?? 'System' }}</p>
                                            @if($activity->causer)
                                                <p class="text-xs text-gray-500">{{ $activity->causer->email }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Network Information -->
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-globe text-blue-600 mr-2"></i>
                                Informasi Jaringan
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">IP Address</label>
                                    <p class="text-sm text-gray-900 font-mono">{{ $activity->ip_address ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Method</label>
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
                                </div>
                                <div class="md:col-span-2 lg:col-span-1">
                                    <label class="block text-sm font-medium text-gray-600 mb-1">URL</label>
                                    <p class="text-sm text-gray-900 font-mono break-all">{{ $activity->url ?? '-' }}</p>
                                </div>
                            </div>
                            @if($activity->user_agent)
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-600 mb-1">User Agent</label>
                                <div class="bg-white p-3 rounded border">
                                    <p class="text-xs text-gray-700 break-all">{{ $activity->user_agent }}</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Subject Information -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Subjek</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Model</label>
                                    <p class="text-sm text-gray-900">{{ class_basename($activity->subject_type) }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">ID Subjek</label>
                                    <p class="text-sm text-gray-900">{{ $activity->subject_id }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        @if($activity->description)
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Deskripsi</h3>
                            <p class="text-sm text-gray-900">{{ $activity->description }}</p>
                        </div>
                        @endif

                        <!-- Properties -->
                        @if($activity->properties && $activity->properties->isNotEmpty())
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Properties</h3>
                            <div class="space-y-4">
                                @foreach($activity->properties as $key => $value)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
                                        <div class="bg-white p-3 rounded border">
                                            <pre class="text-sm text-gray-900 whitespace-pre-wrap">{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value }}</pre>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Changes (for updated events) -->
                        @if($activity->event == 'updated' && $activity->properties->has('old') && $activity->properties->has('attributes'))
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Perubahan Data</h3>
                            <div class="space-y-4">
                                @php
                                    $oldValues = $activity->properties->get('old', []);
                                    $newValues = $activity->properties->get('attributes', []);
                                    $changedFields = array_keys(array_diff_assoc($newValues, $oldValues));
                                @endphp

                                @foreach($changedFields as $field)
                                    <div class="border rounded-lg p-4">
                                        <h4 class="font-medium text-gray-900 mb-2">{{ ucfirst(str_replace('_', ' ', $field)) }}</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-red-600 mb-1">Nilai Lama</label>
                                                <div class="bg-red-50 p-2 rounded text-sm">
                                                    {{ $oldValues[$field] ?? '-' }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-green-600 mb-1">Nilai Baru</label>
                                                <div class="bg-green-50 p-2 rounded text-sm">
                                                    {{ $newValues[$field] ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

