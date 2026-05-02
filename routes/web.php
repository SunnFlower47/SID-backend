<?php

use App\Http\Controllers\Tenant\ProfileController;
use App\Http\Controllers\Tenant\PendudukController;
use App\Http\Controllers\Tenant\MutasiController;
use App\Http\Controllers\Tenant\StatisticsController;
use App\Http\Controllers\Tenant\ImportController;
use App\Http\Controllers\Tenant\UserController;
use App\Http\Controllers\Tenant\RoleController;
use App\Http\Controllers\Tenant\SettingsController;
use App\Http\Controllers\Tenant\DesaSettingsController;
use App\Http\Controllers\Tenant\LaporanController;
use App\Http\Controllers\Tenant\AuditLogController;
use App\Http\Controllers\Tenant\BackupController;
use App\Http\Controllers\Tenant\KartuKeluargaController;
use App\Http\Controllers\Tenant\ExportController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\ComparisonController;
use App\Http\Controllers\Tenant\TrashPendudukController;
use App\Http\Controllers\Tenant\WilayahController;
use App\Http\Controllers\Tenant\SuratTypeController;
use App\Http\Controllers\Tenant\ContactMessageController;
use App\Http\Controllers\Tenant\BeritaController;
use App\Http\Controllers\Tenant\TestimoniController;
use App\Http\Controllers\Tenant\TransparansiDesaController;
use App\Http\Controllers\Tenant\AnggaranController;
use App\Http\Controllers\Tenant\BantuanSosialController;
use App\Http\Controllers\Tenant\PengaduanController;
use App\Http\Controllers\Tenant\FasilitasDesaController;
use App\Http\Controllers\Tenant\StrukturDesaController;
use App\Http\Controllers\Tenant\KontakDesaController;
use App\Http\Controllers\Tenant\UmkmController;
use App\Http\Controllers\Tenant\SuratPengajuanController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Tenant\ManifestController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

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
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Anda telah berhasil logout');
    })->name('logout');

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
        
        // Prevent Directory Traversal
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

    // MODULE 1: Kependudukan
    Route::middleware('can:kependudukan')->group(function () {
        // Penduduk routes
        Route::prefix('penduduk')->name('penduduk.')->controller(PendudukController::class)->group(function () {
            Route::get('/export/excel', 'exportExcel')->name('export.excel');
            Route::get('/check-nik', 'checkNIKExists')->name('check-nik');
            Route::get('/search', 'search')->name('search');
            Route::get('/family/{nkk}/address', 'showFamilyAddressForm')->name('family.address.form');
            Route::patch('/family/{nkk}/address', 'updateFamilyAddress')->name('family.address.update');
            Route::get('/{nkk}/family-members', 'getFamilyMembers')->name('family-members');
            Route::resource('/', PendudukController::class)->parameters(['' => 'penduduk'])->withTrashed(['show']);
        });

        // Mutasi routes
        Route::prefix('mutasi')->name('mutasi.')->controller(MutasiController::class)->group(function () {
            Route::get('/search-kk', 'searchKK')->name('search-kk');
            Route::get('/search-penduduk', 'searchPenduduk')->name('search-penduduk');
            Route::get('/get-anggota-keluarga', 'getAnggotaKeluarga')->name('get-anggota-keluarga');
            Route::get('/check-nkk', 'checkNKKExists')->name('check-nkk');
            Route::post('/undo/{mutasi}', 'undo')->name('undo');
            Route::delete('/cancel/{mutasi}', 'cancel')->name('cancel');
            Route::get('/{mutasi}/print-kematian', 'printSuratKematian')->name('print-kematian');

            Route::resource('data', MutasiController::class)
                ->parameters(['data' => 'mutasi'])
                ->except(['destroy']);
        });

        // Kartu Keluarga routes
        Route::prefix('kartu-keluarga')->name('kk.')->controller(KartuKeluargaController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/bermasalah', 'indexBermasalah')->name('bermasalah.index');
            Route::get('/bermasalah/list', 'getKkBermasalah')->name('bermasalah.list');
            Route::get('/export/excel', 'export')->name('export.excel');
            Route::get('/{nkk}', 'show')->name('show');
            Route::get('/{nkk}/edit', 'edit')->name('edit');
            Route::put('/{nkk}', 'update')->name('update');
            Route::delete('/{nkk}', 'destroy')->name('destroy');
            Route::get('/{nkk}/download-pdf', 'downloadPdf')->name('download-pdf');
            Route::post('/sync-summary', 'syncSummary')->name('sync-summary');
            Route::get('/{nkk}/bermasalah', 'showBermasalah')->name('bermasalah');
            Route::post('/{nkk}/resolve-sementara', 'resolveKkSementara')->name('resolve.sementara');
            Route::post('/{nkk}/resolve-permanen', 'resolveKkPermanen')->name('resolve.permanen');
            Route::post('/{nkk}/batalkan-sementara', 'batalkanSementara')->name('batalkan.sementara');
        });
    });

    // MODULE 2: Pelayanan Informasi
    Route::middleware('can:pelayanan_informasi')->group(function () {
        // Admin Surat Pengajuan routes
        Route::prefix('admin')->name('admin.')->controller(SuratPengajuanController::class)->group(function () {
            Route::get('/surat-pengajuan', 'index')->name('surat-pengajuan.index');
            Route::get('/surat-pengajuan/history', 'history')->name('surat-pengajuan.history');
            Route::get('/surat-pengajuan/create', 'create')->name('surat-pengajuan.create');
            Route::post('/surat-pengajuan', 'store')->name('surat-pengajuan.store');
            Route::get('/surat-pengajuan/{suratPengajuan}', 'show')->name('surat-pengajuan.show');
            Route::patch('/surat-pengajuan/{suratPengajuan}/status', 'updateStatus')->name('surat-pengajuan.update-status');
            Route::post('/surat-pengajuan/{suratPengajuan}/mark-read', 'markAsRead')->name('surat-pengajuan.mark-read');
            Route::get('/surat-pengajuan/{suratPengajuan}/preview', 'preview')->name('surat-pengajuan.preview');
            Route::get('/surat-pengajuan/{suratPengajuan}/pdf', 'generatePdf')->name('surat-pengajuan.pdf');
            Route::get('/surat-pengajuan/{suratPengajuan}/edit', 'edit')->name('surat-pengajuan.edit');
            Route::put('/surat-pengajuan/{suratPengajuan}', 'update')->name('surat-pengajuan.update');
            Route::delete('/surat-pengajuan/{id}', 'destroy')->name('surat-pengajuan.destroy');
            
            // Master Jenis Surat
            Route::resource('surat-type', SuratTypeController::class)->middleware('can:admin_sistem');
            
            // Legacy Surat Routes
            Route::get('/surat-pengajuan/legacy/{id}', 'downloadLegacy')->name('surat-pengajuan.download-legacy');
            Route::delete('/surat-pengajuan/legacy/{id}', 'destroyLegacy')->name('surat-pengajuan.destroy-legacy');
        });

        // Bantuan Sosial routes
        Route::controller(BantuanSosialController::class)->group(function () {
            Route::resource('bantuan-sosial', BantuanSosialController::class);
            Route::post('bantuan-sosial/check-nik', 'checkByNik')->name('bantuan-sosial.check-nik');
            Route::prefix('bantuan-sosial/{bantuanSosial}')->name('bantuan-sosial.penerima.')->group(function () {
                Route::get('/penerima', 'penerimaIndex')->name('index');
                Route::get('/penerima/create', 'penerimaCreate')->name('create');
                Route::post('/penerima', 'penerimaStore')->name('store');
                Route::get('/penerima/{penerima}', 'penerimaShow')->name('show');
                Route::get('/penerima/{penerima}/edit', 'penerimaEdit')->name('edit');
                Route::put('/penerima/{penerima}', 'penerimaUpdate')->name('update');
                Route::delete('/penerima/{penerima}', 'penerimaDestroy')->name('destroy');
            });
        });

        // Other Service Modules
        Route::resource('pengaduan', PengaduanController::class);
        Route::resource('fasilitas-desa', FasilitasDesaController::class);
        Route::resource('struktur-desa', StrukturDesaController::class);
        Route::resource('kontak-desa', KontakDesaController::class);
        Route::resource('umkm', UmkmController::class);
        
        // Contact Messages
        Route::prefix('contact-messages')->controller(ContactMessageController::class)->group(function () {
            Route::get('notifications', 'notifications')->name('contact-messages.notifications');
            Route::post('bulk-action', 'bulkAction')->name('contact-messages.bulk-action');
            Route::post('{contactMessage}/mark-read', 'markAsRead')->name('contact-messages.mark-read');
            Route::post('{contactMessage}/mark-replied', 'markAsReplied')->name('contact-messages.mark-replied');
            Route::post('{contactMessage}/archive', 'archive')->name('contact-messages.archive');
            Route::resource('/', ContactMessageController::class)->names('contact-messages')->parameters(['' => 'contact_message']);
        });

        // Berita & Testimoni
        Route::resource('berita', BeritaController::class);
        Route::prefix('testimoni')->name('testimoni.')->controller(TestimoniController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{testimoni}', 'show')->name('show');
            Route::get('/{testimoni}/edit', 'edit')->name('edit');
            Route::put('/{testimoni}', 'update')->name('update');
            Route::delete('/{testimoni}', 'destroy')->name('destroy');
            Route::patch('/{testimoni}/approve', 'approve')->name('approve');
            Route::patch('/{testimoni}/reject', 'reject')->name('reject');
        });
    });

    // MODULE 3: Keuangan
    Route::middleware('can:keuangan')->group(function () {
        Route::controller(TransparansiDesaController::class)->group(function () {
            Route::get('transparansi-desa', 'index')->name('transparansi-desa.index');
            Route::get('transparansi-desa/apbdes', 'apbdes')->name('transparansi-desa.apbdes');
            Route::get('transparansi-desa/proyek', 'proyek')->name('transparansi-desa.proyek');
        });

        Route::prefix('anggaran')->name('anggaran.')->controller(AnggaranController::class)->group(function () {
            Route::get('create-tahunan', 'createAnggaranTahunan')->name('create-tahunan');
            Route::post('store-tahunan', 'storeAnggaranTahunan')->name('store-tahunan');
            Route::get('create-pengeluaran', 'createPengeluaran')->name('create-pengeluaran');
            Route::post('store-pengeluaran', 'storePengeluaran')->name('store-pengeluaran');
            Route::get('histori-pengeluaran/{id}', 'historiPengeluaran')->name('histori-pengeluaran');
            Route::get('edit-pengeluaran/{id}', 'editPengeluaran')->name('edit-pengeluaran');
            Route::put('update-pengeluaran/{id}', 'updatePengeluaran')->name('update-pengeluaran');
            Route::delete('delete-pengeluaran/{id}', 'deletePengeluaran')->name('delete-pengeluaran');
            Route::get('edit-apbdes/{id}', 'editApbdes')->name('edit-apbdes');
            Route::put('update-apbdes/{id}', 'updateApbdes')->name('update-apbdes');
            Route::delete('delete-apbdes/{id}', 'deleteApbdes')->name('delete-apbdes');
            Route::get('create-proyek', 'createProyek')->name('create-proyek');
            Route::post('store-proyek', 'storeProyek')->name('store-proyek');
            Route::post('update-realisasi-proyek/{proyek}', 'updateRealisasiProyek')->name('update-realisasi-proyek');
        });
    });

    // MODULE 4: Laporan & Statistik
    Route::middleware('can:laporan_statistik')->group(function () {
        // Statistics & Comparison
        Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
        Route::post('/statistics/refresh', [StatisticsController::class, 'refreshCache'])->name('statistics.refresh');
        Route::controller(ComparisonController::class)->group(function () {
            Route::get('/comparison', 'index')->name('comparison.index');
            Route::get('/comparison/detailed', 'getDetailedComparison')->name('comparison.detailed');
        });

        // Laporan
        Route::prefix('laporan')->name('laporan.')->controller(LaporanController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/penduduk', 'penduduk')->name('penduduk');
            Route::get('/kk', 'kk')->name('kk');
            Route::get('/mutasi', 'mutasi')->name('mutasi');
            Route::get('/berita', 'berita')->name('berita');
            Route::get('/surat', 'surat')->name('surat');
            Route::post('/generate', 'generate')->name('generate');
            Route::get('/penduduk/export/excel', 'exportPendudukExcel')->name('penduduk.export.excel');
            Route::get('/mutasi/export/excel', 'exportMutasiExcel')->name('mutasi.export.excel');
        });
    });

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
    });

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
