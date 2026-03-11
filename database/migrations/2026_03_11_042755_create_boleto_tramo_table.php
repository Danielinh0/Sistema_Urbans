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
        Schema::create('boleto_tramo', function (Blueprint $table) {
            $table->foreignId('id_boleto')->constrained('boletos')->cascadeOnDelete();
            $table->foreignId('id_tramo')->constrained('tramos')->cascadeOnDelete();
            $table->primary(['id_boleto', 'id_tramo']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boleto_tramo');
    }
};
