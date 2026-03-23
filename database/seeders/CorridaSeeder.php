<?php

namespace Database\Seeders;

use App\Models\Corrida;
use App\Models\Urban;
use App\Models\UrbanCorrida;
use Illuminate\Database\Seeder;

class CorridaSeeder extends Seeder
{
    public function run(): void
    {
        $corridas = Corrida::factory(25)->create();

        foreach ($corridas as $corrida) {
            $urban = Urban::inRandomOrder()->first();
            UrbanCorrida::create([
                'id_urban' => $urban->id_urban,
                'id_corrida' => $corrida->id_corrida,
            ]);
        }
    }
}
