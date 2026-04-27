<?php

namespace Database\Factories;

use App\Models\Boleto;
use App\Models\Corrida;
use App\Models\Turno;
use App\Models\Cliente;
use App\Models\DetalleVenta;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoletoFactory extends Factory
{
    protected $model = Boleto::class;

    public function definition(): array
    {
        $estados = ['activo', 'cancelado', 'usado', 'reservado'];
        $tiposPago = ['efectivo', 'tarjeta', 'transferencia', 'qr'];

        return [
            'id_corrida' => Corrida::all()->random()->id_corrida,
            'id_detalle_venta' => DetalleVenta::all()->random()->id_detalle_venta,
            'id_cliente' => Cliente::all()->random()->id_cliente,
            'folio' => 'BOL-' . $this->faker->unique()->numerify('######'),
            'estado' => $this->faker->randomElement($estados),
            'tipo_de_pago' => $this->faker->randomElement($tiposPago),
            'descuento' => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}
