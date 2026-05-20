<?php

Route::get('/test-surat', [\App\Http\Controllers\TestSuratController::class, 'testCetak']);

use App\Http\Controllers\Tenant\ProfileController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\Admin\ManifestController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return \Inertia\Inertia::render('Welcome');
})->name('welcome');

Route::get('/kebijakan-privasi', function () {
    return \Inertia\Inertia::render('PrivacyPolicy');
})->name('privacy-policy');

Route::get('/ketentuan-layanan', function () {
    return \Inertia\Inertia::render('TermsOfService');
})->name('terms-of-service');

Route::get('/react-test', function () {
    return inertia('Tenant/Test');
})->middleware(['auth'])->name('react-test');

// PWA Manifest Route
Route::get('/manifest.json', [ManifestController::class, 'manifest']);

// Clear session message route
Route::post('/clear-session-message', function(Request $request) {
    $type = $request->input('type');
    if (in_array($type, ['success', 'error', 'warning', 'info'])) {
        $request->session()->forget($type);
    }
    return response()->json(['status' => 'success']);
})->name('clear-session-message');

Route::middleware('auth')->group(function () {
    // Logout route for all users
    Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Dashboard 
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->middleware(['verified'])->name('dashboard');
        Route::post('/dashboard/refresh', 'refresh')->name('dashboard.refresh');
    });

    // Cache management routes
    Route::post('/clear-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        return redirect()->back()->with('success', 'Cache berhasil dibersihkan!');
    })->name('clear-cache');

    Route::post('/clear-optimization-cache', function () {
        Artisan::call('cache:optimization');
        return redirect()->back()->with('success', 'Optimization cache berhasil dibersihkan!');
    })->name('clear-optimization-cache');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // File download route untuk shared hosting
    Route::get('/file/download/{path}', function ($path) {
        $decodedPath = base64_decode($path);
        if (str_contains($decodedPath, '..') || str_starts_with($decodedPath, '/') || str_starts_with($decodedPath, '\\')) {
            abort(403, 'Unauthorized access');
        }
        return \App\Helpers\StorageHelper::downloadFile($decodedPath);
    })->name('file.download');

    // Notification routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::get('/counts', [NotificationController::class, 'counts'])->name('counts');
    });

    // MODULES
    require __DIR__.'/tenant/kependudukan.php';
    require __DIR__.'/tenant/pelayanan.php';
    require __DIR__.'/tenant/keuangan.php';
    require __DIR__.'/tenant/laporan.php';
    require __DIR__.'/tenant/admin.php';

    // Web Desa Settings (redirect)
    Route::prefix('web-desa')->name('web-desa.')->group(function () {
        Route::get('settings', function() {
            return redirect()->route('settings.desa');
        })->name('settings');
    });
});

require __DIR__.'/auth.php';

Route::get('/saas-test', function () {
    return \Inertia\Inertia::render('Tenant/Test');
});
