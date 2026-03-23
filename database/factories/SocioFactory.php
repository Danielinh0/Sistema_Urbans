<?php

namespace Database\Factories;

use App\Models\Socio;
use Illuminate\Database\Eloquent\Factories\Factory;

class SocioFactory extends Factory
{
    protected $model = Socio::class;

    public function definition(): array
    {
        return [
            'numero_telefonico' => $this->faker->phoneNumber(),
            'correo' => $this->faker->unique()->companyEmail(),
        ];
    }
}
