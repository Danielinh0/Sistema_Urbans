<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaqueteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\BoletoPaquete::all()->each(function ($bp) {
            \App\Models\Paquete::factory()->create(['id_boleto' => $bp->id_boleto]);
        });
    }
}
