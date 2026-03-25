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
            'id_ruta' => Ruta::factory(),
            'id_urban' => Urban::factory(),
            'id_usuario' => User::factory(),
            'fecha' => $fecha->format('Y-m-d'),
            'hora_salida' => $horaSalida,
            'hora_llegada' => $horaLlegadaDate->format('H:i'),
        ];
    }
}
