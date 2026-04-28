<?php

namespace App\Services;

use App\Models\Mutasi;
use App\Models\Penduduk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MutasiService
{
    /**
     * Handle Kelahiran logic
     */
    public function handleKelahiran(array $validated)
    {
        // Ambil data keluarga berdasarkan NKK
        $kartuKeluarga = Penduduk::where('nkk', $validated['nkk'])->first();
        if (!$kartuKeluarga) {
            throw new \Exception('Tidak ditemukan keluarga dengan NKK: ' . $validated['nkk']);
        }

        // Buat penduduk baru
        $penduduk = Penduduk::create([
            'nkk' => $validated['nkk'],
            'nik' => $validated['nik_bayi'],
            'nama' => $validated['nama_bayi'],
            'jenis_kelamin' => $validated['jenis_kelamin_bayi'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'agama' => $validated['agama_bayi'],
            'status_perkawinan' => $validated['status_perkawinan_bayi'],
            'kedudukan_keluarga' => $validated['kedudukan_keluarga_bayi'],
            'pendidikan' => $validated['pendidikan_bayi'] ?? 'Tidak/Belum Sekolah',
            'pekerjaan' => $validated['pekerjaan_bayi'],
            'nama_ayah' => $validated['nama_ayah'],
            'nama_ibu' => $validated['nama_ibu'],
            'alamat' => $validated['alamat_bayi'],
            'rt_id' => $validated['rt_id_bayi'],
            'rw_id' => $validated['rw_id_bayi'],
            'dusun_id' => $validated['dusun_id_bayi'] ?? $kartuKeluarga->dusun_id,
            'keterangan' => $validated['keterangan_bayi'],
        ]);

        // Buat log mutasi
        Mutasi::create([
            'penduduk_id' => $penduduk->id,
            'jenis_mutasi' => 'kelahiran',
            'kategori_mutasi' => 'dalam_kota',
            'asal_tujuan' => 'Kelahiran di Desa Cibatu',
            'tanggal_mutasi' => $validated['tanggal_mutasi'],
            'alasan' => 'Kelahiran bayi baru',
            'dokumen_pendukung' => null,
        ]);

        return $penduduk;
    }

    /**
     * Handle Kematian logic
     */
    public function handleKematian(array $validated)
    {
        $penduduk = Penduduk::findOrFail($validated['penduduk_id']);

        // Buat log mutasi
        Mutasi::create([
            'penduduk_id' => $penduduk->id,
            'jenis_mutasi' => 'kematian',
            'kategori_mutasi' => 'dalam_kota',
            'asal_tujuan' => $validated['bertempat_di'],
            'tanggal_mutasi' => $validated['tanggal_mutasi'],
            'alasan' => $validated['alasan'],
            'dokumen_pendukung' => null,
            'detail_tambahan' => [
                'snapshot_asal' => [
                    'nkk' => $penduduk->nkk,
                    'alamat' => $penduduk->alamat,
                    'rt_id' => $penduduk->rt_id,
                    'rt_kode' => $penduduk->rt_label,
                    'rw_id' => $penduduk->rw_id,
                    'rw_kode' => $penduduk->rw_label,
                    'dusun_id' => $penduduk->dusun_id,
                    'dusun_nama' => $penduduk->dusun_label,
                    'kedudukan' => $penduduk->kedudukan_keluarga,
                ],
                'alasan_kematian' => $validated['alasan'],
                'kematian' => [
                    'hari' => $validated['hari_meninggal'],
                    'tanggal' => $validated['tanggal_mutasi'],
                    'jam' => $validated['jam_meninggal'],
                    'bertempat_di' => $validated['bertempat_di']
                ],
                'pemakaman' => [
                    'hari' => $validated['hari_pemakaman'],
                    'tanggal' => $validated['tanggal_pemakaman'],
                    'jam' => $validated['jam_pemakaman'],
                    'lokasi' => $validated['lokasi_pemakaman']
                ],
                'pelapor_nama' => $validated['pelapor_nama'] ?? null,
                'pelapor_umur' => $validated['pelapor_umur'] ?? null,
                'pelapor_pekerjaan' => $validated['pelapor_pekerjaan'] ?? null,
                'pelapor_alamat' => $validated['pelapor_alamat'] ?? null,
                'pelapor_hubungan' => $validated['pelapor_hubungan'] ?? null,
            ],
        ]);

        // Soft delete penduduk
        $penduduk->delete();
    }

    /**
     * Handle Pindah Masuk logic
     */
    public function handlePindahMasuk(array $validated)
    {
        // Determine which NKK to use
        $nkkToUse = !empty($validated['nkk']) ? $validated['nkk'] : $validated['nkk_new'];

        // Create new penduduk
        $penduduk = Penduduk::create([
            'nkk' => $nkkToUse,
            'nik' => $validated['nik'],
            'nama' => $validated['nama'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'agama' => $validated['agama'],
            'status_perkawinan' => $validated['status_perkawinan'],
            'kedudukan_keluarga' => $validated['kedudukan_keluarga'],
            'pendidikan' => $validated['pendidikan'],
            'pekerjaan' => $validated['pekerjaan'] ?? 'Belum Bekerja',
            'nama_ayah' => $validated['nama_ayah'] ?? 'Tidak Diketahui',
            'nama_ibu' => $validated['nama_ibu'] ?? 'Tidak Diketahui',
            'alamat' => $validated['alamat'],
            'rt_id' => $validated['rt_id'],
            'rw_id' => $validated['rw_id'],
            'dusun_id' => $validated['dusun_id'] ?? null,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        // Create mutasi log
        Mutasi::create([
            'penduduk_id' => $penduduk->id,
            'jenis_mutasi' => 'pindah_masuk',
            'kategori_mutasi' => $validated['kategori_mutasi'],
            'asal_tujuan' => $validated['asal_tujuan'],
            'tanggal_mutasi' => $validated['tanggal_mutasi'],
            'alasan' => $validated['alasan'] ?? 'Pindah masuk ke Desa Cibatu',
            'dokumen_pendukung' => null,
        ]);

        return $penduduk;
    }

    public function handlePindahKeluar(array $validated)
    {
        $penduduk = Penduduk::findOrFail($validated['penduduk_id']);
        
        $snapshotAsal = [
            'nkk' => $penduduk->nkk,
            'alamat' => $penduduk->alamat,
            'rt_id' => $penduduk->rt_id,
            'rt_kode' => $penduduk->rt_label,
            'rw_id' => $penduduk->rw_id,
            'rw_kode' => $penduduk->rw_label,
            'dusun_id' => $penduduk->dusun_id,
            'dusun_nama' => $penduduk->dusun_label,
            'kedudukan' => $penduduk->kedudukan_keluarga,
        ];

        // Handle collective move (anggota_pindah)
        $anggotaPindahIds = $validated['anggota_pindah'] ?? [];
        $snapshotAnggota = [];
        
        if (!empty($anggotaPindahIds)) {
            $anggotaPindah = Penduduk::whereIn('id', $anggotaPindahIds)->get();
            foreach ($anggotaPindah as $member) {
                $snapshotAnggota[] = [
                    'id' => $member->id,
                    'nama' => $member->nama,
                    'nik' => $member->nik,
                    'kedudukan' => $member->kedudukan_keluarga,
                    'rt_id' => $member->rt_id,
                    'rw_id' => $member->rw_id,
                    'dusun_id' => $member->dusun_id,
                    'alamat' => $member->alamat,
                ];
                // Soft delete member
                $member->delete();
            }
        }

        // Buat log mutasi
        $snapshotAsal['anggota_pindah'] = $snapshotAnggota;

        Mutasi::create([
            'penduduk_id' => $penduduk->id,
            'jenis_mutasi' => 'pindah_keluar',
            'kategori_mutasi' => $validated['kategori_mutasi'],
            'asal_tujuan' => $validated['asal_tujuan'],
            'tanggal_mutasi' => $validated['tanggal_mutasi'],
            'alasan' => $validated['alasan'] ?: 'Pindah keluar dari Desa Cibatu',
            'dokumen_pendukung' => null,
            'detail_tambahan' => [
                'snapshot_asal' => $snapshotAsal,
                'tujuan' => $validated['asal_tujuan'],
                'kategori' => $validated['kategori_mutasi'],
            ],
        ]);

        // Soft delete main resident
        $penduduk->delete();
    }

    /**
     * Handle Pindah RT/RW logic
     */
    public function handlePindahRTRW(array $validated)
    {
        // Fallback: jika NKK tidak dikirim tapi penduduk_id ada, ambil dari penduduk
        if (empty($validated['nkk']) && !empty($validated['penduduk_id'])) {
            $validated['nkk'] = Penduduk::findOrFail($validated['penduduk_id'])->nkk;
        }

        // Ambil semua anggota keluarga berdasarkan NKK
        $anggotaKeluarga = Penduduk::where('nkk', $validated['nkk'])->get();

        if ($anggotaKeluarga->isEmpty()) {
            throw new \Exception('Tidak ada anggota keluarga dengan No KK: ' . $validated['nkk']);
        }

        $rtIdTujuan = $validated['rt_id_tujuan'];
        $rwIdTujuan = $validated['rw_id_tujuan'];
        $dusunIdTujuan = $validated['dusun_id_tujuan'] ?? null;

        // Ambil alamat dari anggota keluarga pertama jika alamat_tujuan kosong
        $alamatTujuan = $validated['alamat_tujuan'] ?? $anggotaKeluarga->first()->alamat;

        // Simpan informasi asal untuk log dan revert
        $anggotaPertama = $anggotaKeluarga->first();
        $rtIdAsal = $anggotaPertama->rt_id;
        $rwIdAsal = $anggotaPertama->rw_id;
        $dusunIdAsal = $anggotaPertama->dusun_id;

        // SIMPAN SNAPSHOT SEMUA ANGGOTA SEBELUM UPDATE (untuk cancel/revert)
        $snapshotAnggota = $anggotaKeluarga->map(function ($anggota) {
            return [
                'id' => $anggota->id,
                'nama' => $anggota->nama,
                'rt_id_asal' => $anggota->rt_id,
                'rt_kode_asal' => optional($anggota->rtMaster)->kode,
                'rw_id_asal' => $anggota->rw_id,
                'rw_kode_asal' => optional($anggota->rwMaster)->kode,
                'dusun_id_asal' => $anggota->dusun_id,
                'dusun_nama_asal' => optional($anggota->dusunMaster)->nama,
                'alamat_asal' => $anggota->alamat,
            ];
        })->toArray();

        // UPDATE SEMUA ANGGOTA KELUARGA DENGAN NKK YANG SAMA
        // Use each() to ensure observers (recalculateKK) are triggered for each member
        $anggotaKeluarga->each(function ($anggota) use ($rtIdTujuan, $rwIdTujuan, $dusunIdTujuan, $alamatTujuan) {
            $anggota->update([
                'rt_id' => $rtIdTujuan,
                'rw_id' => $rwIdTujuan,
                'dusun_id' => $dusunIdTujuan,
                'alamat' => $alamatTujuan,
            ]);
        });

        // Ambil kepala keluarga untuk log mutasi
        $kepalaKeluarga = $anggotaKeluarga->where('kedudukan_keluarga', 'Kepala Keluarga')->first();

        // Helper untuk label di log
        $rtAsal = optional(\App\Models\Rt::find($rtIdAsal))->kode;
        $rwAsal = optional(\App\Models\Rw::find($rwIdAsal))->kode;
        $dusunAsal = optional(\App\Models\Dusun::find($dusunIdAsal))->nama;
        
        $rtTujuan = optional(\App\Models\Rt::find($rtIdTujuan))->kode;
        $rwTujuan = optional(\App\Models\Rw::find($rwIdTujuan))->kode;
        $dusunTujuan = optional(\App\Models\Dusun::find($dusunIdTujuan))->nama;

        $asalLengkap = 'RT ' . $rtAsal . '/RW ' . $rwAsal . ' (' . ($dusunAsal ?? '-') . ')';
        $tujuanLengkap = 'RT ' . $rtTujuan . '/RW ' . $rwTujuan . ' (' . ($dusunTujuan ?? '-') . ')';

        // Buat log mutasi dengan snapshot untuk revert
        Mutasi::create([
            'penduduk_id' => $kepalaKeluarga->id ?? $anggotaKeluarga->first()->id,
            'jenis_mutasi' => 'pindah_rt_rw',
            'kategori_mutasi' => 'dalam_desa',
            'asal_tujuan' => $asalLengkap . ' → ' . $tujuanLengkap,
            'tanggal_mutasi' => $validated['tanggal_mutasi'],
            'alasan' => $validated['asal_tujuan'] ?? 'Pindah RT/RW satu KK (' . $anggotaKeluarga->count() . ' anggota)',
            'dokumen_pendukung' => null,
            'detail_tambahan' => [
                'snapshot_asal' => [
                    'nkk' => $validated['nkk'],
                    'rt_id_asal' => $rtIdAsal,
                    'rt_kode_asal' => $rtAsal,
                    'rw_id_asal' => $rwIdAsal,
                    'rw_kode_asal' => $rwAsal,
                    'dusun_id_asal' => $dusunIdAsal,
                    'dusun_nama_asal' => $dusunAsal,
                    'rt_id_tujuan' => $rtIdTujuan,
                    'rt_kode_tujuan' => $rtTujuan,
                    'rw_id_tujuan' => $rwIdTujuan,
                    'rw_kode_tujuan' => $rwTujuan,
                    'dusun_id_tujuan' => $dusunIdTujuan,
                    'dusun_nama_tujuan' => $dusunTujuan,
                    'anggota' => $snapshotAnggota,
                ],
            ],
        ]);
    }

    /**
     * Handle Pisah KK logic
     */
    public function handlePisahKK(array $validated)
    {
        // Get the person who will become new head of family
        $penduduk = Penduduk::findOrFail($validated['penduduk_id']);
        $oldNKK = $penduduk->nkk;

        // Determine new NKK and address based on option
        $newNKK = null;
        $alamat = null;
        $rtId = null;
        $rwId = null;
        $dusunId = null;
        $isNewFamily = false;

        if ($validated['kategori_mutasi'] === 'dalam_desa' && ($validated['kk_option'] ?? '') === 'existing' && !empty($validated['nkk_existing_id'])) {
            // Join existing KK - get address from existing KK
            $newNKK = $validated['nkk_existing_id'];
            $existingFamily = Penduduk::where('nkk', $newNKK)->first();
            if ($existingFamily) {
                $alamat = $existingFamily->alamat;
                $rtId = $existingFamily->rt_id;
                $rwId = $existingFamily->rw_id;
                $dusunId = $existingFamily->dusun_id;
            }
            $isNewFamily = false;
        } elseif ($validated['kategori_mutasi'] === 'dalam_desa' && ($validated['kk_option'] ?? '') === 'new' && !empty($validated['nkk_baru'])) {
            // Check if NKK already exists
            $existingFamily = Penduduk::where('nkk', $validated['nkk_baru'])->first();
            if ($existingFamily) {
                // NKK already exists - join existing family (use their address)
                $newNKK = $validated['nkk_baru'];
                $alamat = $existingFamily->alamat;
                $rtId = $existingFamily->rt_id;
                $rwId = $existingFamily->rw_id;
                $dusunId = $existingFamily->dusun_id;
                $isNewFamily = false;
            } else {
                // NKK is new - create new family (use input address)
                $newNKK = $validated['nkk_baru'];
                $alamat = $validated['alamat'] ?? $penduduk->alamat; // Gunakan alamat dari form atau alamat lama
                // Untuk KK baru dalam desa, gunakan RT/RW dari form
                $rtId = $validated['rt_id'] ?? $penduduk->rt_id;
                $rwId = $validated['rw_id'] ?? $penduduk->rw_id;
                $dusunId = $validated['dusun_id'] ?? $penduduk->dusun_id;
                $isNewFamily = true;
            }
        } else {
            // Gunakan NKK tujuan dari input user (for keluar desa)
            $newNKK = $validated['nkk_tujuan'];
            $alamat = $validated['alamat'];
            // Untuk kategori luar desa/kota/negeri, tidak perlu RT/RW (soft delete)
            $rtId = null;
            $rwId = null;
            $dusunId = null;
            $isNewFamily = true;
        }

        // Ambil snapshot sebelum perubahan untuk kebutuhan rollback/undo
        $snapshotAsal = [
            'nkk_asal' => $penduduk->nkk,
            'alamat_asal' => $penduduk->alamat,
            'rt_id_asal' => $penduduk->rt_id,
            'rt_kode_asal' => optional($penduduk->rtMaster)->kode,
            'rw_id_asal' => $penduduk->rw_id,
            'rw_kode_asal' => optional($penduduk->rwMaster)->kode,
            'dusun_id_asal' => $penduduk->dusun_id,
            'dusun_nama_asal' => optional($penduduk->dusunMaster)->nama,
            'kedudukan_asal' => $penduduk->kedudukan_keluarga,
            'status_perkawinan_asal' => $penduduk->status_perkawinan,
        ];

        // Simpan juga data anggota yang ikut pindah (snapshot sebelum berubah)
        $anggotaPindahSnapshot = [];
        $anggotaIds = $validated['anggota_pisah_ids'] ?? ($validated['move_members'] ?? []);
        if (!empty($anggotaIds)) {
            $anggotaPindah = Penduduk::whereIn('id', $anggotaIds)->get();
            $anggotaPindahSnapshot = $anggotaPindah->map(function ($anggota) {
                return [
                    'id' => $anggota->id,
                    'nama' => $anggota->nama,
                    'nkk_asal' => $anggota->nkk,
                    'rt_id_asal' => $anggota->rt_id,
                    'rt_kode_asal' => optional($anggota->rtMaster)->kode,
                    'rw_id_asal' => $anggota->rw_id,
                    'rw_kode_asal' => optional($anggota->rwMaster)->kode,
                    'dusun_id_asal' => $anggota->dusun_id,
                    'dusun_nama_asal' => optional($anggota->dusunMaster)->nama,
                    'alamat_asal' => $anggota->alamat,
                ];
            })->toArray();
        }
        $snapshotAsal['anggota_pindah'] = $anggotaPindahSnapshot;

        // Update the main person to new KK
        if ($validated['kategori_mutasi'] === 'dalam_desa') {
            // Untuk dalam desa, update semua field termasuk RT/RW/Dusun
            $penduduk->update([
                'nkk' => $newNKK,
                'kedudukan_keluarga' => $validated['kedudukan_keluarga_pisah'],
                'status_perkawinan' => $validated['status_perkawinan_pisah'] ?? $penduduk->status_perkawinan,
                'alamat' => $alamat,
                'rt_id' => $rtId,
                'rw_id' => $rwId,
                'dusun_id' => $dusunId,
            ]);
        }
        // Untuk luar desa/kota/negeri, TIDAK update data penduduk sama sekali
        // Data akan di-soft delete tanpa perubahan, sehingga pas undo bisa kembali ke kondisi asli

        // Move selected family members if any
        $movedCount = 1;
        if (!empty($anggotaIds)) {
            if ($validated['kategori_mutasi'] === 'dalam_desa') {
                // Untuk dalam desa, update semua field termasuk RT/RW/Dusun
                // Use models instead of query builder to trigger observers
                Penduduk::whereIn('id', $anggotaIds)->get()->each(function ($anggota) use ($newNKK, $alamat, $rtId, $rwId, $dusunId) {
                    $anggota->update([
                        'nkk' => $newNKK,
                        'alamat' => $alamat,
                        'rt_id' => $rtId,
                        'rw_id' => $rwId,
                        'dusun_id' => $dusunId,
                    ]);
                });
            }
            // Untuk luar desa/kota/negeri, TIDAK anggota keluarga sama sekali
            // Mereka akan di-soft delete tanpa perubahan
            $movedCount += count($anggotaIds);
        }

        // Create mutation log
        $asalTujuan = "";
        if ($validated['kategori_mutasi'] === 'dalam_desa') {
            if (($validated['kk_option'] ?? '') === 'existing' && ($validated['nkk_existing'] ?? '')) {
                $asalTujuan = "Pisah dari KK {$oldNKK} dan gabung ke KK {$newNKK} (dalam desa)";
            } elseif (($validated['kk_option'] ?? '') === 'new' && ($validated['nkk_baru'] ?? '')) {
                if ($isNewFamily) {
                    $asalTujuan = "Pisah dari KK {$oldNKK} ke KK {$newNKK} baru (dalam desa)";
                } else {
                    $asalTujuan = "Pisah dari KK {$oldNKK} dan gabung ke KK {$newNKK} yang sudah ada (dalam desa)";
                }
            } else {
                $asalTujuan = "Pisah dari KK {$oldNKK} ke KK {$newNKK} baru (dalam desa)";
            }
        } else {
            // Untuk kategori luar kota/desa/luar negeri, gunakan alamat sebagai tujuan
            $tujuan = $validated['alamat'] ?? 'Tidak diketahui';
            $asalTujuan = "Pisah dari KK {$oldNKK} - {$tujuan}";
        }

        // Untuk kategori luar desa, tambahkan informasi tracking
        if (in_array($validated['kategori_mutasi'], ['dalam_kota', 'luar_kota', 'luar_negeri'])) {
            $snapshotAsal['tracking'] = [
                'nkk_tujuan' => $newNKK,
                'alamat_tujuan' => $validated['alamat'] ?? 'Tidak diketahui',
                'kategori_pindah' => $validated['kategori_mutasi'],
                'tanggal_pindah' => $validated['tanggal_mutasi'],
            ];
        }

        // Debug logging
        Log::info('Pisah KK - Data sebelum mutasi disimpan', ['data' => $snapshotAsal]);

        Mutasi::create([
            'penduduk_id' => $penduduk->id,
            'jenis_mutasi' => 'pisah_kk',
            'kategori_mutasi' => $validated['kategori_mutasi'],
            'asal_tujuan' => $asalTujuan,
            'tanggal_mutasi' => $validated['tanggal_mutasi'],
            'alasan' => $validated['alasan'] ?? "Pisah KK - {$penduduk->nama} menjadi kepala keluarga baru ({$movedCount} anggota)",
            'dokumen_pendukung' => null,
            'detail_tambahan' => [
                'snapshot_asal' => $snapshotAsal, // Simpan dalam key 'snapshot_asal' konsisten
            ],
        ]);

        // Soft delete penduduk jika pindah keluar desa/kota
        if (in_array($validated['kategori_mutasi'], ['dalam_kota', 'luar_kota', 'luar_negeri'])) {
            $penduduk->delete();

            // Soft delete anggota keluarga yang ikut pindah juga
            if (!empty($anggotaIds)) {
                Penduduk::whereIn('id', $anggotaIds)->delete();
            }
        }
    }

}
