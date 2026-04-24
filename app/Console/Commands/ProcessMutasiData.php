<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penduduk;
use App\Models\Mutasi;
use Illuminate\Support\Facades\DB;

class ProcessMutasiData extends Command
{
    protected $signature = 'process:mutasi-data';
    protected $description = 'Process mutasi data from keterangan field';

    public function handle()
    {
        $this->info('Memproses data mutasi...');

        // Data dengan keterangan MENINGGAL
        $meninggalData = Penduduk::where('keterangan', 'MENINGGAL')->get();
        $this->info("Ditemukan {$meninggalData->count()} data dengan keterangan MENINGGAL");

        foreach ($meninggalData as $penduduk) {
            try {
                DB::beginTransaction();

                // Buat record mutasi
                Mutasi::create([
                    'penduduk_id' => $penduduk->id,
                    'jenis_mutasi' => 'kematian',
                    'kategori_mutasi' => 'dalam_kota',
                    'asal_tujuan' => 'Desa Cibatu',
                    'tanggal_mutasi' => now(),
                    'alasan' => 'Meninggal dunia',
                ]);

                // Soft delete penduduk
                $penduduk->delete();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Error memproses {$penduduk->nama}: " . $e->getMessage());
            }
        }

        // Data dengan keterangan PINDAH
        $pindahData = Penduduk::where('keterangan', 'PINDAH')->get();
        $this->info("Ditemukan {$pindahData->count()} data dengan keterangan PINDAH");

        foreach ($pindahData as $penduduk) {
            try {
                DB::beginTransaction();

                // Buat record mutasi
                Mutasi::create([
                    'penduduk_id' => $penduduk->id,
                    'jenis_mutasi' => 'pindah_keluar',
                    'kategori_mutasi' => 'luar_kota',
                    'asal_tujuan' => 'Luar Desa',
                    'tanggal_mutasi' => now(),
                    'alasan' => 'Pindah keluar desa',
                ]);

                // Soft delete penduduk
                $penduduk->delete();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Error memproses {$penduduk->nama}: " . $e->getMessage());
            }
        }

        // Data dengan keterangan lain yang menunjukkan pindah
        $otherPindahData = Penduduk::whereNotNull('keterangan')
            ->where('keterangan', '!=', '')
            ->whereNotIn('keterangan', ['MENINGGAL', 'PINDAH'])
            ->get();

        $this->info("Ditemukan {$otherPindahData->count()} data dengan keterangan lain");

        foreach ($otherPindahData as $penduduk) {
            $keterangan = strtoupper(trim($penduduk->keterangan));

            // Tentukan jenis mutasi berdasarkan keterangan
            $jenisMutasi = null;
            $kategoriMutasi = 'luar_kota';
            $asalTujuan = 'Tidak Diketahui';

            if (strpos($keterangan, 'PISAH KK') !== false) {
                $jenisMutasi = 'pindah_keluar';
                $asalTujuan = 'Pisah KK';
            } elseif (strpos($keterangan, 'DI LUAR') !== false ||
                     strpos($keterangan, 'LUAR KOTA') !== false) {
                $jenisMutasi = 'pindah_keluar';
                $asalTujuan = 'Luar Kota';
            } elseif (strpos($keterangan, 'DI BANDUNG') !== false) {
                $jenisMutasi = 'pindah_keluar';
                $asalTujuan = 'Bandung';
            } elseif (strpos($keterangan, 'DI BOGOR') !== false) {
                $jenisMutasi = 'pindah_keluar';
                $asalTujuan = 'Bogor';
            } elseif (strpos($keterangan, 'DI TANGERANG') !== false) {
                $jenisMutasi = 'pindah_keluar';
                $asalTujuan = 'Tangerang';
            } elseif (strpos($keterangan, 'DI KARAWANG') !== false) {
                $jenisMutasi = 'pindah_keluar';
                $asalTujuan = 'Karawang';
            } elseif (strpos($keterangan, 'DI SUMEDANG') !== false) {
                $jenisMutasi = 'pindah_keluar';
                $asalTujuan = 'Sumedang';
            } elseif (strpos($keterangan, 'DI BANTEN') !== false) {
                $jenisMutasi = 'pindah_keluar';
                $asalTujuan = 'Banten';
            } elseif (strpos($keterangan, 'DI CIKAMPEK') !== false) {
                $jenisMutasi = 'pindah_keluar';
                $asalTujuan = 'Cikampek';
            } elseif (strpos($keterangan, 'DI PURWAKARTA') !== false) {
                $jenisMutasi = 'pindah_keluar';
                $kategoriMutasi = 'dalam_kota';
                $asalTujuan = 'Purwakarta';
            } elseif (strpos($keterangan, 'DI PABUARAN') !== false) {
                $jenisMutasi = 'pindah_keluar';
                $kategoriMutasi = 'dalam_kota';
                $asalTujuan = 'Pabuaran';
            } elseif (strpos($keterangan, 'CERAI MATI') !== false) {
                $jenisMutasi = 'kematian';
                $kategoriMutasi = 'dalam_kota';
                $asalTujuan = 'Desa Cibatu';
            } elseif (strpos($keterangan, 'ODGJ') !== false) {
                $jenisMutasi = 'pindah_keluar';
                $asalTujuan = 'Panti/Pusat Rehabilitasi';
            } elseif (strpos($keterangan, 'TIDAK ADA DI DATA BARU') !== false) {
                $jenisMutasi = 'pindah_keluar';
                $asalTujuan = 'Tidak Diketahui';
            } elseif (strpos($keterangan, 'SUDAH MENIKAH BELUM PISAH KK') !== false ||
                     strpos($keterangan, 'BELUM PISAH KK') !== false) {
                // Data ini tetap aktif, tidak perlu mutasi
                continue;
            }

            if ($jenisMutasi) {
                try {
                    DB::beginTransaction();

                    // Buat record mutasi
                    Mutasi::create([
                        'penduduk_id' => $penduduk->id,
                        'jenis_mutasi' => $jenisMutasi,
                        'kategori_mutasi' => $kategoriMutasi,
                        'asal_tujuan' => $asalTujuan,
                        'tanggal_mutasi' => now(),
                        'alasan' => $keterangan,
                    ]);

                    // Soft delete penduduk
                    $penduduk->delete();

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("Error memproses {$penduduk->nama}: " . $e->getMessage());
                }
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

        return 0;
    }
}

