<?php

namespace Database\Factories;

use App\Models\Tramo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tramo>
 */
class TramoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tarifa_cliente'  => $this->faker->randomFloat(2, 20, 300),
            'tarifa_paquete'  => $this->faker->randomFloat(2, 30, 500),
            'id_ruta'         => \App\Models\Ruta::factory(),
            'id_parada_sale'  => \App\Models\Parada::factory(),
            'id_parada_llega' => \App\Models\Parada::factory(),
        ];
    }
}
