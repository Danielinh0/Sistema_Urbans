<?php

namespace Database\Factories;

use App\Models\DetalleVenta;
use app\Models\Venta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DetalleVenta>
 */
class DetalleVentaFactory extends Factory
{
    protected $model = DetalleVenta::class;

    public function definition(): array
    {
        return [
            'id_venta' => Venta::all()->random()->id_venta,
        ];
    }
}
