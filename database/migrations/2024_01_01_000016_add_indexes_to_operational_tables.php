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
        Schema::table('produccion_huevos', function (Blueprint $table) {
            $table->index('Fecha', 'IX_Produccion_Fecha');
        });

        Schema::table('alimentacion', function (Blueprint $table) {
            $table->index('Fecha', 'IX_Alimentacion_Fecha');
        });

        Schema::table('sanidad', function (Blueprint $table) {
            $table->index('Fecha', 'IX_Sanidad_Fecha');
        });

        Schema::table('celo', function (Blueprint $table) {
            $table->index('FechaCelo', 'IX_Celo_Fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produccion_huevos', function (Blueprint $table) {
            $table->dropIndex('IX_Produccion_Fecha');
        });

        Schema::table('alimentacion', function (Blueprint $table) {
            $table->dropIndex('IX_Alimentacion_Fecha');
        });

        Schema::table('sanidad', function (Blueprint $table) {
            $table->dropIndex('IX_Sanidad_Fecha');
        });

        Schema::table('celo', function (Blueprint $table) {
            $table->dropIndex('IX_Celo_Fecha');
        });
    }
};
