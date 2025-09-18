<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GallinaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gallinas = [];
        
        // Generar gallinas para cada lote
        $lotes = [
            // [IDLote, IDTipoGallina, CantidadGallinas, FechaBase]
            [1, 1, 100, Carbon::now()->subMonths(8)], // Lote A1 - Ponedoras
            [2, 1, 80, Carbon::now()->subMonths(6)],  // Lote A2 - Ponedoras
            [3, 2, 150, Carbon::now()->subMonths(2)], // Lote B1 - Engorde
            [4, 1, 120, Carbon::now()->subMonths(10)], // Lote B2 - Ponedoras
            [5, 1, 180, Carbon::now()->subMonths(7)], // Lote C1 - Ponedoras
            [6, 3, 60, Carbon::now()->subMonths(12)], // Lote C2 - Reproductoras
            [7, 1, 70, Carbon::now()->subMonths(5)],  // Lote D1 - Ponedoras
            [8, 2, 100, Carbon::now()->subMonths(1)], // Lote D2 - Engorde
            [9, 1, 150, Carbon::now()->subMonths(9)], // Lote E1 - Ponedoras
            [10, 4, 40, Carbon::now()->subMonths(4)], // Lote E2 - Criollas
        ];

        $contador = 1;
        foreach ($lotes as [$idLote, $idTipoGallina, $cantidad, $fechaBase]) {
            for ($i = 1; $i <= $cantidad; $i++) {
                $estados = ['Activa', 'Activa', 'Activa', 'Activa', 'Vendida', 'Fallecida'];
                $estado = $estados[array_rand($estados)];
                
                // Generar fecha de nacimiento aleatoria cerca de la fecha base
                $fechaNacimiento = $fechaBase->copy()->addDays(rand(-30, 30));
                
                $gallinas[] = [
                    'IDLote' => $idLote,
                    'IDTipoGallina' => $idTipoGallina,
                    'FechaNacimiento' => $fechaNacimiento->format('Y-m-d'),
                    'Estado' => $estado,
                    'NumeroIdentificacion' => sprintf('G%04d-%02d', $contador, $idLote),
                    'UrlImagen' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                $contador++;
                
                // Insertar en lotes para evitar problemas de memoria
                if (count($gallinas) >= 100) {
                    DB::table('gallinas')->insert($gallinas);
                    $gallinas = [];
                }
            }
        }
        
        // Insertar las gallinas restantes
        if (!empty($gallinas)) {
            DB::table('gallinas')->insert($gallinas);
        }
    }
}
