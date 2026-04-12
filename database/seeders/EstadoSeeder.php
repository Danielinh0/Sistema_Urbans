<?php

namespace Database\Seeders;

use App\Models\Estado;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Pais;
use Illuminate\Database\Seeder;

class EstadoSeeder extends Seeder
{
    public function run(): void
    {
        $paises = Pais::all();
        foreach ($paises as $pais) {
            Estado::factory(5)->create([
                'id_pais' => $pais->id_pais,
            ]);
        }
    }
}
