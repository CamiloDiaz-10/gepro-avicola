<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MovimientoLoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movimientos = [];
        
        // Lotes con sus usuarios responsables
        $lotes = [
            ['IDLote' => 1, 'IDUsuario' => 3], // Lote A1
            ['IDLote' => 2, 'IDUsuario' => 3], // Lote A2
            ['IDLote' => 3, 'IDUsuario' => 5], // Lote B1 - Engorde
            ['IDLote' => 4, 'IDUsuario' => 5], // Lote B2
            ['IDLote' => 5, 'IDUsuario' => 4], // Lote C1
            ['IDLote' => 6, 'IDUsuario' => 4], // Lote C2
            ['IDLote' => 7, 'IDUsuario' => 4], // Lote D1
            ['IDLote' => 8, 'IDUsuario' => 4], // Lote D2 - Engorde
            ['IDLote' => 9, 'IDUsuario' => 6], // Lote E1
            ['IDLote' => 10, 'IDUsuario' => 6], // Lote E2
        ];

        $destinos = [
            'Mercado Central de Bucaramanga',
            'Distribuidora Avícola del Norte',
            'Supermercados Éxito',
            'Carulla Vivero',
            'Mercado de San Victorino',
            'Exportadora Colombiana S.A.',
            'Procesadora de Alimentos del Oriente',
            'Cooperativa Avícola Regional',
            'Planta de Beneficio Municipal',
            'Distribuidora La Granja'
        ];

        foreach ($lotes as $lote) {
            $fechaInicio = Carbon::now()->subMonths(6);
            $fechaFin = Carbon::now();
            
            // Movimientos de venta para lotes de engorde (más frecuentes)
            if (in_array($lote['IDLote'], [3, 8])) { // Lotes de engorde
                $numVentas = rand(3, 6);
                
                for ($i = 0; $i < $numVentas; $i++) {
                    $fecha = Carbon::createFromTimestamp(
                        rand($fechaInicio->timestamp, $fechaFin->timestamp)
                    );
                    
                    $cantidad = rand(200, 500); // Ventas de pollos de engorde
                    $destino = $destinos[array_rand($destinos)];
                    
                    $observaciones = [
                        'Venta programada según peso objetivo',
                        'Pollos en peso óptimo para sacrificio',
                        'Entrega sin complicaciones',
                        'Cliente satisfecho con la calidad',
                        'Peso promedio excelente'
                    ];
                    
                    $movimientos[] = [
                        'IDLote' => $lote['IDLote'],
                        'IDUsuario' => $lote['IDUsuario'],
                        'Fecha' => $fecha->format('Y-m-d'),
                        'TipoMovimiento' => 'Venta',
                        'Cantidad' => $cantidad,
                        'Destino' => $destino,
                        'Observaciones' => $observaciones[array_rand($observaciones)],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            } else {
                // Movimientos para lotes de ponedoras (menos frecuentes)
                $numMovimientos = rand(1, 3);
                
                for ($i = 0; $i < $numMovimientos; $i++) {
                    $fecha = Carbon::createFromTimestamp(
                        rand($fechaInicio->timestamp, $fechaFin->timestamp)
                    );
                    
                    $tiposMovimiento = ['Venta', 'Traslado'];
                    $tipoMovimiento = $tiposMovimiento[array_rand($tiposMovimiento)];
                    
                    if ($tipoMovimiento === 'Venta') {
                        $cantidad = rand(50, 200); // Venta de gallinas ponedoras
                        $destino = $destinos[array_rand($destinos)];
                        $observaciones = [
                            'Venta de gallinas de descarte',
                            'Gallinas con baja producción',
                            'Renovación del lote',
                            'Venta por edad avanzada'
                        ];
                    } else { // Traslado
                        $cantidad = rand(20, 100);
                        $fincasDestino = [
                            'Galpón 2 - Misma finca',
                            'Área de cuarentena',
                            'Sección de reproductoras',
                            'Galpón de levante'
                        ];
                        $destino = $fincasDestino[array_rand($fincasDestino)];
                        $observaciones = [
                            'Traslado por reorganización',
                            'Separación por edad',
                            'Mejora de condiciones',
                            'Optimización de espacios'
                        ];
                    }
                    
                    $movimientos[] = [
                        'IDLote' => $lote['IDLote'],
                        'IDUsuario' => $lote['IDUsuario'],
                        'Fecha' => $fecha->format('Y-m-d'),
                        'TipoMovimiento' => $tipoMovimiento,
                        'Cantidad' => $cantidad,
                        'Destino' => $destino,
                        'Observaciones' => $observaciones[array_rand($observaciones)],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            
            // Movimientos de compra ocasionales (reposición)
            if (rand(1, 4) === 1) { // 25% probabilidad por lote
                $fecha = Carbon::createFromTimestamp(
                    rand($fechaInicio->timestamp, $fechaFin->timestamp)
                );
                
                $cantidad = rand(100, 300);
                $proveedores = [
                    'Incubadora Regional S.A.',
                    'Avícola San Fernando',
                    'Granja La Esperanza',
                    'Incubadora del Valle',
                    'Reproductora Colombiana'
                ];
                
                $movimientos[] = [
                    'IDLote' => $lote['IDLote'],
                    'IDUsuario' => $lote['IDUsuario'],
                    'Fecha' => $fecha->format('Y-m-d'),
                    'TipoMovimiento' => 'Compra',
                    'Cantidad' => $cantidad,
                    'Destino' => $proveedores[array_rand($proveedores)],
                    'Observaciones' => 'Reposición de aves para mantener capacidad del lote',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        // Ordenar por fecha
        usort($movimientos, function($a, $b) {
            return strcmp($a['Fecha'], $b['Fecha']);
        });
        
        DB::table('movimiento_lote')->insert($movimientos);
    }
}
