@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('subtitle', 'Kelola pengguna, role, dan konfigurasi sistem')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Card -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white mb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                <div class="flex items-center mb-4 sm:mb-0">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mr-4">
                        <i class="fas fa-cog text-2xl text-yellow-300"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold">Pengaturan Sistem</h1>
                        <p class="text-red-100 text-sm sm:text-base mt-1">Kelola pengguna, role, dan konfigurasi sistem</p>
                    </div>
                </div>
            </div>
        </div>


        <!-- Tabs -->
        <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 sm:px-6 py-4 border-b border-gray-200">
                <nav class="flex flex-wrap gap-2 sm:gap-4 lg:gap-8" aria-label="Tabs">
                    <button onclick="showTab('users')" id="users-tab" class="tab-button active py-2 sm:py-3 px-3 sm:px-4 border-b-2 border-blue-500 font-medium text-xs sm:text-sm text-blue-600 bg-blue-50 rounded-t-lg transition-all duration-300 flex items-center">
                        <i class="fas fa-users mr-1 sm:mr-2"></i>
                        <span class="hidden sm:inline">Pengguna</span>
                        <span class="sm:hidden">User</span>
                    </button>
                    <button onclick="showTab('roles')" id="roles-tab" class="tab-button py-2 sm:py-3 px-3 sm:px-4 border-b-2 border-transparent font-medium text-xs sm:text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50 rounded-t-lg transition-all duration-300 flex items-center">
                        <i class="fas fa-user-shield mr-1 sm:mr-2"></i>
                        <span class="hidden sm:inline">Role & Permission</span>
                        <span class="sm:hidden">Role</span>
                    </button>
                    <button onclick="showTab('system')" id="system-tab" class="tab-button py-2 sm:py-3 px-3 sm:px-4 border-b-2 border-transparent font-medium text-xs sm:text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50 rounded-t-lg transition-all duration-300 flex items-center">
                        <i class="fas fa-cog mr-1 sm:mr-2"></i>
                        <span class="hidden sm:inline">Sistem</span>
                        <span class="sm:hidden">Sys</span>
                    </button>
                    <button onclick="showTab('surat')" id="surat-tab" class="tab-button py-2 sm:py-3 px-3 sm:px-4 border-b-2 border-transparent font-medium text-xs sm:text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50 rounded-t-lg transition-all duration-300 flex items-center">
                        <i class="fas fa-file-signature mr-1 sm:mr-2"></i>
                        <span class="hidden sm:inline">Setting Surat Desa</span>
                        <span class="sm:hidden">Surat</span>
                    </button>
                </nav>
            </div>

            <!-- Users Tab -->
            <div id="users-content" class="tab-content p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 sm:mb-6 space-y-3 sm:space-y-0">
                    <div>
                        <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Kelola Pengguna</h3>
                        <p class="text-gray-600 text-xs sm:text-sm mt-1">Kelola akun pengguna dan hak akses</p>
                    </div>
                    <button onclick="openUserModal()" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl text-sm sm:text-base">
                        <i class="fas fa-plus mr-1 sm:mr-2"></i>
                        <span class="hidden sm:inline">Tambah Pengguna</span>
                        <span class="sm:hidden">Tambah</span>
                    </button>
                </div>



                <!-- Desktop Table -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dibuat</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 sm:h-12 sm:w-12">
                                            <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-gradient-to-r from-blue-100 to-blue-200 flex items-center justify-center">
                                                <i class="fas fa-user text-blue-600 text-sm sm:text-lg"></i>
                                            </div>
                                        </div>
                                        <div class="ml-3 sm:ml-4">
                                            <div class="text-xs sm:text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">ID: {{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                    <div class="text-xs sm:text-sm text-gray-900">{{ $user->email }}</div>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1">
                                        @if($user->roles->count() > 0)
                                            @foreach($user->roles as $role)
                                            <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $role->name }}
                                            </span>
                                            @endforeach
                                        @else
                                            <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                No Role
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                                    <div class="flex space-x-1 sm:space-x-2">
                                        <button onclick="editUser({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', [{{ $user->roles->pluck('id')->filter()->implode(',') }}])" class="text-blue-600 hover:text-blue-900 p-1 sm:p-2 rounded-lg hover:bg-blue-50 transition-colors">
                                            <i class="fas fa-edit text-xs sm:text-sm"></i>
                                        </button>
                                        @if($user->id !== auth()->id())
                                        <button onclick="deleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')" class="text-red-600 hover:text-red-900 p-1 sm:p-2 rounded-lg hover:bg-red-50 transition-colors">
                                            <i class="fas fa-trash text-xs sm:text-sm"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="lg:hidden space-y-3 sm:space-y-4">
                    @foreach($users as $user)
                    <div class="bg-white border border-gray-200 rounded-xl p-3 sm:p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center flex-1 min-w-0">
                                <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-gradient-to-r from-blue-100 to-blue-200 flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas fa-user text-blue-600 text-sm sm:text-lg"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $user->name }}</h4>
                                    <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="flex space-x-1 ml-2">
                                <button onclick="editUser({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', [{{ $user->roles->pluck('id')->filter()->implode(',') }}])" class="text-blue-600 hover:text-blue-900 p-1 sm:p-2 rounded-lg hover:bg-blue-50 transition-colors">
                                    <i class="fas fa-edit text-xs sm:text-sm"></i>
                                </button>
                                @if($user->id !== auth()->id())
                                <button onclick="deleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')" class="text-red-600 hover:text-red-900 p-1 sm:p-2 rounded-lg hover:bg-red-50 transition-colors">
                                    <i class="fas fa-trash text-xs sm:text-sm"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex flex-wrap gap-1">
                                @if($user->roles->count() > 0)
                                    @foreach($user->roles as $role)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $role->name }}
                                    </span>
                                    @endforeach
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        No Role
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">Dibuat: {{ $user->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Roles Tab -->
            <div id="roles-content" class="tab-content p-4 sm:p-6 hidden">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 sm:mb-6 space-y-3 sm:space-y-0">
                    <div>
                        <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Kelola Role & Permission</h3>
                        <p class="text-gray-600 text-xs sm:text-sm mt-1">Kelola role dan hak akses pengguna</p>
                    </div>
                    <button onclick="openRoleModal()" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl text-sm sm:text-base">
                        <i class="fas fa-plus mr-1 sm:mr-2"></i>
                        <span class="hidden sm:inline">Tambah Role</span>
                        <span class="sm:hidden">Tambah</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Roles List -->
                    <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-6">
                        <h4 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center">
                            <i class="fas fa-user-shield text-green-600 mr-2 text-sm sm:text-base"></i>
                            Daftar Role
                        </h4>
                        <div class="space-y-3">
                            @foreach($roles as $role)
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-4 rounded-xl border border-gray-200 hover:shadow-md transition-shadow duration-200">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h5 class="font-semibold text-gray-900 text-sm">{{ $role->name }}</h5>
                                        <p class="text-xs text-gray-500 mt-1">{{ $role->permissions->count() }} permissions</p>
                                        <p class="text-xs text-gray-500">{{ $role->users->count() }} pengguna</p>
                                    </div>
                                    <div class="flex space-x-2 ml-3">
                                        <button onclick="editRole({{ $role->id }}, '{{ addslashes($role->name) }}', [{{ $role->permissions->pluck('id')->implode(',') }}])" class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if($role->users->count() === 0)
                                        <button onclick="deleteRole({{ $role->id }}, '{{ addslashes($role->name) }}')" class="text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Permissions List -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-key text-purple-600 mr-2"></i>
                            Daftar Permission
                        </h4>
                        <div class="space-y-4 max-h-96 overflow-y-auto">
                            @foreach($permissions as $group => $perms)
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-4 rounded-xl border border-gray-200">
                                <h5 class="font-semibold text-gray-900 text-sm mb-3 flex items-center">
                                    <i class="fas fa-folder text-purple-500 mr-2"></i>
                                    {{ ucfirst($group) }}
                                </h5>
                                <div class="space-y-1">
                                    @foreach($perms as $permission)
                                    <div class="text-xs text-gray-600 bg-white px-2 py-1 rounded border">{{ $permission->name }}</div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Tab -->
            <div id="system-content" class="tab-content p-4 sm:p-6 hidden">
                <div class="mb-4 sm:mb-6">
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Pengaturan Sistem</h3>
                    <p class="text-gray-600 text-xs sm:text-sm mt-1">Informasi sistem dan aksi administrasi</p>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Database Info -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 sm:p-6 rounded-xl border border-blue-200">
                        <h4 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center">
                            <i class="fas fa-database text-blue-600 mr-2 text-sm sm:text-base"></i>
                            Informasi Database
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center bg-white p-3 rounded-lg">
                                <span class="text-sm text-gray-600 flex items-center">
                                    <i class="fas fa-users text-blue-500 mr-2"></i>
                                    Total Penduduk:
                                </span>
                                <span class="text-lg font-bold text-blue-600">{{ number_format($stats['totalPenduduk']) }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-white p-3 rounded-lg">
                                <span class="text-sm text-gray-600 flex items-center">
                                    <i class="fas fa-home text-green-500 mr-2"></i>
                                    Total KK:
                                </span>
                                <span class="text-lg font-bold text-green-600">{{ number_format($stats['totalKK']) }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-white p-3 rounded-lg">
                                <span class="text-sm text-gray-600 flex items-center">
                                    <i class="fas fa-exchange-alt text-purple-500 mr-2"></i>
                                    Total Mutasi:
                                </span>
                                <span class="text-lg font-bold text-purple-600">{{ number_format($stats['totalMutasi']) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- System Actions -->
                    <div class="bg-gradient-to-r from-orange-50 to-orange-100 p-6 rounded-xl border border-orange-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-tools text-orange-600 mr-2"></i>
                            Aksi Sistem
                        </h4>
                        <div class="space-y-3">
                            <a href="{{ route('penduduk.export.excel') }}" class="block w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-3 rounded-xl text-center transition-all duration-300 shadow-lg hover:shadow-xl">
                                <i class="fas fa-download mr-2"></i>
                                Export Data Excel
                            </a>
                            <button onclick="clearCache()" class="block w-full bg-gradient-to-r from-yellow-600 to-yellow-700 hover:from-yellow-700 hover:to-yellow-800 text-white px-4 py-3 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl">
                                <i class="fas fa-broom mr-2"></i>
                                Clear Cache
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Setting Surat Desa Tab -->
            <div id="surat-content" class="tab-content p-4 sm:p-6 hidden">
                <div class="mb-4 sm:mb-6">
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Setting Surat Desa</h3>
                    <p class="text-gray-600 text-xs sm:text-sm mt-1">Kelola template surat dan data desa</p>
                </div>

                <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-blue-800">Informasi Penting</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>• Data kepala desa dan sekretaris dikelola di menu <strong>Struktur Desa</strong></p>
                                <p>• Menu ini khusus untuk pengaturan template surat dan data umum desa</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Informasi Umum Desa -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-xl border border-blue-200 hover:shadow-lg transition-shadow duration-300">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-building text-blue-600 mr-3"></i>
                            Informasi Umum Desa
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Kelola data umum desa untuk template surat</p>
                        <a href="{{ route('settings.desa') }}" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="fas fa-edit mr-2"></i>
                            Kelola Data Desa
                        </a>
                    </div>

                    <!-- Struktur Desa -->
                    <div class="bg-gradient-to-r from-green-50 to-green-100 p-6 rounded-xl border border-green-200 hover:shadow-lg transition-shadow duration-300">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-sitemap text-green-600 mr-3"></i>
                            Data Pejabat Desa
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Kelola data kepala desa, sekretaris, dan pejabat lainnya</p>
                        <a href="{{ route('struktur-desa.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="fas fa-users mr-2"></i>
                            Kelola Struktur Desa
                        </a>
                    </div>

                    <!-- Template Surat -->
                    <div class="bg-gradient-to-r from-purple-50 to-purple-100 p-6 rounded-xl border border-purple-200 hover:shadow-lg transition-shadow duration-300">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-file-signature text-purple-600 mr-3"></i>
                            Template Surat
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Kelola format dan template surat</p>
                        <a href="{{ route('settings.desa') }}#template" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="fas fa-cog mr-2"></i>
                            Kelola Template
                        </a>
                    </div>

                    <!-- Logo dan Branding -->
                    <div class="bg-gradient-to-r from-orange-50 to-orange-100 p-6 rounded-xl border border-orange-200 hover:shadow-lg transition-shadow duration-300">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-image text-orange-600 mr-3"></i>
                            Logo dan Branding
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Kelola logo desa dan kabupaten</p>
                        <a href="{{ route('settings.desa') }}#logo" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="fas fa-upload mr-2"></i>
                            Kelola Logo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>

<!-- User Modal -->
<div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white" id="userModalTitle">Tambah Pengguna</h3>
                    <button onclick="closeUserModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">

                <form id="userForm">
                    @csrf
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama</label>
                            <input type="text" name="name" id="userName" class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" id="userEmail" class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                            <input type="password" name="password" id="userPassword" class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            <p class="mt-2 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah password</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="userPasswordConfirmation" class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                            <select name="role" id="userRole" class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                <option value="">Pilih Role</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-8">
                        <button type="button" onclick="closeUserModal()" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl transition-all duration-200 font-medium">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Role Modal -->
<div id="roleModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 w-full max-w-lg">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white" id="roleModalTitle">Tambah Role</h3>
                    <button onclick="closeRoleModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <form id="roleForm">
                    @csrf
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Role</label>
                            <input type="text" name="name" id="roleName" class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Permissions</label>
                            <div class="mt-2 space-y-3 max-h-60 overflow-y-auto border border-gray-200 rounded-xl p-4">
                                @foreach($permissions as $group => $perms)
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <h5 class="font-semibold text-gray-900 text-sm mb-3 flex items-center">
                                        <i class="fas fa-folder text-green-500 mr-2"></i>
                                        {{ ucfirst($group) }}
                                    </h5>
                                    <div class="space-y-2">
                                        @foreach($perms as $permission)
                                        <label class="flex items-center p-2 hover:bg-white rounded-lg transition-colors">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                            <span class="ml-3 text-sm text-gray-700">{{ $permission->name }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-8">
                        <button type="button" onclick="closeRoleModal()" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl transition-all duration-200 font-medium">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script nonce="{{ $csp_nonce }}">
// Tab functionality
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600', 'bg-blue-50');
        button.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab content
    document.getElementById(tabName + '-content').classList.remove('hidden');

    // Add active class to selected tab
    const activeTab = document.getElementById(tabName + '-tab');
    activeTab.classList.add('active', 'border-blue-500', 'text-blue-600', 'bg-blue-50');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
}

// User modal functions
let currentUserId = null;

function openUserModal(userId = null) {
    console.log('Opening user modal with ID:', userId); // Debug log
    currentUserId = userId;
    const modal = document.getElementById('userModal');
    const title = document.getElementById('userModalTitle');
    const form = document.getElementById('userForm');

    if (userId) {
        title.textContent = 'Edit Pengguna';
        console.log('Setting currentUserId to:', userId); // Debug log
        // Load user data for editing
        // This would need to be implemented with an API call
    } else {
        title.textContent = 'Tambah Pengguna';
        form.reset();
    }

    modal.classList.remove('hidden');
}

function closeUserModal() {
    document.getElementById('userModal').classList.add('hidden');
    currentUserId = null;
}

function editUser(id, name, email, roleIds) {
    console.log('Edit user called with:', { id, name, email, roleIds }); // Debug log

    document.getElementById('userName').value = name;
    document.getElementById('userEmail').value = email;

    // Clear previous selection
    document.getElementById('userRole').value = '';

    // Select current role (take first role if multiple)
    if (roleIds && roleIds.length > 0) {
        console.log('Setting role to:', roleIds[0]); // Debug log
        document.getElementById('userRole').value = roleIds[0];
    }

    openUserModal(id);
}

function deleteUser(id, name) {
    Swal.fire({
        title: 'Konfirmasi',
        text: `Apakah Anda yakin ingin menghapus pengguna "${name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/settings/users/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Pengguna berhasil dihapus!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}

// Role modal functions
let currentRoleId = null;

function openRoleModal(roleId = null) {
    currentRoleId = roleId;
    const modal = document.getElementById('roleModal');
    const title = document.getElementById('roleModalTitle');
    const form = document.getElementById('roleForm');

    if (roleId) {
        title.textContent = 'Edit Role';
        form.action = `/settings/roles/${roleId}`;
        form.method = 'PUT';
    } else {
        title.textContent = 'Tambah Role';
        form.action = '/settings/roles';
        form.method = 'POST';
        form.reset();
    }

    modal.classList.remove('hidden');
}

function closeRoleModal() {
    document.getElementById('roleModal').classList.add('hidden');
    currentRoleId = null;
}

function editRole(id, name, permissionIds) {
    document.getElementById('roleName').value = name;

    // Clear previous selections
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });

    // Select current permissions
    permissionIds.forEach(permissionId => {
        const checkbox = document.querySelector(`input[name="permissions[]"][value="${permissionId}"]`);
        if (checkbox) checkbox.checked = true;
    });

    openRoleModal(id);
}

function deleteRole(id, name) {
    Swal.fire({
        title: 'Konfirmasi',
        text: `Apakah Anda yakin ingin menghapus role "${name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/settings/roles/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Role berhasil dihapus!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}

// Form submissions
document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const url = currentUserId ? `/settings/users/${currentUserId}` : '/settings/users';
    const method = currentUserId ? 'PUT' : 'POST';

    // Prepare request options
    const requestOptions = {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    };

    // Only add body for POST/PUT requests
    if (method !== 'GET') {
        const data = Object.fromEntries(formData);
        console.log('User form data:', data); // Debug log
        console.log('Role selected:', data.role); // Debug role
        requestOptions.body = JSON.stringify(data);
    }

    fetch(url, requestOptions)
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);

        // Check if response is ok
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return response.json();
    })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'User berhasil disimpan!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menyimpan user: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
});

document.getElementById('roleForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const url = currentRoleId ? `/settings/roles/${currentRoleId}` : '/settings/roles';
    const method = currentRoleId ? 'PUT' : 'POST';

    // Prepare request options
    const requestOptions = {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    };

    // Only add body for POST/PUT requests
    if (method !== 'GET') {
        const data = Object.fromEntries(formData);

        // Handle permissions array properly
        const permissions = formData.getAll('permissions[]');
        if (permissions.length > 0) {
            data.permissions = permissions;
        }

        requestOptions.body = JSON.stringify(data);
    }

    fetch(url, requestOptions)
    .then(response => {
        console.log('Role response status:', response.status);

        // Check if response is ok
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return response.json();
    })
    .then(data => {
        console.log('Role response data:', data);
        if (data.success) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Role berhasil disimpan!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'Gagal!',
                text: data.message || 'Terjadi kesalahan',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Role Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Terjadi kesalahan saat menyimpan role: ' + error.message,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
});

function clearCache() {
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin membersihkan cache?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Bersihkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/settings/clear-cache', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Cache berhasil dibersihkan!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Gagal membersihkan cache!',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}

// CSP-safe fallback: convert inline onclick handlers into JS listeners
// so buttons tetap berfungsi walau inline handler diblokir browser policy.
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[onclick]').forEach((el) => {
        const handlerCode = el.getAttribute('onclick');
        if (!handlerCode) return;

        // Prevent duplicate binding
        if (el.dataset.onclickBound === '1') return;
        el.dataset.onclickBound = '1';

        el.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            try {
                // Execute existing inline expression in global scope
                (new Function(handlerCode)).call(window);
            } catch (err) {
                console.error('Failed executing onclick handler:', handlerCode, err);
            }
        });
    });
});
</script>

<style>
.tab-button.active {
    border-bottom-color: #3B82F6;
    color: #3B82F6;
    background-color: #EFF6FF;
}

/* Custom scrollbar for permissions */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endsection

