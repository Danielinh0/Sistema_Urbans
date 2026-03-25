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

        foreach ($urbans as $urban) {
            for ($i = 1; $i <= $urban->numero_asientos; $i++) {
                Asiento::create([
                    'id_urban' => $urban->id_urban,
                    'nombre' => 'A' . str_pad($i, 2, '0', STR_PAD_LEFT),
                ]);
            }
        }
    }
}
