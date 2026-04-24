<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\KartuKeluarga;
use App\Models\Penduduk;

class SyncKartuKeluargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Syncing Kartu Keluarga data...');

        // Truncate existing data to start fresh
        KartuKeluarga::truncate();

        // Get distinct NKKs
        $nkks = Penduduk::distinct()->pluck('nkk')->filter();

        $count = 0;
        $total = $nkks->count();

        foreach ($nkks as $nkk) {
            // Get family members
            $members = Penduduk::where('nkk', $nkk)->get();
            $kepala = $members->where('kedudukan_keluarga', 'Kepala Keluarga')->first() ?? $members->first();

            if (!$kepala) continue;

            $kkData = new KartuKeluarga();
            $kkData->nkk = $nkk;
            $kkData->nama_kepala_keluarga = $kepala->nama;
            $kkData->nik_kepala_keluarga = $kepala->nik;
            $kkData->alamat = $kepala->alamat;
            $kkData->rt = $kepala->rt;
            $kkData->rw = $kepala->rw;
            $kkData->dusun = $kepala->dusun;
            
            $kkData->jumlah_anggota = $members->count();
            
            // Calculate stats
            $active = 0;
            $dead = 0;
            $moved = 0;
            $split = 0;
            $mutated = 0;

            foreach ($members as $member) {
                // Check if member has active mutations
                $mutasi = DB::table('mutasis')
                    ->where('penduduk_id', $member->id)
                    ->whereIn('jenis_mutasi', ['kematian', 'pindah_keluar', 'pisah_kk'])
                    ->first();

                if ($mutasi) {
                    $mutated++;
                    if ($mutasi->jenis_mutasi === 'kematian') $dead++;
                    elseif ($mutasi->jenis_mutasi === 'pindah_keluar') $moved++;
                    elseif ($mutasi->jenis_mutasi === 'pisah_kk') $split++;
                } else {
                    $active++;
                }
            }

            $kkData->anggota_aktif = $active;
            $kkData->anggota_mutasi = $mutated;
            $kkData->anggota_meninggal = $dead;
            $kkData->anggota_pindah = $moved;
            $kkData->anggota_pisah_kk = $split;

            $kkData->save();
            
            $count++;
            if ($count % 50 === 0) {
                $this->command->info("Processed {$count}/{$total} KK");
            }
        }

        $this->command->info('Sync Completed!');
    }
}
