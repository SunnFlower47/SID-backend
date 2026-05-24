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
        return Cache::remember('api_desa_info_new', 7200, function () {
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
                return $this->errorResponse('Gagal mengambil informasi desa', $e->getMessage(), 500);
            }
        });
    }

    /**
     * Get public desa info (nama desa + sosial media)
     */
    public function getPublicDesaInfo()
    {
        return Cache::remember('api_public_desa_info', 600, function () {
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
                        'email' => $desaInfo['email'] ?? null,
                        'telepon' => $desaInfo['telepon'] ?? null,
                        'alamat' => $desaInfo['alamat_lengkap'] ?? null,
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
                return $this->errorResponse('Gagal mengambil info desa', null, 500);
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
            return $this->errorResponse('Gagal mengambil informasi kontak', $e->getMessage(), 500);
        }
    }
}
