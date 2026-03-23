<?php

namespace Database\Factories;

use App\Models\Turno;
use App\Models\Cajero;
use App\Models\Taquilla;
use Illuminate\Database\Eloquent\Factories\Factory;

class TurnoFactory extends Factory
{
    protected $model = Turno::class;

    public function definition(): array
    {
        $montoInicial = $this->faker->numberBetween(1000, 5000);
        $fecha = $this->faker->dateTimeBetween('-30 days', 'now');

        return [
            'id_cajero' => Cajero::factory(),
            'id_taquilla' => Taquilla::factory(),
            'id_venta' => null,
            'monto_inicial' => $montoInicial,
            'monto_final' => $montoInicial + $this->faker->numberBetween(500, 15000),
            'fecha' => $fecha->format('Y-m-d'),
            'hora_inicio' => $this->faker->time('H:i', '08:00'),
            'hora_fin' => $this->faker->time('H:i', '18:00'),
        ];
    }
}
