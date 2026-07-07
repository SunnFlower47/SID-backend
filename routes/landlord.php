<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landlord\Auth\LandlordLoginController;
use App\Http\Controllers\Landlord\DashboardController;
use App\Http\Controllers\Landlord\TenantController;
use App\Http\Controllers\Landlord\TenantAllocationController;
use App\Http\Controllers\Landlord\MonitoringController;
use App\Http\Controllers\Landlord\AnnouncementController;
use App\Http\Controllers\Landlord\CentralUserController;
use App\Http\Controllers\Landlord\TenantUserController;
use App\Http\Controllers\Landlord\SettingController;

/*
|--------------------------------------------------------------------------
| Landlord Routes
|--------------------------------------------------------------------------
|
| Semua route di sini dikhususkan untuk Diskominfo / Super Admin.
| Menggunakan auth guard 'landlord'.
|
*/

// Routes yang bisa diakses tanpa login landlord (hanya halaman login)
Route::middleware('guest:landlord')->group(function () {
    Route::get('/login', [LandlordLoginController::class, 'showLoginForm'])->name('landlord.login');
    Route::post('/login', [LandlordLoginController::class, 'login'])
        ->middleware(['throttle:5,1', 'recaptcha'])
        ->name('landlord.login.post');
});

// Routes yang mewajibkan login landlord
Route::middleware('auth:landlord')->group(function () {
    Route::post('/logout', [LandlordLoginController::class, 'logout'])->name('landlord.logout');

    // Routes 2FA (tidak dibatasi oleh RequireLandlord2FA)
    Route::get('/2fa/setup', [\App\Http\Controllers\Landlord\Auth\TwoFactorController::class, 'showSetup'])->name('landlord.2fa.setup');
    Route::post('/2fa/enable', [\App\Http\Controllers\Landlord\Auth\TwoFactorController::class, 'enable'])->name('landlord.2fa.enable');
    Route::get('/2fa/verify', [\App\Http\Controllers\Landlord\Auth\TwoFactorController::class, 'showVerify'])->name('landlord.2fa.verify');
    Route::post('/2fa/verify', [\App\Http\Controllers\Landlord\Auth\TwoFactorController::class, 'verify'])->name('landlord.2fa.verify.post');
    Route::post('/2fa/disable', [\App\Http\Controllers\Landlord\Auth\TwoFactorController::class, 'disable'])->name('landlord.2fa.disable');

    // Routes yang mewajibkan verifikasi 2FA
    Route::middleware(\App\Http\Middleware\RequireLandlord2FA::class)->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('landlord.dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index']);

        Route::resource('tenants', TenantController::class);
        Route::delete('tenants/{tenant}/hard-delete', [TenantController::class, 'hardDelete'])->name('tenants.hard-delete');
        
        Route::resource('users', CentralUserController::class);
        Route::resource('tenant-users', TenantUserController::class);
        
        Route::get('allocations', [TenantAllocationController::class, 'index'])->name('landlord.allocations.index');
        Route::put('allocations/{allocation}', [TenantAllocationController::class, 'update'])->name('landlord.allocations.update');

        Route::get('monitoring', [MonitoringController::class, 'index'])->name('landlord.monitoring.index');
        Route::delete('monitoring/clear-logs', [MonitoringController::class, 'clearLogs'])->name('landlord.monitoring.clear-logs');

        Route::resource('announcements', AnnouncementController::class)->only(['index', 'store']);

        Route::get('settings', [SettingController::class, 'index'])->name('landlord.settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('landlord.settings.update');
        Route::post('settings/roles', [SettingController::class, 'storeRole'])->name('landlord.settings.roles.store');
        Route::put('settings/roles/{role}', [SettingController::class, 'updateRole'])->name('landlord.settings.roles.update');
        Route::delete('settings/roles/{role}', [SettingController::class, 'destroyRole'])->name('landlord.settings.roles.destroy');

        Route::post('settings/backup', [\App\Http\Controllers\Landlord\BackupController::class, 'create'])->name('landlord.settings.backup.create');
        Route::get('settings/backup/download/{filename}', [\App\Http\Controllers\Landlord\BackupController::class, 'download'])->name('landlord.settings.backup.download');
        Route::delete('settings/backup/{filename}', [\App\Http\Controllers\Landlord\BackupController::class, 'destroy'])->name('landlord.settings.backup.destroy');
    });
});
