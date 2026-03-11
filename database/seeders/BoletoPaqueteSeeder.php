<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BoletoPaqueteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $boletos = \App\Models\Boleto::where('tipo', 'paquete')->take(10)->get();
        foreach ($boletos as $boleto) {
            \App\Models\BoletoPaquete::firstOrCreate(['id_boleto' => $boleto->id]);
        }
    }
}
