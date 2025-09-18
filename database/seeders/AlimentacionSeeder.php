<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AlimentacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alimentaciones = [];
        
        // Lotes con sus usuarios responsables y tipo de alimento principal
        $lotes = [
            ['IDLote' => 1, 'IDUsuario' => 3, 'TipoAlimento' => 3], // Lote A1 - Ponedoras Postura
            ['IDLote' => 2, 'IDUsuario' => 3, 'TipoAlimento' => 3], // Lote A2 - Ponedoras Postura
            ['IDLote' => 3, 'IDUsuario' => 5, 'TipoAlimento' => 5], // Lote B1 - Engorde Finalización
            ['IDLote' => 4, 'IDUsuario' => 5, 'TipoAlimento' => 3], // Lote B2 - Ponedoras Postura
            ['IDLote' => 5, 'IDUsuario' => 4, 'TipoAlimento' => 3], // Lote C1 - Ponedoras Postura
            ['IDLote' => 6, 'IDUsuario' => 4, 'TipoAlimento' => 3], // Lote C2 - Reproductoras
            ['IDLote' => 7, 'IDUsuario' => 4, 'TipoAlimento' => 3], // Lote D1 - Ponedoras Postura
            ['IDLote' => 8, 'IDUsuario' => 4, 'TipoAlimento' => 4], // Lote D2 - Engorde Iniciación
            ['IDLote' => 9, 'IDUsuario' => 6, 'TipoAlimento' => 3], // Lote E1 - Ponedoras Postura
            ['IDLote' => 10, 'IDUsuario' => 6, 'TipoAlimento' => 6], // Lote E2 - Maíz (Criollas)
        ];

        foreach ($lotes as $lote) {
            // Generar alimentación diaria para los últimos 60 días
            for ($i = 60; $i >= 1; $i--) {
                $fecha = Carbon::now()->subDays($i);
                
                // Alimentación principal (diaria)
                $cantidadBase = $this->getCantidadBasePorLote($lote['IDLote']);
                $variacion = rand(-10, 10) / 100; // ±10% de variación
                $cantidad = round($cantidadBase * (1 + $variacion), 2);
                
                $observaciones = null;
                if (rand(1, 20) === 1) { // 5% de probabilidad de observaciones
                    $observacionesArray = [
                        'Consumo normal',
                        'Buen apetito de las aves',
                        'Se aumentó la ración por clima frío',
                        'Consumo ligeramente bajo',
                        'Excelente aceptación del alimento',
                        'Se redujo por clima caluroso'
                    ];
                    $observaciones = $observacionesArray[array_rand($observacionesArray)];
                }
                
                $alimentaciones[] = [
                    'IDLote' => $lote['IDLote'],
                    'IDUsuario' => $lote['IDUsuario'],
                    'Fecha' => $fecha->format('Y-m-d'),
                    'IDTipoAlimento' => $lote['TipoAlimento'],
                    'CantidadKg' => $cantidad,
                    'Observaciones' => $observaciones,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // Ocasionalmente agregar suplementos (10% de probabilidad)
                if (rand(1, 10) === 1) {
                    $suplementos = [8, 9, 10]; // Carbonato de Calcio, Premezcla, Sal Mineral
                    $suplemento = $suplementos[array_rand($suplementos)];
                    $cantidadSuplemento = rand(1, 5) + (rand(0, 99) / 100); // 1-5 kg
                    
                    $alimentaciones[] = [
                        'IDLote' => $lote['IDLote'],
                        'IDUsuario' => $lote['IDUsuario'],
                        'Fecha' => $fecha->format('Y-m-d'),
                        'IDTipoAlimento' => $suplemento,
                        'CantidadKg' => $cantidadSuplemento,
                        'Observaciones' => 'Suplemento nutricional',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                // Insertar en lotes para evitar problemas de memoria
                if (count($alimentaciones) >= 100) {
                    DB::table('alimentacion')->insert($alimentaciones);
                    $alimentaciones = [];
                }
            }
        }
        
        // Insertar las alimentaciones restantes
        if (!empty($alimentaciones)) {
            DB::table('alimentacion')->insert($alimentaciones);
        }
    }
    
    /**
     * Obtener cantidad base de alimento por lote según el tamaño
     */
    private function getCantidadBasePorLote($idLote): float
    {
        $cantidades = [
            1 => 120.0,  // Lote A1 - 1500 gallinas
            2 => 96.0,   // Lote A2 - 1200 gallinas
            3 => 180.0,  // Lote B1 - 2000 pollos engorde
            4 => 144.0,  // Lote B2 - 1800 gallinas
            5 => 200.0,  // Lote C1 - 2500 gallinas
            6 => 64.0,   // Lote C2 - 800 reproductoras
            7 => 80.0,   // Lote D1 - 1000 gallinas
            8 => 135.0,  // Lote D2 - 1500 pollos engorde
            9 => 176.0,  // Lote E1 - 2200 gallinas
            10 => 35.0,  // Lote E2 - 500 criollas
        ];
        
        return $cantidades[$idLote] ?? 100.0;
    }
}
