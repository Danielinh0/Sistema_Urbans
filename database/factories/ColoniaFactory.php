<?php

namespace Database\Factories;

use App\Models\Colonia;
use App\Models\CodigoPostal;
use Illuminate\Database\Eloquent\Factories\Factory;

class ColoniaFactory extends Factory
{
    protected $model = Colonia::class;

    public function definition(): array
    {
        return [
            'id_cp' => CodigoPostal::factory(),
            'nombre' => $this->faker->word() . ' ' . $this->faker->word(),
        ];
    }
}
