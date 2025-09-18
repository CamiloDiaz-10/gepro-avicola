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
        Schema::create('movimiento_lote', function (Blueprint $table) {
            $table->id('IDMovimiento');
            $table->unsignedBigInteger('IDLote');
            $table->unsignedBigInteger('IDUsuario');
            $table->date('Fecha');
            $table->string('TipoMovimiento', 50); // Venta, Traslado, Compra
            $table->integer('Cantidad');
            $table->string('Destino', 255)->nullable();
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
        Schema::dropIfExists('movimiento_lote');
    }
};
