<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PublicApiProxyController extends Controller
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('API_KEY', 'desa-cibatu-2024-secure-key');
        $this->baseUrl = env('APP_URL', 'http://sistem-desa-cibatu.test') . '/api/v1';
    }

    /**
     * Proxy untuk check NIK (tanpa expose API key ke frontend)
     */
    public function checkNik(Request $request, $nik): JsonResponse
    {
        try {
            // Log akses API publik
            Log::info('Public API Access - Check NIK', [
                'endpoint' => $request->path(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'nik' => $nik,
            ]);

            // Validasi input NIK
            if (!preg_match('/^\d{16}$/', $nik)) {
                return response()->json([
                    'success' => false,
                    'message' => 'NIK harus berupa 16 digit angka',
                    'data' => ['is_valid' => false]
                ], 422);
            }

            // Call internal API dengan API key
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/check-nik/{$nik}");

            if ($response->successful()) {
                $data = $response->json();

                // Hanya return data yang aman untuk publik
                return response()->json([
                    'success' => $data['success'] ?? true,
                    'message' => $data['message'] ?? 'NIK valid',
                    'data' => [
                        'is_valid' => $data['data']['is_valid'] ?? false,
                        'nama' => $data['data']['nama'] ?? null,
                        'status' => $data['data']['status'] ?? null,
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal memvalidasi NIK',
                'data' => ['is_valid' => false]
            ], 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'NIK tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Public API Error - Check NIK', [
                'error' => $e->getMessage(),
                'nik' => $nik,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'data' => ['is_valid' => false]
            ], 500);
        }
    }

    /**
     * Proxy untuk search penduduk (tanpa expose data sensitif)
     */
    public function searchPenduduk(Request $request): JsonResponse
    {
        try {
            // Log akses API publik
            Log::info('Public API Access - Search Penduduk', [
                'endpoint' => $request->path(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'search_term' => $request->input('search_term'),
            ]);

            // Validasi input
            $request->validate([
                'search_term' => 'required|string|min:3|max:100',
            ]);

            // Call internal API dengan API key
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/search-penduduk", [
                'search_term' => $request->input('search_term'),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Filter data sensitif - hanya return data yang aman
                $safeData = collect($data['data'] ?? [])->map(function ($item) {
                    return [
                        'id' => $item['id'] ?? null,
                        'nama' => $item['nama'] ?? null,
                        'nik' => substr($item['nik'] ?? '', 0, 4) . '****' . substr($item['nik'] ?? '', -4), // Mask NIK
                        'jenis_kelamin' => $item['jenis_kelamin'] ?? null,
                        'tempat_lahir' => $item['tempat_lahir'] ?? null,
                        'tanggal_lahir' => $item['tanggal_lahir'] ?? null,
                        'alamat' => $item['alamat'] ?? null,
                        'rt' => $item['rt'] ?? null,
                        'rw' => $item['rw'] ?? null,
                    ];
                });

                return response()->json([
                    'success' => true,
                    'data' => $safeData,
                    'total' => $safeData->count()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari data penduduk',
                'data' => []
            ], 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data pencarian tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Public API Error - Search Penduduk', [
                'error' => $e->getMessage(),
                'search_term' => $request->input('search_term'),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'data' => []
            ], 500);
        }
    }

    /**
     * Proxy untuk get surat by NIK (tanpa expose data sensitif)
     */
    public function getSuratByNik(Request $request, $nik): JsonResponse
    {
        try {
            // Log akses API publik
            Log::info('Public API Access - Get Surat by NIK', [
                'endpoint' => $request->path(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'nik' => $nik,
            ]);

            // Validasi input
            $request->validate([
                'nik' => 'required|digits:16',
            ]);

            // Call internal API dengan API key
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/surat-pengajuan/nik/{$nik}");

            if ($response->successful()) {
                $data = $response->json();

                // Filter data sensitif - hanya return data yang aman
                $safeData = collect($data['data'] ?? [])->map(function ($item) {
                    return [
                        'id' => $item['id'] ?? null,
                        'nomor_surat' => $item['nomor_surat'] ?? null,
                        'jenis_surat' => $item['jenis_surat'] ?? null,
                        'status' => $item['status'] ?? null,
                        'tanggal_pengajuan' => $item['tanggal_pengajuan'] ?? null,
                        'tanggal_selesai' => $item['tanggal_selesai'] ?? null,
                        'keterangan' => $item['keterangan'] ?? null,
                    ];
                });

                return response()->json([
                    'success' => true,
                    'data' => $safeData,
                    'total' => $safeData->count()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak ada surat ditemukan',
                'data' => []
            ], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'NIK tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Public API Error - Get Surat by NIK', [
                'error' => $e->getMessage(),
                'nik' => $nik,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'data' => []
            ], 500);
        }
    }

    /**
     * Proxy untuk get surat by nomor surat
     */
    public function getSuratByNomor(Request $request, $nomorSurat): JsonResponse
    {
        try {
            // Log akses API publik
            Log::info('Public API Access - Get Surat by Nomor', [
                'endpoint' => $request->path(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'nomor_surat' => $nomorSurat,
            ]);

            // Call internal API dengan API key
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/surat-pengajuan/search", [
                'nomor_surat' => $nomorSurat,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Filter data sensitif
                $safeData = collect($data['data'] ?? [])->map(function ($item) {
                    return [
                        'id' => $item['id'] ?? null,
                        'nomor_surat' => $item['nomor_surat'] ?? null,
                        'jenis_surat' => $item['jenis_surat'] ?? null,
                        'status' => $item['status'] ?? null,
                        'tanggal_pengajuan' => $item['tanggal_pengajuan'] ?? null,
                        'tanggal_selesai' => $item['tanggal_selesai'] ?? null,
                        'keterangan' => $item['keterangan'] ?? null,
                    ];
                });

                return response()->json([
                    'success' => true,
                    'data' => $safeData,
                    'total' => $safeData->count()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Surat tidak ditemukan',
                'data' => []
            ], 404);

        } catch (\Exception $e) {
            Log::error('Public API Error - Get Surat by Nomor', [
                'error' => $e->getMessage(),
                'nomor_surat' => $nomorSurat,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'data' => []
            ], 500);
        }
    }

    /**
     * Proxy untuk get statistics (tanpa expose API key ke frontend)
     */
    public function getStatistics(Request $request): JsonResponse
    {
        try {
            // Log akses API publik
            Log::info('Public API Access - Get Statistics', [
                'endpoint' => $request->path(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Call internal API dengan API key
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/statistics");

            if ($response->successful()) {
                $data = $response->json();
                return response()->json($data);
            }

            // Log error response
            Log::error('Internal API Error - Statistics', [
                'status' => $response->status(),
                'body' => $response->body(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik',
                'data' => []
            ], 400);

        } catch (\Exception $e) {
            Log::error('Public API Error - Get Statistics', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'data' => []
            ], 500);
        }
    }

    /**
     * Proxy untuk get testimonials (tanpa data sensitif)
     */
    public function getTestimonials(Request $request): JsonResponse
    {
        try {
            // Log akses API publik
            Log::info('Public API Access - Get Testimonials', [
                'endpoint' => $request->path(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Call internal API dengan API key
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/testimoni", $request->all());

            if ($response->successful()) {
                $data = $response->json();

                // Filter data sensitif - hanya return data yang aman
                if (isset($data['data']) && is_array($data['data'])) {
                    $safeData = collect($data['data'])->map(function ($item) {
                        return [
                            'id' => $item['id'] ?? null,
                            'nama' => $item['is_anonymous'] ? 'Warga Anonim' : ($item['nama'] ?? 'Warga'),
                            'testimoni' => $item['testimoni'] ?? null,
                            'rating' => $item['rating'] ?? null,
                            'kategori' => $item['kategori'] ?? null,
                            'is_anonymous' => $item['is_anonymous'] ?? false,
                            'created_at' => $item['created_at'] ?? null,
                        ];
                    });

                    $data['data'] = $safeData;
                }

                return response()->json($data);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil testimoni',
                'data' => []
            ], 400);

        } catch (\Exception $e) {
            Log::error('Public API Error - Get Testimonials', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'data' => []
            ], 500);
        }
    }

    /**
     * Proxy untuk get struktur desa (tanpa data sensitif)
     */
    public function getStrukturDesa(Request $request): JsonResponse
    {
        try {
            // Log akses API publik
            Log::info('Public API Access - Get Struktur Desa', [
                'endpoint' => $request->path(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Call internal API dengan API key
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/struktur-desa", $request->all());

            if ($response->successful()) {
                $data = $response->json();

                // Filter data sensitif - hanya return data yang aman
                if (isset($data['data']) && is_array($data['data'])) {
                    $safeData = collect($data['data'])->map(function ($item) {
                        return [
                            'id' => $item['id'] ?? null,
                            'nama' => $item['nama'] ?? null,
                            'jabatan' => $item['jabatan'] ?? null,
                            'kategori' => $item['kategori'] ?? null,
                            'foto' => $item['foto'] ?? null,
                            'urutan' => $item['urutan'] ?? null,
                            'status' => $item['status'] ?? null,
                            // Hapus data sensitif seperti NIK, email, telepon
                        ];
                    });

                    $data['data'] = $safeData;
                }

                return response()->json($data);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil struktur desa',
                'data' => []
            ], 400);

        } catch (\Exception $e) {
            Log::error('Public API Error - Get Struktur Desa', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'data' => []
            ], 500);
        }
    }
}
