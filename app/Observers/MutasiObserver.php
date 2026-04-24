<?php

namespace App\Observers;

use App\Models\Mutasi;
use App\Models\Penduduk;
use App\Models\KartuKeluarga;

class MutasiObserver
{
    /**
     * Handle the Mutasi "created" event.
     */
    public function created(Mutasi $mutasi): void
    {
        $this->updateFromPenduduk($mutasi->penduduk_id);
    }

    /**
     * Handle the Mutasi "updated" event.
     */
    public function updated(Mutasi $mutasi): void
    {
        $this->updateFromPenduduk($mutasi->penduduk_id);
    }

    /**
     * Handle the Mutasi "deleted" event.
     */
    public function deleted(Mutasi $mutasi): void
    {
        $this->updateFromPenduduk($mutasi->penduduk_id);
    }

    private function updateFromPenduduk($pendudukId)
    {
        $penduduk = Penduduk::withTrashed()->find($pendudukId);
        if ($penduduk) {
            // Trigger update on NKK (Observer logic reuse)
            // We can manually trigger logic or refactor.
            // Since we don't have a shared service yet, let's just create a new instance of logic helper or call similar code.
            // To keep it DRY, ideally we'd put this in KartuKeluargaService. 
            // But for now let's just copy the update logic via a static helper or just recalculate.
            
            // Recalculate for this NKK
            $this->recalculateKK($penduduk->nkk);
        }
    }

    private function recalculateKK($nkk)
    {
        if (empty($nkk)) return;

        $members = Penduduk::withTrashed()->where('nkk', $nkk)->get();
        
        if ($members->isEmpty()) {
            KartuKeluarga::where('nkk', $nkk)->delete();
            return;
        }

        $kepala = $members->where('deleted_at', null)->where('kedudukan_keluarga', 'Kepala Keluarga')->first() 
                 ?? $members->where('deleted_at', null)->first()
                 ?? $members->first();

        // Same logic as PendudukObserver
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
