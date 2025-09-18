<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CeloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $celos = [];
        
        // Obtener gallinas reproductoras (tipo 3) para registrar celos
        $gallinasReproductoras = DB::table('gallinas')
            ->where('IDTipoGallina', 3) // Reproductoras
            ->where('Estado', 'Activa')
            ->pluck('IDGallina')
            ->toArray();
        
        if (empty($gallinasReproductoras)) {
            // Si no hay reproductoras, usar algunas ponedoras como ejemplo
            $gallinasReproductoras = DB::table('gallinas')
                ->where('IDTipoGallina', 1) // Ponedoras
                ->where('Estado', 'Activa')
                ->limit(20)
                ->pluck('IDGallina')
                ->toArray();
        }
        
        $observacionesCelo = [
            'Celo normal, gallina receptiva',
            'Comportamiento típico de celo',
            'Gallina en período reproductivo óptimo',
            'Celo detectado por comportamiento',
            'Período fértil identificado',
            'Gallina lista para reproducción',
            'Celo evidente, comportamiento característico',
            'Período reproductivo activo'
        ];
        
        foreach ($gallinasReproductoras as $idGallina) {
            // Generar entre 8-15 registros de celo por gallina en los últimos 6 meses
            $numCelos = rand(8, 15);
            $fechaInicio = Carbon::now()->subMonths(6);
            
            for ($i = 0; $i < $numCelos; $i++) {
                // Los celos en gallinas ocurren aproximadamente cada 21-25 días
                $diasEntreCelos = rand(21, 25);
                $fechaCelo = $fechaInicio->copy()->addDays($i * $diasEntreCelos);
                
                // Solo agregar si la fecha no es futura
                if ($fechaCelo->lte(Carbon::now())) {
                    $celos[] = [
                        'IDGallina' => $idGallina,
                        'FechaCelo' => $fechaCelo->format('Y-m-d'),
                        'Observaciones' => $observacionesCelo[array_rand($observacionesCelo)],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                // Insertar en lotes para evitar problemas de memoria
                if (count($celos) >= 100) {
                    DB::table('celo')->insert($celos);
                    $celos = [];
                }
            }
        }
        
        // Insertar los celos restantes
        if (!empty($celos)) {
            // Ordenar por fecha antes de insertar
            usort($celos, function($a, $b) {
                return strcmp($a['FechaCelo'], $b['FechaCelo']);
            });
            
            DB::table('celo')->insert($celos);
        }
    }
}
