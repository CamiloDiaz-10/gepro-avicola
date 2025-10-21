<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'IDUsuario';
    
    // Eager load the role relationship by default
    protected $with = ['role'];

    protected $fillable = [
        'IDRol',
        'TipoIdentificacion',
        'NumeroIdentificacion',
        'Nombre',
        'Apellido',
        'Email',
        'Telefono',
        'FechaNacimiento',
        'Direccion',
        'Contrasena',
        'UrlImagen',
        'Estado'
    ];

    protected $hidden = [
        'Contrasena',
        'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'FechaNacimiento' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->Contrasena;
    }

    public function getAuthPasswordName()
    {
        return 'Contrasena';
    }

    public function getEmailForPasswordReset()
    {
        return $this->Email;
    }

    public function getAuthIdentifierName()
    {
        return 'IDUsuario';
    }

    public function getAuthIdentifier()
    {
        return $this->IDUsuario;
    }

    // Método eliminado para evitar doble encriptación
    // La encriptación se maneja manualmente en el controlador

    public function role()
    {
        return $this->belongsTo(Role::class, 'IDRol', 'IDRol');
    }

    public function fincas()
    {
        return $this->belongsToMany(Finca::class, 'usuario_finca', 'IDUsuario', 'IDFinca')
                    ->withPivot('RolEnFinca')
                    ->withTimestamps();
    }

    public function hasRole($roleName)
    {
        return $this->role && $this->role->NombreRol === $roleName;
    }

    public function getRoleName()
    {
        return $this->role ? $this->role->NombreRol : null;
    }

    /**
     * Check if user has any fincas assigned
     */
    public function hasFincasAsignadas()
    {
        return $this->fincas()->count() > 0;
    }

    /**
     * Get IDs of user's assigned fincas
     */
    public function getFincaIds()
    {
        return $this->fincas()->pluck('fincas.IDFinca')->toArray();
    }

    /**
     * Check if user has access to a specific finca
     */
    public function hasAccessToFinca($fincaId)
    {
        // Admins have access to all fincas
        if ($this->hasRole('Administrador')) {
            return true;
        }

        return $this->fincas()->where('fincas.IDFinca', $fincaId)->exists();
    }

    /**
     * Check if user has access to a specific lote
     */
    public function hasAccessToLote($loteId)
    {
        // Admins have access to all lotes
        if ($this->hasRole('Administrador')) {
            return true;
        }

        $lote = \App\Models\Lote::find($loteId);
        if (!$lote) {
            return false;
        }

        return $this->hasAccessToFinca($lote->IDFinca);
    }

    /**
     * Get accessible lotes for this user
     */
    public function getAccessibleLotes()
    {
        if ($this->hasRole('Administrador')) {
            return \App\Models\Lote::all();
        }

        $fincaIds = $this->getFincaIds();
        return \App\Models\Lote::whereIn('IDFinca', $fincaIds)->get();
    }
}
