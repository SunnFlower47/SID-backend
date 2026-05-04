<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\Keuangan\TransparansiDesaController;
use App\Http\Controllers\Tenant\Keuangan\AnggaranController;

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
