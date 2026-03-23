<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boleto_paquete', function (Blueprint $table) {
            $table->unsignedBigInteger('id_boleto')->primary();
            $table->foreign('id_boleto')
            ->references('id_boleto')->on('boleto') ->onDelete('cascade');    

            $table->string('guia')->unique();
            $table->string('descripcion');
            $table->decimal('peso', 10, 2);
            $table->string('destinatario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boleto_paquete');
    }
};
