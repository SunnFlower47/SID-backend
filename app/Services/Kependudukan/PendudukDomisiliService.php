<?php

namespace App\Services\Kependudukan;

use App\Models\Penduduk;
use App\Models\PendudukDomisili;
use App\Models\SuratPengajuan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PendudukDomisiliService
{
    /**
     * Validasi NIK: hard block jika sudah terdaftar sebagai penduduk tetap.
     *
     * @throws \Exception
     */
    public function validateNikNotPermanent(string $nik): void
    {
        $isPermanent = Penduduk::where('nik', $nik)->exists();
        if ($isPermanent) {
            throw new \Exception("NIK {$nik} sudah terdaftar sebagai penduduk tetap. Tidak dapat didaftarkan sebagai penduduk domisili.");
        }
    }

    /**
     * Validasi NIK: tidak boleh duplikat di domisili yang masih aktif.
     * $excludeId dipakai saat update agar tidak blok diri sendiri.
     *
     * @throws \Exception
     */
    public function validateNikNotActiveInDomisili(string $nik, ?int $excludeId = null): void
    {
        $query = PendudukDomisili::where('nik', $nik)->where('status', 'aktif');
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        if ($query->exists()) {
            throw new \Exception("NIK {$nik} sudah terdaftar dengan status domisili AKTIF. Cabut atau perpanjang data yang lama terlebih dahulu.");
        }
    }

    /**
     * Buat atau perbarui (perpanjang otomatis) catatan domisili.
     */
    public function create(array $data): PendudukDomisili
    {
        return DB::transaction(function () use ($data) {
            $this->validateNikNotPermanent($data['nik']);

            // Cek apakah NIK ini sudah ada di database domisili
            $existing = PendudukDomisili::where('nik', $data['nik'])->first();

            $tanggalMasuk  = Carbon::parse($data['tanggal_masuk'] ?? now());
            $tanggalBerlaku = isset($data['tanggal_berlaku']) ? Carbon::parse($data['tanggal_berlaku']) : $tanggalMasuk->copy()->addMonths(3);

            if ($existing) {
                // Jika sudah ada, kita perbarui datanya (Auto-Renewal/Update)
                $existing->update(array_merge($data, [
                    'tanggal_berlaku' => $tanggalBerlaku->toDateString(),
                    'status'          => 'aktif', 
                    'perpanjangan_ke' => $existing->status === 'aktif' ? ($existing->perpanjangan_ke + 1) : 0,
                    'updated_at'      => now(),
                ]));
                $domisili = $existing;
                $keterangan = $existing->status === 'aktif' ? "Perpanjangan otomatis via pembuatan surat baru." : "Aktivasi kembali data domisili.";
            } else {
                // Jika benar-benar baru
                $domisili = PendudukDomisili::create(array_merge($data, [
                    'tanggal_berlaku' => $tanggalBerlaku->toDateString(),
                    'status'          => 'aktif',
                    'perpanjangan_ke' => 0,
                    'created_by'      => Auth::id(),
                ]));
                $keterangan = 'Pembuatan awal surat keterangan domisili.';
            }

            // Jika dipanggil dari StoreSuratAction, ID surat sudah ada
            $suratId = $data['surat_pengajuan_id'] ?? null;

            if ($suratId) {
                // Gunakan nomor surat dari surat yang sudah ada
                $surat = SuratPengajuan::find($suratId);
                $nomorSurat = $surat ? $surat->nomor_surat : null;
                $domisili->update([
                    'nomor_surat' => $nomorSurat,
                    'surat_pengajuan_id' => $suratId
                ]);
            } else {
                // Generate nomor surat baru (Jika dibuat langsung dari menu Domisili)
                $type = \App\Models\SuratType::find('keterangan-domisili');
                $kodeSurat = $type ? $type->kode : 'SKD';
                $nomorSurat = \App\Models\DesaSetting::generateNomorSurat($kodeSurat);
                
                $domisili->update(['nomor_surat' => $nomorSurat]);

                // Catat di tabel surat_pengajuans
                $surat = $this->createSuratRecord($domisili, $nomorSurat, $keterangan);
                $domisili->update(['surat_pengajuan_id' => $surat->id]);
            }

            return $domisili->refresh();
        });
    }

    /**
     * Update data domisili (hanya data pribadi & lokasi, bukan perpanjang).
     */
    public function update(PendudukDomisili $domisili, array $data): PendudukDomisili
    {
        return DB::transaction(function () use ($domisili, $data) {
            // Hanya re-validasi jika NIK berubah
            if (isset($data['nik']) && $data['nik'] !== $domisili->nik) {
                $this->validateNikNotPermanent($data['nik']);
                $this->validateNikNotActiveInDomisili($data['nik'], $domisili->id);
            }
            $domisili->update($data);
            return $domisili->refresh();
        });
    }

    /**
     * Perpanjang domisili +3 bulan dari tanggal_berlaku terakhir.
     */
    public function perpanjang(PendudukDomisili $domisili): PendudukDomisili
    {
        return DB::transaction(function () use ($domisili) {
            if ($domisili->status === 'dicabut') {
                throw new \Exception('Domisili yang sudah dicabut tidak dapat diperpanjang.');
            }

            // Hitung tanggal berlaku baru: +3 bulan dari HARI INI (saat dia perpanjang)
            // Ini untuk mengatasi kasus warga telat perpanjang berbulan-bulan
            $newTanggalBerlaku = now()->addMonths(3);

            $type = \App\Models\SuratType::find('keterangan-domisili');
            $kodeSurat = $type ? $type->kode : 'SKD';
            $nomorSurat = \App\Models\DesaSetting::generateNomorSurat($kodeSurat);

            $domisili->update([
                'tanggal_berlaku' => $newTanggalBerlaku->toDateString(),
                'status'          => 'aktif',
                'perpanjangan_ke' => $domisili->perpanjangan_ke + 1,
                'nomor_surat'     => $nomorSurat,
            ]);

            $keterangan = "Perpanjangan ke-{$domisili->perpanjangan_ke}. Berlaku s/d {$newTanggalBerlaku->format('d/m/Y')}.";
            $this->createSuratRecord($domisili, $nomorSurat, $keterangan);

            return $domisili->refresh();
        });
    }

    /**
     * Cabut domisili secara manual.
     */
    public function cabut(PendudukDomisili $domisili, string $alasan): PendudukDomisili
    {
        return DB::transaction(function () use ($domisili, $alasan) {
            if ($domisili->status === 'dicabut') {
                throw new \Exception('Domisili ini sudah dicabut sebelumnya.');
            }
            $domisili->update([
                'status'  => 'dicabut',
                'catatan' => "Dicabut oleh admin. Alasan: {$alasan}",
            ]);
            return $domisili->refresh();
        });
    }

    /**
     * Buat record di tabel surat_pengajuans (status langsung selesai).
     */
    private function createSuratRecord(PendudukDomisili $domisili, string $nomorSurat, string $keterangan): SuratPengajuan
    {
        return SuratPengajuan::create([
            'nik_pengaju'         => $domisili->nik,
            'nama_pengaju'        => $domisili->nama,
            'jenis_surat'         => 'keterangan-domisili',
            'nomor_surat'         => $nomorSurat,
            'keperluan'           => $domisili->keperluan_domisili ?? 'Keterangan Domisili',
            'tanggal_surat'       => now()->toDateString(),
            'keterangan_tambahan' => $keterangan,
            'status'              => 'selesai',
            'admin_id'            => Auth::id(),
            'approved_at'         => now(),
            'completed_at'        => now(),
            'data_tambahan'       => [
                'dm_nik'               => $domisili->nik,
                'dm_nama'              => $domisili->nama,
                'dm_tempat_lahir'      => $domisili->tempat_lahir,
                'dm_tanggal_lahir'     => $domisili->tanggal_lahir?->toDateString(),
                'dm_jenis_kelamin'     => $domisili->jenis_kelamin === 'L' ? 'Laki-laki' : ($domisili->jenis_kelamin === 'P' ? 'Perempuan' : $domisili->jenis_kelamin),
                'dm_agama'             => $domisili->agama,
                'dm_status_perkawinan' => $domisili->status_perkawinan,
                'dm_kewarganegaraan'   => $domisili->kewarganegaraan,
                'dm_pekerjaan'         => $domisili->pekerjaan,
                'dm_asal_daerah'       => $domisili->asal_daerah,
                'dm_alamat_asal'       => $domisili->alamat_asal,
                'dm_alamat_tinggal'    => $domisili->alamat_tinggal,
                'dm_rt'                => optional($domisili->rt)->kode,
                'dm_rw'                => optional($domisili->rw)->kode,
                'dm_dusun'             => optional($domisili->dusun)->nama,
                'dm_tanggal_masuk'     => $domisili->tanggal_masuk?->toDateString(),
                'dm_tanggal_berlaku'   => $domisili->tanggal_berlaku?->toDateString(),
                'dm_perpanjangan_ke'   => $domisili->perpanjangan_ke,
                'dm_keperluan'         => $domisili->keperluan_domisili,
            ],
        ]);
    }
}
