<?php

namespace Database\Seeders;

use App\Models\Ruta;
use Illuminate\Database\Seeder;

class RutaSeeder extends Seeder
{
    public function run(): void
    {
        $rutas = [
            ['nombre' => 'Ciudad de México - Guadalajara', 'distancia' => 535.50],
            ['nombre' => 'Ciudad de México - Monterrey', 'distancia' => 890.00],
            ['nombre' => 'Guadalajara - León', 'distancia' => 210.75],
            ['nombre' => 'Monterrey - Torreón', 'distancia' => 380.25],
            ['nombre' => 'Puebla - Veracruz', 'distancia' => 285.00],
            ['nombre' => 'Ciudad de México - Puebla', 'distancia' => 135.50],
            ['nombre' => 'Guadalajara - Puerto Vallarta', 'distancia' => 330.00],
            ['nombre' => 'Querétaro - San Miguel de Allende', 'distancia' => 65.00],
        ];

        foreach ($rutas as $ruta) {
            Ruta::create($ruta);
        }

        Ruta::factory(5)->create();
    }
}
