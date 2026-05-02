<?php

namespace Database\Factories;

use App\Models\KartuKeluarga;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Dusun;
use Illuminate\Database\Eloquent\Factories\Factory;

class KartuKeluargaFactory extends Factory
{
    protected $model = KartuKeluarga::class;

    public function definition()
    {
        return [
            'nkk' => $this->faker->unique()->numerify('################'),
            'nama_kepala_keluarga' => $this->faker->name('male'),
            'nik_kepala_keluarga' => $this->faker->unique()->numerify('################'),
            'alamat' => $this->faker->address(),
            'rt_id' => Rt::factory(),
            'rw_id' => function (array $attributes) {
                return Rt::find($attributes['rt_id'])->rw_id;
            },
            'dusun_id' => function (array $attributes) {
                return Rt::find($attributes['rt_id'])->dusun_id;
            },
            'jumlah_anggota' => 1,
            'anggota_aktif' => 1,
            'status_kk' => 'normal',
        ];
    }
}
