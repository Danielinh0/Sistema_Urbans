<?php

namespace Database\Factories;

use App\Models\Taquilla;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Taquilla>
 */
class TaquillaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'monto_inicial' => $this->faker->randomFloat(2, 500, 5000),
            'monto_final'   => null,
        ];
    }
}
