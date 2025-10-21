<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFincaScope;

class Lote extends Model
{
    use HasFactory, HasFincaScope;

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
     * Obtener el tipo predominante de gallinas en el lote
     */
    public function getTipoPredominanteAttribute()
    {
        $tipoMasComun = $this->gallinas()
            ->activas()
            ->select('IDTipoGallina', \DB::raw('COUNT(*) as total'))
            ->groupBy('IDTipoGallina')
            ->orderByDesc('total')
            ->first();

        if ($tipoMasComun) {
            return TipoGallina::find($tipoMasComun->IDTipoGallina);
        }

        return null;
    }

    /**
     * Verificar si el lote es de aves de engorde (no ponen huevos)
     */
    public function getEsLoteDeEngordeAttribute()
    {
        $tipoPredominante = $this->tipo_predominante;
        // Si no hay tipo predominante, asumimos que NO es de engorde
        return $tipoPredominante && $tipoPredominante->Nombre === 'Engorde';
    }

    /**
     * Verificar si el lote puede producir huevos
     */
    public function getPuedeProducirHuevosAttribute()
    {
        $tipoPredominante = $this->tipo_predominante;
        // Si no hay tipo, permitir (puede ser lote nuevo sin gallinas)
        if (!$tipoPredominante) {
            return true;
        }
        // Si hay tipo, verificar que NO sea engorde
        return $tipoPredominante->Nombre !== 'Engorde';
    }

    /**
     * Obtener promedios de producción según tipo de gallina
     */
    private function getPromediosPorTipo()
    {
        $tipoPredominante = $this->tipo_predominante;
        
        if (!$tipoPredominante) {
            // Valores por defecto si no hay tipo definido
            return ['min' => 0.5, 'promedio' => 0.8, 'max' => 1.0];
        }

        // Promedios según el tipo de gallina
        switch ($tipoPredominante->Nombre) {
            case 'Ponedora':
                // Ponedoras: 5-7 huevos por día por ave (según requerimiento)
                return ['min' => 5.0, 'promedio' => 6.0, 'max' => 7.0];
            
            case 'Criolla':
                // Criollas: 0.4 - 0.7 huevos por día
                return ['min' => 0.4, 'promedio' => 0.55, 'max' => 0.7];
            
            case 'Doble Propósito':
                return ['min' => 0.7, 'promedio' => 0.8, 'max' => 0.9];
            
            case 'Reproductora':
                return ['min' => 0.6, 'promedio' => 0.8, 'max' => 1.0];
            
            case 'Engorde':
                // Aves de engorde NO ponen huevos
                return ['min' => 0, 'promedio' => 0, 'max' => 0];
            
            default:
                return ['min' => 0.5, 'promedio' => 0.8, 'max' => 1.0];
        }
    }

    /**
     * Obtener el número mínimo de huevos esperado por día según tipo de gallina
     */
    public function getProduccionMinimaEsperadaAttribute()
    {
        $avesActivas = $this->aves_activas_count;
        $promedios = $this->getPromediosPorTipo();
        return round($avesActivas * $promedios['min'], 1);
    }

    /**
     * Obtener el número promedio de huevos esperado por día según tipo de gallina
     */
    public function getProduccionPromedioEsperadaAttribute()
    {
        $avesActivas = $this->aves_activas_count;
        $promedios = $this->getPromediosPorTipo();
        return round($avesActivas * $promedios['promedio'], 1);
    }

    /**
     * Obtener el número máximo de huevos esperado por día según tipo de gallina
     */
    public function getProduccionMaximaEsperadaAttribute()
    {
        $avesActivas = $this->aves_activas_count;
        $promedios = $this->getPromediosPorTipo();
        return round($avesActivas * $promedios['max'], 1);
    }

    /**
     * Validar si una cantidad de huevos es realista para este lote
     */
    public function validarCantidadHuevos($cantidad)
    {
        // Verificar si es lote de engorde
        if ($this->es_lote_de_engorde) {
            return [
                'valido' => false,
                'mensaje' => 'Este lote es de aves de engorde, no producen huevos.'
            ];
        }

        $avesActivas = $this->aves_activas_count;
        
        if ($avesActivas == 0) {
            return [
                'valido' => false,
                'mensaje' => 'El lote no tiene aves activas.'
            ];
        }

        $maxEsperado = $this->produccion_maxima_esperada;
        $minEsperado = $this->produccion_minima_esperada;
        $tipoPredominante = $this->tipo_predominante;
        $nombreTipo = $tipoPredominante ? $tipoPredominante->Nombre : 'general';
        $promedios = $this->getPromediosPorTipo();

        if ($cantidad > $maxEsperado) {
            return [
                'valido' => false,
                'mensaje' => "La cantidad excede el máximo posible de {$maxEsperado} huevos para {$avesActivas} aves {$nombreTipo} (máx: {$promedios['max']} huevos/ave)."
            ];
        }

        if ($cantidad < $minEsperado) {
            return [
                'valido' => false,
                'mensaje' => "La cantidad es demasiado baja. Mínimo esperado: {$minEsperado} huevos para {$avesActivas} aves {$nombreTipo} (mín: {$promedios['min']} huevos/ave)."
            ];
        }

        return [
            'valido' => true,
            'mensaje' => 'Cantidad válida'
        ];
    }
}
