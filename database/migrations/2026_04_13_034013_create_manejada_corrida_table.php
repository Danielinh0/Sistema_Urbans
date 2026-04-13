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
        Schema::create('manejada_corrida', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_manejada')->constrained('manejada', 'id_manejada')->onDelete('cascade');
            $table->foreignId('id_corrida')->constrained('corrida', 'id_corrida')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manejada_corrida');
    }
};
