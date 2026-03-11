<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tramos', function (Blueprint $table) {
            $table->id();
            $table->decimal('tarifa_cliente', 8, 2);
            $table->decimal('tarifa_paquete', 8, 2);
            $table->foreignId('id_ruta')->constrained('rutas')->cascadeOnDelete();
            $table->unsignedBigInteger('id_parada_sale');
            $table->unsignedBigInteger('id_parada_llega');
            $table->foreign('id_parada_sale')->references('id')->on('paradas');
            $table->foreign('id_parada_llega')->references('id')->on('paradas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tramos');
    }
};
