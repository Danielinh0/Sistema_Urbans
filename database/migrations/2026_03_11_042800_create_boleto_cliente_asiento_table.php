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
        Schema::create('boleto_cliente_asiento', function (Blueprint $table) {
            $table->unsignedBigInteger('id_boleto_cliente');
            $table->unsignedBigInteger('id_asiento');
            $table->foreign('id_boleto_cliente')->references('id_boleto')->on('boleto_clientes')->cascadeOnDelete();
            $table->foreign('id_asiento')->references('id_asiento')->on('asientos')->cascadeOnDelete();
            $table->primary(['id_boleto_cliente', 'id_asiento']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boleto_cliente_asiento');
    }
};
