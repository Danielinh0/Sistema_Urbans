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
        Schema::create('urbans', function (Blueprint $table) {
            $table->id('id_combi');
            $table->string('placa')->unique();
            $table->string('codigo_combi')->unique();
            $table->integer('numero_asientos');
            $table->foreignId('id_socio')->constrained('socios')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('urbans');
    }
};
