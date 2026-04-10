<?php

namespace Database\Seeders;

use App\Models\Boleto;
use Illuminate\Database\Seeder;

class BoletoSeeder extends Seeder
{
    public function run(): void
    {
        Boleto::factory(10)->create();
    }
}
