<?php

namespace Database\Factories;

use App\Models\Sucursal;
use App\Models\Direccion;
use Illuminate\Database\Eloquent\Factories\Factory;

class SucursalFactory extends Factory
{
    protected $model = Sucursal::class;

    public function definition(): array
    {
        return [
            'nombre' => 'Sucursal ' . $this->faker->city(),
            'ubicacion' => $this->faker->address(),
            'id_direccion' => Direccion::factory(),
        ];
    }
}
