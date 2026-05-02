<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Dusun;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Inertia\Testing\AssertableInertia as Assert;

class PendudukControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user and assign role
        $this->user = User::factory()->create();
        $role = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $this->user->assignRole($role);
        
        // Mocking can:kependudukan middleware
        // Spatie permissions are automatically handled if roles are assigned
    }

    public function test_index_page_is_accessible_and_has_data()
    {
        $kk = KartuKeluarga::factory()->create();
        Penduduk::factory()->count(5)->create(['kartu_keluarga_id' => $kk->id]);

        $response = $this->actingAs($this->user)
            ->get(route('penduduk.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Tenant/Penduduk/Index')
            ->has('penduduks.data', 5)
            ->has('stats', fn (Assert $page) => $page
                ->where('total', 5)
                ->where('total_kk', 1)
                ->etc()
            )
        );
    }

    public function test_create_page_loads_with_master_data()
    {
        Dusun::factory()->count(2)->create();
        Rw::factory()->count(2)->create();
        Rt::factory()->count(4)->create();

        $response = $this->actingAs($this->user)
            ->get(route('penduduk.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Tenant/Penduduk/Create')
            ->has('masterRwOptions')
        );
    }

    public function test_store_penduduk_creates_record_and_kk_if_new()
    {
        $rt = Rt::factory()->create();
        
        $data = [
            'nik' => '3205051111110001',
            'nama' => 'John New Doe',
            'nkk' => '3205052222220002',
            'jenis_kelamin' => 'LAKI-LAKI',
            'tempat_lahir' => 'Cibatu',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Islam',
            'status_perkawinan' => 'Belum Kawin',
            'kedudukan_keluarga' => 'Kepala Keluarga',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Swasta',
            'nama_ayah' => 'Ayah',
            'nama_ibu' => 'Ibu',
            'alamat' => 'Jl. Baru',
            'rt_id' => $rt->id,
            'rw_id' => $rt->rw_id,
            'kk_option' => 'manual',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('penduduk.store'), $data);

        $response->assertRedirect(route('penduduk.index'));
        
        // Verify KK was created
        $this->assertDatabaseHas('kartu_keluargas', [
            'nkk' => '3205052222220002',
            'nama_kepala_keluarga' => 'John New Doe'
        ]);

        // Verify Penduduk was created and linked
        $kk = KartuKeluarga::where('nkk', '3205052222220002')->first();
        $this->assertDatabaseHas('penduduks', [
            'nik' => '3205051111110001',
            'kartu_keluarga_id' => $kk->id
        ]);
    }

    public function test_update_penduduk_works()
    {
        $kk = KartuKeluarga::factory()->create();
        $penduduk = Penduduk::factory()->create(['kartu_keluarga_id' => $kk->id, 'nama' => 'Old Name']);

        $data = $penduduk->toArray();
        $data['nama'] = 'Updated Name';
        
        // Remove virtual attributes that shouldn't be sent back to database
        unset($data['nkk'], $data['alamat'], $data['rt_id'], $data['rw_id'], $data['dusun_id']);
        
        // Ensure date format matches validation if any
        $data['tanggal_lahir'] = $penduduk->tanggal_lahir->format('Y-m-d');
        $data['kk_option'] = 'existing';
        $data['nkk_existing'] = $kk->nkk;

        $response = $this->actingAs($this->user)
            ->put(route('penduduk.update', $penduduk->id), $data);

        $response->assertRedirect(route('penduduk.index'));
        $this->assertDatabaseHas('penduduks', [
            'id' => $penduduk->id,
            'nama' => 'Updated Name'
        ]);
    }

    public function test_destroy_penduduk_soft_deletes()
    {
        $kk = KartuKeluarga::factory()->create();
        $penduduk = Penduduk::factory()->create(['kartu_keluarga_id' => $kk->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('penduduk.destroy', $penduduk->id));

        $response->assertRedirect(route('penduduk.index'));
        $this->assertSoftDeleted('penduduks', ['id' => $penduduk->id]);
    }
}
