<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'IDRol';

    protected $fillable = [
        'NombreRol'
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class, 'IDRol');
    }
}
