<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venta', function (Blueprint $table) {
            $table->id('id_venta');
            $table->foreignId('id_boleto')->constrained('boleto', 'id_boleto')->onDelete('cascade');
            $table->foreignId('id_cliente')->nullable()->constrained('cliente', 'id_cliente')->onDelete('set null');
            $table->integer('total');
            $table->integer('subtotal');
            $table->integer('descuento')->default(0);
            $table->date('fecha');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta');
    }
};
