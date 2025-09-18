<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SanidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sanidades = [];
        
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

        // Productos y tratamientos comunes en avicultura
        $tratamientos = [
            // Vacunas
            ['Producto' => 'Vacuna Newcastle', 'Tipo' => 'Vacuna', 'Dosis' => '1 dosis/ave'],
            ['Producto' => 'Vacuna Bronquitis Infecciosa', 'Tipo' => 'Vacuna', 'Dosis' => '1 dosis/ave'],
            ['Producto' => 'Vacuna Gumboro', 'Tipo' => 'Vacuna', 'Dosis' => '1 dosis/ave'],
            ['Producto' => 'Vacuna Viruela Aviar', 'Tipo' => 'Vacuna', 'Dosis' => '1 dosis/ave'],
            ['Producto' => 'Vacuna Coriza', 'Tipo' => 'Vacuna', 'Dosis' => '0.5ml/ave'],
            
            // Desparasitantes
            ['Producto' => 'Levamisol', 'Tipo' => 'Desparasitante', 'Dosis' => '20mg/kg peso'],
            ['Producto' => 'Fenbendazol', 'Tipo' => 'Desparasitante', 'Dosis' => '5mg/kg peso'],
            ['Producto' => 'Ivermectina', 'Tipo' => 'Desparasitante', 'Dosis' => '0.2mg/kg peso'],
            
            // Vitaminas y suplementos
            ['Producto' => 'Complejo B', 'Tipo' => 'Vitamina', 'Dosis' => '1ml/litro agua'],
            ['Producto' => 'Vitamina ADE', 'Tipo' => 'Vitamina', 'Dosis' => '2ml/litro agua'],
            ['Producto' => 'Electrolitos', 'Tipo' => 'Suplemento', 'Dosis' => '1g/litro agua'],
            ['Producto' => 'Probióticos', 'Tipo' => 'Suplemento', 'Dosis' => '1g/kg alimento'],
            
            // Antibióticos (uso controlado)
            ['Producto' => 'Enrofloxacina', 'Tipo' => 'Antibiótico', 'Dosis' => '10mg/kg peso'],
            ['Producto' => 'Amoxicilina', 'Tipo' => 'Antibiótico', 'Dosis' => '15mg/kg peso'],
        ];

        foreach ($lotes as $lote) {
            // Generar plan sanitario para los últimos 6 meses
            $fechaInicio = Carbon::now()->subMonths(6);
            $fechaFin = Carbon::now();
            
            // Vacunaciones programadas (cada 2-3 meses)
            $vacunas = array_filter($tratamientos, function($t) { return $t['Tipo'] === 'Vacuna'; });
            $numVacunaciones = rand(2, 4);
            
            for ($i = 0; $i < $numVacunaciones; $i++) {
                $fecha = Carbon::createFromTimestamp(
                    rand($fechaInicio->timestamp, $fechaFin->timestamp)
                );
                
                $vacuna = $vacunas[array_rand($vacunas)];
                
                $observaciones = [
                    'Vacunación preventiva programada',
                    'Aplicación sin complicaciones',
                    'Aves respondieron bien al tratamiento',
                    'Control sanitario rutinario',
                    'Refuerzo de inmunidad'
                ];
                
                $sanidades[] = [
                    'IDLote' => $lote['IDLote'],
                    'IDUsuario' => $lote['IDUsuario'],
                    'Fecha' => $fecha->format('Y-m-d'),
                    'Producto' => $vacuna['Producto'],
                    'TipoTratamiento' => $vacuna['Tipo'],
                    'Dosis' => $vacuna['Dosis'],
                    'Observaciones' => $observaciones[array_rand($observaciones)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // Desparasitaciones (cada 3-4 meses)
            $desparasitantes = array_filter($tratamientos, function($t) { return $t['Tipo'] === 'Desparasitante'; });
            $numDesparasitaciones = rand(1, 2);
            
            for ($i = 0; $i < $numDesparasitaciones; $i++) {
                $fecha = Carbon::createFromTimestamp(
                    rand($fechaInicio->timestamp, $fechaFin->timestamp)
                );
                
                $desparasitante = $desparasitantes[array_rand($desparasitantes)];
                
                $sanidades[] = [
                    'IDLote' => $lote['IDLote'],
                    'IDUsuario' => $lote['IDUsuario'],
                    'Fecha' => $fecha->format('Y-m-d'),
                    'Producto' => $desparasitante['Producto'],
                    'TipoTratamiento' => $desparasitante['Tipo'],
                    'Dosis' => $desparasitante['Dosis'],
                    'Observaciones' => 'Desparasitación preventiva',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // Vitaminas y suplementos (ocasionales)
            $suplementos = array_filter($tratamientos, function($t) { 
                return in_array($t['Tipo'], ['Vitamina', 'Suplemento']); 
            });
            $numSuplementos = rand(3, 6);
            
            for ($i = 0; $i < $numSuplementos; $i++) {
                $fecha = Carbon::createFromTimestamp(
                    rand($fechaInicio->timestamp, $fechaFin->timestamp)
                );
                
                $suplemento = $suplementos[array_rand($suplementos)];
                
                $observacionesSuplemento = [
                    'Suplemento nutricional por estrés',
                    'Refuerzo vitamínico por cambio climático',
                    'Mejora del sistema inmunológico',
                    'Suplemento por baja en producción',
                    'Fortalecimiento general del lote'
                ];
                
                $sanidades[] = [
                    'IDLote' => $lote['IDLote'],
                    'IDUsuario' => $lote['IDUsuario'],
                    'Fecha' => $fecha->format('Y-m-d'),
                    'Producto' => $suplemento['Producto'],
                    'TipoTratamiento' => $suplemento['Tipo'],
                    'Dosis' => $suplemento['Dosis'],
                    'Observaciones' => $observacionesSuplemento[array_rand($observacionesSuplemento)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // Tratamientos curativos ocasionales (antibióticos)
            if (rand(1, 3) === 1) { // 33% probabilidad por lote
                $antibioticos = array_filter($tratamientos, function($t) { return $t['Tipo'] === 'Antibiótico'; });
                $antibiotico = $antibioticos[array_rand($antibioticos)];
                
                $fecha = Carbon::createFromTimestamp(
                    rand($fechaInicio->timestamp, $fechaFin->timestamp)
                );
                
                $sanidades[] = [
                    'IDLote' => $lote['IDLote'],
                    'IDUsuario' => $lote['IDUsuario'],
                    'Fecha' => $fecha->format('Y-m-d'),
                    'Producto' => $antibiotico['Producto'],
                    'TipoTratamiento' => $antibiotico['Tipo'],
                    'Dosis' => $antibiotico['Dosis'],
                    'Observaciones' => 'Tratamiento curativo por síntomas respiratorios',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        // Ordenar por fecha
        usort($sanidades, function($a, $b) {
            return strcmp($a['Fecha'], $b['Fecha']);
        });
        
        DB::table('sanidad')->insert($sanidades);
    }
}
