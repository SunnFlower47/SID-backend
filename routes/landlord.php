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
});
