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
        Schema::create('paquetes', function (Blueprint $table) {
            $table->id();
            $table->decimal('peso', 8, 2);
            $table->string('descripcion');
            $table->string('tipo_de_pago');
            $table->string('destinatario');
            $table->unsignedBigInteger('id_boleto');
            $table->foreign('id_boleto')->references('id_boleto')->on('boleto_paquetes')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paquetes');
    }
};
