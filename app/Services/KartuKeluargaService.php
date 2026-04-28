<?php

namespace App\Services;

use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use App\Models\Mutasi;

class KartuKeluargaService
{
    /**
     * Recalculate statistics for a given NKK
     * Optimizes performance by using eager loading and combined logic
     */
    public function recalculate($nkk)
    {
        if (empty($nkk)) return;

        // Eager load mutasis to avoid N+1 problem in the loop
        $members = Penduduk::withTrashed()
            ->with(['mutasis' => function($q) {
                $q->whereIn('jenis_mutasi', ['kematian', 'pindah_keluar', 'pisah_kk']);
            }])
            ->where('nkk', $nkk)
            ->get();

        if ($members->isEmpty()) {
            KartuKeluarga::where('nkk', $nkk)->delete();
            return;
        }

        // Determine Head of Family
        $kepala = $members->where('deleted_at', null)->where('kedudukan_keluarga', 'Kepala Keluarga')->first()
                 ?? $members->where('deleted_at', null)->first()
                 ?? $members->first();

        $active  = 0;
        $dead    = 0;
        $moved   = 0;
        $split   = 0;
        $mutated = 0;

        foreach ($members as $member) {
            // Check if this member has a qualifying mutation
            $mutasi = $member->mutasis->first();

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

        $existingKK = KartuKeluarga::withTrashed()->where('nkk', $nkk)->first();
        if ($existingKK && $existingKK->trashed() && $active === 0) {
            return $existingKK;
        }

        $kk = KartuKeluarga::withTrashed()->updateOrCreate(
            ['nkk' => $nkk],
            [
                'nama_kepala_keluarga' => $kepala->nama,
                'nik_kepala_keluarga'  => $kepala->nik,
                'alamat'               => $kepala->alamat,
                'rt'                   => $kepala->rt_label ?? '000',
                'rw'                   => $kepala->rw_label ?? '000',
                'dusun'                => $kepala->dusun_label ?? '-',
                'rt_id'                => $kepala->rt_id,
                'rw_id'                => $kepala->rw_id,
                'dusun_id'             => $kepala->dusun_id,
                'jumlah_anggota'       => $members->count(),

                'anggota_aktif'        => $active,
                'anggota_mutasi'       => $mutated,
                'anggota_meninggal'    => $dead,
                'anggota_pindah'       => $moved,
                'anggota_pisah_kk'     => $split,
            ]
        );

        if ($active > 0 && $kk->trashed()) {
            $kk->restore();
        }

        return $kk;
    }
}
