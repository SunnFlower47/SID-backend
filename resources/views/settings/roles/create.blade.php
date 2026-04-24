@extends('layouts.app')

@section('title', 'Tambah Role')
@section('subtitle', 'Buat role baru dengan permission')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Tambah Role</h1>
            <p class="text-gray-600 mt-1">Buat role baru dengan permission yang sesuai</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('settings.roles.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg flex items-center transition-colors shadow-md">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('settings.roles.store') }}" class="space-y-6">
            @csrf

            <!-- Data Role -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Role</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Role *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                               placeholder="Contoh: Admin, Viewer, Operator"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="guard_name" class="block text-sm font-medium text-gray-700 mb-2">Guard Name *</label>
                        <select name="guard_name" id="guard_name"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('guard_name') border-red-500 @enderror"
                                required>
                            <option value="">Pilih Guard</option>
                            <option value="web" {{ old('guard_name', 'web') == 'web' ? 'selected' : '' }}>Web</option>
                            <option value="api" {{ old('guard_name') == 'api' ? 'selected' : '' }}>API</option>
                        </select>
                        @error('guard_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Permissions -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Permissions</h3>
                <div class="space-y-4">
                    @php
                        $permissionGroups = [
                            'Penduduk' => ['penduduk.view', 'penduduk.create', 'penduduk.edit', 'penduduk.delete', 'penduduk.export', 'penduduk.import'],
                            'Mutasi' => ['mutasi.view', 'mutasi.create', 'mutasi.edit', 'mutasi.delete'],
                            'Statistik' => ['statistics.view'],
                            'Settings' => ['settings.users.manage', 'settings.roles.manage']
                        ];
                    @endphp

                    @foreach($permissionGroups as $groupName => $permissions)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="text-md font-semibold text-gray-800 mb-3">{{ $groupName }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($permissions as $permission)
                                @php
                                    $permissionModel = \Spatie\Permission\Models\Permission::where('name', $permission)->first();
                                @endphp
                                @if($permissionModel)
                                <label class="flex items-center">
                                    <input type="checkbox" name="permissions[]" value="{{ $permissionModel->id }}"
                                           {{ in_array($permissionModel->id, old('permissions', [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">{{ $permissionModel->name }}</span>
                                </label>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @error('permissions')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center justify-center transition-colors shadow-md">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Role
                </button>
                <a href="{{ route('settings.roles.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg flex items-center justify-center transition-colors shadow-md">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
            </div>
        </form>
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

