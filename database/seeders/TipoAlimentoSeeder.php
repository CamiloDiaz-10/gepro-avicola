<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoAlimentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipoAlimentos = [
            [
                'Nombre' => 'Concentrado Ponedoras Iniciación',
                'Proteina' => 20.50,
                'Energia' => 285.00, // Kcal/100g
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nombre' => 'Concentrado Ponedoras Levante',
                'Proteina' => 16.00,
                'Energia' => 275.00, // Kcal/100g
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nombre' => 'Concentrado Ponedoras Postura',
                'Proteina' => 17.50,
                'Energia' => 280.00, // Kcal/100g
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nombre' => 'Concentrado Engorde Iniciación',
                'Proteina' => 23.00,
                'Energia' => 300.00, // Kcal/100g
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nombre' => 'Concentrado Engorde Finalización',
                'Proteina' => 19.00,
                'Energia' => 310.00, // Kcal/100g
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nombre' => 'Maíz Molido',
                'Proteina' => 8.50,
                'Energia' => 335.00, // Kcal/100g
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nombre' => 'Torta de Soya',
                'Proteina' => 44.00,
                'Energia' => 223.00, // Kcal/100g
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nombre' => 'Carbonato de Calcio',
                'Proteina' => 0.00,
                'Energia' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nombre' => 'Premezcla Vitamínica',
                'Proteina' => 0.00,
                'Energia' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nombre' => 'Sal Mineral',
                'Proteina' => 0.00,
                'Energia' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tipo_alimentos')->insert($tipoAlimentos);
    }
}
