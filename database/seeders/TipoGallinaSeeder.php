<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoGallinaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipoGallinas = [
            [
                'Nombre' => 'Ponedora',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nombre' => 'Engorde',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nombre' => 'Reproductora',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nombre' => 'Criolla',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nombre' => 'Doble Propósito',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tipo_gallinas')->insert($tipoGallinas);
    }
}
