<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerifikasiSuratWebController extends Controller
{
    public function show($token)
    {
        $apiResponse = app(\App\Http\Controllers\Api\VerifikasiSuratApiController::class)->verify($token);
        
        $statusCode = $apiResponse->getStatusCode();
        $responseData = json_decode($apiResponse->getContent(), true);

        return view('verifikasi-surat', [
            'status' => $statusCode,
            'data' => $responseData['data'] ?? null,
            'message' => $responseData['message'] ?? 'Terjadi kesalahan.',
            'success' => $responseData['success'] ?? false,
        ]);
    }
}
