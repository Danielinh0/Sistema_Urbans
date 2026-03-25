<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('id_sucursal')
                ->references('id_sucursal')
                ->on('sucursal')
                ->onDelete('cascade');

            $table->foreign('id_direccion')
                ->references('id_direccion')
                ->on('direccion')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_sucursal']);
            $table->dropForeign(['id_direccion']);
        });
    }
};
