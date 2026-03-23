<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('codigo_postal', function (Blueprint $table) {
            $table->id('id_cp');
            $table->foreignId('id_estado')->constrained('estado', 'id_estado')->onDelete('cascade');
            $table->string('numero');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('codigo_postal');
    }
};
