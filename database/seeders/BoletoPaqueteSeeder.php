<?php

namespace Database\Seeders;

use App\Models\BoletoPaquete;
use App\Models\Boleto;
use Illuminate\Database\Seeder;

class BoletoPaqueteSeeder extends Seeder
{
    public function run(): void
    {
        $boletos = Boleto::offset(25)->limit(15)->get();

        foreach ($boletos as $boleto) {
            BoletoPaquete::create([
                'id_boleto' => $boleto->id_boleto,
                'guia' => 'GUIA-' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT),
                'descripcion' => 'Paquete diverso',
                'peso' => rand(1, 20) + rand(0, 99) / 100,
                'destinatario' => 'Destinatario ' . rand(1, 100),
            ]);
        }
    }
}
