<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\Keuangan\PajakPbbController;

Route::prefix('pajak-pbb')->name('pajak-pbb.')->group(function () {
    Route::get('/', [PajakPbbController::class, 'index'])->name('index')->middleware('can:pajak_pbb.view');
    Route::get('/search-nop', [PajakPbbController::class, 'searchNop'])->name('search-nop')->middleware('can:pajak_pbb.view');
    Route::get('/{id}', [PajakPbbController::class, 'show'])->name('show')->middleware('can:pajak_pbb.view');
    Route::post('/{id}/sync', [PajakPbbController::class, 'sync'])->name('sync')->middleware('can:pajak_pbb.sync');
    Route::delete('/{id}', [PajakPbbController::class, 'destroy'])->name('destroy')->middleware('can:pajak_pbb.delete');
});
