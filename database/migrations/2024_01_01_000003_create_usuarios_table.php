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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('IDUsuario');
            $table->unsignedBigInteger('IDRol')->nullable();
            $table->string('TipoIdentificacion', 50);
            $table->string('NumeroIdentificacion', 50)->unique();
            $table->string('Nombre', 50);
            $table->string('Apellido', 50);
            $table->string('Email', 150)->unique();
            $table->string('Telefono', 20);
            $table->date('FechaNacimiento')->nullable();
            $table->text('Direccion')->nullable();
            $table->string('Contrasena', 255); // Hash, nunca texto plano
            $table->string('UrlImagen', 255)->nullable();
            $table->timestamp('FechaCreacion')->useCurrent();
            $table->timestamps();
            
            $table->foreign('IDRol')->references('IDRol')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
