<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\KartuKeluarga;
use App\Models\Penduduk;
use Illuminate\Support\Facades\DB;

class SyncKartuKeluarga extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:kartu-keluarga';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Kartu Keluarga summary table from Penduduk data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Kartu Keluarga synchronization...');

        DB::beginTransaction();
        try {
            // Truncate to start fresh
            KartuKeluarga::truncate();

            // Get all distinct NKKs directly from DB to avoid model scopes initially
            $nkks = DB::table('penduduks')
                ->whereNull('deleted_at')
                ->whereNotNull('nkk')
                ->where('nkk', '!=', '')
                ->distinct()
                ->pluck('nkk');

            $bar = $this->output->createProgressBar($nkks->count());
            $bar->start();

            foreach ($nkks as $nkk) {
                // Get members including soft deletes for checking mutations
                $members = Penduduk::withTrashed()->where('nkk', $nkk)->get();
                
                if ($members->isEmpty()) {
                    $bar->advance();
                    continue;
                }

                // Find head of family (prioritize active)
                $kepala = $members->where('deleted_at', null)->where('kedudukan_keluarga', 'Kepala Keluarga')->first() 
                         ?? $members->where('deleted_at', null)->first()
                         ?? $members->first();

                if (!$kepala) {
                    $bar->advance();
                    continue;
                }

                $active = 0;
                $dead = 0;
                $moved = 0;
                $split = 0;
                $mutated = 0;

                foreach ($members as $member) {
                    // Check mutations
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
                        // Only count as active if not soft deleted (unless pure mutation logic requires otherwise)
                        // In this system, active = no mutation AND not deleted manually
                        if (!$member->deleted_at) {
                            $active++;
                        }
                    }
                }

                try {
                    KartuKeluarga::create([
                        'nkk' => (string) $nkk,
                        'nama_kepala_keluarga' => $kepala->nama ?? 'Tidak Diketahui',
                        'nik_kepala_keluarga' => $kepala->nik ? substr((string)$kepala->nik, 0, 16) : null,
                        'alamat' => $kepala->alamat ?? '-',
                        'rt' => $kepala->rt ? substr((string)$kepala->rt, 0, 3) : '000',
                        'rw' => $kepala->rw ? substr((string)$kepala->rw, 0, 3) : '000',
                        'dusun' => $kepala->dusun ?? '-',
                        'jumlah_anggota' => $members->count(), 
                        'anggota_aktif' => $active,
                        'anggota_mutasi' => $mutated,
                        'anggota_meninggal' => $dead,
                        'anggota_pindah' => $moved,
                        'anggota_pisah_kk' => $split,
                    ]);
                } catch (\Exception $e) {
                    $this->error("Gagal sync NKK $nkk: " . $e->getMessage());
                    // Lanjut ke data berikutnya
                }

                $bar->advance();
            }

            $bar->finish();
            DB::commit();
            $this->newLine();
            $this->info('Synchronization completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Synchronization failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
