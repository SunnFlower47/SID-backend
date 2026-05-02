<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BantuanSosial;
use App\Models\PenerimaBantuanSosial;
use App\Models\Penduduk;
use Illuminate\Support\Facades\Validator;
use App\Helpers\DataSanitizer;
use Exception;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BantuanSosialController extends Controller
{
    /**
     * Get bantuan sosial programs
     */
    public function index()
    {
        $bantuanSosials = BantuanSosial::aktif()
            ->withCount('penerima')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bantuanSosials
        ]);
    }

    /**
     * Check bantuan sosial by NIK
     */
    public function checkByNik(Request $request)
    {
        // Debug logging untuk input
        Log::info('Bantuan sosial check request', [
            'nik' => $request->nik,
            'tanggal_lahir' => $request->tanggal_lahir,
            'captcha_answer' => $request->captcha_answer,
            'captcha_question' => $request->captcha_question,
            'all_data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|size:16|regex:/^[0-9]+$/',
            'tanggal_lahir' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 400);
        }

        $penduduk = Penduduk::where('nik', $request->nik)->first();

        if (!$penduduk) {
            return response()->json([
                'success' => false,
                'message' => 'NIK tidak ditemukan dalam database'
            ], 404);
        }

        // Validasi tanggal lahir - KEAMANAN PENTING!
        if ($penduduk->tanggal_lahir) {
            // Convert database Carbon object to Y-m-d format for comparison
            $dbDate = Carbon::parse($penduduk->tanggal_lahir)->format('Y-m-d');
            $requestDate = $request->tanggal_lahir;

            // Debug logging
            Log::info('Tanggal lahir validation', [
                'nik' => $request->nik,
                'db_tanggal_lahir' => $dbDate,
                'request_tanggal_lahir' => $requestDate,
                'db_type' => gettype($penduduk->tanggal_lahir),
                'request_type' => gettype($request->tanggal_lahir)
            ]);

            if ($dbDate != $requestDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal lahir dan nik tidak sesuai dengan data yang tersimpan. Pastikan tanggal lahir dan nik sudah benar.'
                ], 400);
            }
        }

        $bantuanSosials = PenerimaBantuanSosial::with('bantuanSosial')
            ->where('penduduk_id', $penduduk->id)
            ->where('status_penerimaan', 'aktif')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'penduduk' => [
                    'nama' => $penduduk->nama,
                    // Zero Trust: Sensor NIK langsung dari Server
                    'nik' => substr($penduduk->nik, 0, 4) . '****' . substr($penduduk->nik, -4),
                    // Zero Trust: Sembunyikan detail alamat jalan, hanya tampilkan RT/RW/Dusun
                    'alamat' => 'Alamat Tersensor (Verifikasi NIK & Tgl Lahir Berhasil)',
                    'rt' => $penduduk->rt_id,
                    'rw' => $penduduk->rw_id,
                    'dusun' => $penduduk->dusun->nama ?? 'N/A',
                ],
                'bantuan_sosials' => $bantuanSosials->map(function($item) {
                    return [
                        'program' => $item->bantuanSosial->nama_program,
                        'jenis' => $item->bantuanSosial->jenis_bantuan,
                        'nilai' => $item->nilai_diterima_formatted,
                        'tanggal_penerimaan' => $item->tanggal_penerimaan ? date('d/m/Y', strtotime($item->tanggal_penerimaan)) : null,
                        'status' => $item->status_penerimaan_label,
                        'nomor_kartu' => $item->nomor_kartu
                    ];
                })
            ]
        ]);
    }

    /**
     * Generate simple CAPTCHA question
     */
    /**
     * Parse math expression safely (only basic operations) - REMOVED (using reCAPTCHA)
     */
}
