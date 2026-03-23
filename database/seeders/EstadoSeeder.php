<?php

namespace Database\Seeders;

use App\Models\Estado;
use App\Models\Pais;
use Illuminate\Database\Seeder;

class EstadoSeeder extends Seeder
{
    public function run(): void
    {
        $mexico = Pais::where('nombre', 'México')->first();

        $estadosMexico = [
            ['id_pais' => $mexico->id_pais, 'nombre' => 'Ciudad de México'],
            ['id_pais' => $mexico->id_pais, 'nombre' => 'Jalisco'],
            ['id_pais' => $mexico->id_pais, 'nombre' => 'Nuevo León'],
            ['id_pais' => $mexico->id_pais, 'nombre' => 'Puebla'],
            ['id_pais' => $mexico->id_pais, 'nombre' => 'Guanajuato'],
        ];

        foreach ($estadosMexico as $estado) {
            Estado::create($estado);
        }

        Estado::factory(5)->create();
    }
}
