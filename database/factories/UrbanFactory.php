<?php

namespace Database\Factories;

use App\Models\Urban;
use App\Models\Socio;
use Illuminate\Database\Eloquent\Factories\Factory;

class UrbanFactory extends Factory
{
    protected $model = Urban::class;

    public function definition(): array
    {
        return [
            'id_socio' => Socio::query()->inRandomOrder()->value('id_socio'),
            'placa' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{3}'),
            'codigo_urban' => 'URB-' . $this->faker->unique()->numberBetween(100, 999),
            'numero_asientos' => $this->faker->numberBetween(15, 30),
        ];
    }
}
