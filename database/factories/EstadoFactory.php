<?php

namespace Database\Factories;

use App\Models\Estado;
use App\Models\Pais;
use Illuminate\Database\Eloquent\Factories\Factory;

class EstadoFactory extends Factory
{
    protected $model = Estado::class;

    public function definition(): array
    {
        return [
            'id_pais' => Pais::factory(),
            'nombre' => $this->faker->state(),
        ];
    }
}
