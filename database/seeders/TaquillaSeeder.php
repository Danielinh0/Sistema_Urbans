<?php

namespace Database\Seeders;

use App\Models\Taquilla;
use Illuminate\Database\Seeder;

class TaquillaSeeder extends Seeder
{
    public function run(): void
    {
        Taquilla::factory(5)->create();
    }
}
