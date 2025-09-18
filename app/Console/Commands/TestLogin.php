<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class TestLogin extends Command
{
    protected $signature = 'auth:test {email} {password}';
    protected $description = 'Probar autenticación con email y contraseña';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        
        $this->info("Probando login con:");
        $this->info("Email: {$email}");
        $this->info("Password: {$password}");
        $this->newLine();
        
        // Buscar usuario por email
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
        
        // Probar verificación de contraseña
        $this->info("Verificando contraseña...");
        
        if (Hash::check($password, $user->Contrasena)) {
            $this->info("✓ Contraseña CORRECTA");
        } else {
            $this->error("✗ Contraseña INCORRECTA");
        }
        
        // Probar auth()->attempt()
        $this->info("Probando auth()->attempt()...");
        
        if (Auth::attempt(['Email' => $email, 'Contrasena' => $password])) {
            $this->info("✓ auth()->attempt() EXITOSO");
            Auth::logout(); // Limpiar la sesión
        } else {
            $this->error("✗ auth()->attempt() FALLÓ");
        }
    }
}
