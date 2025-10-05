<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:reset-password {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resetea la contraseña de un usuario específico';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? $this->ask('Ingresa el email del usuario');
        $password = $this->argument('password') ?? $this->secret('Ingresa la nueva contraseña');

        // Buscar el usuario
        $usuario = DB::table('usuarios')->where('Email', $email)->first();

        if (!$usuario) {
            $this->error("No se encontró ningún usuario con el email: {$email}");
            return 1;
        }

        // Actualizar la contraseña
        DB::table('usuarios')
            ->where('IDUsuario', $usuario->IDUsuario)
            ->update(['Contrasena' => Hash::make($password)]);

        $this->info("✓ Contraseña actualizada correctamente para: {$email}");
        $this->info("  Usuario: {$usuario->Nombre} {$usuario->Apellido}");
        
        return 0;
    }
}
