@extends('layouts.app')

@section('title', 'Edit Profile')
@section('subtitle', 'Ubah informasi profil Anda')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Card -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white mb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                <div class="flex items-center mb-4 sm:mb-0">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mr-4">
                        <i class="fas fa-user-edit text-2xl text-yellow-300"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold">Edit Profile</h1>
                        <p class="text-blue-100 text-sm sm:text-base mt-1">Ubah informasi profil dan keamanan akun Anda</p>
                    </div>
                </div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white font-medium rounded-xl transition-all duration-300 backdrop-blur-sm">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Profile Information Card -->
        <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-user text-blue-600 mr-2"></i>
                    Informasi Profil
                </h3>
                <p class="text-sm text-gray-600 mt-1">Kelola informasi dasar akun Anda</p>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('name') border-red-500 @enderror"
                                   placeholder="Masukkan nama lengkap"
                                   required>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('email') border-red-500 @enderror"
                                   placeholder="contoh@email.com"
                                   required>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                        <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('dashboard') }}" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-6 py-3 rounded-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password Card -->
        <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 px-6 py-4 border-b border-yellow-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-key text-yellow-600 mr-2"></i>
                    Ganti Password
                </h3>
                <p class="text-sm text-gray-600 mt-1">Ubah password untuk keamanan akun Anda</p>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-6" onsubmit="return confirmPasswordChange()">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">
                                Password Lama *
                            </label>
                            <div class="relative">
                                <input type="password"
                                       id="current_password"
                                       name="current_password"
                                       class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-200 @error('current_password') border-red-500 @enderror"
                                       placeholder="Masukkan password lama"
                                       required>
                                <i class="fas fa-lock absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                            @error('current_password')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                Password Baru *
                            </label>
                            <div class="relative">
                                <input type="password"
                                       id="password"
                                       name="password"
                                       class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-200 @error('password') border-red-500 @enderror"
                                       placeholder="Masukkan password baru"
                                       required>
                                <i class="fas fa-key absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                Konfirmasi Password Baru *
                            </label>
                            <div class="relative">
                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-200 @error('password_confirmation') border-red-500 @enderror"
                                       placeholder="Ulangi password baru"
                                       required>
                                <i class="fas fa-check-circle absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                            @error('password_confirmation')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="flex items-start">
                            <div class="w-full bg-gradient-to-r from-yellow-50 to-yellow-100 p-4 rounded-xl border border-yellow-200">
                                <div class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                                    Kriteria Password
                                </div>
                                <ul class="text-xs text-gray-600 space-y-1">
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-green-500 mr-2"></i>
                                        Minimal 8 karakter
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-green-500 mr-2"></i>
                                        Mengandung huruf dan angka
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-green-500 mr-2"></i>
                                        Tidak sama dengan password lama
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                        <button type="submit" class="bg-gradient-to-r from-yellow-600 to-yellow-700 hover:from-yellow-700 hover:to-yellow-800 text-white px-6 py-3 rounded-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="fas fa-key mr-2"></i>
                            Ganti Password
                        </button>
                        <button type="button" onclick="resetPasswordForm()" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-6 py-3 rounded-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="fas fa-undo mr-2"></i>
                            Reset Form
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@noncescript
function resetPasswordForm() {
    Swal.fire({
        title: 'Reset Form Password',
        text: 'Apakah Anda yakin ingin mengosongkan semua field password?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#6b7280',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Reset',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('current_password').value = '';
            document.getElementById('password').value = '';
            document.getElementById('password_confirmation').value = '';

            Swal.fire({
                icon: 'success',
                title: 'Form Direset!',
                text: 'Semua field password telah dikosongkan.',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    });
}

function confirmPasswordChange() {
    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;

    if (!currentPassword || !newPassword || !confirmPassword) {
        Swal.fire({
            icon: 'error',
            title: 'Form Tidak Lengkap!',
            text: 'Silakan isi semua field password terlebih dahulu.',
            confirmButtonColor: '#ef4444'
        });
        return false;
    }

    if (newPassword !== confirmPassword) {
        Swal.fire({
            icon: 'error',
            title: 'Password Tidak Cocok!',
            text: 'Password baru dan konfirmasi password tidak sama.',
            confirmButtonColor: '#ef4444'
        });
        return false;
    }

    if (newPassword.length < 8) {
        Swal.fire({
            icon: 'error',
            title: 'Password Terlalu Pendek!',
            text: 'Password minimal harus 8 karakter.',
            confirmButtonColor: '#ef4444'
        });
        return false;
    }

    Swal.fire({
        title: 'Konfirmasi Ganti Password',
        html: `
            <div class="text-left">
                <p class="mb-3">Apakah Anda yakin ingin mengganti password?</p>
                <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                    <p class="text-sm text-yellow-800 font-medium mb-2">⚠️ Peringatan:</p>
                    <ul class="text-xs text-yellow-700 space-y-1">
                        <li>• Pastikan Anda mengingat password baru</li>
                        <li>• Anda akan logout otomatis setelah ganti password</li>
                        <li>• Simpan password di tempat yang aman</li>
                    </ul>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Ganti Password',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'swal-wide'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Mengganti Password...',
                text: 'Mohon tunggu sebentar',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Submit form
            document.querySelector('form[action="{{ route('profile.password.update') }}"]').submit();
        }
    });
    return false; // Prevent default form submission
}
@endnoncescript

@if (session('status') == 'profile-updated')
@noncescript
    Swal.fire({
        icon: 'success',
        title: 'Profil Berhasil Diperbarui!',
        text: 'Informasi profil Anda telah berhasil disimpan.',
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        customClass: {
            popup: 'swal-toast'
        }
    });
@endnoncescript
@endif

@if (session('status') == 'password-updated')
@noncescript
    Swal.fire({
        icon: 'success',
        title: 'Password Berhasil Diperbarui!',
        html: `
            <div class="text-center">
                <p class="mb-3">Password Anda telah berhasil diganti.</p>
                <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                    <p class="text-sm text-green-800">🔐 Anda akan logout otomatis untuk keamanan.</p>
                </div>
            </div>
        `,
        timer: 4000,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#10b981'
    });
@endnoncescript
@endif

@if ($errors->any())
@noncescript
    Swal.fire({
        icon: 'error',
        title: 'Terjadi Kesalahan!',
        html: `
            <div class="text-left">
                <p class="mb-3">Terdapat kesalahan saat memperbarui data:</p>
                <ul class="text-sm text-red-600 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        `,
        confirmButtonText: 'OK',
        confirmButtonColor: '#ef4444',
        customClass: {
            popup: 'swal-wide'
        }
    });
@endnoncescript
@endif

<style>
.swal-wide {
    width: 500px !important;
}

.swal-toast {
    width: 350px !important;
}
</style>
@endsection
