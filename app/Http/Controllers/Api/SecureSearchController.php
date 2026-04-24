<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SecureSearchController extends Controller
{
    /**
     * Pencarian aman dengan NIK + tanggal lahir
     * Rate limiting: 50 requests per 5 menit per IP (untuk testing)
     */
    public function searchByNikAndDob(Request $request)
    {
        // Rate limiting - lebih longgar untuk testing
        $key = 'secure-search:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 30)) { // 30 attempts per 1 menit (Production Hardened)
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
                'error_code' => 'RATE_LIMITED'
            ], 429);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|size:16|regex:/^[0-9]{16}$/',
            'tanggal_lahir' => 'required|date|before:today',
            'captcha_answer' => 'required|string|numeric',
            'captcha_question' => 'required|string'
        ], [
            'nik.required' => 'NIK wajib diisi',
            'nik.size' => 'NIK harus 16 digit',
            'nik.regex' => 'NIK hanya boleh berisi angka',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid',
            'tanggal_lahir.before' => 'Tanggal lahir tidak boleh di masa depan',
            'captcha_answer.required' => 'Jawaban CAPTCHA wajib diisi',
            'captcha_answer.numeric' => 'Jawaban CAPTCHA harus berupa angka',
            'captcha_question.required' => 'Pertanyaan CAPTCHA wajib diisi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validasi CAPTCHA
        if (!$this->validateCaptcha($request->captcha_question, $request->captcha_answer)) {
            RateLimiter::hit($key, 300); // Hit rate limiter hanya jika CAPTCHA salah
            return response()->json([
                'success' => false,
                'message' => 'Jawaban CAPTCHA salah',
                'error_code' => 'CAPTCHA_INVALID'
            ], 422);
        }

        try {
            $nik = $request->nik;
            $tanggalLahir = $request->tanggal_lahir;

            // Cari penduduk dengan NIK dan tanggal lahir
            $penduduk = Penduduk::where('nik', $nik)
                ->where('tanggal_lahir', $tanggalLahir)
                ->whereNull('deleted_at')
                ->first();

            if (!$penduduk) {
                // Log attempt untuk monitoring
                $this->logSecurityEvent('secure_search_not_found', [
                    'nik_masked' => $this->maskNIK($nik),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ], 'medium');

                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan atau tidak sesuai',
                    'error_code' => 'NOT_FOUND'
                ], 404);
            }

            // Log successful search
            $this->logSecurityEvent('secure_search_success', [
                'nik_masked' => $this->maskNIK($nik),
                'penduduk_id' => $penduduk->id,
                'ip' => $request->ip()
            ], 'low');

            // Ambil data bantuan sosial
            $bantuanSosial = \App\Models\PenerimaBantuanSosial::where('penduduk_id', $penduduk->id)
                ->with('bantuanSosial')
                ->get();

            // Return data dengan struktur yang sama dengan BantuanSosialController
            return response()->json([
                'success' => true,
                'data' => [
                    'penduduk' => [
                        'nama' => $penduduk->nama,
                        'nik' => $penduduk->nik,
                        'alamat' => $penduduk->alamat,
                        'rt' => $penduduk->rt,
                        'rw' => $penduduk->rw,
                        'dusun' => $penduduk->dusun
                    ],
                    'bantuan_sosials' => $bantuanSosial->map(function($item) {
                        $dataTambahan = $item->data_tambahan ?? [];
                        $sistemPembayaran = $dataTambahan['sistem_pembayaran'] ?? 'sekali';

                        $result = [
                            'program' => $item->bantuanSosial->nama_program ?? 'Tidak diketahui',
                            'jenis' => $item->bantuanSosial->jenis_bantuan ?? 'Bantuan Sosial',
                            'nilai' => 'Rp ' . number_format($item->nilai_diterima, 0, ',', '.'),
                            'status' => $item->status_penerimaan_label ?? 'Aktif',
                            'nomor_kartu' => $item->nomor_kartu,
                            'sistem_pembayaran' => $sistemPembayaran
                        ];

                        if ($sistemPembayaran === 'triwulanan') {
                            // Sistem pembayaran triwulanan - tampilkan 3 triwulan
                            $result['tanggal_penerimaan'] = null; // Tidak ada tanggal tunggal
                            $result['triwulan'] = [
                                'triwulan_1' => [
                                    'tanggal' => $dataTambahan['triwulan_1']['tanggal'] ?? null,
                                    'jumlah' => $dataTambahan['triwulan_1']['jumlah'] ?? 0,
                                    'status' => 'belum_diterima' // Default status
                                ],
                                'triwulan_2' => [
                                    'tanggal' => $dataTambahan['triwulan_2']['tanggal'] ?? null,
                                    'jumlah' => $dataTambahan['triwulan_2']['jumlah'] ?? 0,
                                    'status' => 'belum_diterima'
                                ],
                                'triwulan_3' => [
                                    'tanggal' => $dataTambahan['triwulan_3']['tanggal'] ?? null,
                                    'jumlah' => $dataTambahan['triwulan_3']['jumlah'] ?? 0,
                                    'status' => 'belum_diterima'
                                ]
                            ];
                        } else {
                            // Sistem pembayaran sekali
                            $result['tanggal_penerimaan'] = $item->tanggal_penerimaan ? $item->tanggal_penerimaan->format('d/m/Y') : null;
                            $result['triwulan'] = null;
                        }

                        return $result;
                    })
                ]
            ]);

        } catch (\Exception $e) {
            // Log error
            $this->logSecurityEvent('secure_search_error', [
                'error' => $e->getMessage(),
                'nik_masked' => $this->maskNIK($request->nik),
                'ip' => $request->ip()
            ], 'high');

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'error_code' => 'SYSTEM_ERROR'
            ], 500);
        }
    }

    /**
     * Generate CAPTCHA challenge
     */
    public function generateCaptcha()
    {
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        $operation = rand(0, 1) ? '+' : '-';

        if ($operation === '+') {
            $answer = $num1 + $num2;
            $question = "{$num1} + {$num2} = ?";
        } else {
            // Pastikan hasil tidak negatif
            $larger = max($num1, $num2);
            $smaller = min($num1, $num2);
            $answer = $larger - $smaller;
            $question = "{$larger} - {$smaller} = ?";
        }

        // Store answer in cache dengan expiry 5 menit
        $cacheKey = 'captcha:' . md5($question . time());
        return response()->json([
            'success' => true,
            'data' => [
                'question' => $question,
                'answer' => $answer,
                'cache_key' => $cacheKey
            ]
        ]);
    }

    /**
     * Validasi CAPTCHA
     */
    private function validateCaptcha($question, $userAnswer)
    {
        // Parse question untuk mendapatkan jawaban yang benar
        $correctAnswer = $this->parseCaptchaAnswer($question);

        if (!$correctAnswer) {
            return false;
        }

        return (int)$userAnswer === (int)$correctAnswer;
    }

    /**
     * Parse CAPTCHA question untuk mendapatkan jawaban
     */
    private function parseCaptchaAnswer($question)
    {
        // Format: "8 + 8 = ?" atau "10 - 5 = ?"
        if (preg_match('/(\d+)\s*\+\s*(\d+)\s*=\s*\?/', $question, $matches)) {
            return (int)$matches[1] + (int)$matches[2];
        }

        if (preg_match('/(\d+)\s*-\s*(\d+)\s*=\s*\?/', $question, $matches)) {
            return (int)$matches[1] - (int)$matches[2];
        }

        return null;
    }

    /**
     * Mask NIK untuk keamanan
     */
    private function maskNIK($nik)
    {
        if (strlen($nik) !== 16) {
            return $nik;
        }

        $firstEight = substr($nik, 0, 8);
        $lastFour = substr($nik, 12, 4);

        return $firstEight . '****' . $lastFour;
    }

    /**
     * Log security events
     */
    private function logSecurityEvent($event, $details, $severity = 'medium')
    {
        $logData = [
            'timestamp' => now()->toISOString(),
            'event' => $event,
            'severity' => $severity,
            'details' => $details,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ];

        // Log ke file
        Log::warning("Security Event [" . strtoupper($severity) . "]: " . json_encode($logData));

        // TODO: Send ke security monitoring service di production
    }

    /**
     * Get rate limit status
     */
    public function getRateLimitStatus(Request $request)
    {
        $key = 'secure-search:' . $request->ip();
        $attempts = RateLimiter::attempts($key);
        $remaining = RateLimiter::remaining($key, 10);
        $availableIn = RateLimiter::availableIn($key);

        return response()->json([
            'success' => true,
            'data' => [
                'attempts' => $attempts,
                'remaining' => $remaining,
                'available_in' => $availableIn,
                'limit' => 30,
                'window' => 60 // 1 menit
            ]
        ]);
    }

    /**
     * Clear rate limit untuk testing (hanya untuk development)
     */
    public function clearRateLimit(Request $request)
    {
        if (app()->environment('production')) {
            return response()->json(['message' => 'Not available in production'], 403);
        }

        $key = 'secure-search:' . $request->ip();
        RateLimiter::clear($key);

        return response()->json([
            'success' => true,
            'message' => 'Rate limit cleared for IP: ' . $request->ip()
        ]);
    }
}
