<?php

namespace Database\Factories;

use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use Illuminate\Database\Eloquent\Factories\Factory;

class PendudukFactory extends Factory
{
    protected $model = Penduduk::class;

    public function definition()
    {
        return [
            'nik' => $this->faker->unique()->numerify('################'),
            'nama' => $this->faker->name(),
            'jenis_kelamin' => $this->faker->randomElement(['LAKI-LAKI', 'PEREMPUAN']),
            'tempat_lahir' => $this->faker->city(),
            'tanggal_lahir' => $this->faker->date('Y-m-d', '-20 years'),
            'agama' => $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu']),
            'status_perkawinan' => $this->faker->randomElement(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']),
            'kedudukan_keluarga' => $this->faker->randomElement(['Kepala Keluarga', 'Istri', 'Anak', 'Mertua', 'Orang Tua']),
            'pendidikan' => $this->faker->randomElement(['TIDAK / BELUM SEKOLAH', 'SD', 'SMP', 'SMA', 'D3', 'S1', 'S2']),
            'pekerjaan' => $this->faker->jobTitle(),
            'nama_ayah' => $this->faker->name('male'),
            'nama_ibu' => $this->faker->name('female'),
            'kartu_keluarga_id' => KartuKeluarga::factory(),
            'status' => 'Aktif',
        ];
    }
}
