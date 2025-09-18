<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FincaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fincas = [
            [
                'Nombre' => 'Finca El Paraíso',
                'Ubicacion' => 'Vereda El Paraíso, Municipio de Piedecuesta, Santander',
                'Latitud' => 6.9889,
                'Longitud' => -73.0501,
                'Hectareas' => 15.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nombre' => 'Granja San José',
                'Ubicacion' => 'Km 12 Vía Bucaramanga - Floridablanca, Santander',
                'Latitud' => 7.0621,
                'Longitud' => -73.0985,
                'Hectareas' => 25.75,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nombre' => 'Avícola Los Pinos',
                'Ubicacion' => 'Vereda Los Pinos, Municipio de Girón, Santander',
                'Latitud' => 7.0690,
                'Longitud' => -73.1691,
                'Hectareas' => 32.20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nombre' => 'Finca La Esperanza',
                'Ubicacion' => 'Corregimiento de Café Madrid, Bucaramanga, Santander',
                'Latitud' => 7.1193,
                'Longitud' => -73.1227,
                'Hectareas' => 18.90,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nombre' => 'Granja Santa María',
                'Ubicacion' => 'Vereda Santa María, Municipio de Lebrija, Santander',
                'Latitud' => 7.0089,
                'Longitud' => -73.2089,
                'Hectareas' => 28.40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('fincas')->insert($fincas);
    }
}
