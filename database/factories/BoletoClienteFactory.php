<?php

namespace Database\Factories;

use App\Models\BoletoCliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BoletoCliente>
 */
class BoletoClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_boleto' => \App\Models\Boleto::factory(),
        ];
    }
}
