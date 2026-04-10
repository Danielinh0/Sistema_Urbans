<?php

namespace Database\Seeders;

use App\Models\Ruta;
use Illuminate\Database\Seeder;

class RutaSeeder extends Seeder
{
    public function run(): void
    {
        $rutas = [
            ['nombre' => 'Oaxaca - Juquila', 'tiempo_estimado' => '05:30:00', 'distancia' => 535.50, 'tarifa_clientes' => 50.00, 'tarifa_paquete' => 100.00],
            ['nombre' => 'Juquila - Oaxaca', 'tiempo_estimado' => '06:00:00', 'distancia' => 890.00, 'tarifa_clientes' => 80.00, 'tarifa_paquete' => 160.00],
            
        ];

        foreach ($rutas as $ruta) {
            Ruta::create($ruta);
        }
    }
}
