<?php

namespace App\Services\Kependudukan;

use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use App\Models\Rw;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PendudukService
{
    /**
     * Get list of existing NKKs for dropdowns
     */
    public function getExistingNKKs()
    {
        return KartuKeluarga::withWilayah()
            ->orderBy('nkk')
            ->get()
            ->map(function($kk) {
                return [
                    'nkk' => $kk->nkk,
                    'kepala_keluarga' => $kk->nama_kepala_keluarga,
                    'alamat' => $kk->alamat,
                    'rt' => $kk->rt_label,
                    'rw' => $kk->rw_label,
                ];
            });
    }

    /**
     * Get Master RW Options with nested RTs
     */
    public function getMasterRwOptions()
    {
        $rws = Rw::with(['rts.dusun'])->orderBy('kode')->get();
        return $rws->map(function ($rw) {
            return [
                'id' => $rw->id,
                'kode' => $rw->kode,
                'nama' => $rw->nama,
                'rts' => $rw->rts->map(function ($rt) {
                    return [
                        'id' => $rt->id,
                        'kode' => $rt->kode,
                        'dusun_id' => $rt->dusun_id,
                        'dusun' => optional($rt->dusun)->nama,
                    ];
                })->values(),
            ];
        })->values();
    }
    /**
     * Create a new inhabitants with optional family members
     */
    public function createPenduduk(array $data, ?array $familyMembers = [])
    {
        return DB::transaction(function () use ($data, $familyMembers) {
            $kkId = null;
            
            if (isset($data['kk_option'])) {
                if ($data['kk_option'] === 'existing' && !empty($data['nkk_existing'])) {
                    $kk = \App\Models\KartuKeluarga::where('nkk', $data['nkk_existing'])->first();
                    if ($kk) {
                        $kkId = $kk->id;
                    }
                } elseif ($data['kk_option'] === 'manual' && !empty($data['nkk'])) {
                    $kk = \App\Models\KartuKeluarga::firstOrCreate(
                        ['nkk' => $data['nkk']],
                        [
                            'alamat' => $data['alamat'] ?? 'Belum Diisi',
                            'rt_id' => $data['rt_id'] ?? null,
                            'rw_id' => $data['rw_id'] ?? null,
                            'dusun_id' => $data['dusun_id'] ?? null,
                            'nama_kepala_keluarga' => $data['nama'] ?? 'Belum Ditentukan',
                            'nik_kepala_keluarga' => $data['nik'] ?? null,
                        ]
                    );
                    $kkId = $kk->id;
                }
            }

            if (empty($kkId) && !empty($data['kartu_keluarga_id'])) {
                $kkId = $data['kartu_keluarga_id'];
            }

            if (empty($kkId)) {
                throw new \Exception('Data Kartu Keluarga tidak ditemukan atau tidak dapat dibuat.');
            }

            $data['kartu_keluarga_id'] = $kkId;

            $penduduk = Penduduk::create($data);

            if ($familyMembers && is_array($familyMembers)) {
                $this->createFamilyMembers($penduduk, $familyMembers);
            }

            return $penduduk;
        });
    }

    /**
     * Update existing penduduk
     */
    public function updatePenduduk(Penduduk $penduduk, array $data)
    {
        return DB::transaction(function () use ($penduduk, $data) {
            $penduduk->update($data);

            if (isset($data['nkk']) && (string)$data['nkk'] !== (string)$penduduk->nkk) {
                $newNkk = preg_replace('/\D+/', '', $data['nkk']);
                if (strlen($newNkk) === 16) {
                    $targetKk = \App\Models\KartuKeluarga::firstOrCreate(
                        ['nkk' => $newNkk],
                        [
                            'alamat' => $penduduk->kartuKeluarga->alamat ?? 'Belum Diisi',
                            'rt_id' => $penduduk->kartuKeluarga->rt_id ?? null,
                            'rw_id' => $penduduk->kartuKeluarga->rw_id ?? null,
                            'dusun_id' => $penduduk->kartuKeluarga->dusun_id ?? null,
                            'nama_kepala_keluarga' => $penduduk->nama,
                            'nik_kepala_keluarga' => $penduduk->nik,
                        ]
                    );
                    
                    $oldKkId = $penduduk->kartu_keluarga_id;
                    $penduduk->kartu_keluarga_id = $targetKk->id;
                    $penduduk->save();

                    $kkService = app(\App\Services\Kependudukan\KartuKeluargaService::class);
                    $kkService->recalculate($targetKk->id);
                    if ($oldKkId) $kkService->recalculate($oldKkId);
                }
            }

            if ($penduduk->kartu_keluarga_id) {
                app(\App\Services\Kependudukan\KartuKeluargaService::class)->recalculate($penduduk->kartu_keluarga_id);
            }

            return $penduduk->refresh();
        });
    }

    /**
     * Update family address (Redirected to KartuKeluarga Source of Truth)
     */
    public function updateFamilyAddress($nkk, array $data)
    {
        return DB::transaction(function () use ($nkk, $data) {
            $kk = \App\Models\KartuKeluarga::where('nkk', $nkk)->firstOrFail();
            
            $kk->update([
                'alamat' => $data['alamat'],
                'rt_id' => $data['rt_id'],
                'rw_id' => $data['rw_id'],
                'dusun_id' => $data['dusun_id'] ?? $kk->dusun_id,
            ]);

            app(\App\Services\Kependudukan\KartuKeluargaService::class)->recalculate($kk->id);
            
            return 1;
        });
    }

    /**
     * Process family members creation
     */
    protected function createFamilyMembers(Penduduk $mainPenduduk, array $members)
    {
        foreach ($members as $index => $memberData) {
            if (empty($memberData['nik']) || empty($memberData['nama'])) continue;

            $familyData = array_merge($memberData, [
                'kartu_keluarga_id' => $mainPenduduk->kartu_keluarga_id,
                'agama' => $memberData['agama'] ?? $mainPenduduk->agama,
                'kedudukan_keluarga' => $memberData['kedudukan_keluarga'] ?? 'Anggota Keluarga',
            ]);

            try {
                Penduduk::create($familyData);
            } catch (\Exception $e) {
                Log::error("Failed to create family member {$index}: " . $e->getMessage());
                throw new \Exception("Gagal menambahkan anggota keluarga: " . $e->getMessage());
            }
        }
    }
}
