<?php

namespace Database\Seeders;

use App\Models\Corrida;
use App\Models\Manejada;

use Illuminate\Database\Seeder;

class CorridaSeeder extends Seeder
{
    public function run(): void
    {
        $manejadas = Manejada::query()->pluck('id_manejada');

        if ($manejadas->isEmpty()) {
        return;
    }

        Corrida::factory()->count(5)->create()->each(function ($corrida) use ($manejadas) {
            $cantidad = min(3, $manejadas->count());

            $corrida->manejadas()->sync(
                $manejadas->random($cantidad)->all()
            );
        });
    }
}
