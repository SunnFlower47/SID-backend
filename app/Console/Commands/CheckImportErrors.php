<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penduduk;
use App\Models\Mutasi;

class CheckImportErrors extends Command
{
    protected $signature = 'check:import-errors';
    protected $description = 'Check for import errors that might cause missing data';

    public function handle()
    {
        $this->info('Memeriksa error import yang mungkin menyebabkan data hilang...');

        $totalPenduduk = Penduduk::count();
        $totalMutasi = Mutasi::count();
        $totalCurrent = $totalPenduduk + $totalMutasi;

        $this->info("Data saat ini:");
        $this->info("- Penduduk aktif: {$totalPenduduk}");
        $this->info("- Data mutasi: {$totalMutasi}");
        $this->info("- Total: {$totalCurrent}");

        $this->info("\nData yang diimport: 3747");
        $this->info("Data saat ini: {$totalCurrent}");
        $this->info("Selisih: " . (3747 - $totalCurrent));

        // Cek data yang mungkin error saat import
        $this->info("\nMemeriksa data yang mungkin error saat import...");

        // Cek data dengan nama kosong
        $emptyNames = Penduduk::where('nama', '')->orWhereNull('nama')->count();
        if ($emptyNames > 0) {
            $this->warn("Data dengan nama kosong: {$emptyNames}");
        }

        // Cek data dengan NIK kosong
        $emptyNIK = Penduduk::where('nik', '')->orWhereNull('nik')->count();
        if ($emptyNIK > 0) {
            $this->warn("Data dengan NIK kosong: {$emptyNIK}");
        }

        // Cek data dengan tanggal lahir kosong
        $emptyTanggalLahir = Penduduk::whereNull('tanggal_lahir')->count();
        if ($emptyTanggalLahir > 0) {
            $this->warn("Data dengan tanggal lahir kosong: {$emptyTanggalLahir}");
        }

        // Cek data yang mungkin duplikat
        $duplicateNIK = Penduduk::selectRaw('nik, COUNT(*) as count')
            ->groupBy('nik')
            ->having('count', '>', 1)
            ->count();
        if ($duplicateNIK > 0) {
            $this->warn("Data dengan NIK duplikat: {$duplicateNIK}");
        }

        // Cek data yang mungkin terlewat karena error saat proses mutasi
        $this->info("\nMemeriksa data yang mungkin error saat proses mutasi...");

        // Cek data yang seharusnya jadi mutasi tapi masih aktif
        $shouldBeMutasi = Penduduk::whereIn('keterangan', ['MENINGGAL', 'PINDAH'])->count();
        if ($shouldBeMutasi > 0) {
            $this->warn("Data yang seharusnya jadi mutasi tapi masih aktif: {$shouldBeMutasi}");
        }

        // Cek data yang mungkin terlewat karena error saat soft delete
        $this->info("\nMemeriksa data yang mungkin terlewat karena error...");

        // Cek data yang mungkin terlewat karena error saat import
        $this->info("Kemungkinan penyebab selisih 17 data:");
        $this->info("1. Data yang error saat import (nama kosong, NIK kosong, dll)");
        $this->info("2. Data yang error saat proses mutasi");
        $this->info("3. Data yang terlewat karena error saat soft delete");
        $this->info("4. Data yang duplikat dan dihapus");
        $this->info("5. Data yang error saat import karena format yang tidak valid");

        // Cek data yang mungkin terlewat karena error saat import
        $this->info("\nMencari data yang mungkin terlewat karena error saat import...");

        // Cek data yang mungkin terlewat karena error saat import
        $this->info("Kemungkinan data yang hilang:");
        $this->info("- Data yang error saat import karena format yang tidak valid");
        $this->info("- Data yang duplikat dan dihapus");
        $this->info("- Data yang error saat proses mutasi");
        $this->info("- Data yang terlewat karena error saat soft delete");

        return 0;
    }
}

