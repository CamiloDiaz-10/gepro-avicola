<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsuarioFincaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarioFincas = [
            // Admin tiene acceso a todas las fincas
            ['IDUsuario' => 1, 'IDFinca' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['IDUsuario' => 1, 'IDFinca' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['IDUsuario' => 1, 'IDFinca' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['IDUsuario' => 1, 'IDFinca' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['IDUsuario' => 1, 'IDFinca' => 5, 'created_at' => now(), 'updated_at' => now()],
            
            // María (Propietario) - Finca El Paraíso y Granja San José
            ['IDUsuario' => 2, 'IDFinca' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['IDUsuario' => 2, 'IDFinca' => 2, 'created_at' => now(), 'updated_at' => now()],
            
            // José (Empleado) - Finca El Paraíso
            ['IDUsuario' => 3, 'IDFinca' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Ana (Propietario) - Avícola Los Pinos y Finca La Esperanza
            ['IDUsuario' => 4, 'IDFinca' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['IDUsuario' => 4, 'IDFinca' => 4, 'created_at' => now(), 'updated_at' => now()],
            
            // Pedro (Empleado) - Granja San José y Avícola Los Pinos
            ['IDUsuario' => 5, 'IDFinca' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['IDUsuario' => 5, 'IDFinca' => 3, 'created_at' => now(), 'updated_at' => now()],
            
            // Laura (Empleado) - Granja Santa María
            ['IDUsuario' => 6, 'IDFinca' => 5, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('usuario_finca')->insert($usuarioFincas);
    }
}
