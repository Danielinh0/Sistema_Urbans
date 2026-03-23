<?php

namespace Database\Seeders;

use App\Models\Pais;
use Illuminate\Database\Seeder;

class PaisSeeder extends Seeder
{
    public function run(): void
    {
        $paises = [
            ['nombre' => 'México'],
            ['nombre' => 'Estados Unidos'],
            ['nombre' => 'Guatemala'],
            ['nombre' => 'Canadá'],
            ['nombre' => 'España'],
        ];

        foreach ($paises as $pais) {
            Pais::create($pais);
        }

        Pais::factory(3)->create();
    }
}
