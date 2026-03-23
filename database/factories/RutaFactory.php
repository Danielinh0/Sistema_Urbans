<?php

namespace Database\Factories;

use App\Models\Ruta;
use Illuminate\Database\Eloquent\Factories\Factory;

class RutaFactory extends Factory
{
    protected $model = Ruta::class;

    public function definition(): array
    {
        $origen = $this->faker->city();
        $destino = $this->faker->city();
        while ($origen === $destino) {
            $destino = $this->faker->city();
        }

        return [
            'nombre' => "$origen - $destino",
            'distancia' => $this->faker->randomFloat(2, 50, 500),
        ];
    }
}
