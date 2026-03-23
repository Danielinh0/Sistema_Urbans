<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruta', function (Blueprint $table) {
            $table->id('id_ruta');
            $table->string('nombre');
            $table->decimal('distancia', 10, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruta');
    }
};
