<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->foreignId('id_sucursal')->constrained('sucursal', 'id_sucursal')->onDelete('cascade');
            $table->foreignId('id_direccion')->nullable()->constrained('direccion', 'id_direccion')->onDelete('set null');
            $table->string('nombre');
            $table->string('correo')->unique();
            $table->string('password');
            $table->string('rol')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
