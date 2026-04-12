<?php

namespace Database\Factories;

use App\Models\Asiento;
use App\Models\Urban;
use Illuminate\Database\Eloquent\Factories\Factory;

class AsientoFactory extends Factory
{
    protected $model = Asiento::class;

    public function definition(): array
    {
        return [
            'id_urban' => Urban::factory(),
            'nombre' => $this->faker->regexify('[A-Z][0-9]{2}'),
            'estado' => 'Libre',
        ];
    }
}
