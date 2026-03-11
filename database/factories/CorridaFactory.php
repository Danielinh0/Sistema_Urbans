<?php

namespace Database\Factories;

use App\Models\Corrida;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Corrida>
 */
class CorridaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'Hora_salida'  => $this->faker->time('H:i:s'),
            'Hora_llegada' => $this->faker->time('H:i:s'),
            'Fecha'        => $this->faker->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
            'id_urban'     => \App\Models\Urban::factory(),
        ];
    }
}
