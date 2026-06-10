<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuratPengajuan;
use App\Models\DesaSetting;
use Illuminate\Http\Request;

class VerifikasiSuratApiController extends Controller
{
    /**
     * Verifikasi surat berdasarkan QR token
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify($token)
    {
        $surat = SuratPengajuan::with(['penduduk', 'suratType'])->where('qr_token', $token)->first();

        if (!$surat || !in_array($surat->status, ['selesai', 'diproses'])) {
            return response()->json([
                'success' => false,
                'message' => 'Surat tidak ditemukan atau belum disetujui.',
            ], 404);
        }

        $namaPemohon = $surat->penduduk ? $surat->penduduk->nama : 'Tidak diketahui';
        $maskedName = $this->maskName($namaPemohon);

        $desaInfo = DesaSetting::getDesaInfo();

        return response()->json([
            'success' => true,
            'message' => 'Surat Terverifikasi Asli',
            'data' => [
                'desa' => $desaInfo['nama_desa'] ?? 'Cibatu',
                'nomor_surat' => $surat->nomor_surat,
                'jenis_surat' => $surat->suratType ? $surat->suratType->nama : 'Surat Keterangan',
                'tanggal_surat' => $surat->tanggal_surat ? \Carbon\Carbon::parse($surat->tanggal_surat)->isoFormat('D MMMM Y') : null,
                'nama_pemohon' => $maskedName,
                'status' => 'Asli & Tercatat di Sistem',
            ]
        ]);
    }

    /**
     * Mask name for privacy
     */
    private function maskName($name)
    {
        $words = explode(' ', $name);
        $maskedWords = array_map(function($word) {
            if (strlen($word) <= 3) {
                return $word;
            }
            return substr($word, 0, 3) . str_repeat('*', strlen($word) - 3);
        }, $words);

        return implode(' ', $maskedWords);
    }
}
