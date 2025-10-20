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

    /**
     * Obtener el número de aves activas en el lote
     */
    public function getAvesActivasCountAttribute()
    {
        return $this->gallinas()->activas()->count();
    }

    /**
     * Obtener el número mínimo de huevos esperado por día (promedio: 4 huevos/ave)
     */
    public function getProduccionMinimaEsperadaAttribute()
    {
        $avesActivas = $this->aves_activas_count;
        // Consideramos 3 huevos/ave como mínimo razonable
        return $avesActivas * 3;
    }

    /**
     * Obtener el número promedio de huevos esperado por día (4 huevos/ave)
     */
    public function getProduccionPromedioEsperadaAttribute()
    {
        $avesActivas = $this->aves_activas_count;
        return $avesActivas * 4;
    }

    /**
     * Obtener el número máximo de huevos esperado por día (5 huevos/ave)
     */
    public function getProduccionMaximaEsperadaAttribute()
    {
        $avesActivas = $this->aves_activas_count;
        return $avesActivas * 5;
    }

    /**
     * Validar si una cantidad de huevos es realista para este lote
     */
    public function validarCantidadHuevos($cantidad)
    {
        $avesActivas = $this->aves_activas_count;
        
        if ($avesActivas == 0) {
            return [
                'valido' => false,
                'mensaje' => 'El lote no tiene aves activas.'
            ];
        }

        $maxEsperado = $avesActivas * 5; // Máximo 5 huevos/ave por día

        if ($cantidad > $maxEsperado) {
            return [
                'valido' => false,
                'mensaje' => "La cantidad excede el máximo posible de {$maxEsperado} huevos ({$avesActivas} aves × 5 huevos/ave)."
            ];
        }

        return [
            'valido' => true,
            'mensaje' => 'Cantidad válida'
        ];
    }
}
