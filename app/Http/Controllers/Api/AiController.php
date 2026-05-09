<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuratType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{
    /**
     * Chat dengan Asisten Digital Desa Cibatu (Powered by Gemini)
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'history' => 'nullable|array',
        ]);

        $userMessage = $request->input('message');
        $history = $request->input('history', []);

        try {
            // 1. Ambil Konteks Desa & Layanan dari Database (Model Key-Value)
            $allSettings = \App\Models\DesaSetting::all()->pluck('value', 'key');
            $suratTypes = SuratType::where('is_active', true)->get();
            
            $villageInfo = "Informasi Desa:\n";
            $villageInfo .= "- Nama: " . ($allSettings['nama_desa'] ?? 'Cibatu') . "\n";
            $villageInfo .= "- Kecamatan: " . ($allSettings['kecamatan'] ?? 'Cibatu') . "\n";
            $villageInfo .= "- Kabupaten: " . ($allSettings['kabupaten'] ?? 'Purwakarta') . "\n";
            $villageInfo .= "- Alamat Kantor: " . ($allSettings['alamat_kantor'] ?? 'Jl. Raya Cibatu No. 1') . "\n";
            $villageInfo .= "- Jam Operasional: Senin sampai Jumat jam 08:00 sampai jam 16:00 (Sabtu-Minggu Libur)\n\n";

            $letterContext = "Layanan Surat Digital:\n";
            foreach ($suratTypes as $type) {
                $letterContext .= "- {$type->nama}: {$type->deskripsi}. Syarat: {$type->persyaratan}\n";
            }

            // 2. Siapkan Prompt System
            $systemInstruction = <<<EOD
Kamu adalah 'Asisten Digital Desa Cibatu' yang ramah dan profesional.
Tugas utama kamu adalah membantu warga memahami layanan desa dan informasi umum.

DATA DESA RESMI:
$villageInfo
$letterContext

ATURAN & INSTRUKSI KHUSUS:
1. PROMOSI LAYANAN ONLINE: Setiap kali warga bertanya tentang pengajuan surat, kamu WAJIB memberitahu bahwa pengajuan bisa dilakukan secara mandiri melalui menu 'Ajukan Surat Online' di website ini tanpa harus datang ke kantor desa.
2. ALUR LAYANAN DIGITAL: Jelaskan bahwa setelah mengajukan secara online, warga akan mendapatkan 'Tracking ID atau nomor surat'. Mereka harus menyimpan ID tersebut untuk memantau progres surat di menu 'Cek Status' secara berkala. Surat fisik baru diambil di kantor desa JIKA statusnya sudah dinyatakan 'SELESAI'. Kalau tracking ID atau nomor suratnya lupa, bisa pakai opsi cek menggunakan NIK dan tanggal lahir.
3. KEAMANAN DATA: Kamu TIDAK MEMILIKI AKSES ke data NIK atau privasi warga. JANGAN PERNAH meminta NIK, Nomor HP, atau data sensitif lainnya di dalam chat.
4. CEK STATUS: Jika warga bertanya "sudah jadi belum?" atau "sampai mana surat saya?", arahkan mereka untuk menggunakan menu 'Cek Status' dengan memasukkan Tracking ID atau nomor surat mereka.
5. BAHASA: Gunakan bahasa Indonesia yang ramah, sopan, dan solutif.
6. FALLBACK (JIKA TIDAK TAHU): Jika pertanyaan warga tidak ada dalam data resmi atau di luar konteks layanan desa, arahkan mereka untuk menghubungi petugas melalui menu 'Kontak Desa' di website ini atau datang langsung ke Kantor Desa Cibatu pada jam operasional.
EOD;

            // 3. Format History untuk Gemini API
            $contents = [];
            
            // Tambahkan System Instruction sebagai pesan pertama (model-like behavior)
            // Note: Gemini API v1beta menggunakan 'system_instruction' field, 
            // tapi kita bisa simulasi lewat prompt awal jika pakai model lama.
            
            foreach ($history as $msg) {
                // Filter: Pastikan hanya mengirim role 'user' dan 'model'
                if (in_array($msg['role'], ['user', 'model'])) {
                    $contents[] = [
                        'role' => $msg['role'],
                        'parts' => [['text' => $msg['text'] ?? $msg['parts'][0]['text']]]
                    ];
                }
            }

            // Tambahkan pesan user saat ini
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $userMessage]]
            ];

            // 4. Panggil Gemini API via HTTP Facade
            $apiKey = config('services.gemini.key', env('GEMINI_API_KEY'));
            $model = env('GEMINI_MODEL', 'gemini-1.5-flash');
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => $contents,
                'systemInstruction' => [
                    'parts' => [['text' => $systemInstruction]]
                ]
            ]);

            if ($response->failed()) {
                Log::error('Gemini API Error: ' . $response->body());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal berkomunikasi dengan AI. Silakan coba lagi.'
                ], 500);
            }

            $data = $response->json();
            $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak mengerti.';

            return response()->json([
                'success' => true,
                'message' => $aiResponse
            ]);

        } catch (\Exception $e) {
            Log::error('AI Chat Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem pada asisten AI.'
            ], 500);
        }
    }
}
