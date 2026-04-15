<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

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
            UserSeeder::class,
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

        $Usuario = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    }
}
