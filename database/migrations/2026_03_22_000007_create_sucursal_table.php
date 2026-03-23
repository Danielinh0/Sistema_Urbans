<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sucursal', function (Blueprint $table) {
            $table->id('id_sucursal');
            $table->string('nombre');
            $table->string('ubicacion');
            $table->foreignId('id_direccion')->nullable()->constrained('direccion', 'id_direccion')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sucursal');
    }
};
