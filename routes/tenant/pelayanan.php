<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\Pelayanan\SuratPengajuanController;
use App\Http\Controllers\Tenant\Pelayanan\SuratTypeController;
use App\Http\Controllers\Tenant\Pelayanan\BantuanSosialController;
use App\Http\Controllers\Tenant\Pelayanan\PengaduanController;
use App\Http\Controllers\Tenant\Konten\FasilitasDesaController;
use App\Http\Controllers\Tenant\Konten\StrukturDesaController;
use App\Http\Controllers\Tenant\Pelayanan\KontakDesaController;
use App\Http\Controllers\Tenant\Konten\UmkmController;
use App\Http\Controllers\Tenant\Pelayanan\ContactMessageController;
use App\Http\Controllers\Tenant\Konten\BeritaController;
use App\Http\Controllers\Tenant\Konten\TestimoniController;
use App\Http\Controllers\Tenant\Konten\MasterJabatanController;

// MODULE 2: Pelayanan Informasi
Route::middleware([])->group(function () {
    // Admin Surat Pengajuan routes
    Route::prefix('admin')->name('admin.')->controller(SuratPengajuanController::class)->group(function () {
        Route::get('/surat-pengajuan', 'index')->name('surat-pengajuan.index');
        Route::get('/surat-pengajuan/history', 'history')->name('surat-pengajuan.history');
        Route::get('/surat-pengajuan/search-penduduk', 'searchPenduduk')->name('surat-pengajuan.search-penduduk');
        Route::get('/surat-pengajuan/check-domisili', \App\Http\Controllers\Tenant\Api\CheckDomisiliController::class)->name('surat-pengajuan.check-domisili');
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
        Route::resource('surat-type', SuratTypeController::class);
        Route::post('surat-type/{surat_type}', [SuratTypeController::class, 'update'])->name('surat-type.update.post');
        
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
    Route::post('master-jabatan/reorder', [MasterJabatanController::class, 'reorder'])->name('master-jabatan.reorder');
    Route::resource('master-jabatan', MasterJabatanController::class)->parameters(['master-jabatan' => 'master_jabatan']);
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
    Route::resource('berita', BeritaController::class)->parameters(['berita' => 'berita']);
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
