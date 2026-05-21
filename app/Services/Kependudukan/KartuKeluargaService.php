<?php

namespace App\Services\Kependudukan;

use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use App\Models\Mutasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KartuKeluargaService
{
    protected $statsService;

    public function __construct(VillageStatisticsService $statsService)
    {
        $this->statsService = $statsService;
    }

    /**
     * Get Master RW Options with RTs
     */
    public function getMasterRwOptions()
    {
        $cacheKey = 'master_wilayah_rw_options_v1';

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addDays(7), function() {
            return \App\Models\Rw::with(['rts' => function($q) {
                $q->orderBy('kode');
            }, 'rts.dusun'])->orderBy('kode')->get()->map(function($rw) {
                return [
                    'id' => $rw->id,
                    'kode' => $rw->kode,
                    'nama' => $rw->nama,
                    'rts' => $rw->rts->map(function($rt) {
                        return [
                            'id' => $rt->id,
                            'kode' => $rt->kode,
                            'dusun_id' => $rt->dusun_id,
                            'dusun' => optional($rt->dusun)->nama
                        ];
                    })
                ];
            });
        });
    }

    /**
     * Create a new Kartu Keluarga and its first member
     */
    public function createKK(array $data)
    {
        $rtModel = \App\Models\Rt::find($data['rt_id']);

        return DB::transaction(function () use ($data, $rtModel) {
            $nkkSanitized = substr(str_replace(' ', '', $data['nkk']), 0, 16);
            $nikSanitized = substr(str_replace(' ', '', $data['nik_kepala_keluarga']), 0, 16);

            // 1. Create the KK record
            $kk = KartuKeluarga::create([
                'nkk' => $nkkSanitized,
                'nama_kepala_keluarga' => $data['nama_kepala_keluarga'],
                'nik_kepala_keluarga' => $nikSanitized,
                'alamat' => $data['alamat'],
                'rt_id' => $data['rt_id'],
                'rw_id' => $data['rw_id'],
                'dusun_id' => optional($rtModel)->dusun_id,
            ]);

            // 2. Create the Kepala Keluarga
            Penduduk::create([
                'kartu_keluarga_id' => $kk->id,
                'nik' => $nikSanitized,
                'nama' => $data['nama_kepala_keluarga'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'tempat_lahir' => $data['tempat_lahir'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'agama' => $data['agama'],
                'status_perkawinan' => $data['status_perkawinan'],
                'pekerjaan' => $data['pekerjaan'],
                'pendidikan' => $data['pendidikan'],
                'kedudukan_keluarga' => 'Kepala Keluarga',
            ]);

            return $kk;
        });
    }

    /**
     * Update Kartu Keluarga info
     */
    public function updateKK(KartuKeluarga $kk, array $data)
    {
        $rtModel = \App\Models\Rt::find($data['rt_id']);

        return DB::transaction(function () use ($kk, $data, $rtModel) {
            $kk->update([
                'nama_kepala_keluarga' => $data['nama_kepala_keluarga'],
                'alamat' => $data['alamat'],
                'rt_id' => $data['rt_id'],
                'rw_id' => $data['rw_id'],
                'dusun_id' => optional($rtModel)->dusun_id,
            ]);

            // Sync Head of Family name if it matches the KK head name
            $head = Penduduk::where('kartu_keluarga_id', $kk->id)
                ->where('kedudukan_keluarga', 'Kepala Keluarga')
                ->first();

            if ($head) {
                $head->update(['nama' => $data['nama_kepala_keluarga']]);
            }

            return $kk;
        });
    }

    /**
     * Delete Kartu Keluarga and all members
     */
    public function deleteKK(KartuKeluarga $kk)
    {
        return DB::transaction(function () use ($kk) {
            Penduduk::where('kartu_keluarga_id', $kk->id)->delete();
            $kk->delete();
            $this->statsService->clearStats();
            return true;
        });
    }

    /**
     * Mass Audit and Recalculate all KKs
     */
    public function syncSummary()
    {
        return DB::transaction(function () {
            // 1. Audit Inhabitants: Soft delete those with active death/move mutations
            $mustDeleteIds = Mutasi::whereIn('jenis_mutasi', ['kematian', 'pindah_keluar'])
                ->whereHas('penduduk', fn($q) => $q->whereNull('deleted_at'))
                ->pluck('penduduk_id');
            
            if ($mustDeleteIds->isNotEmpty()) {
                Penduduk::whereIn('id', $mustDeleteIds)->delete();
            }

            // 2. Recalculate every KK
            $total = 0;
            KartuKeluarga::all()->each(function ($kk) use (&$total) {
                $this->recalculate($kk->id);
                $total++;
            });

            $this->statsService->clearStats();
            return $total;
        });
    }

    /**
     * Step 1 Resolution: Appoint Temporary Head of Family
     */
    public function resolveKkSementara(KartuKeluarga $kk, $kandidatId)
    {
        $kandidat = Penduduk::findOrFail($kandidatId);
        $umur = $kandidat->tanggal_lahir ? \Carbon\Carbon::parse($kandidat->tanggal_lahir)->age : 0;

        if ($umur < 17) {
            throw new \Exception("Kandidat masih di bawah umur ({$umur} thn).");
        }

        return DB::transaction(function () use ($kk, $kandidat) {
            // 1. Store original position for undo in mutationCAUSE
            if ($kk->mutasi_penyebab_id) {
                $mutasiPenyebab = Mutasi::find($kk->mutasi_penyebab_id);
                if ($mutasiPenyebab) {
                    $detail = is_array($mutasiPenyebab->detail_tambahan) 
                        ? $mutasiPenyebab->detail_tambahan 
                        : (json_decode($mutasiPenyebab->detail_tambahan, true) ?: []);

                    $detail['kk_sementara_id'] = $kandidat->id;
                    $detail['kk_sementara_kedudukan_asal'] = $kandidat->kedudukan_keluarga;
                    $mutasiPenyebab->update(['detail_tambahan' => $detail]);
                }
            }

            $kedudukanAsal = $kandidat->kedudukan_keluarga;

            // 2. Set Status to bermasalah_sementara
            $kk->update([
                'status_kk' => 'bermasalah_sementara',
                'kk_sementara_id' => $kandidat->id,
            ]);

            // 3. Promote to Head
            $kandidat->update(['kedudukan_keluarga' => 'Kepala Keluarga']);

            // 4. Log Audit
            Mutasi::create([
                'penduduk_id' => $kandidat->id,
                'jenis_mutasi' => 'pembaruan_kk',
                'kategori_mutasi' => 'dalam_desa',
                'asal_tujuan' => "Dinaikkan jadi Kepala Keluarga sementara NKK {$kk->nkk}",
                'tanggal_mutasi' => now()->toDateString(),
                'alasan' => 'Resolusi KK bermasalah — penunjukan sementara',
                'detail_tambahan' => ['nkk' => $kk->nkk, 'tipe' => 'sementara', 'kedudukan_asal' => $kedudukanAsal],
            ]);

            return $kk;
        });
    }

    /**
     * Step 2 Resolution: Finalize with new NKK
     */
    public function resolveKkPermanen(KartuKeluarga $kk, $nkkBaru)
    {
        return DB::transaction(function () use ($kk, $nkkBaru) {
            $oldNkk = $kk->nkk;
            $nkkSanitized = substr(str_replace(' ', '', $nkkBaru), 0, 16);
            $nikSanitized = $kk->kkSementara ? substr(str_replace(' ', '', $kk->kkSementara->nik), 0, 16) : null;

            // 1. Create New KK Record
            $newKk = KartuKeluarga::create([
                'nkk' => $nkkSanitized,
                'nama_kepala_keluarga' => $kk->kkSementara?->nama,
                'nik_kepala_keluarga' => $nikSanitized,
                'alamat' => $kk->alamat,
                'rt_id' => $kk->rt_id,
                'rw_id' => $kk->rw_id,
                'dusun_id' => $kk->dusun_id,
                'status_kk' => 'normal',
                'anggota_aktif' => $kk->anggotaAktif()->count(),
            ]);

            // 2. Move active members
            Penduduk::where('kartu_keluarga_id', $kk->id)
                ->whereDoesntHave('mutasis', function($q) {
                    $q->whereIn('jenis_mutasi', ['kematian', 'pindah_keluar', 'pisah_kk']);
                })
                ->update(['kartu_keluarga_id' => $newKk->id]);

            $this->recalculate($newKk->id);

            // 3. Close Mutasi Penyebab
            if ($kk->mutasi_penyebab_id) {
                $mp = Mutasi::find($kk->mutasi_penyebab_id);
                if ($mp) {
                    $detail = $mp->detail_tambahan ?? [];
                    $detail['kk_sudah_diselesaikan'] = true;
                    $detail['nkk_baru'] = $nkkBaru;
                    $mp->update(['detail_tambahan' => $detail]);
                }
            }

            // 4. Remove Temp Mutasi
            if ($kk->kk_sementara_id) {
                Mutasi::where('penduduk_id', $kk->kk_sementara_id)
                    ->where('jenis_mutasi', 'pembaruan_kk')
                    ->get()
                    ->each(function ($m) use ($oldNkk) {
                        $det = $m->detail_tambahan ?? [];
                        if (($det['tipe'] ?? null) === 'sementara' && ($det['nkk'] ?? null) === $oldNkk) {
                            $m->forceDelete();
                        }
                    });
            }

            // 5. Archive Old KK
            $kk->update([
                'status_kk' => 'resolved',
                'anggota_aktif' => 0,
                'catatan_bermasalah' => json_encode(['nkk_baru' => $newKk->nkk]),
            ]);

            // 6. Log Permanent Success
            Mutasi::create([
                'penduduk_id' => $kk->kk_sementara_id,
                'jenis_mutasi' => 'pembaruan_kk',
                'kategori_mutasi' => 'dalam_desa',
                'asal_tujuan' => "NKK Lama: {$oldNkk} → NKK Baru: {$nkkBaru}",
                'tanggal_mutasi' => now()->toDateString(),
                'alasan' => 'Resolusi KK bermasalah — Penyelesaian Permanen',
                'detail_tambahan' => [
                    'nkk_lama' => $oldNkk,
                    'nkk_baru' => $newKk->nkk,
                    'tipe' => 'permanen',
                    'kk_sudah_diselesaikan' => true,
                ],
            ]);

            $this->statsService->clearStats();
            return $newKk;
        });
    }

    /**
     * Cancel Step 1: Revert to problematic status
     */
    public function batalkanSementara(KartuKeluarga $kk)
    {
        return DB::transaction(function () use ($kk) {
            $kkSementaraId = $kk->kk_sementara_id;
            if (!$kkSementaraId) throw new \Exception('Data KK sementara tidak ditemukan.');

            // 1. Find the mutation to get original role
            $pembaruanMutasi = Mutasi::where('penduduk_id', $kkSementaraId)
                ->where('jenis_mutasi', 'pembaruan_kk')
                ->latest('id')
                ->first();

            $kedudukanAsal = $pembaruanMutasi->detail_tambahan['kedudukan_asal'] ?? 'ANGGOTA';

            // 2. Rollback resident
            $penduduk = Penduduk::find($kkSementaraId);
            if ($penduduk) {
                $penduduk->update(['kedudukan_keluarga' => $kedudukanAsal]);
            }

            // 3. Remove mutation
            if ($pembaruanMutasi) {
                $pembaruanMutasi->forceDelete();
            }

            // 4. Clean mutation cause
            if ($kk->mutasi_penyebab_id) {
                $mp = Mutasi::find($kk->mutasi_penyebab_id);
                if ($mp) {
                    $detail = is_array($mp->detail_tambahan) ? $mp->detail_tambahan : (json_decode($mp->detail_tambahan, true) ?: []);
                    unset($detail['kk_sementara_id'], $detail['kk_sementara_kedudukan_asal']);
                    $mp->update(['detail_tambahan' => $detail]);
                }
            }

            // 5. Reset KK
            $kk->update(['status_kk' => 'bermasalah', 'kk_sementara_id' => null]);
            return true;
        });
    }

    /**
     * Recalculate statistics for a given KK ID or NKK
     */
    public function recalculate($identifier)
    {
        if (empty($identifier)) return;

        $kk = \App\Models\KartuKeluarga::withTrashed()->find($identifier);
        
        if (!$kk) {
            $kk = \App\Models\KartuKeluarga::withTrashed()->where('nkk', (string)$identifier)->first();
        }

        if (!$kk) {
            Log::warning("Recalculate failed: Kartu Keluarga not found for identifier: {$identifier}");
            return;
        }

        $members = \App\Models\Penduduk::withTrashed()
            ->with(['mutasis' => function($q) {
                $q->latest('tanggal_mutasi')->latest('id');
            }])
            ->where('kartu_keluarga_id', $kk->id)
            ->get();

        if ($members->isEmpty()) {
            $kk->update([
                'jumlah_anggota'    => 0,
                'anggota_aktif'     => 0,
                'anggota_mutasi'    => 0,
                'anggota_meninggal' => 0,
                'anggota_pindah'    => 0,
                'anggota_pisah_kk'  => 0,
                'status_kk'         => 'normal',
            ]);
            return;
        }

        $allHOFs = $members->filter(function($m) {
            $role = strtoupper(trim($m->kedudukan_keluarga));
            return $role === 'KEPALA KELUARGA';
        });
        
        $activeHOF = $allHOFs->filter(fn($m) => is_null($m->deleted_at))->first();
        
        $activeMembers = $members->filter(fn($m) => is_null($m->deleted_at))->sortBy('tanggal_lahir');
        $displayHOF = $activeHOF ?? $activeMembers->first() ?? $members->first();

        $stats = [
            'active'  => 0,
            'dead'    => 0,
            'moved'   => 0,
            'split'   => 0,
            'mutated' => 0,
        ];

        foreach ($members as $member) {
            if (is_null($member->deleted_at)) {
                $stats['active']++;
            } else {
                $stats['mutated']++;
                $mutasi = $member->mutasis->first();
                if ($mutasi) {
                    if ($mutasi->jenis_mutasi === 'kematian') $stats['dead']++;
                    elseif ($mutasi->jenis_mutasi === 'pindah_keluar') $stats['moved']++;
                    elseif ($mutasi->jenis_mutasi === 'pisah_kk') $stats['split']++;
                }
            }
        }

        $statusKk = $kk->status_kk;
        $bermasalahSejak = $kk->kk_bermasalah_sejak;
        $mutasiPenyebabId = $kk->mutasi_penyebab_id;

        if ($stats['active'] > 0 && !$activeHOF) {
            if (in_array($statusKk, ['normal', null])) {
                $statusKk = 'bermasalah';
                $bermasalahSejak = $bermasalahSejak ?? now();
                
                $latestMutation = \App\Models\Mutasi::whereIn('penduduk_id', $members->pluck('id'))
                    ->whereIn('jenis_mutasi', ['kematian', 'pindah_keluar', 'pisah_kk'])
                    ->latest('tanggal_mutasi')
                    ->latest('id')
                    ->first();
                $mutasiPenyebabId = $latestMutation ? $latestMutation->id : null;
            }
        } else {
            if ($statusKk === 'bermasalah') {
                $statusKk = 'normal';
                $bermasalahSejak = null;
                $mutasiPenyebabId = null;
                $kk->update(['kk_sementara_id' => null]);
            }
        }

        $kk->update([
            'nama_kepala_keluarga' => $displayHOF ? $displayHOF->nama : null,
            'nik_kepala_keluarga'  => $displayHOF ? substr(str_replace(' ', '', $displayHOF->nik), 0, 16) : null,
            'jumlah_anggota'       => $members->count(),
            'anggota_aktif'        => $stats['active'],
            'anggota_mutasi'       => $stats['mutated'],
            'anggota_meninggal'    => $stats['dead'],
            'anggota_pindah'       => $stats['moved'],
            'anggota_pisah_kk'     => $stats['split'],
            'status_kk'            => $statusKk,
            'kk_bermasalah_sejak'  => $bermasalahSejak,
            'mutasi_penyebab_id'   => $mutasiPenyebabId,
        ]);

        if ($stats['active'] > 0 && $kk->trashed()) {
            $kk->restore();
        }

        return $kk;
    }
}
