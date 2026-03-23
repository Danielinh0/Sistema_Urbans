<?php

namespace Database\Factories;

use App\Models\Gerente;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class GerenteFactory extends Factory
{
    protected $model = Gerente::class;

    public function definition(): array
    {
        return [
            'id_usuario' => Usuario::factory(),
        ];
    }
}
