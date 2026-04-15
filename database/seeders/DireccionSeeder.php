<?php

namespace Database\Seeders;

use App\Models\Direccion;
use Illuminate\Database\Seeder;

class DireccionSeeder extends Seeder
{
    public function run(): void
    {
        Direccion::factory()->count(20)->create();
    }
}
