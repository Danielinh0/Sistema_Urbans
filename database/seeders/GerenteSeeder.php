<?php

namespace Database\Seeders;

use App\Models\Gerente;
use Illuminate\Database\Seeder;

class GerenteSeeder extends Seeder
{
    public function run(): void
    {
        Gerente::factory(5)->create();
    }
}
