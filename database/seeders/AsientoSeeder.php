<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AsientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Urban::all()->each(function ($urban) {
            \App\Models\Asiento::factory($urban->numero_asientos)->create([
                'id_combi' => $urban->id_combi,
            ]);
        });
    }
}
