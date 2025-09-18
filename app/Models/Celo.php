<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Celo extends Model
{
    use HasFactory;

    protected $table = 'celo';
    protected $primaryKey = 'IDCelo';
    
    protected $fillable = [
        'IDGallina',
        'FechaInicioCelo',
        'FechaFinCelo',
        'DuracionDias',
        'IntensidadCelo',
        'FechaApareo',
        'MachoUtilizado',
        'Observaciones'
    ];

    protected $casts = [
        'FechaInicioCelo' => 'date',
        'FechaFinCelo' => 'date',
        'FechaApareo' => 'date',
        'DuracionDias' => 'integer'
    ];

    // Relationships
    public function gallina()
    {
        return $this->belongsTo(Gallina::class, 'IDGallina', 'IDGallina');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->whereNull('FechaFinCelo')
            ->orWhere('FechaFinCelo', '>=', now());
    }

    public function scopeCompletados($query)
    {
        return $query->whereNotNull('FechaFinCelo')
            ->where('FechaFinCelo', '<', now());
    }

    public function scopeConApareo($query)
    {
        return $query->whereNotNull('FechaApareo');
    }

    public function scopeUltimoMes($query)
    {
        return $query->whereBetween('FechaInicioCelo', [now()->subMonth(), now()]);
    }

    public function scopePorIntensidad($query, $intensidad)
    {
        return $query->where('IntensidadCelo', $intensidad);
    }

    // Static methods
    public static function getCelosActivos()
    {
        return static::activos()->count();
    }

    public static function getCelosCompletados($days = 30)
    {
        return static::completados()
            ->whereBetween('FechaInicioCelo', [now()->subDays($days), now()])
            ->count();
    }

    public static function getApareosPendientes()
    {
        return static::activos()
            ->whereNull('FechaApareo')
            ->count();
    }

    public static function getApareosRealizados($days = 30)
    {
        return static::conApareo()
            ->whereBetween('FechaApareo', [now()->subDays($days), now()])
            ->count();
    }

    public static function getCelosPorIntensidad($days = 30)
    {
        return static::select('IntensidadCelo as intensity', DB::raw('COUNT(*) as total'))
            ->whereBetween('FechaInicioCelo', [now()->subDays($days), now()])
            ->groupBy('IntensidadCelo')
            ->get();
    }

    public static function getDuracionPromedio($days = 30)
    {
        return static::completados()
            ->whereBetween('FechaInicioCelo', [now()->subDays($days), now()])
            ->whereNotNull('DuracionDias')
            ->avg('DuracionDias') ?? 0;
    }

    public static function getTasaApareo($days = 30)
    {
        $totalCelos = static::whereBetween('FechaInicioCelo', [now()->subDays($days), now()])->count();
        $celosConApareo = static::conApareo()
            ->whereBetween('FechaInicioCelo', [now()->subDays($days), now()])
            ->count();

        return $totalCelos > 0 ? ($celosConApareo / $totalCelos) * 100 : 0;
    }

    public static function getCelosSummary()
    {
        return [
            'activos' => static::getCelosActivos(),
            'completados_mes' => static::getCelosCompletados(),
            'apareos_pendientes' => static::getApareosPendientes(),
            'apareos_realizados' => static::getApareosRealizados(),
            'duracion_promedio' => static::getDuracionPromedio(),
            'tasa_apareo' => static::getTasaApareo(),
            'por_intensidad' => static::getCelosPorIntensidad()
        ];
    }

    public static function getProximosVencimientos($days = 7)
    {
        return static::with('gallina')
            ->activos()
            ->whereBetween('FechaInicioCelo', [now()->subDays(21), now()->subDays(21 - $days)])
            ->get();
    }

    // Accessors & Mutators
    public function getIntensidadColorAttribute()
    {
        return match($this->IntensidadCelo) {
            'Alta' => 'red',
            'Media' => 'yellow',
            'Baja' => 'green',
            default => 'gray'
        };
    }

    public function getEstaActivoAttribute()
    {
        return is_null($this->FechaFinCelo) || $this->FechaFinCelo >= now();
    }

    public function getDiasTranscurridosAttribute()
    {
        return $this->FechaInicioCelo ? now()->diffInDays($this->FechaInicioCelo) : 0;
    }

    public function getTieneApareoAttribute()
    {
        return !is_null($this->FechaApareo);
    }

    // Mutators
    public function setFechaFinCeloAttribute($value)
    {
        $this->attributes['FechaFinCelo'] = $value;
        
        // Auto-calculate duration if both dates are set
        if ($value && $this->FechaInicioCelo) {
            $this->attributes['DuracionDias'] = $this->FechaInicioCelo->diffInDays($value);
        }
    }
}
