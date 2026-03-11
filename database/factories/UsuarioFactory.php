<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Usuario>
 */
class UsuarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre'      => $this->faker->name(),
            'password'    => bcrypt('password'),
            'correo'      => $this->faker->unique()->safeEmail(),
            'direccion'   => $this->faker->streetAddress(),
            'id_sucursal' => \App\Models\Sucursal::factory(),
        ];
    }
}
