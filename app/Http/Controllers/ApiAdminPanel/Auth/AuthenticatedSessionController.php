<?php

namespace App\Http\Controllers\ApiAdminPanel\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $user = $request->user();
        
        // Buat token Sanctum untuk admin
        $token = $user->createToken('admin-api-token')->plainTextToken;

        Log::info('Admin API Login successful', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                ],
                'token' => $token
            ]
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user) {
                // Cabut/hapus token yang sedang digunakan
                $user->currentAccessToken()->delete();
                
                Log::info('Admin API Logout successful', [
                    'user_id' => $user->id,
                    'ip' => $request->ip()
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Logout berhasil'
            ]);

        } catch (\Exception $e) {
            Log::error('Admin API Logout error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat logout'
            ], 500);
        }
    }
}
