<?php

namespace Database\Factories;

use App\Models\CodigoPostal;
use App\Models\Estado;
use Illuminate\Database\Eloquent\Factories\Factory;

class CodigoPostalFactory extends Factory
{
    protected $model = CodigoPostal::class;

    public function definition(): array
    {
        return [
            'id_estado' => Estado::all()->random()->id_estado,
            'numero' => $this->faker->postcode(),
        ];
    }
}
