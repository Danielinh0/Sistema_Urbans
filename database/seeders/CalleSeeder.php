<?php

namespace Database\Seeders;

use App\Models\Calle;
use Illuminate\Database\Seeder;

class CalleSeeder extends Seeder
{
    public function run(): void
    {
        Calle::factory(20)->create();
    }
}
