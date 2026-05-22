<?php

use App\Http\Controllers\Tenant\Aset\AsetBarangController;
use App\Http\Controllers\Tenant\Aset\AsetInventarisController;
use App\Http\Controllers\Tenant\Aset\AsetMutasiController;
use Illuminate\Support\Facades\Route;


// ─── Aset Desa ────────────────────────────────────────────────────────────────

Route::prefix('aset')->name('aset.')->group(function () {

    // Buku Inventaris
    Route::prefix('inventaris')->name('inventaris.')->controller(AsetInventarisController::class)->group(function () {
        Route::get('/',                    'index')  ->name('index');
        Route::get('/tambah',              'create') ->name('create');
        Route::post('/',                   'store')  ->name('store');
        Route::get('/{inventaris}/edit',   'edit')   ->name('edit');
        Route::put('/{inventaris}',        'update') ->name('update');
        Route::delete('/{inventaris}',     'destroy')->name('destroy');
    });

    // Mutasi Aset (tambah/kurang ke aset existing)
    Route::prefix('mutasi')->name('mutasi.')->group(function () {
        Route::get('/{inventaris}/tambah', [AsetMutasiController::class, 'create'])->name('create');
        Route::post('/{inventaris}',       [AsetMutasiController::class, 'store'])  ->name('store');
        Route::delete('/{mutasi}',         [AsetMutasiController::class, 'destroy'])->name('destroy');
    });

    // Master Kode Barang
    Route::prefix('master-barang')->name('barang.')->controller(AsetBarangController::class)->group(function () {
        Route::get('/',             'index')  ->name('index');
        Route::post('/',            'store')  ->name('store');
        Route::put('/{asetBarang}', 'update') ->name('update');
        Route::delete('/{asetBarang}', 'destroy')->name('destroy');
    });
});
