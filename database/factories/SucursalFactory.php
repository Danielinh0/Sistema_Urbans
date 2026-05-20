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
            'nombre' =>  $this->faker->city(),
            'id_direccion' => Direccion::factory(),
        ];
    }
}
