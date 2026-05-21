<?php

namespace App\Services\Kependudukan;

use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use App\Models\Mutasi;
use App\Services\Kependudukan\KartuKeluargaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use App\Models\SuratPengajuan;
use App\Models\DesaSetting;

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
        try {
            $suratId = $validated['surat_pengajuan_id'] ?? null;

            if (!$suratId) {
                $suratTypeId = 'kematian';
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

            $detailMutasi = $mutasi->detail_tambahan;
            $detailMutasi['surat_pengajuan_id'] = $suratId;
            $mutasi->update(['detail_tambahan' => $detailMutasi]);

        } catch (\Exception $e) {
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
                $member->delete();
            }
        }

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

        $penduduk->delete();
    }

    /**
     * Handle Pindah RT/RW logic
     */
    public function handlePindahRTRW(array $validated)
    {
        if (empty($validated['nkk']) && !empty($validated['penduduk_id'])) {
            $validated['nkk'] = Penduduk::withTrashed()->findOrFail($validated['penduduk_id'])->nkk;
        }

        $kk = KartuKeluarga::where('nkk', $validated['nkk'])->firstOrFail();
        $anggotaKeluarga = Penduduk::where('kartu_keluarga_id', $kk->id)->get();

        if ($anggotaKeluarga->isEmpty()) {
            throw new \Exception('Tidak ada anggota keluarga yang terhubung dengan Kartu Keluarga ID: ' . $kk->id);
        }

        $rtIdTujuan = $validated['rt_id_tujuan'];
        $rwIdTujuan = $validated['rw_id_tujuan'];
        $dusunIdTujuan = $validated['dusun_id_tujuan'] ?? null;
        $alamatTujuan = $validated['alamat_tujuan'] ?? $anggotaKeluarga->first()->alamat;

        $rtIdAsal = $kk->rt_id;
        $rwIdAsal = $kk->rw_id;
        $dusunIdAsal = $kk->dusun_id;
        $alamatAsal = $kk->alamat;

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

        $kk = KartuKeluarga::where('nkk', $validated['nkk'])->first();
        if ($kk) {
            $kk->update([
                'rt_id' => $rtIdTujuan,
                'rw_id' => $rwIdTujuan,
                'dusun_id' => $dusunIdTujuan,
                'alamat' => $alamatTujuan,
            ]);
            
            $this->kkService->recalculate($kk->id);
        }

        $kepalaKeluarga = $anggotaKeluarga->where('kedudukan_keluarga', 'Kepala Keluarga')->first();

        $rtAsal = optional(\App\Models\Rt::find($rtIdAsal))->kode;
        $rwAsal = optional(\App\Models\Rw::find($rwIdAsal))->kode;
        $dusunAsal = optional(\App\Models\Dusun::find($dusunIdAsal))->nama;
        
        $rtTujuan = optional(\App\Models\Rt::find($rtIdTujuan))->kode;
        $rwTujuan = optional(\App\Models\Rw::find($rwIdTujuan))->kode;
        $dusunTujuan = optional(\App\Models\Dusun::find($dusunIdTujuan))->nama;

        $asalLengkap = 'RT ' . $rtAsal . '/RW ' . $rwAsal . ' (' . ($dusunAsal ?? '-') . ')';
        $tujuanLengkap = 'RT ' . $rtTujuan . '/RW ' . $rwTujuan . ' (' . ($dusunTujuan ?? '-') . ')';

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
        $penduduk = Penduduk::withTrashed()->findOrFail($validated['penduduk_id']);
        $oldNKK = $penduduk->nkk;

        $newKK = null;
        $newNKK = null;
        $isNewFamily = false;

        if ($validated['kategori_mutasi'] === 'dalam_desa' && ($validated['kk_option'] ?? '') === 'existing' && !empty($validated['nkk_existing_id'])) {
            $newNKK = $validated['nkk_existing_id'];
            $newKK = KartuKeluarga::where('nkk', $newNKK)->first();
            $isNewFamily = false;
        } elseif ($validated['kategori_mutasi'] === 'dalam_desa' && ($validated['kk_option'] ?? '') === 'new' && !empty($validated['nkk_baru'])) {
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
            $newNKK = $validated['nkk_tujuan'];
            $newKK = null;
            $isNewFamily = true;
        }

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

        $anggotaPindahSnapshot = [];
        $anggotaDataInput = $validated['anggota_pisah_data'] ?? [];
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

        $oldKKRecord = $penduduk->kartuKeluarga;

        if ($validated['kategori_mutasi'] === 'dalam_desa') {
            $penduduk->update([
                'kartu_keluarga_id' => $newKK ? $newKK->id : null,
                'kedudukan_keluarga' => $validated['kedudukan_keluarga_pisah'],
                'status_perkawinan' => $validated['status_perkawinan_pisah'] ?? $penduduk->status_perkawinan,
            ]);
        }

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

        if ($oldKKRecord) {
            $this->kkService->recalculate($oldKKRecord->id);
        }

        if ($newKK) {
            $this->kkService->recalculate($newKK->id);
        }

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
            $tujuan = $validated['alamat'] ?? 'Tidak diketahui';
            $asalTujuan = "Pisah dari KK {$oldNKK} - {$tujuan}";
        }

        if (in_array($validated['kategori_mutasi'], ['dalam_kota', 'luar_kota', 'luar_negeri'])) {
            $snapshotAsal['tracking'] = [
                'nkk_tujuan' => $newNKK,
                'alamat_tujuan' => $validated['alamat'] ?? 'Tidak diketahui',
                'kategori_pindah' => $validated['kategori_mutasi'],
                'tanggal_pindah' => $validated['tanggal_mutasi'],
            ];
        }

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
                'snapshot_asal' => $snapshotAsal,
            ],
        ]);

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

        if (in_array($validated['kategori_mutasi'], ['dalam_kota', 'luar_kota', 'luar_negeri'])) {
            $penduduk->delete();

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

            if ($mutasi->jenis_mutasi == 'pindah_rt_rw') {
                $snapshot = $mutasi->detail_tambahan['snapshot_asal'] ?? [];
                $nkk = $snapshot['nkk'] ?? null;
                if ($nkk) {
                    $kk = KartuKeluarga::where('nkk', $nkk)->first();
                    if ($kk) {
                        $kk->update([
                            'rt_id' => $snapshot['rt_id_asal'] ?? $kk->rt_id,
                            'rw_id' => $snapshot['rw_id_asal'] ?? $kk->rw_id,
                            'dusun_id' => $snapshot['dusun_id_asal'] ?? $kk->dusun_id,
                            'alamat' => $snapshot['alamat_asal'] ?? $kk->alamat,
                        ]);
                        $this->kkService->recalculate($kk->id);
                    }
                }
            }

            if ($mutasi->jenis_mutasi === 'pisah_kk' && $mutasi->kategori_mutasi === 'dalam_desa') {
                $snapshot = $mutasi->detail_tambahan['snapshot_asal'] ?? [];
                $penduduk = $mutasi->penduduk;

                if ($penduduk) {
                    $originalKkNkk = $snapshot['nkk_asal'] ?? null;
                    $originalKk = $originalKkNkk ? KartuKeluarga::where('nkk', $originalKkNkk)->first() : null;

                    if ($originalKk) {
                        $penduduk->update([
                            'kartu_keluarga_id' => $originalKk->id,
                            'kedudukan_keluarga' => $snapshot['kedudukan_asal'] ?? $penduduk->kedudukan_keluarga,
                            'status_perkawinan' => $snapshot['status_perkawinan_asal'] ?? $penduduk->status_perkawinan,
                        ]);
                        $this->kkService->recalculate($originalKk->id);
                    }

                    $anggotaSnapshot = $snapshot['anggota_pindah'] ?? [];
                    foreach ($anggotaSnapshot as $anggotaData) {
                        $anggota = Penduduk::find($anggotaData['id'] ?? null);
                        if ($anggota && $originalKk) {
                            $anggota->update([
                                'kartu_keluarga_id' => $originalKk->id,
                                'kedudukan_keluarga' => $anggotaData['kedudukan_asal'] ?? $anggota->kedudukan_keluarga,
                            ]);
                        }
                    }
                }
            }

            $mutasi->forceDelete();
        });
    }

    /**
     * Handle Mutasi Undo (for permanent types)
     */
    public function undoMutasi(Mutasi $mutasi)
    {
        return DB::transaction(function () use ($mutasi) {
            $snapshot = $mutasi->detail_tambahan['snapshot_asal'] ?? [];
            $penduduk = Penduduk::withTrashed()->find($mutasi->penduduk_id);

            if (!$penduduk) {
                throw new \Exception('Data penduduk tidak ditemukan untuk proses Undo.');
            }

            if (in_array($mutasi->jenis_mutasi, ['kematian', 'pindah_keluar'])) {
                $penduduk->restore();

                $originalKkNkk = $snapshot['nkk'] ?? null;
                if ($originalKkNkk) {
                    $originalKk = KartuKeluarga::where('nkk', $originalKkNkk)->first();
                    if ($originalKk) {
                        $penduduk->update([
                            'kartu_keluarga_id' => $originalKk->id,
                            'kedudukan_keluarga' => $snapshot['kedudukan'] ?? $penduduk->kedudukan_keluarga,
                        ]);
                        $this->kkService->recalculate($originalKk->id);
                    }
                }

                $anggotaSnapshot = $snapshot['anggota_pindah'] ?? [];
                foreach ($anggotaSnapshot as $anggotaData) {
                    $anggota = Penduduk::withTrashed()->find($anggotaData['id'] ?? null);
                    if ($anggota) {
                        $anggota->restore();
                        if ($originalKk ?? false) {
                            $anggota->update(['kartu_keluarga_id' => $originalKk->id]);
                        }
                    }
                }
            }

            $mutasi->forceDelete();

            return $penduduk->fresh();
        });
    }
}
