<?php

namespace Database\Factories;

use App\Models\Manejada;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Urban;

/**
 * @extends Factory<Manejada>
 */
class ManejadaFactory extends Factory
{
    
    protected $model = Manejada::class;

    public function definition(): array
    {
        return [
            'fecha' => $this->faker->date(),
            'id_usuario' => User::all()->random()->id_usuario,
            'id_urban' => Urban::all()->random()->id_urban,
        ];
    }
}
