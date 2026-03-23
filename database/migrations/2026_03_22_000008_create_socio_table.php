<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('socio', function (Blueprint $table) {
            $table->id('id_socio');
            $table->string('numero_telefonico');
            $table->string('correo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('socio');
    }
};
