<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = [
            [
                'TipoIdentificacion' => 'CC',
                'NumeroIdentificacion' => '12345678',
                'Nombre' => 'Carlos',
                'Apellido' => 'Rodríguez',
                'Email' => 'admin@geproavicola.com',
                'Telefono' => '3001234567',
                'FechaNacimiento' => '1980-05-15',
                'Direccion' => 'Calle 45 #23-67, Bucaramanga',
                'Contrasena' => Hash::make('admin123'),
                'IDRol' => 1, // Administrador
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'TipoIdentificacion' => 'CC',
                'NumeroIdentificacion' => '87654321',
                'Nombre' => 'María',
                'Apellido' => 'González',
                'Email' => 'propietario@geproavicola.com',
                'Telefono' => '3009876543',
                'FechaNacimiento' => '1975-08-22',
                'Direccion' => 'Carrera 27 #45-12, Floridablanca',
                'Contrasena' => Hash::make('propietario123'),
                'IDRol' => 2, // Propietario
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'TipoIdentificacion' => 'CC',
                'NumeroIdentificacion' => '11223344',
                'Nombre' => 'José',
                'Apellido' => 'Martínez',
                'Email' => 'empleado@geproavicola.com',
                'Telefono' => '3005551234',
                'FechaNacimiento' => '1990-03-10',
                'Direccion' => 'Calle 12 #34-56, Girón',
                'Contrasena' => Hash::make('empleado123'),
                'IDRol' => 3, // Empleado
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'TipoIdentificacion' => 'CC',
                'NumeroIdentificacion' => '55667788',
                'Nombre' => 'Ana',
                'Apellido' => 'López',
                'Email' => 'ana.lopez@geproavicola.com',
                'Telefono' => '3007778899',
                'FechaNacimiento' => '1985-12-05',
                'Direccion' => 'Avenida 15 #78-90, Piedecuesta',
                'Contrasena' => Hash::make('ana123'),
                'IDRol' => 2, // Propietario
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'TipoIdentificacion' => 'CC',
                'NumeroIdentificacion' => '99887766',
                'Nombre' => 'Pedro',
                'Apellido' => 'Hernández',
                'Email' => 'pedro.hernandez@geproavicola.com',
                'Telefono' => '3002223333',
                'FechaNacimiento' => '1992-07-18',
                'Direccion' => 'Calle 8 #12-34, Lebrija',
                'Contrasena' => Hash::make('pedro123'),
                'IDRol' => 3, // Empleado
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'TipoIdentificacion' => 'CC',
                'NumeroIdentificacion' => '44556677',
                'Nombre' => 'Laura',
                'Apellido' => 'Ramírez',
                'Email' => 'laura.ramirez@geproavicola.com',
                'Telefono' => '3004445555',
                'FechaNacimiento' => '1988-11-25',
                'Direccion' => 'Carrera 20 #56-78, Bucaramanga',
                'Contrasena' => Hash::make('laura123'),
                'IDRol' => 3, // Empleado
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('usuarios')->insert($usuarios);
    }
}
