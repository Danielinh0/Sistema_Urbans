<?php

namespace Database\Factories;

use App\Models\Direccion;
use App\Models\Calle;
use Illuminate\Database\Eloquent\Factories\Factory;

class DireccionFactory extends Factory
{
    protected $model = Direccion::class;

    public function definition(): array
    {
        return [
            'id_calle' => Calle::factory(),
            'numero_exterior' => $this->faker->numberBetween(1, 9999),
            'numero_interior' => $this->faker->optional(0.3)->numberBetween(1, 500),
        ];
    }
}
