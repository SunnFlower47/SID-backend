<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuratType;
use App\Models\DesaSetting;
use App\Models\StrukturDesa;
use App\Models\FasilitasDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AiController extends Controller
{
    /**
     * Chat dengan Asisten Digital Desa (Powered by Gemini dengan Strict Guardrails)
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
            // 1. Ambil Konteks Desa & Layanan secara Optimal (Di-cache 1 jam per tenant)
            $tenantId = $request->header('X-Tenant') ?? 'cibatu';
            $cacheKey = "ai_village_context_{$tenantId}";

            $contextData = Cache::remember($cacheKey, 3600, function () {
                $settings = DesaSetting::all()->pluck('value', 'key');

                $surat = SuratType::where('is_active', true)
                    ->select('nama', 'deskripsi', 'persyaratan')
                    ->get();

                $aparatur = StrukturDesa::where('status_aktif', true)
                    ->orderBy('urutan')
                    ->limit(6)
                    ->select('nama', 'jabatan')
                    ->get();

                $fasilitas = FasilitasDesa::where('status_aktif', true)
                    ->limit(8)
                    ->select('nama', 'jenis', 'alamat')
                    ->get();

                return [
                    'settings' => $settings,
                    'surat' => $surat,
                    'aparatur' => $aparatur,
                    'fasilitas' => $fasilitas,
                ];
            });

            $settings = $contextData['settings'];
            $namaDesa = $settings['nama_desa'] ?? 'Cibatu';
            $kecamatan = $settings['kecamatan'] ?? 'Cibatu';
            $kabupaten = $settings['kabupaten'] ?? 'Purwakarta';
            $alamatKantor = $settings['alamat_kantor'] ?? 'Jl. Raya Cibatu';
            $jamOperasional = $settings['jam_operasional'] ?? 'Senin - Jumat 08:00 - 16:00 (Sabtu-Minggu Libur)';
            $telepon = $settings['telepon'] ?? ($settings['no_hp'] ?? '-');
            $email = $settings['email'] ?? '-';

            $villageInfo = "PROFIL RESMI DESA:\n";
            $villageInfo .= "- Nama Desa: Desa {$namaDesa}\n";
            $villageInfo .= "- Kecamatan: {$kecamatan}, Kabupaten: {$kabupaten}\n";
            $villageInfo .= "- Alamat Kantor Desa: {$alamatKantor}\n";
            $villageInfo .= "- Jam Pelayanan Kantor: {$jamOperasional}\n";
            $villageInfo .= "- Kontak Resmi: Telp/WA: {$telepon}, Email: {$email}\n\n";

            if (!empty($contextData['aparatur']) && $contextData['aparatur']->isNotEmpty()) {
                $villageInfo .= "APARATUR / PERANGKAT DESA:\n";
                foreach ($contextData['aparatur'] as $p) {
                    $villageInfo .= "- {$p->jabatan}: {$p->nama}\n";
                }
                $villageInfo .= "\n";
            }

            if (!empty($contextData['fasilitas']) && $contextData['fasilitas']->isNotEmpty()) {
                $villageInfo .= "FASILITAS UMUM PENTING:\n";
                foreach ($contextData['fasilitas'] as $f) {
                    $villageInfo .= "- {$f->nama} ({$f->jenis}): {$f->alamat}\n";
                }
                $villageInfo .= "\n";
            }

            $letterContext = "LAYANAN ADMINISTRASI & SURAT RESMI:\n";
            foreach ($contextData['surat'] as $type) {
                $letterContext .= "- {$type->nama}: {$type->deskripsi}. Persyaratan: {$type->persyaratan}\n";
            }

            // 2. Siapkan Prompt System dengan Guardrails Ketat
            $systemInstruction = <<<EOD
Kamu adalah 'Asisten Digital Resmi Desa {$namaDesa}' yang bertugas memberikan informasi dan asistensi pelayanan birokrasi kepada warga masyarakat.

BATASAN CAKUPAN TOPIK (STRICT OUT-OF-SCOPE GUARDRAILS - SANGAT KETAT):
1. FOKUS HANYA PADA DESA: Kamu HANYA DIIZINKAN menjawab pertanyaan yang berkaitan langsung dengan pelayanan, administrasi desa, persyaratan surat, informasi dan aparatur desa, fasilitas umum, serta kegiatan di Desa {$namaDesa}.
2. TOLAK PERTANYAAN DI LUAR DESA: Jika pengguna bertanya hal di luar konteks desa (misalnya: tugas sekolah, matematika, coding/pemrograman, politik nasional/internasional, gosip/hiburan, ramalan, resep masakan umum, sains umum, investasi/crypto, kesehatan personal non-faskes desa, dll.), kamu WAJIB MENOLAK DENGAN RAMAH DAN SOPAN, lalu arahkan kembali ke layanan desa.
   Contoh respon penolakan: "Mohon maaf, sebagai Asisten Digital Desa {$namaDesa}, saya hanya dapat melayani informasi seputar layanan administrasi, pembuatan surat, dan informasi di Desa {$namaDesa}. Ada yang bisa saya bantu terkait layanan desa?"
3. GAYA BAHASA & STRUKTUR: Gunakan Bahasa Indonesia yang ramah, santun, lugas, dan solutif. Jawaban harus padat dan to-the-point (maksimal 2-3 paragraf atau gunakan poin-poin agar mudah dibaca di ponsel warga).
4. PANDUAN PENGAJUAN SURAT ONLINE: Jika warga bertanya tentang pembuatan surat, selalu beritahu bahwa mereka bisa mengajukan langsung secara mandiri di website ini melalui menu 'Ajukan Surat' tanpa harus datang mengantre di kantor desa.
5. PANDUAN STATUS SURAT: Jelaskan bahwa setelah mengajukan surat online, warga akan mendapat nomor registrasi/surat untuk memantau statusnya di menu 'Cek Status'. Surat fisik baru diambil di kantor desa jika status sudah dinyatakan 'SELESAI'.
6. KEAMANAN & PRIVASI: Kamu TIDAK memiliki akses ke database NIK atau data rahasia warga. JANGAN PERNAH meminta warga mengetik NIK, password, atau data sensitif di dalam percakapan.
7. JIKA DATA TIDAK DITEMUKAN: Jika ada pertanyaan spesifik tentang desa yang datanya tidak tercantum di bawah ini, arahkan warga untuk menghubungi petugas via menu 'Kontak Desa' atau datang ke Kantor Desa pada jam kerja ({$jamOperasional}).

DATA RESMI DESA UNTUK REFERENSI:
{$villageInfo}
{$letterContext}
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

            // 4. Panggil Gemini API via HTTP Facade dengan Auto-Retry & Fallback Model
            $apiKey = config('services.gemini.key');
            $primaryModel = config('services.gemini.model', 'gemini-flash-lite-latest');

            if (!$apiKey) {
                Log::error('Gemini API Key is missing in configuration.');
                return response()->json([
                    'success' => false,
                    'message' => 'Konfigurasi AI belum lengkap.'
                ], 500);
            }
            
            $payload = [
                'contents' => $contents,
                'systemInstruction' => [
                    'parts' => [['text' => $systemInstruction]]
                ],
                'generationConfig' => [
                    'temperature' => 0.6,
                    'maxOutputTokens' => 500,
                ]
            ];

            // Daftar model fallback jika model utama mengalami antrean tinggi (503) atau timeout
            $modelsToTry = array_unique([$primaryModel, 'gemini-flash-lite-latest', 'gemini-2.5-flash-lite', 'gemini-flash-latest']);
            $response = null;

            foreach ($modelsToTry as $currentModel) {
                try {
                    $response = Http::timeout(10)->withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$currentModel}:generateContent?key={$apiKey}", $payload);

                    if ($response->successful()) {
                        break;
                    }

                    // Jika error 503 (High Demand) atau 429 (Rate Limit), catat warning dan coba model berikutnya
                    if (in_array($response->status(), [503, 429])) {
                        Log::warning("Gemini model {$currentModel} sibuk (Status: {$response->status()}). Mencoba model alternatif...");
                        usleep(300000); // 0.3 detik jeda
                        continue;
                    }

                    // Jika error lain (misal 404/400), coba model berikutnya
                    Log::warning("Gemini model {$currentModel} gagal (Status: {$response->status()}). Mencoba model cadangan...");
                } catch (\Illuminate\Http\Client\ConnectionException $connEx) {
                    // Tangani timeout (cURL error 28) agar tidak langsung fatal error
                    Log::warning("Gemini model {$currentModel} timeout (10s). Mencoba model alternatif...");
                    continue;
                }
            }

            if (!$response || $response->failed()) {
                Log::error('Gemini API Error: ' . ($response ? $response->body() : 'No response'));
                return response()->json([
                    'success' => false,
                    'message' => 'Layanan Asisten AI sedang mengalami antrean tinggi. Silakan coba sesaat lagi.'
                ], 503);
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
