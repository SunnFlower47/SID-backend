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
            // Handle KK logic
            if (isset($data['kk_option'])) {
                if ($data['kk_option'] === 'existing' && !empty($data['nkk_existing'])) {
                    // Use existing NKK
                    $existingMember = Penduduk::where('nkk', $data['nkk_existing'])->first();
                    if ($existingMember) {
                        $data['nkk'] = $existingMember->nkk;
                        $data['alamat'] = $existingMember->alamat;
                        $data['rt'] = $existingMember->rt;
                        $data['rw'] = $existingMember->rw;
                        $data['dusun'] = $existingMember->dusun;
                        $data['kartu_keluarga_id'] = $existingMember->kartu_keluarga_id;
                    }
                } elseif ($data['kk_option'] === 'manual' && !empty($data['nkk'])) {
                    // Use manual NKK
                    $existingKK = Penduduk::where('nkk', $data['nkk'])->first();
                    if ($existingKK) {
                        $data['nkk'] = $data['nkk'];
                        $data['kartu_keluarga_id'] = $existingKK->kartu_keluarga_id;
                        $data['alamat'] = $existingKK->alamat;
                        $data['rt'] = $existingKK->rt;
                        $data['rw'] = $existingKK->rw;
                        $data['dusun'] = $existingKK->dusun;
                    } else {
                        $data['nkk'] = $data['nkk'];
                        $data['kartu_keluarga_id'] = $this->generateKartuKeluargaId();
                        $data['dusun'] = $this->getDusunFromRT($data['rt']);
                    }
                }
            } else {
                $data['kartu_keluarga_id'] = $this->generateKartuKeluargaId();
            }

            // Validation safety net
            if (empty($data['nkk'])) {
                throw new \Exception('No KK tidak dapat ditentukan.');
            }
            if (empty($data['dusun'])) {
                $data['dusun'] = $this->getDusunFromRT($data['rt']);
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
     * Update family address based on RT
     */
    public function updateFamilyAddress($nkk, array $data)
    {
        return DB::transaction(function () use ($nkk, $data) {
            $dusun = $data['dusun'] ?? $this->getDusunFromRT($data['rt']);

            return Penduduk::where('nkk', $nkk)->update([
                'alamat' => $data['alamat'],
                'rt' => str_pad($data['rt'], 3, '0', STR_PAD_LEFT),
                'rw' => str_pad($data['rw'], 3, '0', STR_PAD_LEFT),
                'dusun' => $dusun,
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
                'kartu_keluarga_id' => $mainPenduduk->kartu_keluarga_id,
                'alamat' => $memberData['alamat'] ?? $mainPenduduk->alamat,
                'rt' => $memberData['rt'] ?? $mainPenduduk->rt,
                'rw' => $memberData['rw'] ?? $mainPenduduk->rw,
                'dusun' => $memberData['dusun'] ?? $mainPenduduk->dusun,
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

    /**
     * Generate ID for new KK
     */
    protected function generateKartuKeluargaId()
    {
        $lastKK = Penduduk::whereNotNull('kartu_keluarga_id')
            ->orderBy('kartu_keluarga_id', 'desc')
            ->first();
            
        return ($lastKK && $lastKK->kartu_keluarga_id) ? $lastKK->kartu_keluarga_id + 1 : 1;
    }

    /**
     * Get dusun based on RT (Logic copied from Controller)
     */
    public function getDusunFromRT($rt)
    {
        $rt = str_pad($rt, 3, '0', STR_PAD_LEFT);
        $dusun1RTs = ['001', '002', '003', '004', '006', '007', '008']; // Dusun 1
        $dusun2RTs = ['005', '009', '010']; // Dusun 2

        if (in_array($rt, $dusun1RTs)) return 'Dusun 1';
        if (in_array($rt, $dusun2RTs)) return 'Dusun 2';
        return 'Dusun 1'; // Default
    }
}
