<?php

namespace Database\Factories;

use App\Models\Boleto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Boleto>
 */
class BoletoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'Total'              => $this->faker->randomFloat(2, 50, 800),
            'estado'             => $this->faker->randomElement(['activo', 'cancelado', 'usado']),
            'tipo_de_pago'       => $this->faker->randomElement(['efectivo', 'tarjeta', 'transferencia']),
            'tipo'               => $this->faker->randomElement(['cliente', 'paquete']),
            'timestamp_emision'  => now(),
            'descuento'          => $this->faker->randomFloat(2, 0, 100),
            'folio'              => strtoupper($this->faker->unique()->bothify('BOL-########')),
            'guia'               => null,
            'id_cliente'         => \App\Models\Cliente::factory(),
            'id_taquilla'        => \App\Models\Taquilla::factory(),
            'id_corrida'         => \App\Models\Corrida::factory(),
        ];
    }
}
