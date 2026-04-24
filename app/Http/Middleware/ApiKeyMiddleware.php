<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        // Only accept API key from header, not query parameter for security
        $apiKey = $request->header('X-API-Key');
        $validApiKey = config('app.api_key');

        // Check if API key is configured
        if (!$validApiKey) {
            Log::error('API key not configured in environment');
            return response()->json([
                'success' => false,
                'message' => 'Service temporarily unavailable',
                'error' => 'CONFIGURATION_ERROR'
            ], 503);
        }

        // Check if API key is provided and valid
        if (!$apiKey || $apiKey !== $validApiKey) {
            Log::warning('Invalid API key attempt', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'endpoint' => $request->path(),
                'has_api_key' => !empty($apiKey)
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid or missing API key',
                'error' => 'UNAUTHORIZED'
            ], 401);
        }

        return $next($request);
    }
}
