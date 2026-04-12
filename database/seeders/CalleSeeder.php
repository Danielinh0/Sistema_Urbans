<?php

namespace Database\Seeders;

use App\Models\Calle;
use App\Models\Colonia;
use Illuminate\Database\Seeder;

class CalleSeeder extends Seeder
{
    public function run(): void
    {
        $colonias = Colonia::all();
        foreach ($colonias as $colonia) {
            Calle::factory(5)->create([
                'id_colonia' => $colonia->id_colonia,
            ]);
        }
    }
}
