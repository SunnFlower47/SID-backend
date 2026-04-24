<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penduduk;
use App\Models\Mutasi;

class FindMissingData extends Command
{
    protected $signature = 'find:missing-data';
    protected $description = 'Find missing data by checking import vs current data';

    public function handle()
    {
        $this->info('Mencari data yang hilang...');

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

        // Cek data yang mungkin terlewat
        $this->info("\nMencari data yang mungkin terlewat...");

        // Cek data dengan keterangan yang tidak terproses
        $unprocessedData = Penduduk::whereNotNull('keterangan')
            ->where('keterangan', '!=', '')
            ->whereNotIn('keterangan', ['MENINGGAL', 'PINDAH'])
            ->get();

        $this->info("Data dengan keterangan tidak terproses: {$unprocessedData->count()}");

        if ($unprocessedData->count() > 0) {
            $this->info("Keterangan yang tidak terproses:");
            $keterangan = Penduduk::selectRaw('keterangan, COUNT(*) as count')
                ->whereNotNull('keterangan')
                ->where('keterangan', '!=', '')
                ->whereNotIn('keterangan', ['MENINGGAL', 'PINDAH'])
                ->groupBy('keterangan')
                ->get();

            foreach ($keterangan as $ket) {
                $this->line("- '{$ket->keterangan}': {$ket->count} data");
            }
        }

        // Cek data yang mungkin error saat import
        $this->info("\nMencari data yang mungkin error...");

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

        // Cek data yang mungkin duplikat
        $duplicateNIK = Penduduk::selectRaw('nik, COUNT(*) as count')
            ->groupBy('nik')
            ->having('count', '>', 1)
            ->count();
        if ($duplicateNIK > 0) {
            $this->warn("Data dengan NIK duplikat: {$duplicateNIK}");
        }

        // Cek data yang mungkin terlewat karena error saat proses mutasi
        $this->info("\nMencari data yang mungkin error saat proses mutasi...");

        // Cek data yang seharusnya jadi mutasi tapi masih aktif
        $shouldBeMutasi = Penduduk::whereIn('keterangan', ['MENINGGAL', 'PINDAH'])->count();
        if ($shouldBeMutasi > 0) {
            $this->warn("Data yang seharusnya jadi mutasi tapi masih aktif: {$shouldBeMutasi}");
        }

        // Cek data yang mungkin terlewat karena error saat soft delete
        $this->info("\nMencari data yang mungkin terlewat karena error...");

        // Cek data yang mungkin terlewat karena error saat import
        $this->info("Kemungkinan penyebab selisih:");
        $this->info("1. Data yang error saat import (nama kosong, NIK kosong, dll)");
        $this->info("2. Data yang error saat proses mutasi");
        $this->info("3. Data yang terlewat karena error saat soft delete");
        $this->info("4. Data yang duplikat dan dihapus");

        return 0;
    }
}

