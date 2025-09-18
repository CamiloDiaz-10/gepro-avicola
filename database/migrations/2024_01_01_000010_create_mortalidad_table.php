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
        Schema::create('mortalidad', function (Blueprint $table) {
            $table->id('IDMortalidad');
            $table->unsignedBigInteger('IDLote');
            $table->unsignedBigInteger('IDUsuario');
            $table->date('Fecha');
            $table->integer('Cantidad');
            $table->string('Causa', 255)->nullable();
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
        Schema::dropIfExists('mortalidad');
    }
};
