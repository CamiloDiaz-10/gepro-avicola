<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gallinas', function (Blueprint $table) {
            if (!Schema::hasColumn('gallinas', 'Peso')) {
                $table->decimal('Peso', 8, 2)->nullable()->after('FechaNacimiento');
            }
            if (!Schema::hasColumn('gallinas', 'qr_token')) {
                $table->string('qr_token', 100)->nullable()->unique()->after('UrlImagen');
            }
            if (!Schema::hasColumn('gallinas', 'qr_image_path')) {
                $table->string('qr_image_path', 255)->nullable()->after('qr_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('gallinas', function (Blueprint $table) {
            if (Schema::hasColumn('gallinas', 'qr_image_path')) {
                $table->dropColumn('qr_image_path');
            }
            if (Schema::hasColumn('gallinas', 'qr_token')) {
                $table->dropUnique(['qr_token']);
                $table->dropColumn('qr_token');
            }
            if (Schema::hasColumn('gallinas', 'Peso')) {
                $table->dropColumn('Peso');
            }
        });
    }
};
