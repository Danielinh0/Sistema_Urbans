<?php

namespace Database\Seeders;

use App\Models\CodigoPostal;
use App\Models\Estado;
use Illuminate\Database\Seeder;

class CodigoPostalSeeder extends Seeder
{
    public function run(): void
    {
        $estados = Estado::all();
        foreach ($estados as $estado) {
            CodigoPostal::factory(5)->create([
                'id_estado' => $estado->id_estado,
            ]);
        }
    }
}
