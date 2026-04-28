@extends('layouts.app')

@section('title', 'Kelola Pengguna')
@section('subtitle', 'Kelola pengguna dan role sistem')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-blue-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-xl mr-4">
                        <i class="fas fa-users-cog text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Kelola Pengguna</h2>
                        <p class="text-gray-600">Kelola pengguna dan role sistem</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="openCreateUserModal()"
                            class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Pengguna
                    </button>
                    <button onclick="openCreateRoleModal()"
                            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                        <i class="fas fa-user-tag mr-2"></i>
                        Tambah Role
                    </button>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-2xl p-6 border border-blue-200">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-600">Total Pengguna</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $totalUsers }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-2xl p-6 border border-green-200">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-user-tag text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-green-600">Total Role</p>
                            <p class="text-2xl font-bold text-green-900">{{ $totalRoles }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-2xl p-6 border border-purple-200">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-user-check text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-purple-600">Admin</p>
                            <p class="text-2xl font-bold text-purple-900">{{ $users->where('roles.0.name', 'admin')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-2xl p-6 border border-orange-200">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-user-clock text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-orange-600">Aktif Hari Ini</p>
                            <p class="text-2xl font-bold text-orange-900">{{ $users->where('last_login_at', '>=', today())->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Pengguna</h3>
                    <p class="text-sm text-gray-600">Kelola pengguna dan role sistem</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pengguna
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Terakhir Login
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-white font-bold text-sm mr-4">
                                            @if($user->profile_photo_path)
                                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-xl object-cover">
                                            @else
                                                {{ substr($user->name, 0, 1) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->created_at->format('d M Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->roles->count() > 0)
                                        @foreach($user->roles as $role)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Tidak ada role
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->email_verified_at)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Terverifikasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            Belum Verifikasi
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : 'Belum pernah login' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button onclick="editUser({{ $user->id }})"
                                                class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-colors duration-200"
                                                title="Edit Pengguna">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="assignRole({{ $user->id }})"
                                                class="text-green-600 hover:text-green-900 p-2 rounded-lg hover:bg-green-50 transition-colors duration-200"
                                                title="Assign Role">
                                            <i class="fas fa-user-tag"></i>
                                        </button>
                                        <button onclick="deleteUser({{ $user->id }})"
                                                class="text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50 transition-colors duration-200"
                                                title="Hapus Pengguna">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-users text-4xl mb-4"></i>
                                        <p class="text-lg font-medium">Tidak ada pengguna</p>
                                        <p class="text-sm">Mulai dengan menambahkan pengguna baru</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Roles Table -->
            <div class="mt-8 bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Role</h3>
                    <p class="text-sm text-gray-600">Kelola role dan permission sistem</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Permission
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pengguna
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dibuat
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($roles as $role)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-sm mr-4">
                                            <i class="fas fa-user-tag"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $role->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $role->guard_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($role->permissions->take(3) as $permission)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $permission->name }}
                                            </span>
                                        @endforeach
                                        @if($role->permissions->count() > 3)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                +{{ $role->permissions->count() - 3 }} lainnya
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $role->users->count() }} pengguna
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $role->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button onclick="editRole({{ $role->id }})"
                                                class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-colors duration-200"
                                                title="Edit Role">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="managePermissions({{ $role->id }})"
                                                class="text-green-600 hover:text-green-900 p-2 rounded-lg hover:bg-green-50 transition-colors duration-200"
                                                title="Kelola Permission">
                                            <i class="fas fa-key"></i>
                                        </button>
                                        <button onclick="deleteRole({{ $role->id }})"
                                                class="text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50 transition-colors duration-200"
                                                title="Hapus Role">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-user-tag text-4xl mb-4"></i>
                                        <p class="text-lg font-medium">Tidak ada role</p>
                                        <p class="text-sm">Mulai dengan menambahkan role baru</p>
                                    </div>
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

<!-- Modals will be added here -->
@noncescript
function openCreateUserModal() {
    // Implementation for create user modal
    Swal.fire({
        title: 'Info!',
        text: 'Create User Modal - To be implemented',
        icon: 'info',
        confirmButtonText: 'OK'
    });
}

function openCreateRoleModal() {
    // Implementation for create role modal
    Swal.fire({
        title: 'Info!',
        text: 'Create Role Modal - To be implemented',
        icon: 'info',
        confirmButtonText: 'OK'
    });
}

function editUser(userId) {
    // Implementation for edit user
    Swal.fire({
        title: 'Info!',
        text: 'Edit User ' + userId + ' - To be implemented',
        icon: 'info',
        confirmButtonText: 'OK'
    });
}

function assignRole(userId) {
    // Implementation for assign role
    Swal.fire({
        title: 'Info!',
        text: 'Assign Role to User ' + userId + ' - To be implemented',
        icon: 'info',
        confirmButtonText: 'OK'
    });
}

function deleteUser(userId) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus pengguna ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implementation for delete user
            Swal.fire({
                title: 'Info!',
                text: 'Delete User ' + userId + ' - To be implemented',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }
    });
}

function editRole(roleId) {
    // Implementation for edit role
    Swal.fire({
        title: 'Info!',
        text: 'Edit Role ' + roleId + ' - To be implemented',
        icon: 'info',
        confirmButtonText: 'OK'
    });
}

function managePermissions(roleId) {
    // Implementation for manage permissions
    Swal.fire({
        title: 'Info!',
        text: 'Manage Permissions for Role ' + roleId + ' - To be implemented',
        icon: 'info',
        confirmButtonText: 'OK'
    });
}

function deleteRole(roleId) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus role ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implementation for delete role
            Swal.fire({
                title: 'Info!',
                text: 'Delete Role ' + roleId + ' - To be implemented',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }
    });
}
@endnoncescript


@noncescript
// Session messages handled by global component
@endnoncescript
@endsection

