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
        Schema::create('alimentacion', function (Blueprint $table) {
            $table->id('IDAlimentacion');
            $table->unsignedBigInteger('IDLote');
            $table->unsignedBigInteger('IDUsuario');
            $table->date('Fecha');
            $table->unsignedBigInteger('IDTipoAlimento')->nullable();
            $table->decimal('CantidadKg', 10, 2);
            $table->text('Observaciones')->nullable();
            $table->timestamps();
            
            $table->foreign('IDLote')->references('IDLote')->on('lotes');
            $table->foreign('IDUsuario')->references('IDUsuario')->on('usuarios');
            $table->foreign('IDTipoAlimento')->references('IDTipoAlimento')->on('tipo_alimentos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alimentacion');
    }
};
