<?php

namespace Database\Seeders;

use App\Models\Colonia;
use App\Models\CodigoPostal;
use Illuminate\Database\Seeder;

class ColoniaSeeder extends Seeder
{
    public function run(): void
    {
        $codigosPostales = CodigoPostal::all();
        foreach ($codigosPostales as $codigoPostal) {
            Colonia::factory(2)->create([
                'id_cp' => $codigoPostal->id_cp,
            ]);
        }
    }
}
