<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar seeders en orden de dependencias
        $this->call([
            // 1. Tablas catálogo (sin dependencias)
            RoleSeeder::class,
            FincaSeeder::class,
            TipoGallinaSeeder::class,
            TipoAlimentoSeeder::class,
            
            // 2. Usuarios (depende de roles)
            UsuarioSeeder::class,
            
            // 3. Relaciones usuario-finca (depende de usuarios y fincas)
            UsuarioFincaSeeder::class,
            
            // 4. Lotes (depende de fincas)
            LoteSeeder::class,
            
            // 5. Gallinas (depende de lotes y tipo_gallinas)
            GallinaSeeder::class,
            
            // 6. Tablas operacionales (dependen de lotes, usuarios, etc.)
            ProduccionHuevosSeeder::class,
            MortalidadSeeder::class,
            AlimentacionSeeder::class,
            SanidadSeeder::class,
            MovimientoLoteSeeder::class,
            
            // 7. Celo (depende de gallinas)
            CeloSeeder::class,
            
            // 8. Reportes (depende de usuarios)
            ReporteSeeder::class,
        ]);
        
        $this->command->info('🎉 Base de datos poblada exitosamente con datos de prueba!');
        $this->command->info('📊 Datos creados:');
        $this->command->info('   • 3 Roles (Administrador, Propietario, Empleado)');
        $this->command->info('   • 5 Fincas con ubicaciones reales');
        $this->command->info('   • 6 Usuarios con diferentes roles');
        $this->command->info('   • 10 Lotes de diferentes tipos');
        $this->command->info('   • 1000+ Gallinas distribuidas en los lotes');
        $this->command->info('   • 90 días de producción de huevos');
        $this->command->info('   • Registros de mortalidad, alimentación y sanidad');
        $this->command->info('   • Movimientos de lotes y reportes');
        $this->command->info('');
        $this->command->info('👤 Usuarios de prueba:');
        $this->command->info('   • admin@geproavicola.com (admin123) - Administrador');
        $this->command->info('   • propietario@geproavicola.com (propietario123) - Propietario');
        $this->command->info('   • empleado@geproavicola.com (empleado123) - Empleado');
    }
}
