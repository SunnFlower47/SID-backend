<?php

namespace Database\Factories;

use App\Models\Dusun;
use Illuminate\Database\Eloquent\Factories\Factory;

class DusunFactory extends Factory
{
    protected $model = Dusun::class;

    public function definition()
    {
        return [
            'nama' => 'Dusun ' . $this->faker->unique()->cityPrefix(),
            'kode' => 'DSN-' . $this->faker->unique()->numberBetween(1, 99),
        ];
    }
}
