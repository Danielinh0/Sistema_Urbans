<?php

namespace Database\Seeders;

use App\Models\Asiento;
use App\Models\Urban;
use Illuminate\Database\Seeder;

class AsientoSeeder extends Seeder
{
    public function run(): void
    {
        $urbans = Urban::all();

        foreach ($urbans as $index => $urban) {
            // Calculamos la letra basada en el índice del foreach
            // $index parte de 0, así que 65 + 0 = 'A', 65 + 1 = 'B'...
            $letra = chr(65 + ($index % 26));

            for ($i = 1; $i <= $urban->numero_asientos; $i++) {
                Asiento::create([
                    'id_urban' => $urban->id_urban,
                    'nombre' => $letra . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'estado' => 'Libre',
                ]);
            }
        }
    }
}
