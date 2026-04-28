<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corrida', function (Blueprint $table) {
            $table->id('id_corrida');
            $table->foreignId('id_ruta')->constrained('ruta', 'id_ruta')->onDelete('cascade');
            $table->foreignId('id_usuario')->constrained('users', 'id_usuario')->onDelete('cascade');

            $table->dateTime('datetime_salida');
            $table->dateTime('datetime_llegada')->nullable();
            
            $table->string('estado');

            $table->foreignId('id_urban')->constrained('urban', 'id_urban')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corrida');
    }
};
