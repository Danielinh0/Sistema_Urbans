<?php

namespace Database\Seeders;

use App\Models\CodigoPostal;
use App\Models\Estado;
use Illuminate\Database\Seeder;

class CodigoPostalSeeder extends Seeder
{
    public function run(): void
    {
        CodigoPostal::factory(10)->create();
    }
}
