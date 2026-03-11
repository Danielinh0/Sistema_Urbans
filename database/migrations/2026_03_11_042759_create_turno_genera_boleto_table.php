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
        Schema::create('turno_genera_boleto', function (Blueprint $table) {
            $table->foreignId('id_turno')->constrained('turnos')->cascadeOnDelete();
            $table->foreignId('id_boleto')->constrained('boletos')->cascadeOnDelete();
            $table->primary(['id_turno', 'id_boleto']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turno_genera_boleto');
    }
};
