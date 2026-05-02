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

class KartuKeluargaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $role = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $this->user->assignRole($role);
    }

    public function test_kk_index_page_is_accessible()
    {
        KartuKeluarga::factory()->count(3)->create();

        $response = $this->actingAs($this->user)
            ->get(route('kk.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Tenant/KartuKeluarga/Index')
            ->has('kartuKeluarga.data', 3)
        );
    }

    public function test_kk_show_page_displays_correct_members()
    {
        $kk = KartuKeluarga::factory()->create(['nkk' => '1234567890123456']);
        Penduduk::factory()->create([
            'kartu_keluarga_id' => $kk->id,
            'nama' => 'Kepala',
            'kedudukan_keluarga' => 'Kepala Keluarga'
        ]);
        Penduduk::factory()->create([
            'kartu_keluarga_id' => $kk->id,
            'nama' => 'Anggota',
            'kedudukan_keluarga' => 'Anak'
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('kk.show', '1234567890123456'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Tenant/KartuKeluarga/Show')
            ->has('kartuKeluarga', 2)
            ->where('kepalaKeluarga.nama', 'Kepala')
        );
    }

    public function test_kk_destroy_deletes_kk_and_soft_deletes_members()
    {
        $kk = KartuKeluarga::factory()->create(['nkk' => '1111222233334444']);
        $p1 = Penduduk::factory()->create(['kartu_keluarga_id' => $kk->id]);
        $p2 = Penduduk::factory()->create(['kartu_keluarga_id' => $kk->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('kk.destroy', '1111222233334444'));

        $response->assertRedirect(route('kk.index'));
        
        $this->assertSoftDeleted('kartu_keluargas', ['id' => $kk->id]);
        $this->assertSoftDeleted('penduduks', ['id' => $p1->id]);
        $this->assertSoftDeleted('penduduks', ['id' => $p2->id]);
    }

    public function test_kk_sync_summary_works()
    {
        $kk = KartuKeluarga::factory()->create();
        
        // This route usually triggers recalculations
        $response = $this->actingAs($this->user)
            ->post(route('kk.sync-summary'));

        $response->assertRedirect(route('kk.index'));
        if (session('error')) {
            dd(session('error'));
        }
        $response->assertSessionHas('success');
    }
}
