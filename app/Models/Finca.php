<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finca extends Model
{
    protected $table = 'fincas';
    protected $primaryKey = 'IDFinca';

    protected $fillable = [
        'Nombre',
        'Ubicacion',
        'Latitud',
        'Longitud',
        'Hectareas'
    ];

    // Users assigned to this farm via pivot usuario_finca
    public function users()
    {
        return $this->belongsToMany(User::class, 'usuario_finca', 'IDFinca', 'IDUsuario')
                    ->withPivot('RolEnFinca')
                    ->withTimestamps();
    }


}
