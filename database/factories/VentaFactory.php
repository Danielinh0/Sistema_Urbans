<?php

namespace Database\Factories;

use App\Models\Venta;
use App\Models\Boleto;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class VentaFactory extends Factory
{
    protected $model = Venta::class;

    public function definition(): array
    {
        $subtotal = $this->faker->numberBetween(100, 2000);
        $descuento = $this->faker->numberBetween(0, 200);

        return [
            'id_cliente' => Cliente::factory(),
            'total' => $subtotal - $descuento,
            'subtotal' => $subtotal,
            'descuento' => $descuento,
            'fecha' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
        ];
    }
}
