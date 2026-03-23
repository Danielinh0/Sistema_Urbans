<?php

namespace Database\Seeders;

use App\Models\Chofer;
use Illuminate\Database\Seeder;

class ChoferSeeder extends Seeder
{
    public function run(): void
    {
        Chofer::factory(12)->create();
    }
}
