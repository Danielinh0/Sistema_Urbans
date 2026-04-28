<?php

namespace Database\Factories;

use App\Models\Ruta;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Factories\Factory;

class RutaFactory extends Factory
{
    protected $model = Ruta::class;

    public function definition(): array
    {
        $origen = Sucursal::all()->random()->id_sucursal;
        $destino = Sucursal::all()->random()->id_sucursal;
        while ($origen === $destino) {
            $destino = Sucursal::all()->random()->id_sucursal;
        }

        return [
            'nombre' => " " . Sucursal::find($origen)->nombre . " - " . Sucursal::find($destino)->nombre,
            'distancia' => $this->faker->randomFloat(2, 50, 500),
            'tarifa_clientes' => $this->faker->randomFloat(2, 10, 100),
            'tarifa_paquete' => $this->faker->randomFloat(2, 20, 200),
            'tiempo_estimado' => $this->faker->time('H:i'),
            'id_sucursal_salida' => $origen,
            'id_sucursal_llegada' => $destino,
        ];
    }
}
