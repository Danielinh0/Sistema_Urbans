<?php

namespace Database\Factories;

use App\Models\Cajero;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class CajeroFactory extends Factory
{
    protected $model = Cajero::class;

    public function definition(): array
    {
        return [
            'id_usuario' => Usuario::factory(),
        ];
    }
}
