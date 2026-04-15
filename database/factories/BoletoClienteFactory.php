<?php

namespace Database\Factories;

use App\Models\BoletoCliente;
use App\Models\Boleto;
use App\Models\Asiento;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoletoClienteFactory extends Factory
{
    protected $model = BoletoCliente::class;

    public function definition(): array
    {
        return [
            'id_boleto' => Boleto::factory(),
            'id_asiento' => Asiento::factory(),
            'peso_equipaje' => $this->faker->randomFloat(2, 0, 30),
        ];
    }
}
