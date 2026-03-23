<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('direccion', function (Blueprint $table) {
            $table->id('id_direccion');
            $table->foreignId('id_calle')->constrained('calle', 'id_calle')->onDelete('cascade');
            $table->string('numero_exterior');
            $table->string('numero_interior')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direccion');
    }
};
