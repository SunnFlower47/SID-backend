<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PendudukController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DesaSettingsController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\ManifestController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

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

// Auth routes are handled in routes/auth.php

// Logout route for all users
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/')->with('success', 'Anda telah berhasil logout');
})->name('logout');


Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::post('/dashboard/refresh', [App\Http\Controllers\DashboardController::class, 'refresh'])->middleware(['auth'])->name('dashboard.refresh');

// Cache management routes
Route::post('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return redirect()->back()->with('success', 'Cache berhasil dibersihkan!');
})->middleware(['auth'])->name('clear-cache');

Route::post('/clear-optimization-cache', function () {
    Artisan::call('cache:optimization');
    return redirect()->back()->with('success', 'Optimization cache berhasil dibersihkan!');
})->middleware(['auth'])->name('clear-optimization-cache');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // File download route untuk shared hosting
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

    // Penduduk routes
    Route::prefix('penduduk')->name('penduduk.')->group(function () {
        Route::get('/export/excel', [PendudukController::class, 'exportExcel'])->name('export.excel');
        Route::get('/check-nik', [PendudukController::class, 'checkNIKExists'])->name('check-nik');
        Route::get('/search', [PendudukController::class, 'search'])->name('search');
        Route::get('/family/{nkk}/address', [PendudukController::class, 'showFamilyAddressForm'])->name('family.address.form');
        Route::patch('/family/{nkk}/address', [PendudukController::class, 'updateFamilyAddress'])->name('family.address.update');
        Route::post('/{nkk}/update-kepala-keluarga', [PendudukController::class, 'updateKepalaKeluarga'])->name('update-kepala-keluarga');
        Route::get('/{nkk}/family-members', [PendudukController::class, 'getFamilyMembers'])->name('family-members');
        Route::resource('/', PendudukController::class)->parameters(['' => 'penduduk'])->withTrashed(['show']);
    });


    // Mutasi routes
    Route::prefix('mutasi')->name('mutasi.')->group(function () {
        Route::get('/search-kk', [MutasiController::class, 'searchKK'])->name('search-kk');
        Route::get('/search-penduduk', [MutasiController::class, 'searchPenduduk'])->name('search-penduduk');
        Route::get('/get-anggota-keluarga', [MutasiController::class, 'getAnggotaKeluarga'])->name('get-anggota-keluarga');
        Route::get('/check-nkk', [MutasiController::class, 'checkNKKExists'])->name('check-nkk');
        Route::post('/undo/{mutasi}', [MutasiController::class, 'undo'])->name('undo');
        Route::delete('/cancel/{mutasi}', [MutasiController::class, 'cancel'])->name('cancel');
        Route::get('/{mutasi}/print-kematian', [MutasiController::class, 'printSuratKematian'])->name('print-kematian');

        // resource pakai path 'data' biar nggak ambigu
        Route::resource('data', MutasiController::class)
            ->parameters(['data' => 'mutasi'])
            ->except(['destroy']);
    });



    // Statistics routes
    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
    Route::post('/statistics/refresh', [StatisticsController::class, 'refreshCache'])->name('statistics.refresh');
    Route::get('/statistics/test', function() {
        return view('statistics.test', [
            'totalPenduduk' => \App\Models\Penduduk::count(),
            'totalKK' => \App\Models\Penduduk::distinct('nkk')->count(),
            'totalMutasi' => \App\Models\Mutasi::count(),
        ]);
    })->name('statistics.test');

    // Comparison routes
    Route::get('/comparison', [App\Http\Controllers\ComparisonController::class, 'index'])->name('comparison.index');
    Route::get('/comparison/detailed', [App\Http\Controllers\ComparisonController::class, 'getDetailedComparison'])->name('comparison.detailed');

    // Import routes
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import/excel', [ImportController::class, 'excel'])->name('import.excel');

    // Settings routes - All in one page
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
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        return response()->json(['success' => true, 'message' => 'Cache berhasil dibersihkan!']);
    })->name('settings.clear-cache');

    // Master wilayah (Dusun/RW/RT)
    Route::prefix('settings/wilayah')->name('settings.wilayah.')->group(function () {
        Route::middleware('can:wilayah.view')->group(function () {
            Route::get('/', [App\Http\Controllers\WilayahController::class, 'index'])->name('index');
            Route::get('/rt/{rt}/penduduk', [App\Http\Controllers\WilayahController::class, 'detailRtPenduduk'])->name('rt.penduduk');
            Route::get('/import-conflicts', [App\Http\Controllers\WilayahController::class, 'importConflicts'])->name('import-conflicts.index');
        });

        Route::middleware('can:wilayah.manage')->group(function () {
            Route::post('/dusun', [App\Http\Controllers\WilayahController::class, 'storeDusun'])->name('dusun.store');
            Route::put('/dusun/{dusun}', [App\Http\Controllers\WilayahController::class, 'updateDusun'])->name('dusun.update');
            Route::post('/dusun/{dusun}/preview-impact', [App\Http\Controllers\WilayahController::class, 'previewImpactDusun'])->name('dusun.preview-impact');

            Route::post('/rw', [App\Http\Controllers\WilayahController::class, 'storeRw'])->name('rw.store');
            Route::put('/rw/{rw}', [App\Http\Controllers\WilayahController::class, 'updateRw'])->name('rw.update');
            Route::post('/rw/{rw}/preview-impact', [App\Http\Controllers\WilayahController::class, 'previewImpactRw'])->name('rw.preview-impact');

            Route::post('/rt', [App\Http\Controllers\WilayahController::class, 'storeRt'])->name('rt.store');
            Route::put('/rt/{rt}', [App\Http\Controllers\WilayahController::class, 'updateRt'])->name('rt.update');
            Route::delete('/rt/{rt}', [App\Http\Controllers\WilayahController::class, 'destroyRt'])->name('rt.destroy');
            Route::match(['post','put'], '/rt/{rt}/preview-impact', [App\Http\Controllers\WilayahController::class, 'previewImpactRt'])->name('rt.preview-impact');
            Route::post('/rt/{rt}/apply-update', [App\Http\Controllers\WilayahController::class, 'applyRtUpdate'])->name('rt.apply-update');
            Route::post('/change-log/{log}/rollback', [App\Http\Controllers\WilayahController::class, 'rollbackWilayahChange'])->name('change-log.rollback');
        });

        Route::middleware('can:wilayah.import_conflict.manage')->group(function () {
            Route::post('/import-conflicts/{conflict}/resolve', [App\Http\Controllers\WilayahController::class, 'resolveImportConflict'])->name('import-conflicts.resolve');
            Route::post('/import-conflicts/{conflict}/reprocess', [App\Http\Controllers\WilayahController::class, 'reprocessImportIssue'])->name('import-conflicts.reprocess');
        });
    });

    // Admin Surat Pengajuan routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/surat-pengajuan', [App\Http\Controllers\SuratPengajuanController::class, 'index'])->name('surat-pengajuan.index');
        Route::get('/surat-pengajuan/create', [App\Http\Controllers\SuratPengajuanController::class, 'create'])->name('surat-pengajuan.create');
        Route::post('/surat-pengajuan', [App\Http\Controllers\SuratPengajuanController::class, 'store'])->name('surat-pengajuan.store');
        Route::get('/surat-pengajuan/{suratPengajuan}', [App\Http\Controllers\SuratPengajuanController::class, 'show'])->name('surat-pengajuan.show');
        Route::patch('/surat-pengajuan/{suratPengajuan}/status', [App\Http\Controllers\SuratPengajuanController::class, 'updateStatus'])->name('surat-pengajuan.update-status');
        Route::post('/surat-pengajuan/{suratPengajuan}/mark-read', [App\Http\Controllers\SuratPengajuanController::class, 'markAsRead'])->name('surat-pengajuan.mark-read');
        Route::get('/surat-pengajuan/{suratPengajuan}/preview', [App\Http\Controllers\SuratPengajuanController::class, 'preview'])->name('surat-pengajuan.preview');
        Route::get('/surat-pengajuan/{suratPengajuan}/pdf', [App\Http\Controllers\SuratPengajuanController::class, 'generatePdf'])->name('surat-pengajuan.pdf');
        Route::get('/surat-pengajuan/{suratPengajuan}/edit', [App\Http\Controllers\SuratPengajuanController::class, 'edit'])->name('surat-pengajuan.edit');
        Route::put('/surat-pengajuan/{suratPengajuan}', [App\Http\Controllers\SuratPengajuanController::class, 'update'])->name('surat-pengajuan.update');
    });

    // Kartu Keluarga routes
    Route::get('/kartu-keluarga/export/excel', [App\Http\Controllers\KartuKeluargaController::class, 'export'])->name('kartu-keluarga.export.excel');
    Route::get('/kartu-keluarga/{nkk}/download-pdf', [App\Http\Controllers\KartuKeluargaController::class, 'downloadPdf'])->name('kartu-keluarga.download-pdf');
    Route::post('/kartu-keluarga/sync-summary', [App\Http\Controllers\KartuKeluargaController::class, 'syncSummary'])->name('kartu-keluarga.sync-summary');
    Route::post('/kartu-keluarga/batch-update-kepala-keluarga', [App\Http\Controllers\KartuKeluargaController::class, 'batchUpdateKepalaKeluarga'])->name('kartu-keluarga.batch-update-kepala-keluarga');
    Route::get('/kartu-keluarga/bermasalah/list', [App\Http\Controllers\KartuKeluargaController::class, 'getKkBermasalah'])->name('kartu-keluarga.bermasalah.list');
    Route::post('/kartu-keluarga/{nkk}/update-kepala-keluarga', [App\Http\Controllers\KartuKeluargaController::class, 'updateKepalaKeluarga'])->name('kartu-keluarga.update-kepala-keluarga');
    Route::post('/kartu-keluarga/{nkk}/auto-update-kepala-keluarga', [App\Http\Controllers\KartuKeluargaController::class, 'autoUpdateKepalaKeluarga'])->name('kartu-keluarga.auto-update-kepala-keluarga');

    // FASE 6: KK Bermasalah — resolution routes (harus sebelum resource agar tidak ter-capture)
    Route::get('/kartu-keluarga/bermasalah',             [App\Http\Controllers\KartuKeluargaController::class, 'indexBermasalah'])->name('kk.bermasalah.index');
    Route::get('/kartu-keluarga/{nkk}/bermasalah',       [App\Http\Controllers\KartuKeluargaController::class, 'showBermasalah'])->name('kk.bermasalah');
    Route::post('/kartu-keluarga/{nkk}/resolve-sementara',  [App\Http\Controllers\KartuKeluargaController::class, 'resolveKkSementara'])->name('kk.resolve.sementara');
    Route::post('/kartu-keluarga/{nkk}/resolve-permanen',   [App\Http\Controllers\KartuKeluargaController::class, 'resolveKkPermanen'])->name('kk.resolve.permanen');
    Route::post('/kartu-keluarga/{nkk}/batalkan-sementara', [App\Http\Controllers\KartuKeluargaController::class, 'batalkanSementara'])->name('kk.batalkan.sementara');

    Route::resource('kartu-keluarga', App\Http\Controllers\KartuKeluargaController::class)->parameters(['kartu-keluarga' => 'nkk']);


// Web Desa routes
Route::prefix('web-desa')->name('web-desa.')->group(function () {
    Route::get('settings', function() {
        return redirect()->route('settings.desa');
    })->name('settings');
    Route::put('settings', [App\Http\Controllers\DesaSettingsController::class, 'update'])->name('settings.update');
});

// Bantuan Sosial routes
Route::middleware(['auth'])->group(function () {
    Route::resource('bantuan-sosial', App\Http\Controllers\BantuanSosialController::class);
    Route::post('bantuan-sosial/check-nik', [App\Http\Controllers\BantuanSosialController::class, 'checkByNik'])->name('bantuan-sosial.check-nik');

    // Penerima Bantuan Sosial routes
    Route::prefix('bantuan-sosial/{bantuanSosial}')->name('bantuan-sosial.penerima.')->group(function () {
        Route::get('/penerima', [App\Http\Controllers\BantuanSosialController::class, 'penerimaIndex'])->name('index');
        Route::get('/penerima/create', [App\Http\Controllers\BantuanSosialController::class, 'penerimaCreate'])->name('create');
        Route::post('/penerima', [App\Http\Controllers\BantuanSosialController::class, 'penerimaStore'])->name('store');
        Route::get('/penerima/{penerima}', [App\Http\Controllers\BantuanSosialController::class, 'penerimaShow'])->name('show');
        Route::get('/penerima/{penerima}/edit', [App\Http\Controllers\BantuanSosialController::class, 'penerimaEdit'])->name('edit');
        Route::put('/penerima/{penerima}', [App\Http\Controllers\BantuanSosialController::class, 'penerimaUpdate'])->name('update');
        Route::delete('/penerima/{penerima}', [App\Http\Controllers\BantuanSosialController::class, 'penerimaDestroy'])->name('destroy');
    });
});

// Pengaduan routes
Route::middleware(['auth'])->group(function () {
    Route::resource('pengaduan', App\Http\Controllers\PengaduanController::class);
});

// Fasilitas Desa routes
Route::middleware(['auth'])->group(function () {
    Route::resource('fasilitas-desa', App\Http\Controllers\FasilitasDesaController::class);
});

// Struktur Desa routes
Route::middleware(['auth'])->group(function () {
    Route::resource('struktur-desa', App\Http\Controllers\StrukturDesaController::class);
});

    // Kontak Desa routes
    Route::resource('kontak-desa', App\Http\Controllers\KontakDesaController::class);

    // Contact Messages routes
    // IMPORTANT: define static endpoints before resource route to avoid being captured by {contact_message}
    Route::get('contact-messages/notifications', [App\Http\Controllers\Admin\ContactMessageController::class, 'notifications'])->name('contact-messages.notifications');
    Route::post('contact-messages/bulk-action', [App\Http\Controllers\Admin\ContactMessageController::class, 'bulkAction'])->name('contact-messages.bulk-action');
    Route::post('contact-messages/{contactMessage}/mark-read', [App\Http\Controllers\Admin\ContactMessageController::class, 'markAsRead'])->name('contact-messages.mark-read');
    Route::post('contact-messages/{contactMessage}/mark-replied', [App\Http\Controllers\Admin\ContactMessageController::class, 'markAsReplied'])->name('contact-messages.mark-replied');
    Route::post('contact-messages/{contactMessage}/archive', [App\Http\Controllers\Admin\ContactMessageController::class, 'archive'])->name('contact-messages.archive');
    Route::resource('contact-messages', App\Http\Controllers\Admin\ContactMessageController::class);

    // UMKM routes
    Route::resource('umkm', App\Http\Controllers\UmkmController::class);

    // Berita routes
    Route::get('/berita', [App\Http\Controllers\BeritaController::class, 'index'])->name('berita.index');
    Route::get('/berita/create', [App\Http\Controllers\BeritaController::class, 'create'])->name('berita.create');
    Route::post('/berita', [App\Http\Controllers\BeritaController::class, 'store'])->name('berita.store');
    Route::get('/berita/{berita}', [App\Http\Controllers\BeritaController::class, 'show'])->name('berita.show');
    Route::get('/berita/{berita}/edit', [App\Http\Controllers\BeritaController::class, 'edit'])->name('berita.edit');
    Route::put('/berita/{berita}', [App\Http\Controllers\BeritaController::class, 'update'])->name('berita.update');
    Route::delete('/berita/{berita}', [App\Http\Controllers\BeritaController::class, 'destroy'])->name('berita.destroy');

    // Testimoni routes
    Route::prefix('testimoni')->name('testimoni.')->group(function () {
        Route::get('/', [App\Http\Controllers\TestimoniController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\TestimoniController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\TestimoniController::class, 'store'])->name('store');
        Route::get('/{testimoni}', [App\Http\Controllers\TestimoniController::class, 'show'])->name('show');
        Route::get('/{testimoni}/edit', [App\Http\Controllers\TestimoniController::class, 'edit'])->name('edit');
        Route::put('/{testimoni}', [App\Http\Controllers\TestimoniController::class, 'update'])->name('update');
        Route::delete('/{testimoni}', [App\Http\Controllers\TestimoniController::class, 'destroy'])->name('destroy');
        Route::patch('/{testimoni}/approve', [App\Http\Controllers\TestimoniController::class, 'approve'])->name('approve');
        Route::patch('/{testimoni}/reject', [App\Http\Controllers\TestimoniController::class, 'reject'])->name('reject');
    });

    // Export/Import routes
    Route::prefix('export-import')->name('export-import.')->group(function () {
        Route::get('/', [App\Http\Controllers\ExportImportController::class, 'index'])->name('index');
        Route::get('/template/{type}', [App\Http\Controllers\ExportImportController::class, 'downloadTemplate'])->name('template');
    });

    // Export routes
    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/penduduk', [App\Http\Controllers\ExportImportController::class, 'exportPenduduk'])->name('penduduk');
        Route::get('/kartu-keluarga', [App\Http\Controllers\ExportImportController::class, 'exportKartuKeluarga'])->name('kartu-keluarga');
        Route::get('/bantuan-sosial', [App\Http\Controllers\ExportImportController::class, 'exportBantuanSosial'])->name('bantuan-sosial');
        Route::get('/penerima-bantuan-sosial', [App\Http\Controllers\ExportImportController::class, 'exportPenerimaBantuanSosial'])->name('penerima-bantuan-sosial');
        Route::get('/pengaduan', [App\Http\Controllers\ExportImportController::class, 'exportPengaduan'])->name('pengaduan');
        Route::get('/umkm', [App\Http\Controllers\ExportImportController::class, 'exportUmkm'])->name('umkm');
        Route::get('/surat-pengajuan', [App\Http\Controllers\ExportImportController::class, 'exportSuratPengajuan'])->name('surat-pengajuan');
    });

    // Import routes
    Route::prefix('import')->name('import.')->group(function () {
        Route::post('/penduduk/preview', [App\Http\Controllers\ExportImportController::class, 'previewPenduduk'])->name('penduduk.preview');
        Route::post('/penduduk/preview-invalid-report', [App\Http\Controllers\ExportImportController::class, 'downloadPendudukInvalidReport'])->name('penduduk.preview-invalid-report');
        Route::post('/penduduk', [App\Http\Controllers\ExportImportController::class, 'importPenduduk'])->name('penduduk');
        Route::post('/bantuan-sosial', [App\Http\Controllers\ExportImportController::class, 'importBantuanSosial'])->name('bantuan-sosial');
        Route::post('/umkm', [App\Http\Controllers\ExportImportController::class, 'importUmkm'])->name('umkm');
    });

    // Transparansi Desa routes
    Route::get('transparansi-desa', [App\Http\Controllers\TransparansiDesaController::class, 'index'])->name('transparansi-desa.index');
    Route::get('transparansi-desa/apbdes', [App\Http\Controllers\TransparansiDesaController::class, 'apbdes'])->name('transparansi-desa.apbdes');
    Route::get('transparansi-desa/proyek', [App\Http\Controllers\TransparansiDesaController::class, 'proyek'])->name('transparansi-desa.proyek');

    // Anggaran Management routes
    Route::prefix('anggaran')->name('anggaran.')->group(function () {
        Route::get('create-tahunan', [App\Http\Controllers\AnggaranController::class, 'createAnggaranTahunan'])->name('create-tahunan');
        Route::post('store-tahunan', [App\Http\Controllers\AnggaranController::class, 'storeAnggaranTahunan'])->name('store-tahunan');
        Route::get('create-pengeluaran', [App\Http\Controllers\AnggaranController::class, 'createPengeluaran'])->name('create-pengeluaran');
        Route::post('store-pengeluaran', [App\Http\Controllers\AnggaranController::class, 'storePengeluaran'])->name('store-pengeluaran');
        Route::get('histori-pengeluaran/{id}', [App\Http\Controllers\AnggaranController::class, 'historiPengeluaran'])->name('histori-pengeluaran');
        Route::get('edit-pengeluaran/{id}', [App\Http\Controllers\AnggaranController::class, 'editPengeluaran'])->name('edit-pengeluaran');
        Route::put('update-pengeluaran/{id}', [App\Http\Controllers\AnggaranController::class, 'updatePengeluaran'])->name('update-pengeluaran');
        Route::delete('delete-pengeluaran/{id}', [App\Http\Controllers\AnggaranController::class, 'deletePengeluaran'])->name('delete-pengeluaran');
        Route::get('edit-apbdes/{id}', [App\Http\Controllers\AnggaranController::class, 'editApbdes'])->name('edit-apbdes');
        Route::put('update-apbdes/{id}', [App\Http\Controllers\AnggaranController::class, 'updateApbdes'])->name('update-apbdes');
        Route::delete('delete-apbdes/{id}', [App\Http\Controllers\AnggaranController::class, 'deleteApbdes'])->name('delete-apbdes');
        Route::get('create-proyek', [App\Http\Controllers\AnggaranController::class, 'createProyek'])->name('create-proyek');
        Route::post('store-proyek', [App\Http\Controllers\AnggaranController::class, 'storeProyek'])->name('store-proyek');
        Route::post('update-realisasi-proyek/{proyek}', [App\Http\Controllers\AnggaranController::class, 'updateRealisasiProyek'])->name('update-realisasi-proyek');
    });

    // Laporan routes
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/penduduk', [LaporanController::class, 'penduduk'])->name('penduduk');
        Route::get('/kk', [LaporanController::class, 'kk'])->name('kk');
        Route::get('/mutasi', [LaporanController::class, 'mutasi'])->name('mutasi');
        Route::get('/berita', [LaporanController::class, 'berita'])->name('berita');
        Route::get('/surat', [LaporanController::class, 'surat'])->name('surat');
        Route::post('/generate', [LaporanController::class, 'generate'])->name('generate');
        Route::get('/penduduk/export/excel', [LaporanController::class, 'exportPendudukExcel'])->name('penduduk.export.excel');
        Route::get('/mutasi/export/excel', [LaporanController::class, 'exportMutasiExcel'])->name('mutasi.export.excel');
    });

    // Audit Log routes
    Route::prefix('audit-log')->name('audit-log.')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::get('/{activity}', [AuditLogController::class, 'show'])->name('show');
        Route::get('/export/excel', [AuditLogController::class, 'export'])->name('export.excel');
        Route::get('/statistics', [AuditLogController::class, 'statistics'])->name('statistics');
    });

    // Backup routes
    Route::prefix('backup')->name('backup.')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::post('/create', [BackupController::class, 'create'])->name('create');
        Route::get('/download/{filename}', [BackupController::class, 'download'])->name('download');
        Route::post('/restore', [BackupController::class, 'restore'])->name('restore');
        Route::delete('/{filename}', [BackupController::class, 'delete'])->name('delete');
        Route::get('/export/data', [BackupController::class, 'exportData'])->name('export.data');
        Route::get('/statistics', [BackupController::class, 'statistics'])->name('statistics');
    });

    // Surat routes
    Route::prefix('surat')->name('surat.')->group(function () {
        Route::get('/', [SuratController::class, 'index'])->name('index');
        Route::post('/{type}/generate', [SuratController::class, 'generate'])->name('generate');
        Route::get('/{type}/preview', [SuratController::class, 'preview'])->name('preview');
        Route::post('/{type}/store', [SuratController::class, 'store'])->name('store');
        Route::get('/statistics', [SuratController::class, 'statistics'])->name('statistics');
        Route::get('/history', [SuratController::class, 'history'])->name('history');
        Route::get('/{id}/show', [SuratController::class, 'show'])->name('show');
        Route::get('/{id}/download', [SuratController::class, 'download'])->name('download');
        Route::get('/{surat}/edit', [SuratController::class, 'edit'])->name('edit');
        Route::put('/{surat}', [SuratController::class, 'update'])->name('update');
        Route::delete('/{id}', [SuratController::class, 'destroy'])->name('destroy');
    });

    // Desa Settings routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/desa', [DesaSettingsController::class, 'index'])->name('desa');
        Route::put('/desa', [DesaSettingsController::class, 'update'])->name('desa.update');
        Route::put('/desa/{key}', [DesaSettingsController::class, 'updateSetting'])->name('desa.update-setting');
        Route::post('/desa/reset', [DesaSettingsController::class, 'reset'])->name('desa.reset');
        Route::get('/desa/export', [DesaSettingsController::class, 'export'])->name('desa.export');
        Route::post('/desa/import', [DesaSettingsController::class, 'import'])->name('desa.import');
    });

});

require __DIR__.'/auth.php';
