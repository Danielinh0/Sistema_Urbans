<?php

namespace Database\Factories;

use App\Models\BoletoPaquete;
use App\Models\Boleto;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoletoPaqueteFactory extends Factory
{
    protected $model = BoletoPaquete::class;

    public function definition(): array
    {
        $tiposPaquete = ['Electrónico', 'Documentos', 'Ropa', 'Alimentos', 'Artículos de hogar'];

        return [
            'id_boleto' => Boleto::all()->random()->id_boleto, // Asumiendo que ya hay boletos creados
            'guia' => 'GUIA-' . $this->faker->unique()->numerify('########'),
            'descripcion' => $this->faker->randomElement($tiposPaquete),
            'peso' => $this->faker->randomFloat(2, 0.5, 25),
            'destinatario' => $this->faker->name(),
        ];
    }
}
