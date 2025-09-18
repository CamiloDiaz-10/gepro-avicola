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
        Schema::table('usuarios', function (Blueprint $table) {
            // Verificar si las columnas no existen antes de agregarlas
            if (!Schema::hasColumn('usuarios', 'NumeroIdentificacion')) {
                $table->string('NumeroIdentificacion', 50)->unique()->after('TipoIdentificacion');
            }
            if (!Schema::hasColumn('usuarios', 'FechaNacimiento')) {
                $table->date('FechaNacimiento')->nullable()->after('Telefono');
            }
            if (!Schema::hasColumn('usuarios', 'Direccion')) {
                $table->text('Direccion')->nullable()->after('FechaNacimiento');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['NumeroIdentificacion', 'FechaNacimiento', 'Direccion']);
        });
    }
};
