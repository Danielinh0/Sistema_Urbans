<?php

namespace Database\Seeders;

use App\Models\Ruta;
use Illuminate\Database\Seeder;

class RutaSeeder extends Seeder
{
    public function run(): void
    {
        $rutas = [
            ['nombre' => 'Oaxaca - Juquila', 'distancia' => 535.50, 'tarifa_clientes' => 50.00, 'tarifa_paquete' => 100.00],
            ['nombre' => 'Juquila - Oaxaca', 'distancia' => 890.00, 'tarifa_clientes' => 80.00, 'tarifa_paquete' => 160.00],
            
        ];

        foreach ($rutas as $ruta) {
            Ruta::create($ruta);
        }
    }
}
