<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taquilla', function (Blueprint $table) {
            $table->id('id_taquilla');
            $table->decimal('monto_actual', 10, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taquilla');
    }
};
