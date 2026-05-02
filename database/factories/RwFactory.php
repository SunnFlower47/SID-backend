<?php

namespace Database\Factories;

use App\Models\Rw;
use App\Models\Dusun;
use Illuminate\Database\Eloquent\Factories\Factory;

class RwFactory extends Factory
{
    protected $model = Rw::class;

    public function definition()
    {
        return [
            'kode' => str_pad($this->faker->unique()->numberBetween(1, 99), 3, '0', STR_PAD_LEFT),
            'nama' => 'RW ' . $this->faker->numberBetween(1, 99),
        ];
    }
}
