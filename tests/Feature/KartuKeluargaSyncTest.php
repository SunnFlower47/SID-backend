<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use Illuminate\Support\Facades\Artisan;

class KartuKeluargaSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_command_populates_summary_table_correctly()
    {
        // 1. Setup Data: Create a family
        $nkk = '3205051234567890';
        $kk = KartuKeluarga::factory()->create([
            'nkk' => $nkk,
            'nama_kepala_keluarga' => 'Budi Santoso',
            'alamat' => 'Kp. Durian Runtuh',
        ]);
        
        // Kepala Keluarga
        Penduduk::factory()->create([
            'nik' => '3205051111110001',
            'kartu_keluarga_id' => $kk->id,
            'nama' => 'Budi Santoso',
            'kedudukan_keluarga' => 'Kepala Keluarga',
        ]);

        // Istri
        Penduduk::factory()->create([
            'nik' => '3205052222220002',
            'kartu_keluarga_id' => $kk->id,
            'nama' => 'Siti Aminah',
            'kedudukan_keluarga' => 'Istri',
        ]);

        // 2. Clear statistics to test Recalculate/Sync
        $kk->update([
            'jumlah_anggota' => 0,
            'anggota_aktif' => 0,
        ]);
        
        // 3. Run Sync Command (If it exists and uses recalulate)
        // Artisan::call('sync:kartu-keluarga'); 
        // Or if we use the service directly:
        app(\App\Services\KartuKeluargaService::class)->recalculate($kk->id);

        // 4. Assertions
        $this->assertDatabaseHas('kartu_keluargas', [
            'nkk' => $nkk,
            'nama_kepala_keluarga' => 'Budi Santoso',
            'jumlah_anggota' => 2,
            'anggota_aktif' => 2,
        ]);
    }
}
