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
        // 1. Create a Kartu Keluarga first (Source of Truth)
        $kk = KartuKeluarga::factory()->create([
            'nkk' => '1234567890123456',
            'alamat' => 'Jl. Test',
        ]);

        // 2. Create a resident linked to the KK
        $penduduk = Penduduk::create([
            'nik' => '1234567890123456',
            'kartu_keluarga_id' => $kk->id,
            'nama' => 'John Doe',
            'jenis_kelamin' => 'LAKI-LAKI',
            'tempat_lahir' => 'Cibatu',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Islam',
            'status_perkawinan' => 'Belum Kawin',
            'pekerjaan' => 'Buruh',
            'pendidikan' => 'SMA',
            'kedudukan_keluarga' => 'Kepala Keluarga',
        ]);

        // 3. Perform Mutation (Kematian)
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

        // 4. Assertions
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
        
        // Check Kartu Keluarga Summary Updated (If Observer/Service works)
        $this->assertDatabaseHas('kartu_keluargas', [
            'id' => $kk->id,
            'anggota_meninggal' => 1
        ]);
    }

    public function test_mutasi_pindah_keluar_soft_deletes_penduduk()
    {
        $kk = KartuKeluarga::factory()->create([
            'nkk' => '9876543210987654',
            'alamat' => 'Jl. Test 2',
        ]);

        $penduduk = Penduduk::create([
            'nik' => '9876543210987654',
            'kartu_keluarga_id' => $kk->id,
            'nama' => 'Jane Doe',
            'jenis_kelamin' => 'PEREMPUAN',
            'tempat_lahir' => 'Cibatu',
            'tanggal_lahir' => '1995-01-01',
            'agama' => 'Islam',
            'status_perkawinan' => 'Kawin',
            'pekerjaan' => 'IRT',
            'pendidikan' => 'D3',
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
            'id' => $kk->id,
            'anggota_pindah' => 1
        ]);
    }

    public function test_mutasi_kelahiran_with_enrichment_fields()
    {
        $kk = KartuKeluarga::factory()->create([
            'nkk' => '3214051212260001',
            'alamat' => 'Jl. Cibatu Asri',
        ]);

        $response = $this->post(route('mutasi.data.store'), [
            'jenis_mutasi' => 'kelahiran',
            'nkk' => '3214051212260001',
            'nik_bayi' => '3214051212260002',
            'nama_bayi' => 'BAYI ENRICHMENT',
            'jenis_kelamin_bayi' => 'LAKI-LAKI',
            'tempat_lahir' => 'Purwakarta',
            'tanggal_lahir' => '2026-06-01',
            'agama_bayi' => 'Islam',
            'status_perkawinan_bayi' => 'Belum Kawin',
            'kedudukan_keluarga_bayi' => 'Anak',
            'pendidikan_bayi' => 'Tidak/Belum Sekolah',
            'pekerjaan_bayi' => 'Belum/Tidak Bekerja',
            'nama_ayah' => 'Ayah Bayi',
            'nama_ibu' => 'Ibu Bayi',
            'alamat_bayi' => 'Jl. Cibatu Asri',
            'rt_id_bayi' => $kk->rt_id,
            'rw_id_bayi' => $kk->rw_id,
            'dusun_id_bayi' => $kk->dusun_id,
            'tanggal_mutasi' => '2026-06-01',
            'keterangan_bayi' => 'Lahir selamat',
            'golongan_darah' => 'O',
            'no_akta_lahir' => 'AKTA-NEW-11',
            'status_asuransi' => 'BPJS PBI',
            'cacat_type' => 'Tidak Ada',
            'sakit_menahun' => 'Tidak Ada',
            'telepon' => '08123456789'
        ]);

        $response->assertRedirect(route('mutasi.data.index'));

        $this->assertDatabaseHas('penduduks', [
            'nik' => '3214051212260002',
            'golongan_darah' => 'O',
            'no_akta_lahir' => 'AKTA-NEW-11',
            'status_asuransi' => 'BPJS PBI',
            'telepon' => '08123456789'
        ]);
    }

    public function test_mutasi_pindah_masuk_with_enrichment_fields()
    {
        $rt = \App\Models\Rt::factory()->create();

        $response = $this->post(route('mutasi.data.store'), [
            'jenis_mutasi' => 'pindah_masuk',
            'nik' => '3214051212260003',
            'nama' => 'PINDAHAN ENRICHMENT',
            'jenis_kelamin' => 'PEREMPUAN',
            'agama' => 'Islam',
            'status_perkawinan' => 'Kawin',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1992-05-05',
            'kedudukan_keluarga' => 'Kepala Keluarga',
            'pendidikan' => 'S1',
            'pekerjaan' => 'Swasta',
            'nama_ayah' => 'Ayah Pindah',
            'nama_ibu' => 'Ibu Pindah',
            'nkk_new' => '3214051212260004',
            'alamat' => 'Jl. Cibatu Raya',
            'rt_id' => $rt->id,
            'rw_id' => $rt->rw_id,
            'kategori_mutasi' => 'luar_kota',
            'asal_tujuan' => 'Bandung',
            'tanggal_mutasi' => '2026-06-01',
            'alasan' => 'Ikut Suami',
            'golongan_darah' => 'AB',
            'warganegara' => 'WNI',
            'no_akta_lahir' => 'AKTA-NEW-12',
            'status_pendidikan' => 'Tamat Sekolah',
            'telepon' => '08123456799',
            'status_asuransi' => 'BPJS Mandiri',
            'family_members' => [
                [
                    'nik' => '3214051212260005',
                    'nama' => 'ANAK PINDAHAN',
                    'jenis_kelamin' => 'LAKI-LAKI',
                    'kedudukan_keluarga' => 'Anak',
                    'tempat_lahir' => 'Bandung',
                    'tanggal_lahir' => '2015-05-05',
                    'golongan_darah' => 'B',
                    'warganegara' => 'WNI',
                    'no_akta_lahir' => 'AKTA-NEW-13',
                    'status_pendidikan' => 'Sedang Sekolah',
                    'telepon' => '08123456711',
                    'status_asuransi' => 'BPJS Mandiri'
                ]
            ]
        ]);

        $response->assertRedirect(route('mutasi.data.index'));

        $this->assertDatabaseHas('penduduks', [
            'nik' => '3214051212260003',
            'golongan_darah' => 'AB',
            'warganegara' => 'WNI',
            'no_akta_lahir' => 'AKTA-NEW-12',
            'telepon' => '08123456799'
        ]);

        $this->assertDatabaseHas('penduduks', [
            'nik' => '3214051212260005',
            'golongan_darah' => 'B',
            'warganegara' => 'WNI',
            'no_akta_lahir' => 'AKTA-NEW-13',
            'status_pendidikan' => 'Sedang Sekolah'
        ]);
    }
}
    