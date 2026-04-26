<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boleto', function (Blueprint $table) {
            $table->id('id_boleto');
            $table->foreignId('id_corrida')->constrained('corrida', 'id_corrida')->onDelete('cascade');
            $table->foreignId('id_cliente')->constrained('cliente', 'id_cliente')->onDelete('cascade');

            // Se agrega la FK en una migración posterior porque 'detalle_venta' se crea después.
            $table->foreignId('id_detalle_venta');

            $table->string('folio')->unique();
            $table->string('estado');
            $table->string('tipo_de_pago');
            $table->decimal('descuento', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boleto');
    }
};
