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
        Schema::create('sanidad', function (Blueprint $table) {
            $table->id('IDSanidad');
            $table->unsignedBigInteger('IDLote');
            $table->unsignedBigInteger('IDUsuario');
            $table->date('Fecha');
            $table->string('Producto', 100);
            $table->string('TipoTratamiento', 50)->nullable(); // Vacuna, Desparasitante, Vitamina
            $table->string('Dosis', 50)->nullable();
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
        Schema::dropIfExists('sanidad');
    }
};
