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
        Schema::create('boletos', function (Blueprint $table) {
            $table->id();
            $table->decimal('Total', 10, 2);
            $table->string('estado');
            $table->string('tipo_de_pago');
            $table->string('tipo');
            $table->timestamp('timestamp_emision')->useCurrent();
            $table->decimal('descuento', 8, 2)->default(0);
            $table->string('folio')->unique();
            $table->string('guia')->nullable();
            $table->foreignId('id_cliente')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('id_taquilla')->constrained('taquillas');
            $table->foreignId('id_corrida')->constrained('corridas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boletos');
    }
};
