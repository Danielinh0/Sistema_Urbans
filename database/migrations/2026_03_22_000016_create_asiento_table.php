<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asiento', function (Blueprint $table) {
            $table->id('id_asiento');
            $table->foreignId('id_urban')->constrained('urban', 'id_urban')->onDelete('cascade');
            $table->string('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asiento');
    }
};
