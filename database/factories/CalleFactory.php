<?php

namespace Database\Factories;

use App\Models\Calle;
use App\Models\Colonia;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalleFactory extends Factory
{
    protected $model = Calle::class;

    public function definition(): array
    {
        $tipos = ['Calle', 'Avenida', 'Boulevard', 'Cerrada'];
        return [
            'id_colonia' => Colonia::all()->random()->id_colonia,
            'nombre' => $this->faker->randomElement($tipos) . ' ' . $this->faker->lastName(),
        ];
    }
}
