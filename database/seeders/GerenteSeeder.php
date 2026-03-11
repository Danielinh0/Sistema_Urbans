<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GerenteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = \App\Models\Usuario::inRandomOrder()->take(3)->get();
        foreach ($usuarios as $usuario) {
            \App\Models\Gerente::firstOrCreate(['id_usuario' => $usuario->id]);
        }
    }
}
