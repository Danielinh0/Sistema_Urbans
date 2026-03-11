<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SucursalSeeder::class,
            SocioSeeder::class,
            RutaSeeder::class,
            ParadaSeeder::class,
            UsuarioSeeder::class,
            GerenteSeeder::class,
            ChoferSeeder::class,
            CajeroSeeder::class,
            UrbanSeeder::class,
            AsientoSeeder::class,
            CorridaSeeder::class,
            TramoSeeder::class,
            TurnoSeeder::class,
            TaquillaSeeder::class,
            ClienteSeeder::class,
            BoletoSeeder::class,
            BoletoClienteSeeder::class,
            BoletoPaqueteSeeder::class,
            PaqueteSeeder::class,
        ]);
    }
}
