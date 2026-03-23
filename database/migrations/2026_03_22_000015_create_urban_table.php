<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('urban', function (Blueprint $table) {
            $table->id('id_urban');
            $table->foreignId('id_socio')->constrained('socio', 'id_socio')->onDelete('cascade');
            $table->string('placa')->unique();
            $table->string('codigo_urban')->unique();
            $table->integer('numero_asientos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('urban');
    }
};
