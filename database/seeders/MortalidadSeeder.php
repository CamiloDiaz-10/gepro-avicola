<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MortalidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mortalidades = [];
        
        // Lotes con sus usuarios responsables
        $lotes = [
            ['IDLote' => 1, 'IDUsuario' => 3], // Lote A1
            ['IDLote' => 2, 'IDUsuario' => 3], // Lote A2
            ['IDLote' => 3, 'IDUsuario' => 5], // Lote B1
            ['IDLote' => 4, 'IDUsuario' => 5], // Lote B2
            ['IDLote' => 5, 'IDUsuario' => 4], // Lote C1
            ['IDLote' => 6, 'IDUsuario' => 4], // Lote C2
            ['IDLote' => 7, 'IDUsuario' => 4], // Lote D1
            ['IDLote' => 8, 'IDUsuario' => 4], // Lote D2
            ['IDLote' => 9, 'IDUsuario' => 6], // Lote E1
            ['IDLote' => 10, 'IDUsuario' => 6], // Lote E2
        ];

        $causas = [
            'Muerte natural',
            'Enfermedad respiratoria',
            'Problemas digestivos',
            'Estrés por calor',
            'Newcastle',
            'Bronquitis infecciosa',
            'Coccidiosis',
            'Accidente',
            'Canibalismo',
            'Síndrome ascítico',
            'Problemas cardíacos',
            'Infección bacteriana'
        ];

        foreach ($lotes as $lote) {
            // Generar eventos de mortalidad esporádicos en los últimos 6 meses
            $fechaInicio = Carbon::now()->subMonths(6);
            $fechaFin = Carbon::now();
            
            // Generar entre 5-15 eventos de mortalidad por lote
            $numEventos = rand(5, 15);
            
            for ($i = 0; $i < $numEventos; $i++) {
                $fecha = Carbon::createFromTimestamp(
                    rand($fechaInicio->timestamp, $fechaFin->timestamp)
                );
                
                // Cantidad de mortalidad (1-8 aves por evento)
                $cantidad = rand(1, 8);
                
                // Seleccionar causa aleatoria
                $causa = $causas[array_rand($causas)];
                
                $mortalidades[] = [
                    'IDLote' => $lote['IDLote'],
                    'IDUsuario' => $lote['IDUsuario'],
                    'Fecha' => $fecha->format('Y-m-d'),
                    'Cantidad' => $cantidad,
                    'Causa' => $causa,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        // Ordenar por fecha
        usort($mortalidades, function($a, $b) {
            return strcmp($a['Fecha'], $b['Fecha']);
        });
        
        DB::table('mortalidad')->insert($mortalidades);
    }
}
