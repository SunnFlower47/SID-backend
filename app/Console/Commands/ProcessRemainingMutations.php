<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penduduk;
use App\Models\Mutasi;
use Illuminate\Support\Facades\DB;

class ProcessRemainingMutations extends Command
{
    protected $signature = 'process:remaining-mutations';
    protected $description = 'Process remaining mutations that were missed';

    public function handle()
    {
        $this->info('Memproses mutasi yang terlewat...');

        // Data dengan keterangan yang mengandung PINDAH tapi tidak tepat "PINDAH"
        $pindahData = Penduduk::where('keterangan', 'like', '%PINDAH%')
            ->where('keterangan', '!=', 'PINDAH')
            ->get();

        $this->info("Ditemukan {$pindahData->count()} data dengan keterangan PINDAH yang terlewat");

        foreach ($pindahData as $penduduk) {
            $keterangan = strtoupper(trim($penduduk->keterangan));

            try {
                DB::beginTransaction();

                // Buat record mutasi
                Mutasi::create([
                    'penduduk_id' => $penduduk->id,
                    'jenis_mutasi' => 'pindah_keluar',
                    'kategori_mutasi' => $this->getKategoriMutasi($keterangan),
                    'asal_tujuan' => $this->getAsalTujuan($keterangan),
                    'tanggal_mutasi' => now(),
                    'alasan' => $keterangan,
                ]);

                // Soft delete penduduk
                $penduduk->delete();

                DB::commit();
                $this->line("✓ Diproses: {$penduduk->nama} - {$keterangan}");

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("✗ Error memproses {$penduduk->nama}: " . $e->getMessage());
            }
        }

        // Data dengan keterangan CERAI PINDAH
        $ceraiPindahData = Penduduk::where('keterangan', 'like', '%CERAI%')
            ->where('keterangan', 'like', '%PINDAH%')
            ->get();

        $this->info("Ditemukan {$ceraiPindahData->count()} data dengan keterangan CERAI PINDAH");

        foreach ($ceraiPindahData as $penduduk) {
            $keterangan = strtoupper(trim($penduduk->keterangan));

            try {
                DB::beginTransaction();

                // Buat record mutasi
                Mutasi::create([
                    'penduduk_id' => $penduduk->id,
                    'jenis_mutasi' => 'pindah_keluar',
                    'kategori_mutasi' => $this->getKategoriMutasi($keterangan),
                    'asal_tujuan' => $this->getAsalTujuan($keterangan),
                    'tanggal_mutasi' => now(),
                    'alasan' => $keterangan,
                ]);

                // Soft delete penduduk
                $penduduk->delete();

                DB::commit();
                $this->line("✓ Diproses: {$penduduk->nama} - {$keterangan}");

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("✗ Error memproses {$penduduk->nama}: " . $e->getMessage());
            }
        }

        // Tampilkan hasil
        $totalPenduduk = Penduduk::count();
        $totalMutasi = Mutasi::count();
        $totalData = $totalPenduduk + $totalMutasi;

        $this->info("\nHasil pemrosesan:");
        $this->info("Total penduduk aktif: {$totalPenduduk}");
        $this->info("Total data mutasi: {$totalMutasi}");
        $this->info("Total data: {$totalData}");

        $selisih = 3747 - $totalData;
        if ($selisih == 0) {
            $this->info("✅ Data sudah sama dengan Excel asli!");
        } else {
            $this->warn("Masih ada selisih: {$selisih} data");
        }

        return 0;
    }

    private function getKategoriMutasi($keterangan)
    {
        if (strpos($keterangan, 'JAKARTA') !== false) {
            return 'luar_kota';
        } elseif (strpos($keterangan, 'JAWA') !== false) {
            return 'luar_kota';
        } else {
            return 'luar_kota';
        }
    }

    private function getAsalTujuan($keterangan)
    {
        if (strpos($keterangan, 'CAMPAKA') !== false) {
            return 'Campaka';
        } elseif (strpos($keterangan, 'CIMAUNG') !== false) {
            return 'Cimaung';
        } elseif (strpos($keterangan, 'CIPINANG') !== false) {
            return 'Cipinang';
        } elseif (strpos($keterangan, 'JAKARTA') !== false) {
            return 'Jakarta';
        } elseif (strpos($keterangan, 'CIMAHI') !== false) {
            return 'Cimahi';
        } elseif (strpos($keterangan, 'JAWA') !== false) {
            return 'Jawa';
        } else {
            return 'Tidak Diketahui';
        }
    }
}

