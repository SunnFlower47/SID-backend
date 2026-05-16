<?php

namespace App\Services;

use App\Models\Mutasi;
use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use App\Models\SuratPengajuan;
use App\Models\DesaSetting;
use App\Services\KartuKeluargaService;

class MutasiService
{
    protected $kkService;

    public function __construct(KartuKeluargaService $kkService)
    {
        $this->kkService = $kkService;
    }

    /**
     * Get Hierarchical Wilayah Tree (Dusun -> RW -> RT)
     */
    public function getWilayahTree()
    {
        $dusuns = \App\Models\Dusun::orderBy('nama')->get();
        $rws = \App\Models\Rw::with('rts')->orderBy('kode')->get();

        return $dusuns->map(function($dusun) use ($rws) {
            return [
                'id' => $dusun->id,
                'nama' => $dusun->nama,
                'rws' => $rws->filter(function($rw) use ($dusun) {
                    return $rw->rts->where('dusun_id', $dusun->id)->count() > 0;
                })->map(function($rw) use ($dusun) {
                    return [
                        'id' => $rw->id,
                        'kode' => $rw->kode,
                        'rts' => $rw->rts->where('dusun_id', $dusun->id)->map(function($rt) {
                            return [
                                'id' => $rt->id,
                                'kode' => $rt->kode
                            ];
                        })->values()
                    ];
                })->values()
            ];
        })->values();
    }

    /**
     * Get Master RW Options with nested RTs
     */
    public function getMasterRwOptions()
    {
        $rws = \App\Models\Rw::with(['rts.dusun'])->orderBy('kode')->get();
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
        $this->kkService->recalculate($kk->id);

        return $penduduk;
    }

    /**
     * Handle Kematian logic
     */
    public function handleKematian(array $validated)
    {
        $penduduk = Penduduk::withTrashed()->findOrFail($validated['penduduk_id']);

        // Buat log mutasi
        $mutasi = Mutasi::create([
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

        // OTOMATISASI: Buat record Surat Keterangan Kematian (SuratPengajuan)
        // Agar nomor surat ter-generate dan bisa langsung dicetak dari riwayat surat
        try {
            $suratId = $validated['surat_pengajuan_id'] ?? null;

            if (!$suratId) {
                $suratTypeId = 'kematian'; // ID dari tabel surat_types
                $nomorSurat = \App\Models\DesaSetting::generateNomorSurat($suratTypeId);

                $surat = \App\Models\SuratPengajuan::create([
                    'jenis_surat' => $suratTypeId,
                    'penduduk_id' => $penduduk->id, 
                    'nomor_surat' => $nomorSurat,
                    'tanggal_surat' => now(),
                    'status' => 'SELESAI', 
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                    'data_tambahan' => [
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
                        'alasan' => $validated['alasan'],
                        'pelapor_nama' => $validated['pelapor_nama'] ?? null,
                        'pelapor_umur' => $validated['pelapor_umur'] ?? null,
                        'pelapor_pekerjaan' => $validated['pelapor_pekerjaan'] ?? null,
                        'pelapor_alamat' => $validated['pelapor_alamat'] ?? null,
                        'pelapor_hubungan' => $validated['pelapor_hubungan'] ?? null,
                    ],
                    'keterangan_tambahan' => 'Dibuat otomatis melalui mutasi kematian'
                ]);
                $suratId = $surat->id;
            }

            // Hubungkan ID Surat ke Mutasi untuk kebutuhan Undo/Sync
            $detailMutasi = $mutasi->detail_tambahan;
            $detailMutasi['surat_pengajuan_id'] = $suratId;
            $mutasi->update(['detail_tambahan' => $detailMutasi]);

        } catch (\Exception $e) {
            // Log error tapi jangan gagalkan proses mutasi utama
            \Illuminate\Support\Facades\Log::error('Gagal memproses link surat kematian: ' . $e->getMessage());
        }

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
                'detail_tambahan' => [
                    'kk_option' => !empty($validated['nkk']) ? 'existing' : 'new',
                    'family_members' => $validated['family_members'] ?? []
                ]
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
            $this->kkService->recalculate($kk->id);

            return $penduduk;
        });
    }

    public function handlePindahKeluar(array $validated)
    {
        $penduduk = Penduduk::withTrashed()->findOrFail($validated['penduduk_id']);
        
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
            $validated['nkk'] = Penduduk::withTrashed()->findOrFail($validated['penduduk_id'])->nkk;
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
            $this->kkService->recalculate($kk->id);
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
        $penduduk = Penduduk::withTrashed()->findOrFail($validated['penduduk_id']);
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
        if ($oldKKRecord) {
            $this->kkService->recalculate($oldKKRecord->id);
        }

        // 2. Recalculate New KK (if within village)
        if ($newKK) {
            $this->kkService->recalculate($newKK->id);
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

    /**
     * Handle Mutasi Update
     */
    public function updateMutasi(Mutasi $mutasi, array $validated, $file = null)
    {
        return DB::transaction(function () use ($mutasi, $validated, $file) {
            $detailTambahan = $mutasi->detail_tambahan ?? [];

            // Update specific metadata based on type
            if ($mutasi->jenis_mutasi === 'kematian') {
                $detailTambahan['kematian'] = [
                    'hari' => $validated['hari_meninggal'],
                    'jam' => $validated['jam_meninggal'],
                    'bertempat_di' => $validated['bertempat_di'],
                    'tanggal' => $validated['tanggal_mutasi'],
                ];
                $detailTambahan['pemakaman'] = [
                    'hari' => $validated['hari_pemakaman'],
                    'tanggal' => $validated['tanggal_pemakaman'],
                    'jam' => $validated['jam_pemakaman'],
                    'lokasi' => $validated['lokasi_pemakaman'],
                ];
                $mutasi->asal_tujuan = $validated['bertempat_di'];
            } 
            elseif ($mutasi->jenis_mutasi === 'kelahiran') {
                if ($mutasi->penduduk) {
                    $mutasi->penduduk->update([
                        'nama' => $validated['nama_bayi'],
                        'nik' => $validated['nik_bayi'],
                        'jenis_kelamin' => $validated['jenis_kelamin_bayi'],
                        'tanggal_lahir' => $validated['tanggal_lahir'],
                        'tempat_lahir' => $validated['tempat_lahir'],
                    ]);
                }
            }
            elseif ($mutasi->jenis_mutasi === 'pisah_kk' || $mutasi->jenis_mutasi === 'pindah_keluar') {
                $snapshot = $detailTambahan['snapshot_asal'] ?? [];
                if (isset($validated['anggota_pisah_data'])) {
                    $snapshot['anggota_pindah'] = $validated['anggota_pisah_data'];
                }
                if (isset($validated['anggota_pindah'])) {
                    $members = Penduduk::whereIn('id', $validated['anggota_pindah'])->get()->map(function($m) {
                        return [
                            'id' => $m->id,
                            'nama' => $m->nama,
                            'nik' => $m->nik,
                            'kedudukan' => $m->kedudukan_keluarga
                        ];
                    });
                    $snapshot['anggota_pindah'] = $members;
                }
                $detailTambahan['snapshot_asal'] = $snapshot;
            }

            // Handle file upload
            if ($file) {
                if ($mutasi->dokumen_pendukung) {
                    Storage::delete($mutasi->dokumen_pendukung);
                }
                $mutasi->dokumen_pendukung = $file->store('mutasi-documents');
            }

            $mutasi->tanggal_mutasi = $validated['tanggal_mutasi'];
            $mutasi->alasan = $validated['alasan'];
            $mutasi->kategori_mutasi = $validated['kategori_mutasi'] ?? $mutasi->kategori_mutasi;
            $mutasi->asal_tujuan = $validated['asal_tujuan'] ?? $mutasi->asal_tujuan;
            $mutasi->detail_tambahan = $detailTambahan;
            $mutasi->save();

            return $mutasi;
        });
    }

    /**
     * Handle Mutasi Cancel
     */
    public function cancelMutasi(Mutasi $mutasi)
    {
        return DB::transaction(function () use ($mutasi) {
            // Guard conditions
            if ($mutasi->jenis_mutasi == 'pisah_kk' && !in_array($mutasi->kategori_mutasi, ['dalam_desa'])) {
                throw new \Exception('Mutasi ini tidak bisa dibatalkan. Gunakan tombol Undo untuk mengembalikan data.');
            }

            if (in_array($mutasi->jenis_mutasi, ['kematian', 'pindah_keluar', 'pembaruan_kk'])) {
                throw new \Exception('Mutasi ini tidak bisa dibatalkan. Gunakan tombol Undo untuk mengembalikan data.');
            }

            if ($mutasi->jenis_mutasi == 'pindah_masuk' || $mutasi->jenis_mutasi == 'kelahiran') {
                $penduduk = $mutasi->penduduk;
                if ($penduduk) {
                    $penduduk->forceDelete();
                }
            }

            // Revert data Pindah RT/RW dari snapshot
            if ($mutasi->jenis_mutasi == 'pindah_rt_rw') {
                $snapshot = $mutasi->detail_tambahan['snapshot_asal'] ?? null;
                if ($snapshot && !empty($snapshot['nkk'])) {
                    $kk = KartuKeluarga::where('nkk', $snapshot['nkk'])->first();
                    if ($kk) {
                        $kk->update([
                            'rt_id' => $snapshot['rt_id_asal'] ?? $kk->rt_id,
                            'rw_id' => $snapshot['rw_id_asal'] ?? $kk->rw_id,
                            'dusun_id' => $snapshot['dusun_id_asal'] ?? $kk->dusun_id,
                            'alamat' => $snapshot['alamat_asal'] ?? $kk->alamat,
                        ]);
                    }
                }
            }

            // Revert data Pisah KK dalam_desa dari snapshot
            if ($mutasi->jenis_mutasi == 'pisah_kk' && $mutasi->kategori_mutasi === 'dalam_desa') {
                $snapshot = $mutasi->detail_tambahan['snapshot_asal'] ?? null;
                if ($snapshot) {
                    $penduduk = Penduduk::find($mutasi->penduduk_id);
                    if ($penduduk && !empty($snapshot['nkk_asal'])) {
                        $originalKk = KartuKeluarga::where('nkk', $snapshot['nkk_asal'])->first();
                        if ($originalKk) {
                            $penduduk->update([
                                'kartu_keluarga_id' => $originalKk->id,
                                'kedudukan_keluarga' => $snapshot['kedudukan_asal'] ?? $penduduk->kedudukan_keluarga,
                            ]);
                        }
                    }
                    if (!empty($snapshot['anggota_pindah'])) {
                        foreach ($snapshot['anggota_pindah'] as $anggotaData) {
                            $anggota = Penduduk::find($anggotaData['id']);
                            if ($anggota && !empty($anggotaData['nkk_asal'])) {
                                $originalKk = KartuKeluarga::where('nkk', $anggotaData['nkk_asal'])->first();
                                if ($originalKk) {
                                    $anggota->update([
                                        'kartu_keluarga_id' => $originalKk->id,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            $mutasi->forceDelete();
            return true;
        });
    }

    /**
     * Handle Mutasi Undo
     */
    public function undoMutasi(Mutasi $mutasi)
    {
        if ($mutasi->detail_tambahan['kk_sudah_diselesaikan'] ?? false) {
            throw new \Exception('Undo tidak dapat dilakukan. KK dari mutasi ini sudah diselesaikan secara permanen.');
        }

        return DB::transaction(function () use ($mutasi) {
            // Rollback KK Sementara
            $kkSementaraId = $mutasi->detail_tambahan['kk_sementara_id'] ?? null;
            $kkSementaraAsal = $mutasi->detail_tambahan['kk_sementara_kedudukan_asal'] ?? null;
            if ($kkSementaraId && $kkSementaraAsal) {
                $kkSementara = Penduduk::find($kkSementaraId);
                if ($kkSementara) {
                    $kkSementara->update(['kedudukan_keluarga' => $kkSementaraAsal]);
                }
                $pembaruanKkMutasi = Mutasi::where('penduduk_id', $kkSementaraId)
                    ->where('jenis_mutasi', 'pembaruan_kk')
                    ->latest('id')
                    ->first();

                if ($pembaruanKkMutasi) {
                    $detPem = $pembaruanKkMutasi->detail_tambahan ?? [];
                    if (($detPem['tipe'] ?? null) === 'sementara') {
                        $pembaruanKkMutasi->forceDelete();
                    }
                }
                $pendudukKk = $kkSementara?->kartuKeluarga;
                if ($pendudukKk && in_array($pendudukKk->status_kk, ['bermasalah', 'bermasalah_sementara'])) {
                    $pendudukKk->update(['kk_sementara_id' => null]);
                }
            }

            $snapshot = $mutasi->detail_tambahan['snapshot_asal'] ?? null;
            $oldKK = null;
            if ($snapshot && !empty($snapshot['nkk_asal'])) {
                $oldKK = KartuKeluarga::where('nkk', $snapshot['nkk_asal'])->first();
            }

            $currentKKIdOfPenduduk = null;
            $pendudukForRecalc = Penduduk::find($mutasi->penduduk_id);
            if ($pendudukForRecalc) {
                $currentKKIdOfPenduduk = $pendudukForRecalc->kartu_keluarga_id;
            }

            // HANDLER: Pembaruan KK
            if ($mutasi->jenis_mutasi === 'pembaruan_kk') {
                $penduduk = Penduduk::find($mutasi->penduduk_id);
                if ($penduduk) {
                    $kedudukanAsal = $mutasi->detail_tambahan['kedudukan_asal'] ?? 'ANGGOTA';
                    $penduduk->update(['kedudukan_keluarga' => $kedudukanAsal]);
                }
                $nkk = $mutasi->detail_tambahan['nkk'] ?? null;
                if ($nkk) {
                    $kk = KartuKeluarga::where('nkk', $nkk)->first();
                    if ($kk) {
                        $kk->update([
                            'status_kk' => 'bermasalah',
                            'kk_sementara_id' => null,
                            'catatan_bermasalah' => 'Dikembalikan via Undo mutasi pembaruan KK',
                            'kk_bermasalah_sejak' => now(),
                        ]);
                        if ($kk->mutasi_penyebab_id) {
                            $mutasiPenyebab = Mutasi::find($kk->mutasi_penyebab_id);
                            if ($mutasiPenyebab) {
                                $detail = $mutasiPenyebab->detail_tambahan;
                                unset($detail['kk_sementara_id'], $detail['kk_sementara_kedudukan_asal']);
                                $mutasiPenyebab->update(['detail_tambahan' => $detail]);
                            }
                        }
                        $this->kkService->recalculate($kk->id);
                    }
                }
                $mutasi->forceDelete();
                return true;
            }

            // HANDLER: Kematian / Pindah Keluar / Pisah KK
            if (in_array($mutasi->jenis_mutasi, ['kematian', 'pindah_keluar', 'pisah_kk'])) {
                $suratId = $mutasi->detail_tambahan['surat_pengajuan_id'] ?? null;
                if ($mutasi->jenis_mutasi === 'kematian') {
                    $surat = $suratId ? SuratPengajuan::find($suratId) : SuratPengajuan::where('penduduk_id', $mutasi->penduduk_id)->where('jenis_surat', 'kematian')->whereIn('status', ['SELESAI', 'selesai', 'completed'])->latest()->first();
                    if ($surat) $surat->delete();
                }

                $penduduk = Penduduk::withTrashed()->find($mutasi->penduduk_id);
                if ($penduduk) {
                    if ($penduduk->trashed()) $penduduk->restore();
                    $kedudukanRestore = $snapshot['kedudukan_asal'] ?? $penduduk->kedudukan_keluarga;
                    if ($oldKK && $penduduk->kartu_keluarga_id != $oldKK->id) {
                        $penduduk->update(['kartu_keluarga_id' => $oldKK->id, 'kedudukan_keluarga' => $kedudukanRestore]);
                    } else {
                        $penduduk->update(['kedudukan_keluarga' => $kedudukanRestore]);
                    }
                }

                if ($snapshot && !empty($snapshot['anggota_pindah'])) {
                    foreach ($snapshot['anggota_pindah'] as $anggotaData) {
                        $anggota = Penduduk::withTrashed()->find($anggotaData['id']);
                        if ($anggota) {
                            if ($anggota->trashed()) $anggota->restore();
                            if ($oldKK && $anggota->kartu_keluarga_id != $oldKK->id) {
                                $anggota->update(['kartu_keluarga_id' => $oldKK->id, 'kedudukan_keluarga' => $anggotaData['kedudukan_asal'] ?? $anggota->kedudukan_keluarga]);
                            }
                        }
                    }
                }
            }

            // HANDLER: Pindah RT/RW
            if ($mutasi->jenis_mutasi == 'pindah_rt_rw') {
                if ($snapshot && $oldKK) {
                    $oldKK->update([
                        'rt_id' => $snapshot['rt_id_asal'] ?? $oldKK->rt_id,
                        'rw_id' => $snapshot['rw_id_asal'] ?? $oldKK->rw_id,
                        'dusun_id' => $snapshot['dusun_id_asal'] ?? $oldKK->dusun_id,
                        'alamat' => $snapshot['alamat_asal'] ?? $oldKK->alamat,
                    ]);
                }
            }

            if ($oldKK) $this->kkService->recalculate($oldKK->id);
            if ($currentKKIdOfPenduduk && (!$oldKK || $currentKKIdOfPenduduk != $oldKK->id)) $this->kkService->recalculate($currentKKIdOfPenduduk);

            $mutasi->forceDelete();
            return true;
        });
    }
}
