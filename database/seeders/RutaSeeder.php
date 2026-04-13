<?php

namespace Database\Seeders;

use App\Models\Ruta;
use Illuminate\Database\Seeder;


class RutaSeeder extends Seeder
{
    public function run(): void
    {
        Ruta::factory(10)->create();
    }
}
