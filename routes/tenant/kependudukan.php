<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\Kependudukan\PendudukController;
use App\Http\Controllers\Tenant\Kependudukan\MutasiController;
use App\Http\Controllers\Tenant\Kependudukan\KartuKeluargaController;
use App\Http\Controllers\Tenant\Kependudukan\PendudukDomisiliController;

// MODULE 1: Kependudukan
Route::middleware([])->group(function () {
    // Penduduk routes
    Route::prefix('penduduk')->name('penduduk.')->controller(PendudukController::class)->group(function () {
        Route::get('/export/excel', 'exportExcel')->name('export.excel');
        Route::get('/export-dinamis', function () {
            return inertia('Tenant/Penduduk/ExportDinamis', [
                'rtList'    => \App\Models\Rt::orderBy('kode')->get(),
                'rwList'    => \App\Models\Rw::orderBy('kode')->get(),
                'dusunList' => \App\Models\Dusun::orderBy('nama')->get(),
            ]);
        })->name('export-dinamis.index');
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

    // Penduduk Domisili routes
    Route::prefix('domisili')->name('domisili.')->controller(PendudukDomisiliController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{domisili}', 'show')->name('show');
        Route::get('/{domisili}/edit', 'edit')->name('edit');
        Route::put('/{domisili}', 'update')->name('update');
        Route::post('/{domisili}/perpanjang', 'perpanjang')->name('perpanjang');
        Route::post('/{domisili}/cabut', 'cabut')->name('cabut');
        Route::delete('/{domisili}', 'destroy')->name('destroy');
        Route::get('/check-nik', 'checkNik')->name('check-nik');
    });
});
