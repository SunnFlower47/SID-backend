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
        // 0. VALIDATE HANDSHAKE (Mastikan yang minta tanda tangan adalah Frontend Resmi kita)
        $clientKey = $request->header('X-Proxy-App-Id');
        $serverKey = env('PROXY_CLIENT_KEY', 'CIBATU_VIBE_2026');

        if (!$clientKey || $clientKey !== $serverKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized Access. Proxy Handshake Failed.',
            ], 403);
        }

        try {
            $method = strtoupper($request->method());
            $uri = $request->getRequestUri();
            
            // Industrial-grade path extraction
            $pattern = '/proxy\/v1\/?/';
            $fullPath = '/';
            if (preg_match($pattern, $uri, $matches, PREG_OFFSET_CAPTURE)) {
                $pos = $matches[0][1] + strlen($matches[0][0]);
                $remaining = substr($uri, $pos);
                $pathParts = explode('?', $remaining);
                $fullPath = '/' . ltrim($pathParts[0], '/');
            }

            $queryString = $request->getQueryString();
            // Resolve base URL from config for flexibility
            $baseUrl = config('app.url', 'http://sistem-desa-cibatu.test');
            $internalUrl = rtrim($baseUrl, '/') . '/api/v1' . $fullPath . ($queryString ? "?{$queryString}" : '');

            // Signature Handshake
            $timestamp = (string)time();
            $signature = $this->generateSignature($method, $fullPath, $timestamp);

            $headers = [
                'Accept' => 'application/json',
                'X-Timestamp' => $timestamp,
                'X-Signature' => $signature,
                'X-Forwarded-For' => $request->ip(),
                'User-Agent' => $request->header('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Cibatu-Proxy/1.0'),
                'Origin' => 'http://sistem-desa-cibatu.test',
            ];

            // Teruskan token-token penting
            if ($request->header('X-Recaptcha-V3-Token')) $headers['X-Recaptcha-V3-Token'] = $request->header('X-Recaptcha-V3-Token');
            if ($request->header('X-Recaptcha-Token')) $headers['X-Recaptcha-Token'] = $request->header('X-Recaptcha-Token');
            if ($request->header('X-CSRF-Token')) $headers['X-CSRF-Token'] = $request->header('X-CSRF-Token');

            // 3. Eksekusi Request ke Internal API secara INTERNAL (Mencegah Deadlock di artisan serve)
            $internalRequest = Request::create(
                $internalUrl,
                $method,
                $request->all(),
                $request->cookies->all(),
                $request->allFiles(),
                $request->server()
            );

            // Copy header penting ke request internal
            foreach ($headers as $key => $value) {
                $internalRequest->headers->set($key, $value);
            }

            // Jika ada raw body JSON, set ke content request
            if (in_array($method, ['POST', 'PUT', 'PATCH']) && !empty($request->getContent())) {
                // Duplicate request dengan body json
                $internalRequest = $internalRequest->duplicate(
                    null, null, null, null, null, null, $request->getContent()
                );
            }

            // Handle secara internal tanpa keluar dari PHP process!
            $response = app()->handle($internalRequest);

            // DEBUG LOG: Sangat membantu jika masih error
            if ($response->getStatusCode() >= 400) {
                Log::error('Proxy Request Failed', [
                    'url' => $internalUrl,
                    'status' => $response->getStatusCode(),
                    'response' => $response->getContent()
                ]);
            }

            return $response;

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
                'Access-Control-Allow-Headers' => 'Content-Type, Accept, Origin, User-Agent, X-Recaptcha-V3-Token, X-Recaptcha-Token, X-CSRF-Token, X-Proxy-App-Id',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Max-Age' => '86400',
            ]);
    }

    /**
     * Generate secure HMAC signature for internal handshake
     */
    private function generateSignature($method, $fullPath, $timestamp)
    {
        $signaturePath = 'api/v1' . $fullPath;
        $message = $timestamp . $method . $signaturePath;
        return hash_hmac('sha256', $message, $this->apiKey);
    }
}
