<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\KartuKeluarga;
use App\Models\Penduduk;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KartuKeluargaTest extends TestCase
{
    use RefreshDatabase;

    public function test_kk_anggota_aktif_relationship_excludes_deleted_residents()
    {
        $kk = KartuKeluarga::factory()->create();
        
        $aktif = Penduduk::factory()->create([
            'kartu_keluarga_id' => $kk->id,
            'nama' => 'Aktif'
        ]);
        
        $mutasi = Penduduk::factory()->create([
            'kartu_keluarga_id' => $kk->id,
            'nama' => 'Meninggal'
        ]);
        
        $mutasi->delete(); // Soft delete

        $this->assertCount(1, $kk->anggotaAktif);
        $this->assertEquals('Aktif', $kk->anggotaAktif->first()->nama);
        $this->assertCount(2, $kk->penduduks()->withTrashed()->get());
    }

    public function test_kk_bermasalah_scopes()
    {
        KartuKeluarga::factory()->create(['status_kk' => 'normal']);
        KartuKeluarga::factory()->create(['status_kk' => 'bermasalah']);
        KartuKeluarga::factory()->create(['status_kk' => 'bermasalah_sementara']);
        
        $this->assertCount(2, KartuKeluarga::bermasalah()->get());
        $this->assertCount(1, KartuKeluarga::normal()->get());
    }

    public function test_kk_is_bermasalah_helper()
    {
        $kkNormal = KartuKeluarga::factory()->make(['status_kk' => 'normal']);
        $kkBermasalah = KartuKeluarga::factory()->make(['status_kk' => 'bermasalah']);
        
        $this->assertFalse($kkNormal->isBermasalah());
        $this->assertTrue($kkBermasalah->isBermasalah());
    }
}
