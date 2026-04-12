<?php

namespace Database\Seeders;

use App\Models\Pais;
use Illuminate\Database\Seeder;

class PaisSeeder extends Seeder
{
    public function run(): void
    {
        Pais::factory(10)->create();
    }
}
