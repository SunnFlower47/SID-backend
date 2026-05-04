<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\Laporan\StatisticsController;
use App\Http\Controllers\Tenant\Laporan\ComparisonController;
use App\Http\Controllers\Tenant\Laporan\LaporanController;

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
