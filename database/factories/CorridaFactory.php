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
        $fecha = $this->faker->dateTimeBetween('now', '+30 days');
        $horaSalida = $this->faker->time('H:i');
        $horaLlegadaDate = clone $fecha;
        $horaLlegadaDate->modify('+4 hours');

        return [
            'id_ruta' => Ruta::all()->random()->id_ruta,
            'id_usuario' => User::all()->random()->id_usuario,
            'id_urban' => Urban::all()->random()->id_urban,
            'fecha' => $fecha->format('Y-m-d'),
            'hora_salida' => $horaSalida,
            'hora_llegada' => $horaLlegadaDate->format('H:i'),
        ];
    }
}
