<?php

namespace Database\Factories;

use App\Models\Taquilla;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaquillaFactory extends Factory
{
    protected $model = Taquilla::class;

    public function definition(): array
    {
        return [
            'monto_actual' => $this->faker->randomFloat(2, 1000, 50000),
        ];
    }
}
