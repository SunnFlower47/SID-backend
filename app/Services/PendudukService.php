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
            // Handle KK logic (Simplified to only NKK)
            if (isset($data['kk_option'])) {
                if ($data['kk_option'] === 'existing' && !empty($data['nkk_existing'])) {
                    // Use existing NKK
                    $existingMember = Penduduk::where('nkk', $data['nkk_existing'])->first();
                    if ($existingMember) {
                        $data['nkk'] = $existingMember->nkk;
                        $data['alamat'] = $existingMember->alamat;
                        $data['rt_id'] = $existingMember->rt_id;
                        $data['rw_id'] = $existingMember->rw_id;
                        $data['dusun_id'] = $existingMember->dusun_id;
                    }
                } elseif ($data['kk_option'] === 'manual' && !empty($data['nkk'])) {
                    // Use manual NKK
                    $existingKK = Penduduk::where('nkk', $data['nkk'])->first();
                    if ($existingKK) {
                        $data['alamat'] = $existingKK->alamat;
                        $data['rt_id'] = $existingKK->rt_id;
                        $data['rw_id'] = $existingKK->rw_id;
                        $data['dusun_id'] = $existingKK->dusun_id;
                    }
                }
            }

            // Validation safety net
            if (empty($data['nkk'])) {
                throw new \Exception('No KK tidak dapat ditentukan.');
            }

            // Create main record
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
            $penduduk->update($data);
            return $penduduk->refresh();
        });
    }

    /**
     * Update family address based on RT ID
     */
    public function updateFamilyAddress($nkk, array $data)
    {
        return DB::transaction(function () use ($nkk, $data) {
            return Penduduk::where('nkk', $nkk)->update([
                'alamat' => $data['alamat'],
                'rt_id' => $data['rt_id'],
                'rw_id' => $data['rw_id'],
                'dusun_id' => $data['dusun_id'] ?? null,
            ]);
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
                'nkk' => $mainPenduduk->nkk,
                'alamat' => $memberData['alamat'] ?? $mainPenduduk->alamat,
                'rt_id' => $memberData['rt_id'] ?? $mainPenduduk->rt_id,
                'rw_id' => $memberData['rw_id'] ?? $mainPenduduk->rw_id,
                'dusun_id' => $memberData['dusun_id'] ?? $mainPenduduk->dusun_id,
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


