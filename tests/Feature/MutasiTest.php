<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Penduduk;
use App\Models\Mutasi;
use App\Models\KartuKeluarga;

class MutasiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Create user for authentication
        // Create user for authentication
        $this->user = User::factory()->create();
        
        // Assign permission (using Spatie Permission)
        // We assume the system uses Spatie Permission
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $this->user->assignRole($role);

        $this->actingAs($this->user);
    }

    public function test_can_view_mutasi_index()
    {
        $response = $this->get(route('mutasi.data.index'));
        $response->assertStatus(200);
    }

    public function test_mutasi_kematian_soft_deletes_penduduk()
    {
        // 1. Create a resident
        $penduduk = Penduduk::create([
            'nik' => '1234567890123456',
            'nama' => 'John Doe',
            'nkk' => '1234567890123456',
            'jenis_kelamin' => 'LAKI-LAKI',
            'tempat_lahir' => 'Cibatu',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Islam',
            'status_perkawinan' => 'Belum Kawin',
            'pekerjaan' => 'Buruh',
            'pendidikan' => 'SMA',
            'status' => 'aktif',
            'alamat' => 'Jl. Test',
            'rt' => '001',
            'rw' => '001',
            'dusun' => 'Dusun 1',
            'kedudukan_keluarga' => 'Kepala Keluarga',
        ]);

        // 2. Perform Mutation (Kematian)
        $response = $this->post(route('mutasi.data.store'), [
            'jenis_mutasi' => 'kematian',
            'penduduk_id' => $penduduk->id,
            'tanggal_mutasi' => now()->toDateString(),
            'hari_meninggal' => 'Senin',
            'jam_meninggal' => '10:00',
            'bertempat_di' => 'Rumah',
            'hari_pemakaman' => 'Selasa',
            'tanggal_pemakaman' => now()->addDay()->toDateString(),
            'jam_pemakaman' => '09:00',
            'lokasi_pemakaman' => 'TPU Cibatu',
            'alasan' => 'Sakit Tua'
        ]);

        // 3. Assertions
        $response->assertRedirect(route('mutasi.data.index'));
        $response->assertSessionHas('success');

        // Check Mutation Record Created
        $this->assertDatabaseHas('mutasis', [
            'penduduk_id' => $penduduk->id,
            'jenis_mutasi' => 'kematian',
        ]);

        // Check Resident Soft Deleted
        $this->assertSoftDeleted('penduduks', [
            'id' => $penduduk->id
        ]);
        
        // Check Kartu Keluarga Summary Updated (If Observer works)
        $this->assertDatabaseHas('kartu_keluargas', [
            'nkk' => $penduduk->nkk,
            'anggota_meninggal' => 1
        ]);
    }

    public function test_mutasi_pindah_keluar_soft_deletes_penduduk()
    {
        $penduduk = Penduduk::create([
            'nik' => '9876543210987654',
            'nama' => 'Jane Doe',
            'nkk' => '9876543210987654',
            'jenis_kelamin' => 'PEREMPUAN',
            'tempat_lahir' => 'Cibatu',
            'tanggal_lahir' => '1995-01-01',
            'agama' => 'Islam',
            'status_perkawinan' => 'Kawin',
            'pekerjaan' => 'IRT',
            'pendidikan' => 'D3',
            'status' => 'aktif',
            'alamat' => 'Jl. Test 2',
            'rt' => '002',
            'rw' => '002',
            'dusun' => 'Dusun 2',
            'kedudukan_keluarga' => 'Istri',
        ]);

        $response = $this->post(route('mutasi.data.store'), [
            'jenis_mutasi' => 'pindah_keluar',
            'penduduk_id' => $penduduk->id,
            'kategori_mutasi' => 'luar_kota',
            'tanggal_mutasi' => now()->toDateString(),
            'asal_tujuan' => 'Jakarta',
            'alasan' => 'Pindah Kerja'
        ]);

        $response->assertRedirect(route('mutasi.data.index'));
        
        $this->assertDatabaseHas('mutasis', [
            'penduduk_id' => $penduduk->id,
            'jenis_mutasi' => 'pindah_keluar',
        ]);

        $this->assertSoftDeleted('penduduks', [
            'id' => $penduduk->id
        ]);
        
        $this->assertDatabaseHas('kartu_keluargas', [
            'nkk' => $penduduk->nkk,
            'anggota_pindah' => 1
        ]);
    }
}
    