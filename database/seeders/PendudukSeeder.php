<?php

namespace Database\Seeders;

use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use App\Models\Rt;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PendudukSeeder extends Seeder
{
    public function run(): void
    {
        $rts = Rt::all();
        if ($rts->isEmpty()) {
            $this->command->error('Run WilayahSeeder first!');
            return;
        }

        // --- 1. SEED SIMULATION USERS ---
        
        // User A
        $rtA = $rts->first();
        $kkA = KartuKeluarga::create([
            'nkk' => '1234567812345000',
            'nama_kepala_keluarga' => 'User Demo A',
            'nik_kepala_keluarga' => '1234567812345678',
            'alamat' => 'Jl. Cibatu Hilir No. 10',
            'rt_id' => $rtA->id,
            'rw_id' => $rtA->rw_id,
            'dusun_id' => $rtA->dusun_id,
            'status_kk' => 'normal'
        ]);

        Penduduk::create([
            'nik' => '1234567812345678',
            'nama' => 'User Demo A',
            'jenis_kelamin' => 'LAKI-LAKI',
            'tempat_lahir' => 'Purwakarta',
            'tanggal_lahir' => Carbon::createFromFormat('d-m-Y', '01-01-1990'),
            'agama' => 'ISLAM',
            'status_perkawinan' => 'BELUM KAWIN',
            'kedudukan_keluarga' => 'Kepala Keluarga',
            'pendidikan' => 'SLTA / SEDERAJAT',
            'pekerjaan' => 'KARYAWAN SWASTA',
            'kartu_keluarga_id' => $kkA->id,
        ]);

        // User B
        $rtB = $rts->skip(1)->first();
        $kkB = KartuKeluarga::create([
            'nkk' => '8888999988880000',
            'nama_kepala_keluarga' => 'User Demo B',
            'nik_kepala_keluarga' => '8888999988889999',
            'alamat' => 'Jl. Cibatu Girang No. 22',
            'rt_id' => $rtB->id,
            'rw_id' => $rtB->rw_id,
            'dusun_id' => $rtB->dusun_id,
            'status_kk' => 'normal'
        ]);

        Penduduk::create([
            'nik' => '8888999988889999',
            'nama' => 'User Demo B',
            'jenis_kelamin' => 'PEREMPUAN',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => Carbon::createFromFormat('d-m-Y', '20-05-1985'),
            'agama' => 'ISLAM',
            'status_perkawinan' => 'KAWIN',
            'kedudukan_keluarga' => 'Kepala Keluarga',
            'pendidikan' => 'DIPLOMA IV / STRATA I',
            'pekerjaan' => 'WIRASWASTA',
            'kartu_keluarga_id' => $kkB->id,
        ]);

        // --- 2. SEED RANDOM INHABITANTS (for stats) ---
        
        $faker = \Faker\Factory::create('id_ID');
        $pekerjaanList = ['PETANI', 'WIRASWASTA', 'BURUH HARIAN LEPAS', 'KARYAWAN SWASTA', 'TIDAK / BELUM BEKERJA', 'PELAJAR / MAHASISWA', 'PEDAGANG', 'GURU', 'PERAWAT'];
        $pendidikanList = ['TIDAK / BELUM SEKOLAH', 'TAMAT SD / SEDERAJAT', 'SLTP / SEDERAJAT', 'SLTA / SEDERAJAT', 'DIPLOMA IV / STRATA I'];
        $agamaList = ['ISLAM', 'KRISTEN', 'KATHOLIK', 'HINDU', 'BUDHA', 'KHONGHUCU'];

        for ($i = 0; $i < 150; $i++) {
            $rt = $rts->random();
            $nkk = $faker->numerify('3214################');
            
            $kk = KartuKeluarga::create([
                'nkk' => $nkk,
                'nama_kepala_keluarga' => $faker->name,
                'nik_kepala_keluarga' => $faker->numerify('3214################'),
                'alamat' => $faker->streetAddress,
                'rt_id' => $rt->id,
                'rw_id' => $rt->rw_id,
                'dusun_id' => $rt->dusun_id,
                'status_kk' => 'normal'
            ]);

            // Create 1-5 members per KK
            $numMembers = rand(1, 5);
            for ($j = 0; $j < $numMembers; $j++) {
                $jk = $faker->randomElement(['LAKI-LAKI', 'PEREMPUAN']);
                $kedudukan = ($j === 0) ? 'Kepala Keluarga' : $faker->randomElement(['Istri', 'Anak', 'Orang Tua']);
                
                Penduduk::create([
                    'nik' => $faker->numerify('3214################'),
                    'nama' => $faker->name($jk === 'LAKI-LAKI' ? 'male' : 'female'),
                    'jenis_kelamin' => $jk,
                    'tempat_lahir' => $faker->city,
                    'tanggal_lahir' => $faker->dateTimeBetween('-70 years', '-5 years'),
                    'agama' => $faker->randomElement($agamaList),
                    'status_perkawinan' => $kedudukan === 'Anak' ? 'BELUM KAWIN' : $faker->randomElement(['KAWIN', 'BELUM KAWIN', 'CERAI HIDUP', 'CERAI MATI']),
                    'kedudukan_keluarga' => $kedudukan,
                    'pendidikan' => $faker->randomElement($pendidikanList),
                    'pekerjaan' => $faker->randomElement($pekerjaanList),
                    'kartu_keluarga_id' => $kk->id,
                ]);
            }
        }
    }
}
