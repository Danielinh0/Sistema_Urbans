<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BoletoClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $boletos = \App\Models\Boleto::where('tipo', 'cliente')->take(15)->get();
        foreach ($boletos as $boleto) {
            \App\Models\BoletoCliente::firstOrCreate(['id_boleto' => $boleto->id]);
        }
    }
}
