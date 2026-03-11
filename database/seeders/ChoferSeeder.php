<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChoferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = \App\Models\Usuario::inRandomOrder()->take(5)->get();
        foreach ($usuarios as $usuario) {
            \App\Models\Chofer::firstOrCreate(['id_usuario' => $usuario->id]);
        }
    }
}
