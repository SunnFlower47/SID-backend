<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiProxyController extends Controller
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = config('app.api_key');
    }

    /**
     * Proxy untuk semua request ke private API
     */
    public function proxy(Request $request, $path = '')
    {
        try {
            $method = strtoupper($request->method());
            
            // Ambil host asli dari request yang datang (lebih akurat daripada APP_URL)
            $host = $request->getSchemeAndHttpHost();
            $fullPath = $path ? "/{$path}" : '';
            $queryString = $request->getQueryString();
            
            // Build URL: Selalu pastikan formatnya adalah http://domain.test/api/v1/endpoint
            $internalUrl = $host . '/api/v1' . $fullPath . ($queryString ? "?{$queryString}" : '');

            // 1. GENERATE SIGNATURE
            $timestamp = (string)time();
            // Path yang akan dicocokkan oleh Middleware di sisi penerima
            $signaturePath = 'api/v1' . $fullPath;
            
            $message = $timestamp . $method . $signaturePath;
            $signature = hash_hmac('sha256', $message, $this->apiKey);

            // 2. Siapkan Headers
            $headers = [
                'Accept' => 'application/json',
                'X-Timestamp' => $timestamp,
                'X-Signature' => $signature,
                'X-Forwarded-For' => $request->ip(),
                'User-Agent' => $request->header('User-Agent'),
                'Origin' => $request->header('Origin'),
            ];

            // Teruskan token-token penting
            if ($request->header('X-Recaptcha-V3-Token')) $headers['X-Recaptcha-V3-Token'] = $request->header('X-Recaptcha-V3-Token');
            if ($request->header('X-Recaptcha-Token')) $headers['X-Recaptcha-Token'] = $request->header('X-Recaptcha-Token');
            if ($request->header('X-CSRF-Token')) $headers['X-CSRF-Token'] = $request->header('X-CSRF-Token');

            // 3. Eksekusi Request ke Internal API
            $response = Http::withHeaders($headers)
                ->withOptions(['verify' => false])
                ->timeout(30);

            if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
                $contentType = $request->header('Content-Type', 'application/json');
                
                if (strpos($contentType, 'multipart/form-data') !== false) {
                    $client = new \GuzzleHttp\Client(['verify' => false, 'timeout' => 30]);
                    $multipart = [];

                    // 1. Masukkan semua teks/field biasa
                    foreach ($request->except(array_keys($request->allFiles())) as $key => $value) {
                        $multipart[] = [
                            'name' => $key,
                            'contents' => (string)($value ?? '')
                        ];
                    }

                    // 2. Masukkan semua file
                    foreach ($request->allFiles() as $key => $file) {
                        if (is_array($file)) {
                            foreach ($file as $f) {
                                if ($f->isValid()) {
                                    $multipart[] = [
                                        'name' => $key,
                                        'contents' => fopen($f->getRealPath(), 'r'),
                                        'filename' => $f->getClientOriginalName()
                                    ];
                                }
                            }
                        } else {
                            if ($file->isValid()) {
                                $multipart[] = [
                                    'name' => $key,
                                    'contents' => fopen($file->getRealPath(), 'r'),
                                    'filename' => $file->getClientOriginalName()
                                ];
                            }
                        }
                    }

                    $response = $client->request($method, $internalUrl, [
                        'headers' => $headers,
                        'multipart' => $multipart,
                        'http_errors' => false,
                    ]);
                } else {
                    $response = Http::withHeaders($headers)
                        ->withOptions(['verify' => false])
                        ->withBody($request->getContent(), $contentType)
                        ->send($method, $internalUrl);
                }
            } else {
                $response = $response->send($method, $internalUrl);
            }

            // Ambil status dan body dari Guzzle response (PSR-7)
            $statusCode = ($response instanceof \Illuminate\Http\Client\Response) ? $response->status() : $response->getStatusCode();
            $responseBody = ($response instanceof \Illuminate\Http\Client\Response) ? $response->body() : (string)$response->getBody();

            // DEBUG LOG: Sangat membantu jika masih error
            if ($statusCode >= 400) {
                Log::error('Proxy Request Failed', [
                    'url' => $internalUrl,
                    'status' => $statusCode,
                    'response' => $responseBody
                ]);
            }

            return response($responseBody, $statusCode)
                ->header('Content-Type', 'application/json');

        } catch (\Exception $e) {
            Log::error('Proxy Critical Error', ['msg' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada koneksi internal.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function options(Request $request)
    {
        return response('', 200)
            ->withHeaders([
                'Access-Control-Allow-Origin' => $request->header('Origin', '*'),
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Accept, Origin, User-Agent, X-Recaptcha-V3-Token, X-Recaptcha-Token, X-CSRF-Token',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Max-Age' => '86400',
            ]);
    }
}
