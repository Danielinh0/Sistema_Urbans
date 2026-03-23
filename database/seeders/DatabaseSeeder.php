<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PaisSeeder::class,
            EstadoSeeder::class,
            CodigoPostalSeeder::class,
            ColoniaSeeder::class,
            CalleSeeder::class,
            DireccionSeeder::class,
            SucursalSeeder::class,
            SocioSeeder::class,
            ClienteSeeder::class,
            TaquillaSeeder::class,
            UsuarioSeeder::class,
            GerenteSeeder::class,
            CajeroSeeder::class,
            ChoferSeeder::class,
            UrbanSeeder::class,
            AsientoSeeder::class,
            RutaSeeder::class,
            CorridaSeeder::class,
            TurnoSeeder::class,
            BoletoSeeder::class,
            BoletoClienteSeeder::class,
            BoletoPaqueteSeeder::class,
            VentaSeeder::class,
        ]);
    }
}
