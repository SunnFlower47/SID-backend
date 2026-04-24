<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiProxyController extends Controller
{
    private $privateApiUrl;
    private $apiKey;

    public function __construct()
    {
        $this->privateApiUrl = config('app.url') . '/api/v1';
        $this->apiKey = config('app.api_key');
    }

    /**
     * Proxy untuk semua request ke private API
     */
    public function proxy(Request $request, $path = '')
    {
        try {
            // Validasi origin dan User-Agent (sudah dilakukan oleh middleware)
            $fullPath = $path ? "/{$path}" : '';
            $queryString = $request->getQueryString();
            $url = $this->privateApiUrl . $fullPath . ($queryString ? "?{$queryString}" : '');

            // Cegah infinite loop - jangan proxy ke proxy endpoint
            if (strpos($url, '/api/proxy/') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Infinite loop detected'
                ], 400);
            }

            // Headers yang akan dikirim ke private API
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-API-Key' => $this->apiKey,
                'Origin' => $request->header('Origin'),
                'User-Agent' => $request->header('User-Agent'),
            ];

            // Method dan body
            $method = $request->method();
            $body = null;

            if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
                $body = $request->getContent();
                $headers['Content-Type'] = $request->header('Content-Type', 'application/json');
            }

            // Log request untuk debugging
            Log::info('API Proxy Request', [
                'method' => $method,
                'url' => $url,
                'origin' => $request->header('Origin'),
                'user_agent' => $request->header('User-Agent'),
            ]);

            // Kirim request ke private API
            $response = Http::withHeaders($headers)
                ->timeout(30);

            if ($body) {
                $response = $response->withBody($body, 'application/json');
            }

            $response = $response->send($method, $url);

            // Log response untuk debugging
            Log::info('API Proxy Response', [
                'status' => $response->status(),
                'url' => $url,
            ]);

            // Return response dengan headers yang sesuai
            return response($response->body(), $response->status())
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Access-Control-Allow-Origin' => $request->header('Origin'),
                    'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
                    'Access-Control-Allow-Headers' => 'Content-Type, Accept, Origin, User-Agent',
                ]);

        } catch (\Exception $e) {
            Log::error('API Proxy Error', [
                'error' => $e->getMessage(),
                'url' => $url ?? 'unknown',
                'method' => $method ?? 'unknown',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'API Proxy Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle OPTIONS request untuk CORS
     */
    public function options(Request $request)
    {
        return response('', 200)
            ->withHeaders([
                'Access-Control-Allow-Origin' => $request->header('Origin'),
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Accept, Origin, User-Agent',
                'Access-Control-Max-Age' => '86400',
            ]);
    }
}
