@extends('layouts.app')

@section('title', 'Manajemen Role')
@section('subtitle', 'Kelola role dan permission sistem')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-3 lg:space-y-0 px-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manajemen Role</h1>
            <p class="text-gray-600 mt-1">Kelola role dan permission sistem</p>
        </div>
        <div class="flex flex-wrap gap-3">
            @can('admin_sistem')
            <a href="{{ route('settings.roles.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center transition-colors shadow-md">
                <i class="fas fa-plus mr-2"></i>
                Tambah Role
            </a>
            @endcan
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mx-6 sm:mx-0">
        <div class="px-3 sm:px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Role</h3>
            <p class="text-sm text-gray-600">Total {{ $roles->total() }} role</p>
        </div>

        @if($roles->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 sm:px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Role
                            </th>
                            <th class="px-3 sm:px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Permissions
                            </th>
                            <th class="px-3 sm:px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jumlah User
                            </th>
                            <th class="px-3 sm:px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dibuat
                            </th>
                            <th class="px-3 sm:px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($roles as $role)
                        <tr class="odd:bg-gray-50 even:bg-white hover:bg-blue-50 cursor-pointer transition-colors group" onclick="window.location='{{ route('settings.roles.show', $role) }}'">
                            <td class="px-3 sm:px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3 group-hover:bg-blue-200 transition-colors">
                                        <i class="fas fa-user-tag text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 group-hover:text-blue-900 transition-colors">{{ $role->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $role->guard_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 sm:px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($role->permissions->take(3) as $permission)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $permission->name }}
                                        </span>
                                    @endforeach
                                    @if($role->permissions->count() > 3)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            +{{ $role->permissions->count() - 3 }} lainnya
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 sm:px-4 py-3 text-sm text-gray-900">
                                {{ $role->users_count ?? 0 }} user
                            </td>
                            <td class="px-3 sm:px-4 py-3 text-sm text-gray-900">
                                {{ $role->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-3 sm:px-4 py-3 text-right">
                                <div class="flex items-center justify-end space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <button class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50 transition-colors"
                                            onclick="event.stopPropagation(); window.location='{{ route('settings.roles.show', $role) }}'"
                                            title="Lihat Detail">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>
                                    @can('admin_sistem')
                                    <a href="{{ route('settings.roles.edit', $role) }}"
                                       class="text-gray-600 hover:text-gray-900 p-1 rounded hover:bg-gray-50 transition-colors"
                                       onclick="event.stopPropagation()"
                                       title="Edit Role">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    @endcan
                                    @can('admin_sistem')
                                    @if($role->name !== 'Super Admin')
                                    <button onclick="event.stopPropagation(); confirmDelete('{{ $role->id }}', '{{ $role->name }}')"
                                            class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 transition-colors"
                                            title="Hapus Role">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                    @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-3 sm:px-6 py-4 border-t border-gray-200">
                {{ $roles->links() }}
            </div>
        @else
            <div class="text-center py-12 px-6">
                <i class="fas fa-user-tag text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada role</h3>
                <p class="text-gray-500 mb-6">Mulai dengan menambahkan role baru</p>
                @can('admin_sistem')
                <a href="{{ route('settings.roles.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg inline-flex items-center transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Role
                </a>
                @endcan
            </div>
        @endif
    </div>
</div>

@noncescript
// SweetAlert untuk konfirmasi delete
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus role ${name}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form delete
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/settings/roles/${id}`;

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
}
@endnoncescript

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
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


