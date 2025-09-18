<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reportes = [];
        
        // Usuarios que pueden generar reportes
        $usuarios = [
            1, // Admin
            2, // María (Propietario)
            3, // José (Empleado)
            4, // Ana (Propietario)
            5, // Pedro (Empleado)
            6, // Laura (Empleado)
        ];
        
        $tiposReporte = [
            // Reportes de producción
            'Reporte semanal de producción de huevos - Lote A1: Producción estable con promedio de 1,200 huevos diarios. Calidad excelente.',
            'Análisis mensual de postura - Lote C1: Porcentaje de postura del 89%, dentro de parámetros normales para la raza.',
            'Reporte de eficiencia productiva - Finca El Paraíso: Incremento del 5% en producción respecto al mes anterior.',
            
            // Reportes de sanidad
            'Reporte sanitario semanal - Lote B2: Aplicación de vacuna Newcastle sin complicaciones. Todas las aves en buen estado.',
            'Control veterinario mensual - Granja San José: Revisión general satisfactoria, no se detectaron enfermedades.',
            'Reporte de mortalidad - Lote D1: Mortalidad del 0.8%, dentro de rangos aceptables. Causas naturales.',
            
            // Reportes de alimentación
            'Reporte de consumo de alimento - Lote E1: Consumo promedio de 110g/ave/día, óptimo para la etapa productiva.',
            'Análisis nutricional mensual - Avícola Los Pinos: Cambio a concentrado con mayor proteína mostró mejores resultados.',
            'Control de inventario de alimentos - Finca La Esperanza: Stock suficiente para 3 semanas, próximo pedido programado.',
            
            // Reportes económicos
            'Reporte financiero semanal - Granja Santa María: Ingresos por venta de huevos superaron proyecciones en 12%.',
            'Análisis de costos de producción - Lote B1: Costo por kg de pollo en rango competitivo del mercado.',
            'Reporte de ventas mensual - Finca El Paraíso: Venta de 45,000 huevos con excelente aceptación del cliente.',
            
            // Reportes de mantenimiento
            'Reporte de mantenimiento de instalaciones - Galpón 1: Reparación de bebederos completada, funcionamiento óptimo.',
            'Inspección de equipos - Granja San José: Todos los sistemas de ventilación operando correctamente.',
            'Reporte de infraestructura - Avícola Los Pinos: Ampliación del galpón 3 completada según cronograma.',
            
            // Reportes de personal
            'Reporte de capacitación del personal - Finca La Esperanza: Entrenamiento en bioseguridad completado exitosamente.',
            'Evaluación de desempeño - Equipo de trabajo: Cumplimiento de metas del 95%, excelente coordinación.',
            
            // Reportes de calidad
            'Control de calidad de huevos - Lote A2: 98% de huevos clasificados como categoría A, excelente estándar.',
            'Reporte de peso promedio - Lote D2: Pollos alcanzaron peso objetivo de 2.2 kg en tiempo esperado.',
            'Análisis de conversión alimenticia - Lote B1: Ratio de 1.8:1, eficiencia superior al promedio del sector.',
            
            // Reportes ambientales
            'Reporte ambiental - Granja Santa María: Manejo de residuos orgánicos según normativas ambientales.',
            'Control de temperatura y humedad - Galpón 2: Condiciones ambientales óptimas mantenidas durante la semana.',
            
            // Reportes de bioseguridad
            'Reporte de bioseguridad - Finca El Paraíso: Protocolos de desinfección implementados correctamente.',
            'Control de acceso - Avícola Los Pinos: Registro de visitantes y vehículos actualizado según procedimientos.',
        ];
        
        foreach ($usuarios as $idUsuario) {
            // Generar entre 8-15 reportes por usuario en los últimos 3 meses
            $numReportes = rand(8, 15);
            $fechaInicio = Carbon::now()->subMonths(3);
            $fechaFin = Carbon::now();
            
            for ($i = 0; $i < $numReportes; $i++) {
                $fecha = Carbon::createFromTimestamp(
                    rand($fechaInicio->timestamp, $fechaFin->timestamp)
                );
                
                // Seleccionar descripción aleatoria
                $descripcion = $tiposReporte[array_rand($tiposReporte)];
                
                $reportes[] = [
                    'IDUsuario' => $idUsuario,
                    'Fecha' => $fecha->format('Y-m-d'),
                    'Descripcion' => $descripcion,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        // Ordenar por fecha
        usort($reportes, function($a, $b) {
            return strcmp($a['Fecha'], $b['Fecha']);
        });
        
        DB::table('reportes')->insert($reportes);
    }
}
