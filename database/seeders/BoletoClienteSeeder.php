<?php

namespace Database\Seeders;

use App\Models\BoletoCliente;
use App\Models\Boleto;
use App\Models\Asiento;
use Illuminate\Database\Seeder;

class BoletoClienteSeeder extends Seeder
{
    public function run(): void
    {
        $boletos = Boleto::limit(25)->get();
        $asientos = Asiento::all();

        foreach ($boletos as $boleto) {
            BoletoCliente::create([
                'id_boleto' => $boleto->id_boleto,
                'id_asiento' => $asientos->random()->id_asiento,
                'peso_equipaje' => rand(0, 25) + rand(0, 99) / 100,
            ]);
        }
    }
}
