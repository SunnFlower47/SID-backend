<?php

namespace App\Services;

use App\Models\Mutasi;
use App\Models\Penduduk;
use App\Models\KartuKeluarga;
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
        // Ambil data keluarga (Source of Truth)
        $kk = KartuKeluarga::where('nkk', $validated['nkk'])->first();
        if (!$kk) {
            throw new \Exception('Tidak ditemukan data Kartu Keluarga dengan NKK: ' . $validated['nkk']);
        }

        // Buat penduduk baru (Hanya field unik, alamat & wilayah ikut KK)
        $penduduk = Penduduk::create([
            'kartu_keluarga_id' => $kk->id,
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

        // Trigger recalculation for the family
        app(\App\Services\KartuKeluargaService::class)->recalculate($kk->id);

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
                    'nkk' => $penduduk->kartuKeluarga->nkk ?? $penduduk->nkk,
                    'alamat' => $penduduk->kartuKeluarga->alamat ?? $penduduk->alamat,
                    'rt_id' => $penduduk->kartuKeluarga->rt_id ?? $penduduk->rt_id,
                    'rt_kode' => $penduduk->rt_label,
                    'rw_id' => $penduduk->kartuKeluarga->rw_id ?? $penduduk->rw_id,
                    'rw_kode' => $penduduk->rw_label,
                    'dusun_id' => $penduduk->kartuKeluarga->dusun_id ?? $penduduk->dusun_id,
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
        return DB::transaction(function () use ($validated) {
            // 1. Determine which NKK to use and handle KK Source of Truth
            $nkkToUse = !empty($validated['nkk']) ? $validated['nkk'] : ($validated['nkk_new'] ?? null);
            
            $kk = KartuKeluarga::firstOrCreate(
                ['nkk' => $nkkToUse],
                [
                    'alamat' => $validated['alamat'] ?? 'Belum Diisi',
                    'rt_id' => $validated['rt_id'] ?? null,
                    'rw_id' => $validated['rw_id'] ?? null,
                    'dusun_id' => $validated['dusun_id'] ?? null,
                    'nama_kepala_keluarga' => $validated['kedudukan_keluarga'] === 'Kepala Keluarga' ? $validated['nama'] : 'Belum Ditentukan',
                    'nik_kepala_keluarga' => $validated['kedudukan_keluarga'] === 'Kepala Keluarga' ? $validated['nik'] : null,
                ]
            );

            // 2. Create main person (Resident)
            $penduduk = Penduduk::create([
                'kartu_keluarga_id' => $kk->id,
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
                'keterangan' => $validated['alasan'] ?? null,
            ]);

            // 3. Create main person's mutation log
            Mutasi::create([
                'penduduk_id' => $penduduk->id,
                'jenis_mutasi' => 'pindah_masuk',
                'kategori_mutasi' => $validated['kategori_mutasi'],
                'asal_tujuan' => $validated['asal_tujuan'],
                'tanggal_mutasi' => $validated['tanggal_mutasi'],
                'alasan' => $validated['alasan'] ?? 'Pindah masuk ke Desa Cibatu',
            ]);

            // 4. Handle additional family members (BATCH)
            if (isset($validated['family_members']) && is_array($validated['family_members'])) {
                foreach ($validated['family_members'] as $member) {
                    if (empty($member['nik']) || empty($member['nama'])) continue;

                    $newMember = Penduduk::create([
                        'kartu_keluarga_id' => $kk->id,
                        'nik' => $member['nik'],
                        'nama' => $member['nama'],
                        'jenis_kelamin' => $member['jenis_kelamin'],
                        'tempat_lahir' => $member['tempat_lahir'] ?? $penduduk->tempat_lahir,
                        'tanggal_lahir' => $member['tanggal_lahir'] ?? null,
                        'agama' => $member['agama'] ?? $penduduk->agama,
                        'status_perkawinan' => $member['status_perkawinan'] ?? 'Belum Kawin',
                        'kedudukan_keluarga' => $member['kedudukan_keluarga'] ?? 'Anggota Keluarga',
                        'pendidikan' => $member['pendidikan'] ?? 'Tidak/Belum Sekolah',
                        'pekerjaan' => $member['pekerjaan'] ?? 'Belum Bekerja',
                        'nama_ayah' => $member['nama_ayah'] ?? null,
                        'nama_ibu' => $member['nama_ibu'] ?? null,
                    ]);

                    Mutasi::create([
                        'penduduk_id' => $newMember->id,
                        'jenis_mutasi' => 'pindah_masuk',
                        'kategori_mutasi' => $validated['kategori_mutasi'],
                        'asal_tujuan' => $validated['asal_tujuan'],
                        'tanggal_mutasi' => $validated['tanggal_mutasi'],
                        'alasan' => "Pindah masuk bersama keluarga ({$penduduk->nama})",
                    ]);
                }
            }

            // Trigger recalculation
            app(\App\Services\KartuKeluargaService::class)->recalculate($kk->id);

            return $penduduk;
        });
    }

    public function handlePindahKeluar(array $validated)
    {
        $penduduk = Penduduk::findOrFail($validated['penduduk_id']);
        
        $snapshotAsal = [
            'nkk' => $penduduk->kartuKeluarga->nkk ?? $penduduk->nkk,
            'alamat' => $penduduk->kartuKeluarga->alamat ?? $penduduk->alamat,
            'rt_id' => $penduduk->kartuKeluarga->rt_id ?? $penduduk->rt_id,
            'rt_kode' => $penduduk->rt_label,
            'rw_id' => $penduduk->kartuKeluarga->rw_id ?? $penduduk->rw_id,
            'rw_kode' => $penduduk->rw_label,
            'dusun_id' => $penduduk->kartuKeluarga->dusun_id ?? $penduduk->dusun_id,
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

        // Buat log mutasi UTAMA
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

        // Buat log mutasi untuk SETIAP ANGGOTA yang ikut pindah
        if (!empty($anggotaPindahIds)) {
            foreach ($anggotaPindah as $member) {
                Mutasi::create([
                    'penduduk_id' => $member->id,
                    'jenis_mutasi' => 'pindah_keluar',
                    'kategori_mutasi' => $validated['kategori_mutasi'],
                    'asal_tujuan' => $validated['asal_tujuan'],
                    'tanggal_mutasi' => $validated['tanggal_mutasi'],
                    'alasan' => "Pindah keluar bersama keluarga ({$penduduk->nama})",
                    'detail_tambahan' => [
                        'pindah_bersama' => $penduduk->nama,
                        'penduduk_utama_id' => $penduduk->id,
                    ],
                ]);
            }
        }

        // Soft delete penduduk utama
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

        // Ambil record KK (Source of Truth)
        $kk = KartuKeluarga::where('nkk', $validated['nkk'])->firstOrFail();

        // Ambil semua anggota keluarga berdasarkan ID relasi (Bukan teks NKK)
        $anggotaKeluarga = Penduduk::where('kartu_keluarga_id', $kk->id)->get();

        if ($anggotaKeluarga->isEmpty()) {
            throw new \Exception('Tidak ada anggota keluarga yang terhubung dengan Kartu Keluarga ID: ' . $kk->id);
        }

        $rtIdTujuan = $validated['rt_id_tujuan'];
        $rwIdTujuan = $validated['rw_id_tujuan'];
        $dusunIdTujuan = $validated['dusun_id_tujuan'] ?? null;

        // Ambil alamat dari anggota keluarga pertama jika alamat_tujuan kosong
        $alamatTujuan = $validated['alamat_tujuan'] ?? $anggotaKeluarga->first()->alamat;

        // Simpan informasi asal untuk log dan revert (Ambil langsung dari KK sebelum diupdate)
        $rtIdAsal = $kk->rt_id;
        $rwIdAsal = $kk->rw_id;
        $dusunIdAsal = $kk->dusun_id;
        $alamatAsal = $kk->alamat;

        // SIMPAN SNAPSHOT SEMUA ANGGOTA SEBELUM UPDATE (untuk cancel/revert)
        $snapshotAnggota = $anggotaKeluarga->map(function ($anggota) {
            return [
                'id' => $anggota->id,
                'nama' => $anggota->nama,
                'rt_id_asal' => $anggota->rt_id,
                'rt_kode_asal' => $anggota->rt_label,
                'rw_id_asal' => $anggota->rw_id,
                'rw_kode_asal' => $anggota->rw_label,
                'dusun_id_asal' => $anggota->dusun_id,
                'dusun_nama_asal' => $anggota->dusun_label,
                'alamat_asal' => $anggota->alamat,
            ];
        })->toArray();

        // UPDATE KARTU KELUARGA (Source of Truth)
        // Semua anggota otomatis ikut karena Accessor di model Penduduk
        $kk = KartuKeluarga::where('nkk', $validated['nkk'])->first();
        if ($kk) {
            $kk->update([
                'rt_id' => $rtIdTujuan,
                'rw_id' => $rwIdTujuan,
                'dusun_id' => $dusunIdTujuan,
                'alamat' => $alamatTujuan,
            ]);
            
            // Trigger recalculation untuk statistik terbaru
            app(\App\Services\KartuKeluargaService::class)->recalculate($kk->id);
        }

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
                    'alamat_asal' => $alamatAsal,
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

        // Determine new KK ID and NKK based on option
        $newKK = null;
        $newNKK = null;
        $isNewFamily = false;

        if ($validated['kategori_mutasi'] === 'dalam_desa' && ($validated['kk_option'] ?? '') === 'existing' && !empty($validated['nkk_existing_id'])) {
            // Join existing KK (Input nkk_existing_id usually is NKK string, let's find the ID)
            $newNKK = $validated['nkk_existing_id'];
            $newKK = KartuKeluarga::where('nkk', $newNKK)->first();
            $isNewFamily = false;
        } elseif ($validated['kategori_mutasi'] === 'dalam_desa' && ($validated['kk_option'] ?? '') === 'new' && !empty($validated['nkk_baru'])) {
            // Create or Find New KK record
            $newNKK = $validated['nkk_baru'];
            $newKK = KartuKeluarga::firstOrCreate(
                ['nkk' => $newNKK],
                [
                    'alamat' => $validated['alamat'] ?? $penduduk->alamat,
                    'rt_id' => $validated['rt_id'] ?? $penduduk->rt_id,
                    'rw_id' => $validated['rw_id'] ?? $penduduk->rw_id,
                    'dusun_id' => $validated['dusun_id'] ?? $penduduk->dusun_id,
                    'nama_kepala_keluarga' => $validated['kedudukan_keluarga_pisah'] === 'Kepala Keluarga' ? $penduduk->nama : 'Belum Ditentukan',
                    'nik_kepala_keluarga' => $validated['kedudukan_keluarga_pisah'] === 'Kepala Keluarga' ? $penduduk->nik : null,
                ]
            );
            $isNewFamily = $newKK->wasRecentlyCreated;
        } else {
            // For OUTSIDE VILLAGE mutation, we don't need a real KK record in our table
            $newNKK = $validated['nkk_tujuan'];
            $newKK = null;
            $isNewFamily = true;
        }

        // Ambil snapshot sebelum perubahan untuk kebutuhan rollback/undo
        $snapshotAsal = [
            'nkk_asal' => $penduduk->kartuKeluarga->nkk ?? $penduduk->nkk,
            'alamat_asal' => $penduduk->kartuKeluarga->alamat ?? $penduduk->alamat,
            'rt_id_asal' => $penduduk->rt_id,
            'rt_kode_asal' => $penduduk->rt_label,
            'rw_id_asal' => $penduduk->rw_id,
            'rw_kode_asal' => $penduduk->rw_label,
            'dusun_id_asal' => $penduduk->dusun_id,
            'dusun_nama_asal' => $penduduk->dusun_label,
            'kedudukan_asal' => $penduduk->kedudukan_keluarga,
            'status_perkawinan_asal' => $penduduk->status_perkawinan,
        ];

        // Simpan juga data anggota yang ikut pindah (snapshot sebelum berubah)
        $anggotaPindahSnapshot = [];
        $anggotaDataInput = $validated['anggota_pisah_data'] ?? []; // Expecting [{id, kedudukan_keluarga}, ...]
        $anggotaIds = collect($anggotaDataInput)->pluck('id')->toArray();

        if (!empty($anggotaIds)) {
            $anggotaPindah = Penduduk::whereIn('id', $anggotaIds)->get();
            $anggotaPindahSnapshot = $anggotaPindah->map(function ($anggota) {
                return [
                    'id' => $anggota->id,
                    'nama' => $anggota->nama,
                    'nkk_asal' => $anggota->nkk,
                    'rt_id_asal' => $anggota->rt_id,
                    'rt_kode_asal' => $anggota->rt_label,
                    'rw_id_asal' => $anggota->rw_id,
                    'rw_kode_asal' => $anggota->rw_label,
                    'dusun_id_asal' => $anggota->dusun_id,
                    'dusun_nama_asal' => $anggota->dusun_label,
                    'alamat_asal' => $anggota->alamat,
                    'kedudukan_asal' => $anggota->kedudukan_keluarga,
                ];
            })->toArray();
        }
        $snapshotAsal['anggota_pindah'] = $anggotaPindahSnapshot;

        // Update the main person to new KK
        if ($validated['kategori_mutasi'] === 'dalam_desa') {
            // Untuk dalam desa, update semua field termasuk kartu_keluarga_id
            $penduduk->update([
                'kartu_keluarga_id' => $newKK ? $newKK->id : null,
                'kedudukan_keluarga' => $validated['kedudukan_keluarga_pisah'],
                'status_perkawinan' => $validated['status_perkawinan_pisah'] ?? $penduduk->status_perkawinan,
            ]);
        }
        // Untuk luar desa/kota/negeri, TIDAK update data penduduk sama sekali
        // Data akan di-soft delete tanpa perubahan, sehingga pas undo bisa kembali ke kondisi asli

        // Move selected family members if any
        $movedCount = 1;
        if (!empty($anggotaDataInput)) {
            if ($validated['kategori_mutasi'] === 'dalam_desa') {
                foreach ($anggotaDataInput as $memberData) {
                    $mId = $memberData['id'] ?? null;
                    $mRole = $memberData['kedudukan_keluarga'] ?? 'ANGGOTA KELUARGA';
                    
                    if ($mId) {
                        Penduduk::where('id', $mId)->update([
                            'kartu_keluarga_id' => $newKK ? $newKK->id : null,
                            'kedudukan_keluarga' => $mRole
                        ]);
                    }
                }
            }
            $movedCount += count($anggotaDataInput);
        }

        // Trigger recalculation for both KKs
        $kkService = app(\App\Services\KartuKeluargaService::class);
        
        // 1. Recalculate Old KK (Source of Truth)
        $oldKKRecord = KartuKeluarga::where('nkk', $oldNKK)->first();
        if ($oldKKRecord) {
            $kkService->recalculate($oldKKRecord->id);
        }

        // 2. Recalculate New KK (if within village)
        if ($newKK) {
            $kkService->recalculate($newKK->id);
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

        // Buat log mutasi untuk SETIAP ANGGOTA yang ikut Pisah KK
        if (!empty($anggotaIds)) {
            $anggotaPindah = Penduduk::whereIn('id', $anggotaIds)->get();
            foreach ($anggotaPindah as $member) {
                Mutasi::create([
                    'penduduk_id' => $member->id,
                    'jenis_mutasi' => 'pisah_kk',
                    'kategori_mutasi' => $validated['kategori_mutasi'],
                    'asal_tujuan' => $asalTujuan,
                    'tanggal_mutasi' => $validated['tanggal_mutasi'],
                    'alasan' => "Ikut Pisah KK bersama ({$penduduk->nama})",
                    'detail_tambahan' => [
                        'pisah_bersama' => $penduduk->nama,
                        'penduduk_utama_id' => $penduduk->id,
                    ],
                ]);
            }
        }

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
