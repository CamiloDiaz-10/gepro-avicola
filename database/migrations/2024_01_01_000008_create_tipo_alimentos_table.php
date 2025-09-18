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
        Schema::create('tipo_alimentos', function (Blueprint $table) {
            $table->id('IDTipoAlimento');
            $table->string('Nombre', 100)->unique();
            $table->decimal('Proteina', 5, 2)->nullable();
            $table->decimal('Energia', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_alimentos');
    }
};
