<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CheckUsers extends Command
{
    protected $signature = 'users:check';
    protected $description = 'Verificar usuarios en la base de datos y sus contraseñas';

    public function handle()
    {
        $this->info('Verificando usuarios en la base de datos...');
        
        $users = User::with('role')->get();
        
        if ($users->isEmpty()) {
            $this->error('No hay usuarios en la base de datos.');
            return;
        }
        
        $this->info("Total de usuarios encontrados: {$users->count()}");
        $this->newLine();
        
        foreach ($users as $user) {
            $this->info("Usuario ID: {$user->IDUsuario}");
            $this->info("Nombre: {$user->Nombre} {$user->Apellido}");
            $this->info("Email: {$user->Email}");
            $this->info("Rol: " . ($user->role ? $user->role->NombreRol : 'Sin rol'));
            $this->info("Contraseña encriptada: " . substr($user->Contrasena, 0, 20) . "...");
            
            // Verificar si la contraseña parece estar correctamente encriptada
            if (strlen($user->Contrasena) === 60 && str_starts_with($user->Contrasena, '$2y$')) {
                $this->info("✓ Contraseña correctamente encriptada con bcrypt");
            } else {
                $this->error("✗ Contraseña NO parece estar correctamente encriptada");
            }
            
            $this->newLine();
        }
        
        // Sugerir contraseña de prueba
        $this->warn('Para probar el login, asegúrate de usar la contraseña que usaste al registrarte.');
        $this->warn('Si no recuerdas la contraseña, puedes crear un nuevo usuario o resetear la contraseña.');
    }
}
