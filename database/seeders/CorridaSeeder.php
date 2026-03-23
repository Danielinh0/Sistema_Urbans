<?php

namespace Database\Seeders;

use App\Models\Corrida;
use App\Models\Urban;
use Illuminate\Database\Seeder;

class CorridaSeeder extends Seeder
{
    public function run(): void
    {
        $urbanIds = Urban::query()->pluck('id_urban');

        if ($urbanIds->isEmpty()) {
            return;
        }

        $corridas = Corrida::factory(25)->create();

        foreach ($corridas as $corrida) {
            $corrida->urbans()->attach($urbanIds->random());
        }
    }
}
