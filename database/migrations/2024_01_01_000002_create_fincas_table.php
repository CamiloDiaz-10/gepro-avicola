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
        Schema::create('fincas', function (Blueprint $table) {
            $table->id('IDFinca');
            $table->string('Nombre', 100);
            $table->string('Ubicacion', 255);
            $table->decimal('Latitud', 9, 6)->nullable();
            $table->decimal('Longitud', 9, 6)->nullable();
            $table->decimal('Hectareas', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fincas');
    }
};
