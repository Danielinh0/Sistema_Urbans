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
            RolSeeder::class,
            UserSeeder::class,
            UrbanSeeder::class,
            AsientoSeeder::class,
            RutaSeeder::class,
            CorridaSeeder::class,
            TurnoSeeder::class,
            VentaSeeder::class,
            DetalleVentaSeeder::class,
            BoletoSeeder::class,
            BoletoClienteSeeder::class,
            BoletoPaqueteSeeder::class,
            
        ]);

        $Usuario = User::factory()->create([
            'name' => 'Test User',
            'apellido_paterno' => 'Prueba',
            'apellido_materno' => 'Sistema',
            'email' => 'test@example.com',
        ]);

    }
}
