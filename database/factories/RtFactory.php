<?php

namespace Database\Factories;

use App\Models\Rt;
use App\Models\Rw;
use App\Models\Dusun;
use Illuminate\Database\Eloquent\Factories\Factory;

class RtFactory extends Factory
{
    protected $model = Rt::class;

    public function definition()
    {
        return [
            'rw_id' => Rw::factory(),
            'dusun_id' => Dusun::factory(),
            'kode' => str_pad($this->faker->unique()->numberBetween(1, 99), 3, '0', STR_PAD_LEFT),
            'nama' => 'RT ' . $this->faker->numberBetween(1, 99),
        ];
    }
}
