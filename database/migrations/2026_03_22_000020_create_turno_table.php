<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turno', function (Blueprint $table) {
            $table->id('id_turno');
            $table->foreignId('id_usuario')->constrained('users', 'id_usuario')->onDelete('cascade');
            $table->foreignId('id_taquilla')->constrained('taquilla', 'id_taquilla')->onDelete('cascade');
            // Se agrega la clave foránea 'id_venta' en una migración posterior porque 'venta' es creada después de 'turno'.
            $table->foreignId('id_venta')->nullable();
            $table->integer('monto_inicial');
            $table->integer('monto_final');
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turno');
    }
};
