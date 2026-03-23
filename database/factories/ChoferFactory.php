<?php

namespace Database\Factories;

use App\Models\Chofer;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChoferFactory extends Factory
{
    protected $model = Chofer::class;

    public function definition(): array
    {
        return [
            'id_usuario' => Usuario::factory(),
        ];
    }
}
