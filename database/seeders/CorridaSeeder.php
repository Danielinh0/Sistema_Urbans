<?php

namespace Database\Seeders;

use App\Models\Corrida;
use Illuminate\Database\Seeder;

class CorridaSeeder extends Seeder
{
    public function run(): void
    {
         Corrida::factory()->times(10)->create();
    }
}

