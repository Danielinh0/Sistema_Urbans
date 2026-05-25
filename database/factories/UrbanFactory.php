<?php

namespace Database\Factories;

use App\Models\Socio;
use App\Models\Urban;
use Illuminate\Database\Eloquent\Factories\Factory;

class UrbanFactory extends Factory
{
    protected $model = Urban::class;

    public function definition(): array
    {
        return [
            'id_socio' => Socio::all()->random()->id_socio,
            'placa' => $this->faker->unique()->regexify('[A-Z]{3}-\d{2}-\d{2}'),
            'codigo_urban' => 'URB-'.$this->faker->unique()->numberBetween(100, 999),
            'numero_asientos' => $this->faker->randomElement([10, 15, 20]),
            'estado' => $this->faker->randomElement(['Activa', 'Fuera de servicio', 'Mantenimiento', 'Inactiva']),
        ];
    }
}
