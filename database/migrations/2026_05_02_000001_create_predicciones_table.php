<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('predicciones', function (Blueprint $table) {
            $table->id('id_prediccion');
            $table->foreignId('id_ruta')->constrained('ruta', 'id_ruta')->cascadeOnDelete();
            $table->foreignId('id_urban')->constrained('urban', 'id_urban')->cascadeOnDelete();
            $table->date('fecha_salida');
            $table->time('hora_salida');
            $table->tinyInteger('dia_semana');
            $table->tinyInteger('mes');
            $table->tinyInteger('dia_mes');
            $table->boolean('es_festivo')->default(false);
            $table->boolean('es_finde')->default(false);
            $table->integer('boletos_estimados');
            $table->string('modelo_version')->nullable();
            $table->decimal('r2_modelo', 8, 4)->nullable();
            $table->foreignId('id_usuario')->constrained('users', 'id_usuario')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('predicciones');
    }
};
