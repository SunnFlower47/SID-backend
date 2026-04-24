<?php

namespace App\Observers;

use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use App\Console\Commands\SyncKartuKeluarga;
use Illuminate\Support\Facades\Artisan;

class PendudukObserver
{
    /**
     * Handle the Penduduk "created" event.
     */
    public function created(Penduduk $penduduk): void
    {
        $this->updateKartuKeluarga($penduduk->nkk);
    }

    /**
     * Handle the Penduduk "updated" event.
     */
    public function updated(Penduduk $penduduk): void
    {
        // If NKK changed, update both old and new KK
        if ($penduduk->isDirty('nkk')) {
            $this->updateKartuKeluarga($penduduk->getOriginal('nkk'));
            $this->updateKartuKeluarga($penduduk->nkk);
        } else {
            $this->updateKartuKeluarga($penduduk->nkk);
        }
    }

    /**
     * Handle the Penduduk "deleted" event.
     */
    public function deleted(Penduduk $penduduk): void
    {
        $this->updateKartuKeluarga($penduduk->nkk);
    }

    /**
     * Handle the Penduduk "restored" event.
     */
    public function restored(Penduduk $penduduk): void
    {
        $this->updateKartuKeluarga($penduduk->nkk);
    }

    /**
     * Update/Recalculate KK stats for a given NKK
     * This essentially runs a mini-sync for one KK
     */
    private function updateKartuKeluarga($nkk)
    {
        if (empty($nkk)) return;

        // Note: Using Artisan call might be too heavy for observer.
        // Better to reproduce the calculation logic or extract it to a Service/Helper.
        // For now, to ensure consistency, we'll duplicate the calculation logic here simply.
        
        $members = Penduduk::withTrashed()->where('nkk', $nkk)->get();
        
        if ($members->isEmpty()) {
            KartuKeluarga::where('nkk', $nkk)->delete();
            return;
        }

        $kepala = $members->where('deleted_at', null)->where('kedudukan_keluarga', 'Kepala Keluarga')->first() 
                 ?? $members->where('deleted_at', null)->first()
                 ?? $members->first();

        $active = 0;
        $dead = 0;
        $moved = 0;
        $split = 0;
        $mutated = 0;

        foreach ($members as $member) {
            $mutasi = \App\Models\Mutasi::where('penduduk_id', $member->id)
                ->whereIn('jenis_mutasi', ['kematian', 'pindah_keluar', 'pisah_kk'])
                ->first();

            if ($mutasi) {
                $mutated++;
                if ($mutasi->jenis_mutasi === 'kematian') $dead++;
                elseif ($mutasi->jenis_mutasi === 'pindah_keluar') $moved++;
                elseif ($mutasi->jenis_mutasi === 'pisah_kk') $split++;
            } else {
                if (!$member->deleted_at) {
                    $active++;
                }
            }
        }

        KartuKeluarga::updateOrCreate(
            ['nkk' => $nkk],
            [
                'nama_kepala_keluarga' => $kepala->nama,
                'nik_kepala_keluarga' => $kepala->nik,
                'alamat' => $kepala->alamat,
                'rt' => $kepala->rt,
                'rw' => $kepala->rw,
                'dusun' => $kepala->dusun,
                'jumlah_anggota' => $members->count(),
                'anggota_aktif' => $active,
                'anggota_mutasi' => $mutated,
                'anggota_meninggal' => $dead,
                'anggota_pindah' => $moved,
                'anggota_pisah_kk' => $split,
            ]
        );
    }
}
