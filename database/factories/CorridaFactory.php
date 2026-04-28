<?php

namespace Database\Factories;

use App\Models\Corrida;
use App\Models\Ruta;
use App\Models\Urban;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CorridaFactory extends Factory
{
    protected $model = Corrida::class;

    public function definition(): array
    {
        
        $salida = $this->faker->dateTimeBetween('now', '+20 days');
        $llegada = (clone $salida)->modify('+'.rand(2, 8).' hours');


        return [
            'id_ruta' => Ruta::all()->random()->id_ruta,
            'id_usuario' => User::all()->random()->id_usuario,
            'id_urban' => Urban::all()->random()->id_urban,
            'datetime_salida' => $salida,
            'datetime_llegada' => $llegada,
            'estado' => $this->faker->randomElement(['programada', 'reservada', 'finalizada', 'cancelada', 'en viaje']),
        ];
    }
}
