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
            'captcha_answer' => 'required|string|numeric',
            'captcha_question' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 400);
        }

        // Validasi CAPTCHA - WAJIB!
        if (!$request->has('captcha_answer') || !$request->has('captcha_question')) {
            return response()->json([
                'success' => false,
                'message' => 'CAPTCHA wajib diisi'
            ], 400);
        }

        $captchaAnswer = $request->captcha_answer;
        $captchaQuestion = $request->captcha_question;

        // Simple math CAPTCHA validation
        if (!is_numeric($captchaAnswer)) {
            return response()->json([
                'success' => false,
                'message' => 'CAPTCHA tidak valid'
            ], 400);
        }

        // Parse math expression safely (only basic operations)
        $expectedAnswer = $this->safeMathEval($captchaQuestion);
        if ($captchaAnswer != $expectedAnswer) {
            return response()->json([
                'success' => false,
                'message' => 'CAPTCHA salah'
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
                    'nik' => $penduduk->nik, // Tidak perlu hash karena user sudah input NIK
                    'alamat' => $penduduk->alamat,
                    'rt' => $penduduk->rt_label,
                    'rw' => $penduduk->rw_label,
                    'dusun' => $penduduk->dusun_label
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
    public function generateCaptcha()
    {
        $operations = ['+', '-'];
        $operation = $operations[array_rand($operations)];

        switch ($operation) {
            case '+':
                $a = rand(1, 9);
                $b = rand(1, 9);
                return response()->json([
                    'success' => true,
                    'data' => [
                        'question' => "$a + $b",
                        'answer' => $a + $b
                    ]
                ]);
            case '-':
                $a = rand(5, 13);
                $b = rand(1, 4);
                return response()->json([
                    'success' => true,
                    'data' => [
                        'question' => "$a - $b",
                        'answer' => $a - $b
                    ]
                ]);
        }
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
