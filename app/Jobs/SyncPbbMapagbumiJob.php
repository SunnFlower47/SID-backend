<?php

namespace App\Jobs;

use App\Models\PajakPbbObjek;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncPbbMapagbumiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pbbObjek;
    protected $delaySeconds;

    /**
     * Create a new job instance.
     */
    public function __construct(PajakPbbObjek $pbbObjek, $delaySeconds = 5)
    {
        $this->pbbObjek = $pbbObjek;
        $this->delaySeconds = $delaySeconds;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Check global rate limit in cache (flexible based on env CACHE_STORE: redis, database, file, etc.)
            try {
                $cacheKey = 'pbb_global_api_limit:' . date('YmdHi');
                
                // Menggunakan Cache facade agar otomatis mengikuti driver cache yang aktif di .env
                $requestCount = \Illuminate\Support\Facades\Cache::increment($cacheKey);
                if ($requestCount === 1) {
                    \Illuminate\Support\Facades\Cache::put($cacheKey, 1, 60); // Set expired 60 detik
                }

                $globalLimit = (int) env('PBB_API_GLOBAL_LIMIT', 25);
                if ($requestCount > $globalLimit) {
                    Log::warning("PBB Sync: Global rate limit reached for this minute (" . date('H:i') . "). Skipping NOP {$this->pbbObjek->nop} to prevent 429 from Mapagbumi API.");
                    return;
                }
            } catch (\Exception $cacheEx) {
                Log::warning("PBB Sync: Cache connection failed, skipping global rate limit check: " . $cacheEx->getMessage());
            }

            $nop = str_replace(['.', '-'], '', $this->pbbObjek->nop);
            
            $response = Http::timeout(30)->withoutVerifying()->get('https://mapagbumi.purwakartakab.go.id/nop?nop=' . $nop);
            
            if ($response->successful()) {
                $html = $response->body();
                
                // Extract data-page attribute
                if (preg_match('/<div[^>]*id="app"[^>]*data-page="([^"]+)"/', $html, $matches)) {
                    $jsonStr = html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
                    $data = json_decode($jsonStr, true);
                    
                    if (isset($data['props'])) {
                        $props = $data['props'];

                        // Extract uncensored name from dataSppt if available
                        $uncensoredName = null;
                        if (!empty($props['dataSppt']) && isset($props['dataSppt'][0]['nm_wp_sppt'])) {
                            $uncensoredName = $props['dataSppt'][0]['nm_wp_sppt'];
                        }

                        // Update Objek
                        $updateData = [
                            'alamat_wp' => $props['alamat_wp'] ?? null,
                            'alamat_objek' => $props['alamat_op'] ?? null,
                            'luas_bumi' => $props['luas_bumi'] ?? null,
                            'luas_bangunan' => $props['luas_bng'] ?? null,
                            'last_synced_at' => now(),
                        ];

                        if ($uncensoredName) {
                            $updateData['nama_wp'] = $uncensoredName;
                        } else if (empty($this->pbbObjek->nama_wp)) {
                            // Hanya gunakan nama bersensor jika DB masih kosong
                            $updateData['nama_wp'] = $props['nm_wp'] ?? null;
                        }

                        $this->pbbObjek->update($updateData);
                        
                        // Delete old tagihan to replace with new ones
                        $this->pbbObjek->tagihans()->delete();
                        
                        // Insert new tagihan
                        if (!empty($props['dataSppt'])) {
                            $tagihans = [];
                            foreach ($props['dataSppt'] as $sppt) {
                                $tagihans[] = [
                                    'pajak_pbb_objek_id' => $this->pbbObjek->id,
                                    'tahun' => $sppt['thn_pajak_sppt'],
                                    'pbb_terhutang' => $sppt['pbb_yg_harus_dibayar_sppt'] ?? 0,
                                    'jatuh_tempo' => $sppt['tgl_jatuh_tempo_sppt'] ?? null,
                                    'status' => $sppt['status'] ?? 'BELUM LUNAS',
                                    'tanggal_bayar' => $sppt['tgl_pembayaran_sppt'] ?? null,
                                    'denda' => $sppt['denda'] ?? 0,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                            
                            // Batch insert
                            \App\Models\PajakPbbTagihan::insert($tagihans);
                        }
                    }
                }
            } else {
                Log::warning("Gagal fetch data NOP: {$nop}, status HTTP: " . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Error saat scraping NOP {$this->pbbObjek->nop}: " . $e->getMessage());
        }

        // Delay to prevent rate limiting
        if ($this->delaySeconds > 0) {
            sleep($this->delaySeconds);
        }
    }
}
