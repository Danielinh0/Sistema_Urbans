<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boleto_cliente', function (Blueprint $table) {
            $table->id('id_boleto');
            $table->foreignId('id_boleto')->constrained('boleto', 'id_boleto')->onDelete('cascade');
            $table->foreignId('id_asiento')->constrained('asiento', 'id_asiento')->onDelete('cascade');
            $table->decimal('peso_equipaje', 10, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boleto_cliente');
    }
};
