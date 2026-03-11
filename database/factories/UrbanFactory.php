<?php

namespace Database\Factories;

use App\Models\Urban;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Urban>
 */
class UrbanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'placa'          => strtoupper($this->faker->bothify('???-####')),
            'codigo_combi'   => strtoupper($this->faker->bothify('URB-####')),
            'numero_asientos'=> $this->faker->numberBetween(15, 30),
            'id_socio'       => \App\Models\Socio::factory(),
        ];
    }
}
