<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProduccionHuevosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $producciones = [];
        
        // Lotes de ponedoras para generar producción
        $lotesPonedoras = [
            ['IDLote' => 1, 'IDUsuario' => 3, 'CantidadGallinas' => 1500], // Lote A1
            ['IDLote' => 2, 'IDUsuario' => 3, 'CantidadGallinas' => 1200], // Lote A2
            ['IDLote' => 4, 'IDUsuario' => 5, 'CantidadGallinas' => 1800], // Lote B2
            ['IDLote' => 5, 'IDUsuario' => 4, 'CantidadGallinas' => 2500], // Lote C1
            ['IDLote' => 7, 'IDUsuario' => 4, 'CantidadGallinas' => 1000], // Lote D1
            ['IDLote' => 9, 'IDUsuario' => 6, 'CantidadGallinas' => 2200], // Lote E1
        ];

        foreach ($lotesPonedoras as $lote) {
            // Generar producción para los últimos 90 días
            for ($i = 90; $i >= 1; $i--) {
                $fecha = Carbon::now()->subDays($i);
                
                // Solo días laborales (lunes a sábado)
                if ($fecha->dayOfWeek == 0) continue; // Saltar domingos
                
                $turnos = ['Mañana', 'Tarde'];
                foreach ($turnos as $turno) {
                    // Calcular producción basada en el lote y turno
                    $porcentajeBase = rand(75, 95); // 75-95% de postura
                    if ($turno === 'Tarde') {
                        $porcentajeBase = $porcentajeBase * 0.7; // Menor producción en la tarde
                    }
                    
                    $cantidadHuevos = round(($lote['CantidadGallinas'] * $porcentajeBase / 100));
                    $huevosRotos = rand(0, round($cantidadHuevos * 0.05)); // 0-5% rotos
                    $pesoPromedio = rand(55, 70) + (rand(0, 99) / 100); // 55-70 gramos
                    $porcentajePostura = round(($cantidadHuevos / $lote['CantidadGallinas']) * 100, 2);
                    
                    $observaciones = null;
                    if (rand(1, 10) === 1) { // 10% de probabilidad de observaciones
                        $observacionesArray = [
                            'Producción normal',
                            'Algunas gallinas con estrés por calor',
                            'Excelente calidad de huevos',
                            'Se observó ligera disminución en la tarde',
                            'Gallinas muy activas',
                            'Calidad del alimento excelente'
                        ];
                        $observaciones = $observacionesArray[array_rand($observacionesArray)];
                    }
                    
                    $producciones[] = [
                        'IDLote' => $lote['IDLote'],
                        'IDUsuario' => $lote['IDUsuario'],
                        'Fecha' => $fecha->format('Y-m-d'),
                        'CantidadHuevos' => $cantidadHuevos,
                        'HuevosRotos' => $huevosRotos,
                        'Turno' => $turno,
                        'PesoPromedio' => $pesoPromedio,
                        'PorcentajePostura' => $porcentajePostura,
                        'Observaciones' => $observaciones,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    
                    // Insertar en lotes para evitar problemas de memoria
                    if (count($producciones) >= 100) {
                        DB::table('produccion_huevos')->insert($producciones);
                        $producciones = [];
                    }
                }
            }
        }
        
        // Insertar las producciones restantes
        if (!empty($producciones)) {
            DB::table('produccion_huevos')->insert($producciones);
        }
    }
}
