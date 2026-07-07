<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SignatureValidationMiddleware
{
    /**
     * Handle an incoming request.
     * Validasi timestamp + signature untuk keamanan maksimal
     */
    public function handle(Request $request, Closure $next): Response
    {
        $timestamp = $request->header('X-Timestamp');
        $signature = $request->header('X-Signature');
        $secret = config('app.api_key');

        // Validasi timestamp (max 10 detik selisih)
        if (!$timestamp || !$signature) {
            Log::warning('Missing timestamp/signature', [
                'ip' => $request->ip(),
                'origin' => $request->header('Origin'),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Missing required headers',
                'error' => 'MISSING_HEADERS'
            ], 400);
        }

        $currentTime = time();
        $timeDiff = abs($currentTime - (int)$timestamp);

        if ($timeDiff > 300) { // H2 FIX: 5 menit (bukan 1 jam) untuk cegah replay attack
            Log::warning('Timestamp expired', [
                'ip' => $request->ip(),
                'timestamp' => $timestamp,
                'current_time' => $currentTime,
                'time_diff' => $timeDiff,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Request expired',
                'error' => 'TIMESTAMP_EXPIRED'
            ], 400);
        }

        // Validasi signature
        $expectedSignature = hash_hmac('sha256', $timestamp, $secret);

        // H2 FIX: JANGAN log expected_signature — bisa bocor via log access
        Log::info('Signature Validation', [
            'timestamp'       => $timestamp,
            'secret_length'   => strlen($secret),
            'signature_match' => hash_equals($expectedSignature, $signature),
            'ip'              => $request->ip(),
        ]);

        if (!hash_equals($expectedSignature, $signature)) {
            Log::warning('Invalid signature', [
                'ip'        => $request->ip(),
                'timestamp' => $timestamp,
                // Zero Trust: JANGAN catat provided_signature atau expected_signature di log
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid signature',
                'error' => 'INVALID_SIGNATURE'
            ], 401);
        }

        // Log successful request (sampling 1%)
        if (rand(1, 100) === 1) {
            Log::info('API Access', [
                'origin' => $request->header('Origin'),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
                'timestamp' => $timestamp,
                'endpoint' => $request->path(),
            ]);
        }

        return $next($request);
    }
}
