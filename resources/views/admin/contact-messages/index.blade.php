@extends('layouts.app')

@section('title', 'Kelola Pesan Kontak')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8 mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center mb-4 sm:mb-0">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-envelope text-yellow-300 text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">Kelola Pesan Kontak</h1>
                    <p class="text-green-100 text-sm sm:text-base">Kelola pesan yang masuk dari warga melalui form kontak</p>
                </div>
            </div>
            <a href="{{ route('contact-messages.index', ['status' => 'unread']) }}" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                <i class="fas fa-envelope mr-2"></i>
                Belum Dibaca ({{ $stats['unread'] }})
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6">
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-blue-600 uppercase tracking-wide">Total Pesan</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-envelope text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-yellow-600 uppercase tracking-wide">Belum Dibaca</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['unread'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-envelope-open text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-green-600 uppercase tracking-wide">Sudah Dibaca</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['read'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-purple-600 uppercase tracking-wide">Sudah Dibalas</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['replied'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-reply text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-gray-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Diarsipkan</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['archived'] }}</p>
                </div>
                <div class="bg-gray-100 p-3 rounded-full">
                    <i class="fas fa-archive text-2xl text-gray-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-2xl shadow-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h6 class="text-lg font-semibold text-gray-900">Filter & Pencarian</h6>
        </div>
        <div class="p-6">
            <form method="GET" class="flex flex-col lg:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari Pesan</label>
                <input type="text"
                       id="search"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari berdasarkan nama, email, atau subjek..."
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
            </div>

            <!-- Status Filter -->
            <div class="lg:w-48">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status"
                        name="status"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                    <option value="">Semua Status</option>
                    <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Belum Dibaca</option>
                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Sudah Dibaca</option>
                    <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Sudah Dibalas</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Diarsipkan</option>
                </select>
            </div>

            <!-- Date Filter -->
            <div class="lg:w-48">
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                <input type="date"
                       id="date_from"
                       name="date_from"
                       value="{{ request('date_from') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
            </div>

            <div class="lg:w-48">
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                <input type="date"
                       id="date_to"
                       name="date_to"
                       value="{{ request('date_to') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
            </div>

            <!-- Filter Buttons -->
            <div class="flex gap-2 items-end">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl">
                    <i class="fas fa-search mr-2"></i>
                    Filter
                </button>
                <a href="{{ route('contact-messages.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl">
                    <i class="fas fa-times mr-2"></i>
                    Reset
                </a>
            </div>
            </form>
        </div>
    </div>

    <!-- Messages Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Pesan</h3>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Total: {{ $messages->total() }} pesan</span>
                </div>
            </div>
        </div>

        @if($messages->count() > 0)
            <!-- Mobile Card View -->
            <div class="block lg:hidden">
                <div class="p-4 space-y-4">
                    @foreach($messages as $message)
                    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow cursor-pointer group {{ $message->status === 'unread' ? 'bg-blue-50 border-blue-200' : '' }}" onclick="window.location='{{ route('contact-messages.show', $message) }}'">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                                    <span class="text-white font-semibold text-sm">
                                        {{ strtoupper(substr($message->nama, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-900 truncate group-hover:text-blue-900 transition-colors">
                                        {{ $message->nama }}
                                    </h4>
                                    <p class="text-xs text-gray-500 truncate">{{ $message->email }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $message->telepon }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="checkbox"
                                       name="selected_messages[]"
                                       value="{{ $message->id }}"
                                       class="message-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       onclick="event.stopPropagation()">
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="text-sm font-medium text-gray-900 mb-1">{{ $message->subjek }}</h5>
                            <p class="text-xs text-gray-500 line-clamp-2">{{ Str::limit($message->pesan, 80) }}</p>
                        </div>

                        <!-- Action Buttons - Always Visible -->
                        <div class="flex flex-wrap items-center justify-end gap-2 mb-4">
                            <button class="flex items-center px-2 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors text-xs font-medium"
                                    onclick="event.stopPropagation(); window.location='{{ route('contact-messages.show', $message) }}'"
                                    title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if($message->status === 'unread')
                                <form action="{{ route('contact-messages.mark-read', $message) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="flex items-center px-2 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg transition-colors text-xs font-medium"
                                            onclick="event.stopPropagation()"
                                            title="Tandai sebagai Dibaca">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif
                            @if($message->status === 'read' && $message->status !== 'replied')
                                <form action="{{ route('contact-messages.mark-replied', $message) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="flex items-center px-2 py-1.5 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 rounded-lg transition-colors text-xs font-medium"
                                            onclick="event.stopPropagation()"
                                            title="Tandai sebagai Dibalas">
                                        <i class="fas fa-reply"></i>
                                    </button>
                                </form>
                            @endif
                            @if($message->status !== 'archived')
                                <form action="{{ route('contact-messages.archive', $message) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="flex items-center px-2 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg transition-colors text-xs font-medium"
                                            onclick="event.stopPropagation()"
                                            title="Arsipkan">
                                        <i class="fas fa-archive"></i>
                                    </button>
                                </form>
                            @endif
                            <button onclick="event.stopPropagation(); confirmDelete('{{ $message->id }}', '{{ addslashes($message->subjek) }}')"
                                    class="flex items-center px-2 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg transition-colors text-xs font-medium"
                                    title="Hapus Data">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Status:</span>
                                @php
                                    $statusColors = [
                                        'unread' => 'bg-yellow-100 text-yellow-800',
                                        'read' => 'bg-blue-100 text-blue-800',
                                        'replied' => 'bg-green-100 text-green-800',
                                        'archived' => 'bg-gray-100 text-gray-800'
                                    ];
                                    $statusLabels = [
                                        'unread' => 'Belum Dibaca',
                                        'read' => 'Sudah Dibaca',
                                        'replied' => 'Sudah Dibalas',
                                        'archived' => 'Diarsipkan'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$message->status] }}">
                                    {{ $statusLabels[$message->status] }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Tanggal:</span>
                                <span class="text-sm text-gray-900">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden lg:block">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subjek</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($messages as $message)
                                <tr class="hover:bg-gray-50 transition-colors duration-200 {{ $message->status === 'unread' ? 'bg-blue-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox"
                                               name="selected_messages[]"
                                               value="{{ $message->id }}"
                                               class="message-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                                    <span class="text-white font-semibold text-sm">
                                                        {{ strtoupper(substr($message->nama, 0, 1)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $message->nama }}</div>
                                                <div class="text-sm text-gray-500">{{ $message->email }}</div>
                                                <div class="text-xs text-gray-400">{{ $message->telepon }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $message->subjek }}</div>
                                        <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($message->pesan, 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'unread' => 'bg-yellow-100 text-yellow-800',
                                                'read' => 'bg-blue-100 text-blue-800',
                                                'replied' => 'bg-green-100 text-green-800',
                                                'archived' => 'bg-gray-100 text-gray-800'
                                            ];
                                            $statusLabels = [
                                                'unread' => 'Belum Dibaca',
                                                'read' => 'Sudah Dibaca',
                                                'replied' => 'Sudah Dibalas',
                                                'archived' => 'Diarsipkan'
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$message->status] }}">
                                            {{ $statusLabels[$message->status] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>{{ $message->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs">{{ $message->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('contact-messages.show', $message) }}"
                                               class="inline-flex items-center px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors text-sm font-medium"
                                               title="Lihat Detail">
                                                <i class="fas fa-eye mr-1"></i>
                                                Detail
                                            </a>

                                            @if($message->status === 'unread')
                                                <form action="{{ route('contact-messages.mark-read', $message) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="inline-flex items-center px-3 py-2 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg transition-colors text-sm font-medium"
                                                            title="Tandai sebagai Dibaca">
                                                        <i class="fas fa-check mr-1"></i>
                                                        Baca
                                                    </button>
                                                </form>
                                            @endif

                                            @if($message->status === 'read' && $message->status !== 'replied')
                                                <form action="{{ route('contact-messages.mark-replied', $message) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="inline-flex items-center px-3 py-2 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 rounded-lg transition-colors text-sm font-medium"
                                                            title="Tandai sebagai Dibalas">
                                                        <i class="fas fa-reply mr-1"></i>
                                                        Balas
                                                    </button>
                                                </form>
                                            @endif

                                            @if($message->status !== 'archived')
                                                <form action="{{ route('contact-messages.archive', $message) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="inline-flex items-center px-3 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg transition-colors text-sm font-medium"
                                                            title="Arsipkan">
                                                        <i class="fas fa-archive mr-1"></i>
                                                        Arsip
                                                    </button>
                                                </form>
                                            @endif

                                            <button type="button"
                                                    onclick="confirmDelete('{{ $message->id }}', '{{ addslashes($message->subjek) }}')"
                                                    class="inline-flex items-center px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg transition-colors text-sm font-medium"
                                                    title="Hapus">
                                                <i class="fas fa-trash mr-1"></i>
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <button type="button"
                                id="bulk-mark-read"
                                class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center shadow-lg hover:shadow-xl"
                                disabled>
                            <i class="fas fa-check mr-2"></i>
                            <span class="hidden sm:inline">Tandai sebagai</span> Dibaca
                        </button>
                        <button type="button"
                                id="bulk-mark-replied"
                                class="bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center shadow-lg hover:shadow-xl"
                                disabled>
                            <i class="fas fa-reply mr-2"></i>
                            <span class="hidden sm:inline">Tandai sebagai</span> Dibalas
                        </button>
                        <button type="button"
                                id="bulk-archive"
                                class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center shadow-lg hover:shadow-xl"
                                disabled>
                            <i class="fas fa-archive mr-2"></i>
                            Arsipkan
                        </button>
                        <button type="button"
                                id="bulk-delete"
                                class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center shadow-lg hover:shadow-xl"
                                disabled>
                            <i class="fas fa-trash mr-2"></i>
                            Hapus
                        </button>
                    </div>
                    <div class="text-sm text-gray-600 text-center lg:text-right">
                        <span id="selected-count" class="font-semibold text-green-600">0</span> pesan dipilih
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $messages->appends(request()->query())->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400">
                    <i class="fas fa-envelope text-6xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada pesan</h3>
                <p class="mt-2 text-sm text-gray-500">
                    @if(request()->has('search') || request()->has('status'))
                        Tidak ada pesan yang sesuai dengan filter yang dipilih.
                    @else
                        Belum ada pesan yang masuk dari warga.
                    @endif
                </p>
                @if(request()->has('search') || request()->has('status'))
                    <div class="mt-6">
                        <a href="{{ route('contact-messages.index') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl inline-flex items-center transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="fas fa-times mr-2"></i>
                            Hapus Filter
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Bulk Action Form -->
<form id="bulk-action-form" method="POST" action="{{ route('contact-messages.bulk-action') }}" style="display: none;">
    @csrf
    <input type="hidden" name="action" id="bulk-action">
    <div id="bulk-message-ids"></div>
</form>

@noncescript
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const messageCheckboxes = document.querySelectorAll('.message-checkbox');
    const bulkButtons = document.querySelectorAll('[id^="bulk-"]');
    const selectedCountSpan = document.getElementById('selected-count');
    const bulkActionForm = document.getElementById('bulk-action-form');
    const bulkActionInput = document.getElementById('bulk-action');
    const bulkMessageIdsDiv = document.getElementById('bulk-message-ids');

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        messageCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkButtons();
    });

    // Individual checkbox change
    messageCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkButtons();
        });
    });

    // Update bulk buttons state
    function updateBulkButtons() {
        const checkedBoxes = document.querySelectorAll('.message-checkbox:checked');
        const count = checkedBoxes.length;

        selectedCountSpan.textContent = count;

        bulkButtons.forEach(button => {
            button.disabled = count === 0;
        });

        // Update select all checkbox state
        if (count === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (count === messageCheckboxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }

    // Bulk action handlers
    document.getElementById('bulk-mark-read').addEventListener('click', function() {
        performBulkAction('mark_read');
    });

    document.getElementById('bulk-mark-replied').addEventListener('click', function() {
        performBulkAction('mark_replied');
    });

    document.getElementById('bulk-archive').addEventListener('click', function() {
        performBulkAction('archive');
    });

    document.getElementById('bulk-delete').addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.message-checkbox:checked');
        if (checkedBoxes.length === 0) return;

        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: `Apakah Anda yakin ingin menghapus ${checkedBoxes.length} pesan yang dipilih?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                performBulkAction('delete');
            }
        });
    });

    function performBulkAction(action) {
        const checkedBoxes = document.querySelectorAll('.message-checkbox:checked');
        const messageIds = Array.from(checkedBoxes).map(checkbox => checkbox.value);

        if (messageIds.length === 0) return;

        // Clear previous inputs
        bulkMessageIdsDiv.innerHTML = '';

        // Add message IDs as hidden inputs
        messageIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            bulkMessageIdsDiv.appendChild(input);
        });

        // Set action and submit
        bulkActionInput.value = action;
        bulkActionForm.submit();
    }

    // Individual delete confirmation
    window.confirmDelete = function(messageId, subject) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: `Apakah Anda yakin ingin menghapus pesan "${subject}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create and submit delete form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/contact-messages/${messageId}`;

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
        });
    };

    // Show success/error messages from session
    @if(session('success'))
        Swal.fire({
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#10b981'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Error!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        });
    @endif

    // Show validation errors
    @if($errors->any())
        Swal.fire({
            title: 'Terjadi Kesalahan!',
            html: '<ul class="text-left">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        });
    @endif
});
@endnoncescript
@endsection
