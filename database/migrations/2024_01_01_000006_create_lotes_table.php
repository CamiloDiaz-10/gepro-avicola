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
        Schema::create('lotes', function (Blueprint $table) {
            $table->id('IDLote');
            $table->unsignedBigInteger('IDFinca');
            $table->string('Nombre', 100);
            $table->date('FechaIngreso');
            $table->integer('CantidadInicial');
            $table->string('Raza', 50)->nullable();
            $table->timestamps();
            
            $table->foreign('IDFinca')->references('IDFinca')->on('fincas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
