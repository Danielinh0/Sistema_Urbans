<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('urban_corrida', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_urban')->constrained('urban', 'id_urban')->onDelete('cascade');
            $table->foreignId('id_corrida')->constrained('corrida', 'id_corrida')->onDelete('cascade');
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('urban_corrida');
    }
};
