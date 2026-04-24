<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Submit contact message
     */
    public function submit(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'telepon' => 'required|string|max:20',
                'subjek' => 'required|string|max:255',
                'pesan' => 'required|string|max:2000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get validated data
            $data = $validator->validated();

            // Save to database
            $contactMessage = ContactMessage::create([
                'nama' => $data['nama'],
                'email' => $data['email'],
                'telepon' => $data['telepon'],
                'subjek' => $data['subjek'],
                'pesan' => $data['pesan'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'unread'
            ]);

            // Log the contact message
            Log::info('Contact message received and saved', [
                'id' => $contactMessage->id,
                'nama' => $data['nama'],
                'email' => $data['email'],
                'subjek' => $data['subjek'],
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil dikirim! Terima kasih atas masukan Anda.',
                'data' => [
                    'id' => $contactMessage->id,
                    'nama' => $data['nama'],
                    'email' => $data['email'],
                    'subjek' => $data['subjek'],
                    'timestamp' => now()->format('d/m/Y H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in contact submit: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get contact information
     */
    public function info()
    {
        try {
            $contactInfo = [
                'alamat' => 'Jl. Cibatu Km. 15, Desa Cibatu, Kec. Cibatu, Kab. Purwakarta, Cibatu, Purwakarta, Jawa Barat 41161',
                'telepon' => '(0264) 123456',
                'email' => 'desacibatu.2001@gmail.com',
                'website' => 'https://desa-cibatu.test',
                'kode_pos' => '41161',
                'kecamatan' => 'Cibatu',
                'kabupaten' => 'Purwakarta',
                'provinsi' => 'Jawa Barat',
                'jam_kerja' => 'Senin - Jumat: 08:00 - 16:00',
                'jam_kerja_weekend' => 'Sabtu: 08:00 - 12:00'
            ];

            return response()->json([
                'success' => true,
                'data' => $contactInfo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil informasi kontak',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
