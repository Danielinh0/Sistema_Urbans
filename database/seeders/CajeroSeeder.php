<?php

namespace Database\Seeders;

use App\Models\Cajero;
use Illuminate\Database\Seeder;

class CajeroSeeder extends Seeder
{
    public function run(): void
    {
        Cajero::factory(8)->create();
    }
}
