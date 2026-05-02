<?php

namespace App\Services;

use App\Models\Penduduk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PendudukService
{
    /**
     * Create a new inhabitants with optional family members
     */
    public function createPenduduk(array $data, ?array $familyMembers = [])
    {
        return DB::transaction(function () use ($data, $familyMembers) {
            // Handle KK logic - Always get KartuKeluarga ID
            $kkId = null;
            
            if (isset($data['kk_option'])) {
                if ($data['kk_option'] === 'existing' && !empty($data['nkk_existing'])) {
                    // Use existing NKK
                    $kk = \App\Models\KartuKeluarga::where('nkk', $data['nkk_existing'])->first();
                    if ($kk) {
                        $kkId = $kk->id;
                    }
                } elseif ($data['kk_option'] === 'manual' && !empty($data['nkk'])) {
                    // Use manual NKK - Find or Create KK record (Source of Truth)
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

            // Fallback for direct kartu_keluarga_id
            if (empty($kkId) && !empty($data['kartu_keluarga_id'])) {
                $kkId = $data['kartu_keluarga_id'];
            }

            // Validation safety net
            if (empty($kkId)) {
                throw new \Exception('Data Kartu Keluarga tidak ditemukan atau tidak dapat dibuat.');
            }

            // Set the relational ID
            $data['kartu_keluarga_id'] = $kkId;

            // Create main record (Only unique fields will be saved due to $fillable)
            $penduduk = Penduduk::create($data);

            // Handle family members
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
            // Update personal data on Penduduk model
            $penduduk->update($data);

            // Handle NKK change (Family Merging / Migration)
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

                    // Recalculate both old and new families
                    $kkService = app(\App\Services\KartuKeluargaService::class);
                    $kkService->recalculate($targetKk->id);
                    if ($oldKkId) $kkService->recalculate($oldKkId);
                }
            }

            // KK Address update logic removed from personal update to enforce centralized family address management
            
            // SELALU Picu sinkronisasi statistik & data identitas KK setelah update
            if ($penduduk->kartu_keluarga_id) {
                app(\App\Services\KartuKeluargaService::class)->recalculate($penduduk->kartu_keluarga_id);
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

            // Trigger recalculation
            app(\App\Services\KartuKeluargaService::class)->recalculate($kk->id);
            
            return 1; // Simplified return
        });
    }

    /**
     * Process family members creation
     */
    protected function createFamilyMembers(Penduduk $mainPenduduk, array $members)
    {
        foreach ($members as $index => $memberData) {
            if (empty($memberData['nik']) || empty($memberData['nama'])) continue;

            // Only send essential fields + the relationship ID
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


