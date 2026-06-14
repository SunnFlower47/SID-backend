<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DesaSetting;
use Illuminate\Support\Facades\Cache;
use App\Traits\ApiResponse;

class DesaInfoApiController extends Controller
{
    use ApiResponse;

    /**
     * Get desa information
     */
    public function getDesaInfo()
    {
        return Cache::remember('api_desa_info_new', 15, function () {
            try {
                $desaInfo = DesaSetting::getDesaInfo();
                $kepalaDesa = DesaSetting::getKepalaDesaInfo();
                $sekretaris = DesaSetting::getSekretarisInfo();
                $logos = DesaSetting::getLogos();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'desa' => $desaInfo,
                        'kepala_desa' => $kepalaDesa,
                        'sekretaris' => $sekretaris,
                        'logos' => $logos,
                        'nama_desa' => $desaInfo['nama_desa'] ?? 'Desa Cibatu',
                        'kecamatan' => $desaInfo['kecamatan'] ?? 'Cibatu',
                        'kabupaten' => $desaInfo['kabupaten'] ?? 'Purwakarta',
                        'provinsi' => $desaInfo['provinsi'] ?? 'Jawa Barat',
                        'alamat' => $desaInfo['alamat_lengkap'] ?? 'Jl. Cibatu Km. 15, Desa Cibatu',
                        'kode_pos' => $desaInfo['kode_pos'] ?? '41151',
                        'telepon' => $desaInfo['telepon'] ?? '(0264) 123456',
                        'email' => $desaInfo['email'] ?? 'desa@cibatu.id',
                        'website' => $desaInfo['website'] ?? 'https://cibatu.desa.id'
                    ]
                ])->withHeaders([
                    'Cache-Control' => 'public, max-age=7200',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            } catch (\Exception $e) {
                return $this->errorResponse('Gagal mengambil informasi desa', 500, $e->getMessage());
            }
        });
    }

    /**
     * Get public desa info (nama desa + sosial media)
     */
    public function getPublicDesaInfo()
    {
        return Cache::remember('api_public_desa_info', 15, function () {
            try {
                $desaInfo = DesaSetting::getDesaInfo();

                $sanitizeUrl = function($url) {
                    if (empty($url)) return null;
                    $parsed = parse_url($url);
                    if (!isset($parsed['scheme']) || !in_array($parsed['scheme'], ['http', 'https'])) {
                        return null;
                    }
                    return filter_var($url, FILTER_SANITIZE_URL);
                };

                return response()->json([
                    'success' => true,
                    'data' => [
                        'nama_desa' => $desaInfo['nama_desa'] ?? 'Desa Cibatu',
                        'kecamatan' => $desaInfo['kecamatan'] ?? null,
                        'kabupaten' => $desaInfo['kabupaten'] ?? null,
                        'provinsi' => $desaInfo['provinsi'] ?? null,
                        'kode_pos' => $desaInfo['kode_pos'] ?? null,
                        'email' => $desaInfo['email'] ?? null,
                        'telepon' => $desaInfo['telepon'] ?? null,
                        'alamat' => $desaInfo['alamat_lengkap'] ?? null,
                        'logo_desa' => DesaSetting::getValue('logo_desa', null),
                        'visi' => DesaSetting::getValue('visi', null),
                        'misi' => DesaSetting::getValue('misi', null),
                        'sejarah' => DesaSetting::getValue('sejarah_desa', null),
                        'tahun_berdiri' => DesaSetting::getValue('tahun_berdiri', '1860'),
                        'kepala_desa_pertama' => DesaSetting::getValue('kepala_desa_pertama', 'Ki Arpan'),
                        'karakteristik_desa' => DesaSetting::getValue('karakteristik_desa', 'Industri'),
                        'latitude' => $desaInfo['latitude'] ?? null,
                        'longitude' => $desaInfo['longitude'] ?? null,
                        'social' => [
                            'facebook' => $sanitizeUrl(DesaSetting::getValue('link_facebook', '')),
                            'instagram' => $sanitizeUrl(DesaSetting::getValue('link_instagram', '')),
                            'whatsapp' => $sanitizeUrl(DesaSetting::getValue('link_whatsapp', '')),
                            'youtube' => $sanitizeUrl(DesaSetting::getValue('link_youtube', '')),
                        ],
                    ]
                ])->withHeaders([
                    'Cache-Control' => 'public, max-age=600',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            } catch (\Exception $e) {
                return $this->errorResponse('Gagal mengambil info desa', 500, $e->getMessage());
            }
        });
    }

    /**
     * Get contact information
     */
    public function getContactInfo()
    {
        try {
            $desaInfo = DesaSetting::getDesaInfo();

            return $this->successResponse([
                'alamat' => $desaInfo['alamat_lengkap'] ?? '',
                'telepon' => $desaInfo['telepon'] ?? '',
                'email' => $desaInfo['email'] ?? '',
                'website' => $desaInfo['website'] ?? '',
                'kode_pos' => $desaInfo['kode_pos'] ?? '',
                'kecamatan' => $desaInfo['kecamatan'] ?? '',
                'kabupaten' => $desaInfo['kabupaten'] ?? '',
                'provinsi' => $desaInfo['provinsi'] ?? ''
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil informasi kontak', 500, $e->getMessage());
        }
    }
    /**
     * Get GeoJSON batas wilayah desa
     * File disimpan di storage/app/public/geojson/ atau di S3/R2 dan dibaca server-side
     */
    public function getGeoJson()
    {
        return Cache::remember('api_geojson_batas_wilayah', 3600, function () {
            try {
                // Ambil path dari settings
                $storedUrl = DesaSetting::getValue('batas_wilayah_geojson', null);

                if (!$storedUrl) {
                    return response()->json([
                        'success' => false,
                        'message' => 'GeoJSON batas wilayah belum diupload.'
                    ], 404);
                }

                // Resolve relative path dari URL
                $path = parse_url($storedUrl, PHP_URL_PATH);
                if (!$path) {
                    return response()->json([
                        'success' => false,
                        'message' => 'URL GeoJSON tidak valid.'
                    ], 404);
                }

                $cleanPath = ltrim($path, '/');

                // Hapus nama bucket S3/R2 jika ada di segmen awal path
                $bucket = env('AWS_BUCKET');
                if ($bucket && (str_starts_with($cleanPath, $bucket . '/') || $cleanPath === $bucket)) {
                    $cleanPath = substr($cleanPath, strlen($bucket . '/'));
                }

                // Hapus 'storage/' jika menggunakan local public storage
                if (str_starts_with($cleanPath, 'storage/')) {
                    $cleanPath = substr($cleanPath, strlen('storage/'));
                }

                $geojsonContent = null;

                // Coba ambil dari disk default
                if (\Illuminate\Support\Facades\Storage::exists($cleanPath)) {
                    $geojsonContent = \Illuminate\Support\Facades\Storage::get($cleanPath);
                }
                // Coba ambil dari disk public (lokal)
                elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath)) {
                    $geojsonContent = \Illuminate\Support\Facades\Storage::disk('public')->get($cleanPath);
                }
                // Coba ambil dari disk s3 jika terkonfigurasi
                elseif (config('filesystems.disks.s3') && \Illuminate\Support\Facades\Storage::disk('s3')->exists($cleanPath)) {
                    $geojsonContent = \Illuminate\Support\Facades\Storage::disk('s3')->get($cleanPath);
                }
                // Jika semua gagal, coba ambil file secara langsung jika lokal, atau via HTTP jika URL luar
                else {
                    $absolutePath = storage_path('app/public/' . $cleanPath);
                    if (file_exists($absolutePath)) {
                        $geojsonContent = file_get_contents($absolutePath);
                    } elseif (filter_var($storedUrl, FILTER_VALIDATE_URL)) {
                        // Coba ambil via HTTP client jika itu URL valid (misal Cloudflare R2 / S3 public URL)
                        $response = \Illuminate\Support\Facades\Http::timeout(10)->get($storedUrl);
                        if ($response->successful()) {
                            $geojsonContent = $response->body();
                        }
                    }
                }

                if (!$geojsonContent) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File GeoJSON tidak ditemukan di server penyimpanan.'
                    ], 404);
                }

                $geojson = json_decode($geojsonContent, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File GeoJSON tidak valid.'
                    ], 422);
                }

                return response()->json([
                    'success' => true,
                    'data' => $geojson
                ])->withHeaders([
                    'Cache-Control' => 'public, max-age=3600',
                    'X-Content-Type-Options' => 'nosniff',
                ]);

            } catch (\Exception $e) {
                return $this->errorResponse('Gagal mengambil GeoJSON', 500, $e->getMessage());
            }
        });
    }
}
