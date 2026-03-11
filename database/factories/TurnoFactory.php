<?php

namespace Database\Factories;

use App\Models\Turno;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Turno>
 */
class TurnoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fecha'   => $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'horario' => $this->faker->randomElement(['Matutino', 'Vespertino', 'Nocturno']),
        ];
    }
}
