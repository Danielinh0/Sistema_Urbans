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

        Corrida::factory()->count(5)->create()->each(function ($corrida) use ($urbanIds) {
            $cantidad = min(3, $urbanIds->count());

            $corrida->urbans()->sync(
                $urbanIds->random($cantidad)->all()
            );
        });
    }
}
