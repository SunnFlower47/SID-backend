<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Dusun;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class PendudukTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_penduduk_accessors_pull_from_kartu_keluarga()
    {
        $dusun = Dusun::factory()->create(['nama' => 'Cibatu Hilir']);
        $rw = Rw::factory()->create(['kode' => '001']);
        $rt = Rt::factory()->create(['rw_id' => $rw->id, 'kode' => '002']);

        $kk = KartuKeluarga::factory()->create([
            'nkk' => '3205051234567890',
            'alamat' => 'Jl. Merdeka No. 10',
            'rt_id' => $rt->id,
            'rw_id' => $rw->id,
            'dusun_id' => $dusun->id,
        ]);

        $penduduk = Penduduk::factory()->create([
            'nama' => 'Ahmad',
            'kartu_keluarga_id' => $kk->id,
            'tanggal_lahir' => Carbon::now()->subYears(25)->format('Y-m-d')
        ]);

        // Test NKK accessor
        $this->assertEquals('3205051234567890', $penduduk->nkk);

        // Test Alamat accessor
        $this->assertEquals('Jl. Merdeka No. 10', $penduduk->alamat);

        // Test RT/RW Labels
        $this->assertEquals('002', $penduduk->rt_label);
        $this->assertEquals('001', $penduduk->rw_label);
        $this->assertEquals('Cibatu Hilir', $penduduk->dusun_label);

        // Test Usia
        $this->assertEquals(25, $penduduk->usia);

        // Test Alamat Lengkap
        $this->assertStringContainsString('Jl. Merdeka No. 10', $penduduk->alamat_lengkap);
        $this->assertStringContainsString('RT 002/RW 001', $penduduk->alamat_lengkap);
        $this->assertStringContainsString('Cibatu Hilir', $penduduk->alamat_lengkap);
    }

    public function test_penduduk_without_kk_returns_null_or_defaults()
    {
        // Use factory without relationship (might need to handle if model requires it)
        $penduduk = new Penduduk();
        $penduduk->nama = 'Tanpa KK';

        $this->assertNull($penduduk->nkk);
        $this->assertNull($penduduk->alamat);
        $this->assertEquals('000', $penduduk->rt_label);
        $this->assertEquals('-', $penduduk->dusun_label);
    }
}
