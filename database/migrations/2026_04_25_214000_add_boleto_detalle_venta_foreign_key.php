<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boleto', function (Blueprint $table) {
            $table->foreign('id_detalle_venta')
                ->references('id_detalle_venta')
                ->on('detalle_venta')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('boleto', function (Blueprint $table) {
            $table->dropForeign(['id_detalle_venta']);
        });
    }
};
