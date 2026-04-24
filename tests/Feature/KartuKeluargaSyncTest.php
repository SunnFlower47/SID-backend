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
        
        // Kepala Keluarga
        Penduduk::create([
            'nik' => '3205051111110001',
            'nkk' => $nkk,
            'nama' => 'Budi Santoso',
            'jenis_kelamin' => 'LAKI-LAKI',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '1980-01-01',
            'agama' => 'Islam',
            'pendidikan' => 'S1',
            'pekerjaan' => 'Wiraswasta',
            'status_perkawinan' => 'Kawin',
            'status' => 'aktif',
            'alamat' => 'Kp. Durian Runtuh',
            'rt' => '001',
            'rw' => '002',
            'dusun' => 'Dusun 1',
            'kedudukan_keluarga' => 'Kepala Keluarga',
        ]);

        // Istri
        Penduduk::create([
            'nik' => '3205052222220002',
            'nkk' => $nkk,
            'nama' => 'Siti Aminah',
            'jenis_kelamin' => 'PEREMPUAN',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '1982-05-05',
            'agama' => 'Islam',
            'pendidikan' => 'D3',
            'pekerjaan' => 'Ibu Rumah Tangga',
            'status_perkawinan' => 'Kawin',
            'status' => 'aktif',
            'alamat' => 'Kp. Durian Runtuh',
            'rt' => '001',
            'rw' => '002',
            'dusun' => 'Dusun 1',
            'kedudukan_keluarga' => 'Istri',
        ]);

        // Anak
        Penduduk::create([
            'nik' => '3205053333330003',
            'nkk' => $nkk,
            'nama' => 'Rizky',
            'jenis_kelamin' => 'LAKI-LAKI',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2010-10-10',
            'agama' => 'Islam',
            'pendidikan' => 'SD',
            'pekerjaan' => 'Pelajar',
            'status_perkawinan' => 'Belum Kawin',
            'status' => 'aktif',
            'alamat' => 'Kp. Durian Runtuh',
            'rt' => '001',
            'rw' => '002',
            'dusun' => 'Dusun 1',
            'kedudukan_keluarga' => 'Anak',
        ]);

        // 2. Clear Observer side-effects
        // Since Observers run on Create, the KK table might already be populated. 
        // We want to test the SYNC command, so we truncate the summary table first.
        KartuKeluarga::truncate();
        
        $this->assertDatabaseCount('kartu_keluargas', 0);

        // 3. Run Sync Command
        Artisan::call('sync:kartu-keluarga');

        // 4. Assertions
        $this->assertDatabaseHas('kartu_keluargas', [
            'nkk' => $nkk,
            'nama_kepala_keluarga' => 'Budi Santoso',
            'jumlah_anggota' => 3,
            'anggota_aktif' => 3,
            'anggota_mutasi' => 0,
            'dusun' => 'Dusun 1',
        ]);
    }

    public function test_sync_handles_mixed_status()
    {
        $nkk = '9999999999999999';
        
        // Active
        Penduduk::create([
            'nik' => '9991', 'nkk' => $nkk, 'nama' => 'A', 'jenis_kelamin' => 'L', 'tempat_lahir' => 'X', 'tanggal_lahir' => '2000-01-01', 'agama' => 'Islam', 'pendidikan' => 'SD', 'pekerjaan' => 'Tani', 'status' => 'aktif', 'alamat' => 'X', 'rt' => '01', 'rw' => '01', 'dusun' => 'D1', 'kedudukan_keluarga' => 'Kepala Keluarga'
        ]);

        // Create Mutation (Meninggal) manually to simulate existing bad data or non-observer state
        // But for sync command logic, it checks 'mutasis' table or 'deleted_at'.
        
        $p2 = Penduduk::create([
            'nik' => '9992', 'nkk' => $nkk, 'nama' => 'B', 'jenis_kelamin' => 'P', 'tempat_lahir' => 'X', 'tanggal_lahir' => '2000-01-01', 'agama' => 'Islam', 'pendidikan' => 'SD', 'pekerjaan' => 'Tani', 'status' => 'meninggal', 'alamat' => 'X', 'rt' => '01', 'rw' => '01', 'dusun' => 'D1', 'kedudukan_keluarga' => 'Istri'
        ]);
        
        // Simulate existing mutation record
        \Illuminate\Support\Facades\DB::table('mutasis')->insert([
            'penduduk_id' => $p2->id,
            'jenis_mutasi' => 'kematian',
            'kategori_mutasi' => 'meninggal_sakit', // Dummy value valid for enum or varchar
            'tanggal_mutasi' => now(),
            'asal_tujuan' => '-', // Required by schema apparently
            'alasan' => '-',      // Likely required too
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Soft delete the person because mutation observer usually does this, but we simulate state.
        $p2->delete();

        // Truncate summary
        KartuKeluarga::truncate();

        // Run Sync
        Artisan::call('sync:kartu-keluarga');

        // Assert
        $this->assertDatabaseHas('kartu_keluargas', [
            'nkk' => $nkk,
            'jumlah_anggota' => 2, // Total history (A + B)
            'anggota_aktif' => 1,  // Only A
            'anggota_meninggal' => 1, // B
        ]);
    }
}
