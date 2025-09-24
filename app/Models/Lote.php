<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    use HasFactory;

    protected $table = 'lotes';
    protected $primaryKey = 'IDLote';
    
    protected $fillable = [
        'IDFinca',
        'Nombre',
        'FechaIngreso',
        'CantidadInicial',
        'Raza'
    ];

    protected $casts = [
        'FechaIngreso' => 'date',
        'CantidadInicial' => 'integer'
    ];

    // Relationships
    public function finca()
    {
        return $this->belongsTo(Finca::class, 'IDFinca', 'IDFinca');
    }

    public function gallinas()
    {
        return $this->hasMany(Gallina::class, 'IDLote', 'IDLote');
    }

    public function produccionHuevos()
    {
        return $this->hasMany(ProduccionHuevos::class, 'IDLote', 'IDLote');
    }

    public function alimentacion()
    {
        return $this->hasMany(Alimentacion::class, 'IDLote', 'IDLote');
    }

    public function sanidad()
    {
        return $this->hasMany(Sanidad::class, 'IDLote', 'IDLote');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoLote::class, 'IDLote', 'IDLote');
    }

    public function mortalidad()
    {
        return $this->hasMany(Mortalidad::class, 'IDLote', 'IDLote');
    }

    public function scopePorFinca($query, $fincaId)
    {
        return $query->where('IDFinca', $fincaId);
    }

    // Accessors & Mutators
    public function getEdadEnDiasAttribute()
    {
        return $this->FechaIngreso ? now()->diffInDays($this->FechaIngreso) : 0;
    }
}
