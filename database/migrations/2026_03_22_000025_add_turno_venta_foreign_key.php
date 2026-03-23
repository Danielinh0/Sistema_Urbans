<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('turno', function (Blueprint $table) {
            $table->foreign('id_venta')
                ->references('id_venta')
                ->on('venta')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('turno', function (Blueprint $table) {
            $table->dropForeign(['id_venta']);
        });
    }
};
