<?php

namespace Database\Factories;

use App\Models\Cajero;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cajero>
 */
class CajeroFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_usuario' => \App\Models\Usuario::factory(),
        ];
    }
}
