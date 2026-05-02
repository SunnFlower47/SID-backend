@extends('layouts.app')

@section('title', 'Detail Pesan Kontak')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-500 rounded-2xl shadow-lg p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center mb-4 sm:mb-0">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-envelope text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">Detail Pesan Kontak</h1>
                    <p class="text-green-100 text-sm sm:text-base">Lihat detail pesan dari {{ $contactMessage->nama }}</p>
                </div>
            </div>
            <a href="{{ route('contact-messages.index') }}"
               class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Message Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Message Card -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Pesan</h3>
                        <div class="flex items-center space-x-2">
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
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$contactMessage->status] }}">
                                {{ $statusLabels[$contactMessage->status] }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="mb-6">
                        <h4 class="text-xl font-semibold text-gray-900 mb-2">{{ $contactMessage->subjek }}</h4>
                        <div class="text-gray-600 whitespace-pre-wrap">{{ $contactMessage->pesan }}</div>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <div class="text-sm text-gray-500">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>Dikirim pada {{ $contactMessage->created_at->format('d F Y, H:i') }}</span>
                            </div>
                            @if($contactMessage->read_at)
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-eye mr-2"></i>
                                    <span>Dibaca pada {{ $contactMessage->read_at->format('d F Y, H:i') }}</span>
                                </div>
                            @endif
                            @if($contactMessage->replied_at)
                                <div class="flex items-center">
                                    <i class="fas fa-reply mr-2"></i>
                                    <span>Dibalas pada {{ $contactMessage->replied_at->format('d F Y, H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Reply Section -->
            @if($contactMessage->admin_reply)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-reply mr-2 text-green-600"></i>
                            Balasan Admin
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="text-gray-700 whitespace-pre-wrap">{{ $contactMessage->admin_reply }}</div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Sender Info -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Pengirim</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 h-16 w-16">
                            <div class="h-16 w-16 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                <span class="text-white font-bold text-xl">
                                    {{ strtoupper(substr($contactMessage->nama, 0, 1)) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-lg font-semibold text-gray-900">{{ $contactMessage->nama }}</div>
                            <div class="text-sm text-gray-500">{{ $contactMessage->email }}</div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-gray-400 w-5"></i>
                            <span class="ml-3 text-sm text-gray-600">{{ $contactMessage->email }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-phone text-gray-400 w-5"></i>
                            <span class="ml-3 text-sm text-gray-600">{{ $contactMessage->telepon }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-globe text-gray-400 w-5"></i>
                            <span class="ml-3 text-sm text-gray-600">{{ $contactMessage->ip_address ?? 'Tidak diketahui' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Aksi</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3">
                        @if($contactMessage->status === 'unread')
                            <form action="{{ route('contact-messages.mark-read', $contactMessage) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 py-3 rounded-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl">
                                    <i class="fas fa-check mr-2"></i>
                                    <span class="hidden sm:inline">Tandai sebagai</span> Dibaca
                                </button>
                            </form>
                        @endif

                        @if($contactMessage->status === 'read' && $contactMessage->status !== 'replied')
                            <form action="{{ route('contact-messages.mark-replied', $contactMessage) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white px-4 py-3 rounded-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl">
                                    <i class="fas fa-reply mr-2"></i>
                                    <span class="hidden sm:inline">Tandai sebagai</span> Dibalas
                                </button>
                            </form>
                        @endif

                        @if($contactMessage->status !== 'archived')
                            <form action="{{ route('contact-messages.archive', $contactMessage) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="w-full bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-4 py-3 rounded-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl">
                                    <i class="fas fa-archive mr-2"></i>
                                    Arsipkan
                                </button>
                            </form>
                        @endif

                        <button type="button"
                                onclick="confirmDelete('{{ $contactMessage->id }}', '{{ addslashes($contactMessage->subjek) }}')"
                                class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-4 py-3 rounded-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="fas fa-trash mr-2"></i>
                            Hapus Pesan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Admin Reply Form -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Balas Pesan</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('contact-messages.mark-replied', $contactMessage) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="admin_reply" class="block text-sm font-medium text-gray-700 mb-2">Balasan Admin</label>
                            <textarea id="admin_reply"
                                      name="admin_reply"
                                      rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                      placeholder="Tulis balasan untuk {{ $contactMessage->nama }}...">{{ old('admin_reply', $contactMessage->admin_reply) }}</textarea>
                            @error('admin_reply')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Kirim Balasan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Technical Info -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Teknis</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">ID Pesan:</span>
                            <span class="font-mono text-gray-900">#{{ $contactMessage->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">IP Address:</span>
                            <span class="font-mono text-gray-900">{{ $contactMessage->ip_address ?? 'Tidak diketahui' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">User Agent:</span>
                            <span class="font-mono text-gray-900 text-xs break-all">{{ Str::limit($contactMessage->user_agent, 50) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Dibuat:</span>
                            <span class="text-gray-900">{{ $contactMessage->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Diupdate:</span>
                            <span class="text-gray-900">{{ $contactMessage->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@noncescript
document.addEventListener('DOMContentLoaded', function() {
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

    // Form submission with loading
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Show loading for non-delete forms
            if (!form.querySelector('input[name="_method"][value="DELETE"]')) {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
                }
            }
        });
    });
});
@endnoncescript
@endsection

