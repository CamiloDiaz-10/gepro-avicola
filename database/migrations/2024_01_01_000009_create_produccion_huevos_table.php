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
        Schema::create('produccion_huevos', function (Blueprint $table) {
            $table->id('IDProduccion');
            $table->unsignedBigInteger('IDLote');
            $table->unsignedBigInteger('IDUsuario');
            $table->date('Fecha');
            $table->integer('CantidadHuevos');
            $table->integer('HuevosRotos')->default(0);
            $table->string('Turno', 50)->nullable(); // Mañana, Tarde
            $table->decimal('PesoPromedio', 5, 2)->nullable(); // en gramos
            $table->decimal('PorcentajePostura', 5, 2)->nullable(); // % de postura
            $table->text('Observaciones')->nullable();
            $table->timestamps();
            
            $table->foreign('IDLote')->references('IDLote')->on('lotes');
            $table->foreign('IDUsuario')->references('IDUsuario')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produccion_huevos');
    }
};
