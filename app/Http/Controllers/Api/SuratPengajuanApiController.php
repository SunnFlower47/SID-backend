<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuratPengajuan;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Helpers\DataSanitizer;
use Exception;

class SuratPengajuanApiController extends Controller
{
    /**
     * Submit pengajuan surat dari web-desa
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik_pengaju' => 'required|string|size:16',
            'nama_pengaju' => 'required|string|max:255',
            'email_pengaju' => 'nullable|email|max:255',
            'no_hp_pengaju' => 'nullable|string|max:20',
            'jenis_surat' => 'required|string|in:sku,sktm_dewasa,sktm_anak,domisili,kelahiran,kematian,pindah',
            'keperluan' => 'required|string|max:500',
            'tujuan' => 'nullable|string|max:255',
            'tanggal_surat' => 'required|date',
            'keterangan_tambahan' => 'nullable|string|max:1000',
            'data_tambahan' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cari penduduk berdasarkan NIK
        $penduduk = Penduduk::where('nik', $request->nik_pengaju)->first();
        if (!$penduduk) {
            return response()->json([
                'success' => false,
                'message' => 'Data penduduk dengan NIK tersebut tidak ditemukan'
            ], 404);
        }

        try {
            $suratPengajuan = SuratPengajuan::create([
                'jenis_surat' => $request->jenis_surat,
                'penduduk_id' => $penduduk->id,
                'nomor_surat' => $this->generateNomorSurat($request->jenis_surat),
                'keperluan' => $request->keperluan,
                'tujuan' => $request->tujuan,
                'tanggal_surat' => $request->tanggal_surat,
                'keterangan_tambahan' => $request->keterangan_tambahan,
                'data_tambahan' => json_encode($request->data_tambahan ?? []),
                'status' => 'pending',
                'nik_pengaju' => $request->nik_pengaju,
                'nama_pengaju' => $request->nama_pengaju,
                'email_pengaju' => $request->email_pengaju,
                'no_hp_pengaju' => $request->no_hp_pengaju
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan surat berhasil dikirim',
                'data' => [
                    'id' => $suratPengajuan->id,
                    'nomor_surat' => $suratPengajuan->nomor_surat ?? 'SP-' . str_pad($suratPengajuan->id, 6, '0', STR_PAD_LEFT),
                    'status' => $suratPengajuan->status,
                    'tanggal_pengajuan' => $suratPengajuan->created_at->format('d/m/Y H:i')
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan pengajuan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate nomor surat
     */
    private function generateNomorSurat($suratType)
    {
        $suratSettings = \App\Models\DesaSetting::getSuratSettings();
        $kodeSurat = $suratSettings["kode_surat_{$suratType}"] ?? 'SK';

        return \App\Models\DesaSetting::generateNomorSurat($kodeSurat);
    }

    /**
     * Cek validitas NIK penduduk
     */
    public function checkNik($nik)
    {
        if (strlen($nik) !== 16) {
            return response()->json([
                'valid' => false,
                'message' => 'NIK harus 16 digit'
            ], 422);
        }

        $penduduk = Penduduk::where('nik', $nik)->first();

        if (!$penduduk) {
            return response()->json([
                'valid' => false,
                'message' => 'NIK tidak ditemukan dalam database penduduk'
            ], 404);
        }

        return response()->json([
            'valid' => true,
            'message' => 'NIK valid',
            'data' => [
                'id' => $penduduk->id,
                'nik' => DataSanitizer::hashSensitiveData($penduduk->nik),
                'nama' => $penduduk->nama,
                'alamat' => $penduduk->alamat,
                'rt' => $penduduk->rt,
                'rw' => $penduduk->rw,
                'dusun' => $penduduk->dusun
            ]
        ]);
    }

    /**
     * Cek status pengajuan surat
     */
    public function checkStatus($id)
    {
        $suratPengajuan = SuratPengajuan::find($id);

        if (!$suratPengajuan) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan surat tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $suratPengajuan->id,
                'nomor_pengajuan' => 'SP-' . str_pad($suratPengajuan->id, 6, '0', STR_PAD_LEFT),
                'jenis_surat' => $suratPengajuan->jenis_surat,
                'status' => $suratPengajuan->status,
                'tanggal_pengajuan' => $suratPengajuan->created_at->format('d/m/Y H:i'),
                'tanggal_approve' => $suratPengajuan->updated_at->format('d/m/Y H:i'),
                'nomor_surat' => $suratPengajuan->nomor_surat,
                'keterangan_admin' => $suratPengajuan->keterangan_admin
            ]
        ]);
    }

    /**
     * Daftar pengajuan surat berdasarkan NIK
     */
    public function getByNik(Request $request, $nik)
    {
        // Validasi NIK format
        if (!preg_match('/^[0-9]{16}$/', $nik)) {
            return response()->json([
                'success' => false,
                'message' => 'Format NIK tidak valid'
            ], 400);
        }

        // Validasi CAPTCHA jika ada
        if ($request->has('captcha_answer') && $request->has('captcha_question')) {
            $captchaAnswer = $request->captcha_answer;
            $captchaQuestion = $request->captcha_question;

            if (!is_numeric($captchaAnswer)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CAPTCHA tidak valid'
                ], 400);
            }

            $expectedAnswer = $this->safeMathEval($captchaQuestion);
            if ($captchaAnswer != $expectedAnswer) {
                return response()->json([
                    'success' => false,
                    'message' => 'CAPTCHA salah'
                ], 400);
            }
        }

        $suratPengajuans = SuratPengajuan::where('nik_pengaju', $nik)
            ->with('penduduk')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $suratPengajuans->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nomor_surat' => $item->nomor_surat,
                    'jenis_surat' => $item->jenis_surat,
                    'status' => $item->status,
                    'tanggal_pengajuan' => $item->created_at->format('Y-m-d'),
                    'tanggal_surat' => $item->tanggal_surat,
                    'keperluan' => $item->keperluan,
                    'tujuan' => $item->tujuan,
                    'keterangan_tambahan' => $item->keterangan_tambahan,
                    'penduduk' => [
                        'nama' => $item->penduduk->nama ?? $item->nama_pengaju,
                        'nik' => $item->penduduk->nik ?? $item->nik_pengaju, // Tidak perlu hash karena user sudah input NIK
                        'alamat' => $item->penduduk->alamat ?? 'Alamat tidak tersedia'
                    ],
                    'created_at' => $item->created_at->toISOString(),
                    'updated_at' => $item->updated_at->toISOString()
                ];
            })
        ]);
    }

    /**
     * Mask NIK untuk keamanan data
     */
    private function maskNik($nik)
    {
        if (strlen($nik) !== 16) {
            return $nik;
        }

        // Tampilkan 4 digit pertama dan 4 digit terakhir, sisanya di-sensor
        return substr($nik, 0, 4) . '****' . substr($nik, -4);
    }

    /**
     * Cari surat berdasarkan nomor surat
     */
    public function getByNomorSurat(Request $request)
    {
        $nomorSurat = $request->query('nomor');

        if (!$nomorSurat) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter nomor surat diperlukan'
            ], 400);
        }

        $suratPengajuan = SuratPengajuan::where('nomor_surat', $nomorSurat)
            ->with('penduduk')
            ->first();

        if (!$suratPengajuan) {
            return response()->json([
                'success' => false,
                'message' => 'Surat dengan nomor tersebut tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                [
                    'id' => $suratPengajuan->id,
                    'nomor_surat' => $suratPengajuan->nomor_surat,
                    'jenis_surat' => $suratPengajuan->jenis_surat,
                    'status' => $suratPengajuan->status,
                    'tanggal_pengajuan' => $suratPengajuan->created_at->format('Y-m-d'),
                    'tanggal_surat' => $suratPengajuan->tanggal_surat,
                    'keperluan' => $suratPengajuan->keperluan,
                    'tujuan' => $suratPengajuan->tujuan,
                    'keterangan_tambahan' => $suratPengajuan->keterangan_tambahan,
                    'penduduk' => [
                        'nama' => $suratPengajuan->penduduk->nama ?? $suratPengajuan->nama_pengaju,
                        'nik' => $suratPengajuan->penduduk->nik ?? $suratPengajuan->nik_pengaju, // Tidak perlu hash karena user sudah input NIK
                        'alamat' => $suratPengajuan->penduduk->alamat ?? 'Alamat tidak tersedia'
                    ],
                    'created_at' => $suratPengajuan->created_at->toISOString(),
                    'updated_at' => $suratPengajuan->updated_at->toISOString()
                ]
            ]
        ]);
    }

    /**
     * Safe math evaluation for CAPTCHA
     */
    private function safeMathEval($expression)
    {
        // Remove all non-numeric and non-operator characters
        $expression = preg_replace('/[^0-9+\-*\/\(\)\s]/', '', $expression);

        // Only allow basic math operations
        if (!preg_match('/^[0-9+\-*\/\(\)\s]+$/', $expression)) {
            return 0;
        }

        // Use eval only for safe math expressions
        try {
            return eval('return ' . $expression . ';');
        } catch (Exception $e) {
            return 0;
        }
    }
}
