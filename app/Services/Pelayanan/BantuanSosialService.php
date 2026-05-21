<?php

namespace App\Services\Pelayanan;

use App\Models\BantuanSosial;
use App\Models\PenerimaBantuanSosial;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BantuanSosialService
{
    /**
     * Statistik ringkasan untuk dashboard / header card.
     */
    public function getStats(): array
    {
        return [
            'total_program'  => BantuanSosial::count(),
            'program_aktif'  => BantuanSosial::aktif()->count(),
            'total_penerima' => PenerimaBantuanSosial::count(),
            'penerima_aktif' => PenerimaBantuanSosial::where('status_penerimaan', 'aktif')
                ->whereHas('bantuanSosial', fn ($q) => $q->aktif())
                ->count(),
        ];
    }

    /**
     * Build payload record penerima berdasarkan sistem pembayaran.
     * Memisahkan kalkulasi bisnis dari controller agar dapat diuji mandiri.
     *
     * @param  Request|array  $input  Data input dari form
     * @param  int            $pendudukId
     * @return array  Data siap simpan ke tabel penerima_bantuan_sosials
     */
    public function buildPenerimaPayload($input, int $pendudukId): array
    {
        // Support both Request object and plain array
        $get = fn ($key) => is_array($input) ? ($input[$key] ?? null) : $input->$key;

        $sistemPembayaran = $get('sistem_pembayaran');

        if ($sistemPembayaran === 'sekali') {
            return [
                'penduduk_id'        => $pendudukId,
                'nilai_diterima'     => $get('nilai_diterima'),
                'tanggal_penerimaan' => $get('tanggal_penerimaan'),
                'status_penerimaan'  => $get('status_penerimaan'),
                'keterangan'         => $get('keterangan'),
                'data_tambahan'      => json_encode(['sistem_pembayaran' => 'sekali']),
            ];
        }

        // Sistem pembayaran berkala: distribusi 4 tahap
        $total     = (float) $get('nilai_total_berkala');
        $perTahap  = (int) floor($total / 4);
        $remainder = (int) ($total % 4);

        return [
            'penduduk_id'        => $pendudukId,
            'nilai_diterima'     => $total,
            'tanggal_penerimaan' => $get('tanggal_tahap_1'),
            'status_penerimaan'  => $get('status_penerimaan'),
            'keterangan'         => $get('keterangan'),
            'data_tambahan'      => json_encode([
                'sistem_pembayaran' => 'berkala',
                'total_amount'      => $total,
                'tahap_1' => ['tanggal' => $get('tanggal_tahap_1'), 'jumlah' => $perTahap + ($remainder >= 1 ? 1 : 0)],
                'tahap_2' => ['tanggal' => $get('tanggal_tahap_2'), 'jumlah' => $perTahap + ($remainder >= 2 ? 1 : 0)],
                'tahap_3' => ['tanggal' => $get('tanggal_tahap_3'), 'jumlah' => $perTahap + ($remainder >= 3 ? 1 : 0)],
                'tahap_4' => ['tanggal' => $get('tanggal_tahap_4'), 'jumlah' => $perTahap],
            ]),
        ];
    }

    /**
     * Simpan banyak penerima sekaligus, dengan skip jika sudah terdaftar.
     * Handle DB transaction dan return ringkasan hasil operasi.
     *
     * @param  BantuanSosial  $bansos
     * @param  array          $pendudukIds  Array of penduduk ID
     * @param  mixed          $input        Request atau array payload
     * @return array          ['success' => n, 'skipped' => n]
     */
    public function storePenerima(BantuanSosial $bansos, array $pendudukIds, $input): array
    {
        $successCount = 0;
        $skipCount    = 0;

        DB::transaction(function () use ($bansos, $pendudukIds, $input, &$successCount, &$skipCount) {
            foreach ($pendudukIds as $pendudukId) {
                $alreadyExists = PenerimaBantuanSosial::where('bantuan_sosial_id', $bansos->id)
                    ->where('penduduk_id', $pendudukId)
                    ->exists();

                if ($alreadyExists) {
                    $skipCount++;
                    continue;
                }

                $payload = $this->buildPenerimaPayload($input, $pendudukId);
                $bansos->penerima()->create($payload);
                $successCount++;
            }
        });

        return ['success' => $successCount, 'skipped' => $skipCount];
    }

    /**
     * Update data penerima yang sudah ada.
     */
    public function updatePenerima(PenerimaBantuanSosial $penerima, $input): void
    {
        $get = fn ($key) => is_array($input) ? ($input[$key] ?? null) : $input->$key;

        $payload = $this->buildPenerimaPayload($input, $get('penduduk_id') ?? $penerima->penduduk_id);

        DB::transaction(fn () => $penerima->update($payload));
    }

    /**
     * Guard: cek apakah program masih menerima penerima baru/perubahan.
     */
    public function isEditable(BantuanSosial $bansos): bool
    {
        if ($bansos->status === 'selesai') {
            return false;
        }

        if ($bansos->tanggal_selesai && $bansos->tanggal_selesai->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Cek apakah penduduk menerima bantuan aktif berdasarkan NIK.
     */
    public function getBantuanByNik(string $nik): array
    {
        $penduduk = Penduduk::where('nik', $nik)->first();

        if (!$penduduk) {
            return ['found' => false, 'penduduk' => null, 'bantuan' => []];
        }

        $bantuan = PenerimaBantuanSosial::with('bantuanSosial')
            ->where('penduduk_id', $penduduk->id)
            ->where('status_penerimaan', 'aktif')
            ->get();

        return ['found' => true, 'penduduk' => $penduduk, 'bantuan' => $bantuan];
    }
}
