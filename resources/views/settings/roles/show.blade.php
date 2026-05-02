@extends('layouts.app')

@section('title', 'Detail Role')
@section('subtitle', 'Informasi lengkap role dan permission')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-4 lg:space-y-0 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detail Role: {{ $role->name }}</h1>
            <p class="text-gray-600 mt-1">Informasi lengkap mengenai role dan permission</p>
        </div>
        <div class="flex flex-wrap gap-3">
            @can('admin_sistem')
            <a href="{{ route('settings.roles.edit', $role) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center transition-colors shadow-md">
                <i class="fas fa-edit mr-2"></i>
                Edit Role
            </a>
            @endcan
            @can('admin_sistem')
            @if($role->name !== 'Super Admin')
            <form method="POST" action="{{ route('settings.roles.destroy', $role) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus role ini?')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg flex items-center transition-colors shadow-md">
                    <i class="fas fa-trash mr-2"></i>
                    Hapus Role
                </button>
            </form>
            @endif
            @endcan
            <a href="{{ route('settings.roles.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg flex items-center transition-colors shadow-md">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Data Role -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Role</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-600">Nama Role</label>
                <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $role->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Guard Name</label>
                <p class="mt-1 text-sm text-gray-900">{{ $role->guard_name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Jumlah Permission</label>
                <p class="mt-1 text-sm text-gray-900">{{ $role->permissions->count() }} permission</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Jumlah User</label>
                <p class="mt-1 text-sm text-gray-900">{{ $role->users->count() }} user</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Dibuat</label>
                <p class="mt-1 text-sm text-gray-900">{{ $role->created_at->format('d F Y H:i') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Diperbarui</label>
                <p class="mt-1 text-sm text-gray-900">{{ $role->updated_at->format('d F Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Permissions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Permissions</h3>
        @if($role->permissions->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($role->permissions as $permission)
                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <span class="text-sm font-medium text-gray-900">{{ $permission->name }}</span>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-exclamation-triangle text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">Role ini belum memiliki permission</p>
            </div>
        @endif
    </div>

    <!-- Users dengan Role ini -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Users dengan Role ini</h3>
        @if($role->users->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($role->users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $user->email }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-users text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">Belum ada user yang memiliki role ini</p>
            </div>
        @endif
    </div>
</div>

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
</script>
@endsection


