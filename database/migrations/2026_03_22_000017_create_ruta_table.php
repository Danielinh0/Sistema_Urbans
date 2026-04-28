<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruta', function (Blueprint $table) {
            $table->id('id_ruta');
            $table->string('nombre');
            $table ->time('tiempo_estimado');
            $table->decimal('distancia', 10, 2);
            $table->decimal('tarifa_clientes', 10, 2);
            $table->decimal('tarifa_paquete', 10, 2);
            $table->foreignId('id_sucursal_salida')->constrained('sucursal', 'id_sucursal')->onDelete('cascade');
            $table->foreignId('id_sucursal_llegada')->constrained('sucursal', 'id_sucursal')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruta');
    }
};
