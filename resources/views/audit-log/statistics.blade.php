@extends('layouts.app')

@section('title', 'Statistik Audit Log')
@section('subtitle', 'Statistik aktivitas sistem')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Statistik Audit Log</h2>
                            <p class="text-gray-600 mt-1">Statistik aktivitas dan perubahan data dalam sistem</p>
                        </div>
                        <a href="{{ route('audit-log.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Audit Log
                        </a>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-blue-50 rounded-lg p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-chart-line text-blue-600 text-3xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-600">Total Aktivitas</p>
                                    <p class="text-3xl font-bold text-blue-900">{{ number_format($totalActivities) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-calendar-day text-green-600 text-3xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-600">Hari Ini</p>
                                    <p class="text-3xl font-bold text-green-900">{{ number_format($todayActivities) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-calendar-week text-yellow-600 text-3xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-yellow-600">Minggu Ini</p>
                                    <p class="text-3xl font-bold text-yellow-900">{{ number_format($thisWeekActivities) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-calendar-alt text-purple-600 text-3xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-purple-600">Bulan Ini</p>
                                    <p class="text-3xl font-bold text-purple-900">{{ number_format($thisMonthActivities) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Activities by Event Type -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aktivitas Berdasarkan Jenis Event</h3>
                            <div class="space-y-3">
                                @forelse($activitiesByEvent as $event)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($event->event == 'created') bg-green-100 text-green-800
                                                @elseif($event->event == 'updated') bg-yellow-100 text-yellow-800
                                                @elseif($event->event == 'deleted') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($event->event) }}
                                            </span>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ number_format($event->total) }}</span>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-sm">Tidak ada data</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Activities by Subject Type -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aktivitas Berdasarkan Model</h3>
                            <div class="space-y-3">
                                @forelse($activitiesBySubject as $subject)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-900">{{ class_basename($subject->subject_type) }}</span>
                                        <span class="text-sm font-medium text-gray-900">{{ number_format($subject->total) }}</span>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-sm">Tidak ada data</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Activities by User -->
                    <div class="mt-8">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aktivitas Berdasarkan User</h3>
                            <div class="space-y-3">
                                @forelse($activitiesByUser as $user)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-900">{{ $user->causer->name ?? 'System' }}</span>
                                        <span class="text-sm font-medium text-gray-900">{{ number_format($user->total) }}</span>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-sm">Tidak ada data</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="mt-8">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aktivitas Terbaru</h3>
                            <div class="space-y-3">
                                @forelse($recentActivities as $activity)
                                    <div class="flex items-center justify-between p-3 bg-white rounded-lg border">
                                        <div class="flex items-center space-x-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($activity->event == 'created') bg-green-100 text-green-800
                                                @elseif($activity->event == 'updated') bg-yellow-100 text-yellow-800
                                                @elseif($activity->event == 'deleted') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($activity->event) }}
                                            </span>
                                            <span class="text-sm text-gray-900">{{ class_basename($activity->subject_type) }}</span>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-900">{{ $activity->causer->name ?? 'System' }}</p>
                                            <p class="text-xs text-gray-500">{{ $activity->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-sm">Tidak ada aktivitas terbaru</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

