<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CajeroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = \App\Models\Usuario::inRandomOrder()->take(4)->get();
        foreach ($usuarios as $usuario) {
            \App\Models\Cajero::firstOrCreate(['id_usuario' => $usuario->id]);
        }
    }
}
