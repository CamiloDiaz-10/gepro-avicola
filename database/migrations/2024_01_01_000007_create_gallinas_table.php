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
        Schema::create('gallinas', function (Blueprint $table) {
            $table->id('IDGallina');
            $table->unsignedBigInteger('IDLote');
            $table->unsignedBigInteger('IDTipoGallina');
            $table->date('FechaNacimiento');
            $table->string('Estado', 50); // Activa, Vendida, Fallecida
            $table->string('NumeroIdentificacion', 50)->nullable();
            $table->string('UrlImagen', 255)->nullable();
            $table->timestamps();
            
            $table->foreign('IDLote')->references('IDLote')->on('lotes');
            $table->foreign('IDTipoGallina')->references('IDTipoGallina')->on('tipo_gallinas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallinas');
    }
};
