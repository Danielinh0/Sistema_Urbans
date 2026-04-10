<?php

namespace Database\Seeders;

use App\Models\Colonia;
use Illuminate\Database\Seeder;

class ColoniaSeeder extends Seeder
{
    public function run(): void
    {
        Colonia::factory(5)->create();
    }
}
