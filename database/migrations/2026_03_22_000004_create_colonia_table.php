<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colonia', function (Blueprint $table) {
            $table->id('id_colonia');
            $table->foreignId('id_cp')->constrained('codigo_postal', 'id_cp')->onDelete('cascade');
            $table->string('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colonia');
    }
};
