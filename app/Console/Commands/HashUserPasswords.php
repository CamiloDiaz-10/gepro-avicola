<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HashUserPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:hash-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hashea todas las contraseñas de usuarios que están en texto plano';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando proceso de hasheo de contraseñas...');

        // Obtener todos los usuarios
        $usuarios = DB::table('usuarios')->get();

        if ($usuarios->isEmpty()) {
            $this->warn('No se encontraron usuarios en la base de datos.');
            return 0;
        }

        $updated = 0;
        $skipped = 0;

        foreach ($usuarios as $usuario) {
            // Verificar si la contraseña ya está hasheada
            // Las contraseñas hasheadas con bcrypt empiezan con $2y$ y tienen 60 caracteres
            if (strlen($usuario->Contrasena) === 60 && str_starts_with($usuario->Contrasena, '$2y$')) {
                $this->line("✓ Usuario {$usuario->Email} - Contraseña ya hasheada (omitido)");
                $skipped++;
                continue;
            }

            // Hashear la contraseña en texto plano
            $hashedPassword = Hash::make($usuario->Contrasena);
            
            DB::table('usuarios')
                ->where('IDUsuario', $usuario->IDUsuario)
                ->update(['Contrasena' => $hashedPassword]);

            $this->info("✓ Usuario {$usuario->Email} - Contraseña hasheada correctamente");
            $updated++;
        }

        $this->newLine();
        $this->info("Proceso completado:");
        $this->info("- Contraseñas actualizadas: {$updated}");
        $this->info("- Contraseñas omitidas (ya hasheadas): {$skipped}");
        $this->info("- Total usuarios procesados: " . ($updated + $skipped));

        return 0;
    }
}
