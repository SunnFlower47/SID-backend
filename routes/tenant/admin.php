<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\Admin\ImportController;
use App\Http\Controllers\Tenant\Admin\SettingsController;
use App\Http\Controllers\Tenant\Kependudukan\TrashPendudukController;
use App\Http\Controllers\Tenant\Kependudukan\WilayahController;
use App\Http\Controllers\Tenant\Admin\ExportController;
use App\Http\Controllers\Tenant\Admin\AuditLogController;
use App\Http\Controllers\Tenant\Admin\BackupController;
use App\Http\Controllers\Tenant\Admin\DesaSettingsController;
use App\Http\Controllers\Tenant\Admin\VillageProfileController;
use Illuminate\Support\Facades\Artisan;

// MODULE 5: Admin Sistem
Route::middleware('can:admin_sistem')->group(function () {
    // Import
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import/excel', [ImportController::class, 'excel'])->name('import.excel');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/settings/users', [SettingsController::class, 'users'])->name('settings.users.index');
    Route::post('/settings/users', [SettingsController::class, 'createUser'])->name('settings.users.create');
    Route::put('/settings/users/{user}', [SettingsController::class, 'updateUser'])->name('settings.users.update');
    Route::delete('/settings/users/{user}', [SettingsController::class, 'deleteUser'])->name('settings.users.delete');
    Route::post('/settings/roles', [SettingsController::class, 'createRole'])->name('settings.roles.create');
    Route::put('/settings/roles/{role}', [SettingsController::class, 'updateRole'])->name('settings.roles.update');
    Route::delete('/settings/roles/{role}', [SettingsController::class, 'deleteRole'])->name('settings.roles.delete');
    Route::post('/settings/clear-cache', function() {
        Artisan::call('cache:clear');
        return response()->json(['success' => true, 'message' => 'Cache berhasil dibersihkan!']);
    })->name('settings.clear-cache');

    // Trash
    Route::prefix('settings/trash')->name('settings.trash.')->controller(TrashPendudukController::class)->group(function () {
        Route::get('/penduduk', 'index')->name('penduduk.index');
        Route::post('/penduduk/{id}/restore', 'restore')->name('penduduk.restore');
        Route::delete('/penduduk/{id}/force-delete', 'forceDelete')->name('penduduk.force-delete');
    });

    // Wilayah
    Route::prefix('settings/wilayah')->name('settings.wilayah.')->controller(WilayahController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/rt/{rt}/penduduk', 'detailRtPenduduk')->name('rt.penduduk');
        Route::get('/import-conflicts', 'importConflicts')->name('import-conflicts.index');
        Route::post('/dusun', 'storeDusun')->name('dusun.store');
        Route::put('/dusun/{dusun}', 'updateDusun')->name('dusun.update');
        Route::post('/dusun/{dusun}/preview-impact', 'previewImpactDusun')->name('dusun.preview-impact');
        Route::post('/rw', 'storeRw')->name('rw.store');
        Route::put('/rw/{rw}', 'updateRw')->name('rw.update');
        Route::post('/rw/{rw}/preview-impact', 'previewImpactRw')->name('rw.preview-impact');
        Route::post('/rt', 'storeRt')->name('rt.store');
        Route::put('/rt/{rt}', 'updateRt')->name('rt.update');
        Route::delete('/rt/{rt}', 'destroyRt')->name('rt.destroy');
        Route::match(['post','put'], '/rt/{rt}/preview-impact', 'previewImpactRt')->name('rt.preview-impact');
        Route::post('/rt/{rt}/apply-update', 'applyRtUpdate')->name('rt.apply-update');
        Route::post('/change-log/{log}/rollback', 'rollbackWilayahChange')->name('change-log.rollback');
        Route::post('/import-conflicts/{conflict}/resolve', 'resolveImportConflict')->name('import-conflicts.resolve');
        Route::post('/import-conflicts/{conflict}/reset', 'resetImportConflict')->name('import-conflicts.reset');
        Route::post('/import-conflicts/{conflict}/reprocess', 'reprocessImportIssue')->name('import-conflicts.reprocess');
    });

    // Export/Import/Backup
    Route::prefix('export-import')->name('export-import.')->controller(ImportController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/template/{type}', 'downloadTemplate')->name('template');
    });

    Route::prefix('export')->name('export.')->controller(ExportController::class)->group(function () {
        Route::get('/penduduk', 'exportPenduduk')->name('penduduk');
        Route::get('/kartu-keluarga', 'exportKartuKeluarga')->name('kartu-keluarga');
        Route::get('/bantuan-sosial', 'exportBantuanSosial')->name('bantuan-sosial');
        Route::get('/penerima-bantuan-sosial', 'exportPenerimaBantuanSosial')->name('penerima-bantuan-sosial');
        Route::get('/pengaduan', 'exportPengaduan')->name('pengaduan');
        Route::get('/umkm', 'exportUmkm')->name('umkm');
        Route::get('/surat-pengajuan', 'exportSuratPengajuan')->name('surat-pengajuan');
    });

    Route::prefix('import')->name('import.')->controller(ImportController::class)->group(function () {
        Route::post('/penduduk/preview', 'previewPenduduk')->name('penduduk.preview');
        Route::post('/penduduk/preview-invalid-report', 'downloadPendudukInvalidReport')->name('penduduk.preview-invalid-report');
        Route::post('/penduduk', 'importPenduduk')->name('penduduk');
        Route::post('/bantuan-sosial', 'importBantuanSosial')->name('bantuan-sosial');
        Route::post('/umkm', 'importUmkm')->name('umkm');
    });

    Route::prefix('audit-log')->name('audit-log.')->controller(AuditLogController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{activity}', 'show')->name('show');
        Route::get('/export/excel', 'export')->name('export.excel');
        Route::get('/statistics', 'statistics')->name('statistics');
    });

    Route::prefix('backup')->name('backup.')->controller(BackupController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/create', 'create')->name('create');
        Route::get('/download/{filename}', 'download')->name('download');
        Route::post('/restore', 'restore')->name('restore');
        Route::delete('/{filename}', 'delete')->name('delete');
        Route::get('/export/data', 'exportData')->name('export.data');
        Route::get('/statistics', 'statistics')->name('statistics');
    });

    Route::prefix('settings')->name('settings.')->controller(DesaSettingsController::class)->group(function () {
        Route::get('/desa', 'index')->name('desa');
        Route::put('/desa', 'update')->name('desa.update');
        Route::put('/desa/{key}', 'updateSetting')->name('desa.update-setting');
        Route::post('/desa/reset', 'reset')->name('desa.reset');
        Route::get('/desa/export', 'export')->name('desa.export');
        Route::post('/desa/import', 'import')->name('desa.import');
    });

    // Profil Desa Terpusat
    Route::prefix('profil-desa')->name('profil-desa.')->controller(VillageProfileController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/update', 'update')->name('update');
        Route::post('/update-logos', 'updateLogos')->name('update-logos');
    });
});
