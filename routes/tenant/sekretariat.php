<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\Sekretariat\KeputusanKadesController;

use App\Http\Controllers\Tenant\Sekretariat\PeraturanDesaController;

use App\Http\Controllers\Tenant\Sekretariat\BukuAgendaController;
use App\Http\Controllers\Tenant\Sekretariat\TanahDiDesaController;
use App\Http\Controllers\Tenant\Sekretariat\KaderPemberdayaanController;
use App\Http\Controllers\Tenant\Sekretariat\BukuEkspedisiController;

Route::prefix('sekretariat')->name('sekretariat.')->group(function () {
    Route::resource('keputusan-kades', KeputusanKadesController::class)->parameters([
        'keputusan-kades' => 'keputusan_kade'
    ]);
    
    Route::resource('peraturan-desa', PeraturanDesaController::class);
    Route::resource('buku-agenda', BukuAgendaController::class);
    Route::resource('buku-ekspedisi', BukuEkspedisiController::class);
    Route::resource('tanah-di-desa', TanahDiDesaController::class);
    Route::post('tanah-di-desa/{tanah_di_desa}/mutasi', [TanahDiDesaController::class, 'storeMutasi'])->name('tanah-di-desa.mutasi');
    Route::get('kader-pemberdayaan/api/check-nik', [KaderPemberdayaanController::class, 'checkNik'])->name('kader-pemberdayaan.check-nik');
    Route::resource('kader-pemberdayaan', KaderPemberdayaanController::class);

    Route::get('anggota-bpd/api/check-nik', [\App\Http\Controllers\Tenant\Sekretariat\AnggotaBpdController::class, 'checkNik'])->name('anggota-bpd.check-nik');
    Route::resource('anggota-bpd', \App\Http\Controllers\Tenant\Sekretariat\AnggotaBpdController::class);
});
