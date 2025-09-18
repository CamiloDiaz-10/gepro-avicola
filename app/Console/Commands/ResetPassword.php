<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPassword extends Command
{
    protected $signature = 'user:reset-password {email} {password}';
    protected $description = 'Resetear la contraseña de un usuario';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        
        $user = User::where('Email', $email)->first();
        
        if (!$user) {
            $this->error("No se encontró usuario con email: {$email}");
            return;
        }
        
        $this->info("Usuario encontrado:");
        $this->info("ID: {$user->IDUsuario}");
        $this->info("Nombre: {$user->Nombre} {$user->Apellido}");
        $this->info("Email: {$user->Email}");
        $this->newLine();
        
        if ($this->confirm("¿Estás seguro de que quieres resetear la contraseña?")) {
            $user->Contrasena = Hash::make($password);
            $user->save();
            
            $this->info("✓ Contraseña actualizada exitosamente");
            $this->info("Nueva contraseña: {$password}");
            $this->warn("Ahora puedes usar esta contraseña para iniciar sesión.");
        } else {
            $this->info("Operación cancelada.");
        }
    }
}
