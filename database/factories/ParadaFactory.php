<?php

namespace Database\Factories;

use App\Models\Parada;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Parada>
 */
class ParadaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre'    => $this->faker->city(),
            'direccion' => $this->faker->streetAddress(),
        ];
    }
}
