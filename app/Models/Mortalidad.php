<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Mortalidad extends Model
{
    use HasFactory;

    protected $table = 'mortalidad';
    protected $primaryKey = 'IDMortalidad';
    
    protected $fillable = [
        'IDLote',
        'FechaMortalidad',
        'CantidadMuertas',
        'CausaMortalidad',
        'EdadPromedio',
        'PesoPromedio',
        'Observaciones'
    ];

    protected $casts = [
        'FechaMortalidad' => 'date',
        'CantidadMuertas' => 'integer',
        'EdadPromedio' => 'integer',
        'PesoPromedio' => 'decimal:2'
    ];

    // Relationships
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'IDLote', 'IDLote');
    }

    // Scopes
    public function scopeHoy($query)
    {
        return $query->whereDate('FechaMortalidad', now()->toDateString());
    }

    public function scopeUltimaSemana($query)
    {
        return $query->whereBetween('FechaMortalidad', [now()->subWeek(), now()]);
    }

    public function scopeUltimoMes($query)
    {
        return $query->whereBetween('FechaMortalidad', [now()->subMonth(), now()]);
    }

    public function scopePorCausa($query, $causa)
    {
        return $query->where('CausaMortalidad', $causa);
    }

    public function scopePorLote($query, $loteId)
    {
        return $query->where('IDLote', $loteId);
    }

    // Static methods
    public static function getMortalityToday()
    {
        return static::whereDate('FechaMortalidad', now()->toDateString())
            ->sum('CantidadMuertas') ?? 0;
    }

    public static function getMortalityThisWeek()
    {
        return static::whereBetween('FechaMortalidad', [now()->startOfWeek(), now()])
            ->sum('CantidadMuertas') ?? 0;
    }

    public static function getMortalityThisMonth()
    {
        return static::whereBetween('FechaMortalidad', [now()->startOfMonth(), now()])
            ->sum('CantidadMuertas') ?? 0;
    }

    public static function getMortalityByCause($days = 30)
    {
        return static::select('CausaMortalidad as cause', DB::raw('SUM(CantidadMuertas) as total'))
            ->whereBetween('FechaMortalidad', [now()->subDays($days), now()])
            ->groupBy('CausaMortalidad')
            ->orderByDesc('total')
            ->get();
    }

    public static function getDailyMortality($days = 7)
    {
        return static::select('FechaMortalidad as date', DB::raw('SUM(CantidadMuertas) as total'))
            ->whereBetween('FechaMortalidad', [now()->subDays($days - 1), now()])
            ->groupBy('FechaMortalidad')
            ->orderBy('FechaMortalidad')
            ->get();
    }

    public static function getMortalityByLot($days = 30)
    {
        return static::with('lote')
            ->select('IDLote', DB::raw('SUM(CantidadMuertas) as total_deaths'))
            ->whereBetween('FechaMortalidad', [now()->subDays($days), now()])
            ->groupBy('IDLote')
            ->orderByDesc('total_deaths')
            ->get();
    }

    public static function getMortalityRate($loteId = null, $days = 30)
    {
        $query = static::query();
        
        if ($loteId) {
            $query->where('IDLote', $loteId);
        }

        $totalDeaths = $query->whereBetween('FechaMortalidad', [now()->subDays($days), now()])
            ->sum('CantidadMuertas');

        // Get total birds in the lot(s)
        $totalBirds = Gallina::when($loteId, function($q) use ($loteId) {
            return $q->where('IDLote', $loteId);
        })->count();

        return $totalBirds > 0 ? ($totalDeaths / $totalBirds) * 100 : 0;
    }

    public static function getMortalitySummary()
    {
        return [
            'today' => static::getMortalityToday(),
            'this_week' => static::getMortalityThisWeek(),
            'this_month' => static::getMortalityThisMonth(),
            'by_cause' => static::getMortalityByCause(),
            'mortality_rate' => static::getMortalityRate()
        ];
    }

    // Accessors & Mutators
    public function getCausaColorAttribute()
    {
        return match($this->CausaMortalidad) {
            'Enfermedad' => 'red',
            'Accidente' => 'orange',
            'Vejez' => 'blue',
            'Depredador' => 'purple',
            'Desconocida' => 'gray',
            default => 'gray'
        };
    }
}
