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
        Schema::create('usuario_finca', function (Blueprint $table) {
            $table->id('IDUsuarioFinca');
            $table->unsignedBigInteger('IDUsuario');
            $table->unsignedBigInteger('IDFinca');
            $table->string('RolEnFinca', 50)->nullable(); // Ej: Propietario, Administrador
            $table->timestamps();
            
            $table->foreign('IDUsuario')->references('IDUsuario')->on('usuarios');
            $table->foreign('IDFinca')->references('IDFinca')->on('fincas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario_finca');
    }
};
