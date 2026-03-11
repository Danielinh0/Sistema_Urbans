<?php

namespace Database\Factories;

use App\Models\Paquete;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Paquete>
 */
class PaqueteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'peso'         => $this->faker->randomFloat(2, 0.5, 50),
            'descripcion'  => $this->faker->sentence(),
            'tipo_de_pago' => $this->faker->randomElement(['efectivo', 'tarjeta', 'transferencia']),
            'destinatario' => $this->faker->name(),
            'id_boleto'    => \App\Models\BoletoPaquete::factory(),
        ];
    }
}
