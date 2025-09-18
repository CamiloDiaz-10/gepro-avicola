<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProduccionHuevos extends Model
{
    use HasFactory;

    protected $table = 'produccion_huevos';
    protected $primaryKey = 'IDProduccion';
    
    protected $fillable = [
        'IDLote',
        'IDUsuario',
        'Fecha',
        'CantidadHuevos',
        'HuevosRotos',
        'Turno',
        'PesoPromedio',
        'PorcentajePostura',
        'Observaciones'
    ];

    protected $casts = [
        'Fecha' => 'date',
        'CantidadHuevos' => 'integer',
        'HuevosRotos' => 'integer',
        'PesoPromedio' => 'decimal:2',
        'PorcentajePostura' => 'decimal:2'
    ];

    // Relationships
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'IDLote', 'IDLote');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'IDUsuario', 'IDUsuario');
    }

    // Scopes
    public function scopeHoy($query)
    {
        return $query->whereDate('Fecha', now()->toDateString());
    }

    public function scopeUltimaSemana($query)
    {
        return $query->whereBetween('Fecha', [now()->subWeek(), now()]);
    }

    public function scopeUltimoMes($query)
    {
        return $query->whereBetween('Fecha', [now()->subMonth(), now()]);
    }

    public function scopePorTurno($query, $turno)
    {
        return $query->where('Turno', $turno);
    }

    // Static methods
    public static function getTotalEggsToday()
    {
        return static::whereDate('Fecha', now()->toDateString())
            ->sum('CantidadHuevos') ?? 0;
    }

    public static function getTotalEggsThisWeek()
    {
        return static::whereBetween('Fecha', [now()->startOfWeek(), now()])
            ->sum('CantidadHuevos') ?? 0;
    }

    public static function getTotalEggsThisMonth()
    {
        return static::whereBetween('Fecha', [now()->startOfMonth(), now()])
            ->sum('CantidadHuevos') ?? 0;
    }

    public static function getProductionByQuality()
    {
        return static::select('Turno as quality_grade', DB::raw('SUM(CantidadHuevos) as total'))
            ->whereDate('Fecha', now()->toDateString())
            ->groupBy('Turno')
            ->get();
    }

    public static function getWeeklyProduction()
    {
        return static::select('Fecha as date', DB::raw('SUM(CantidadHuevos) as total'))
            ->whereBetween('Fecha', [now()->subDays(6), now()])
            ->groupBy('Fecha')
            ->orderBy('Fecha')
            ->get();
    }

    public static function getDailyProductionLast30Days()
    {
        return static::select('Fecha as date', DB::raw('SUM(CantidadHuevos) as total'))
            ->whereBetween('Fecha', [now()->subDays(30), now()])
            ->groupBy('Fecha')
            ->orderBy('Fecha')
            ->get();
    }

    public static function getProductionByLot($days = 30)
    {
        return static::with('lote')
            ->select('IDLote', DB::raw('SUM(CantidadHuevos) as total_eggs'))
            ->whereBetween('Fecha', [now()->subDays($days), now()])
            ->groupBy('IDLote')
            ->orderByDesc('total_eggs')
            ->get();
    }

    public static function getAverageProductionPerBird($loteId = null)
    {
        $query = static::query();
        
        if ($loteId) {
            $query->where('IDLote', $loteId);
        }

        return $query->whereBetween('Fecha', [now()->subDays(30), now()])
            ->selectRaw('AVG(CantidadHuevos) as average_per_bird')
            ->first()
            ->average_per_bird ?? 0;
    }

    // Accessors & Mutators
    public function getTurnoColorAttribute()
    {
        return match($this->Turno) {
            'Mañana' => 'blue',
            'Tarde' => 'orange',
            'Noche' => 'purple',
            default => 'gray'
        };
    }

    public function getHuevosBuenosAttribute()
    {
        return $this->CantidadHuevos - $this->HuevosRotos;
    }

    public function getPorcentajeRotosAttribute()
    {
        return $this->CantidadHuevos > 0 ? ($this->HuevosRotos / $this->CantidadHuevos) * 100 : 0;
    }
}
