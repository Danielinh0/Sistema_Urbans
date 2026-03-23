<?php

namespace Database\Seeders;

use App\Models\Urban;
use Illuminate\Database\Seeder;

class UrbanSeeder extends Seeder
{
    public function run(): void
    {
        Urban::factory(15)->create();
    }
}
