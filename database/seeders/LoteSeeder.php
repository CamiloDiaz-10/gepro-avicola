<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lotes = [
            // Finca El Paraíso (ID: 1)
            [
                'IDFinca' => 1,
                'Nombre' => 'Lote A1 - Ponedoras',
                'FechaIngreso' => Carbon::now()->subMonths(8)->format('Y-m-d'),
                'CantidadInicial' => 1500,
                'Raza' => 'Hy-Line Brown',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'IDFinca' => 1,
                'Nombre' => 'Lote A2 - Ponedoras',
                'FechaIngreso' => Carbon::now()->subMonths(6)->format('Y-m-d'),
                'CantidadInicial' => 1200,
                'Raza' => 'Lohmann Brown',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Granja San José (ID: 2)
            [
                'IDFinca' => 2,
                'Nombre' => 'Lote B1 - Engorde',
                'FechaIngreso' => Carbon::now()->subMonths(2)->format('Y-m-d'),
                'CantidadInicial' => 2000,
                'Raza' => 'Ross 308',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'IDFinca' => 2,
                'Nombre' => 'Lote B2 - Ponedoras',
                'FechaIngreso' => Carbon::now()->subMonths(10)->format('Y-m-d'),
                'CantidadInicial' => 1800,
                'Raza' => 'Hy-Line W-36',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Avícola Los Pinos (ID: 3)
            [
                'IDFinca' => 3,
                'Nombre' => 'Lote C1 - Ponedoras',
                'FechaIngreso' => Carbon::now()->subMonths(7)->format('Y-m-d'),
                'CantidadInicial' => 2500,
                'Raza' => 'Isa Brown',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'IDFinca' => 3,
                'Nombre' => 'Lote C2 - Reproductoras',
                'FechaIngreso' => Carbon::now()->subMonths(12)->format('Y-m-d'),
                'CantidadInicial' => 800,
                'Raza' => 'Ross 308 Reproductoras',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Finca La Esperanza (ID: 4)
            [
                'IDFinca' => 4,
                'Nombre' => 'Lote D1 - Ponedoras',
                'FechaIngreso' => Carbon::now()->subMonths(5)->format('Y-m-d'),
                'CantidadInicial' => 1000,
                'Raza' => 'Lohmann LSL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'IDFinca' => 4,
                'Nombre' => 'Lote D2 - Engorde',
                'FechaIngreso' => Carbon::now()->subMonths(1)->format('Y-m-d'),
                'CantidadInicial' => 1500,
                'Raza' => 'Cobb 500',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Granja Santa María (ID: 5)
            [
                'IDFinca' => 5,
                'Nombre' => 'Lote E1 - Ponedoras',
                'FechaIngreso' => Carbon::now()->subMonths(9)->format('Y-m-d'),
                'CantidadInicial' => 2200,
                'Raza' => 'Hy-Line Brown',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'IDFinca' => 5,
                'Nombre' => 'Lote E2 - Criollas',
                'FechaIngreso' => Carbon::now()->subMonths(4)->format('Y-m-d'),
                'CantidadInicial' => 500,
                'Raza' => 'Criolla Colombiana',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('lotes')->insert($lotes);
    }
}
