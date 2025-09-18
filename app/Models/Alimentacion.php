<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Alimentacion extends Model
{
    use HasFactory;

    protected $table = 'alimentacion';
    protected $primaryKey = 'IDAlimentacion';
    
    protected $fillable = [
        'IDLote',
        'IDTipoAlimento',
        'Fecha',
        'CantidadAlimento',
        'UnidadMedida',
        'CostoTotal',
        'Observaciones'
    ];

    protected $casts = [
        'Fecha' => 'date',
        'CantidadAlimento' => 'decimal:2',
        'CostoTotal' => 'decimal:2'
    ];

    // Relationships
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'IDLote', 'IDLote');
    }

    public function tipoAlimento()
    {
        return $this->belongsTo(TipoAlimento::class, 'IDTipoAlimento', 'IDTipoAlimento');
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

    public function scopePorLote($query, $loteId)
    {
        return $query->where('IDLote', $loteId);
    }

    public function scopePorTipoAlimento($query, $tipoId)
    {
        return $query->where('IDTipoAlimento', $tipoId);
    }

    // Static methods
    public static function getConsumptionToday()
    {
        return static::whereDate('Fecha', now()->toDateString())
            ->sum('CantidadAlimento') ?? 0;
    }

    public static function getConsumptionThisWeek()
    {
        return static::whereBetween('Fecha', [now()->startOfWeek(), now()])
            ->sum('CantidadAlimento') ?? 0;
    }

    public static function getConsumptionThisMonth()
    {
        return static::whereBetween('Fecha', [now()->startOfMonth(), now()])
            ->sum('CantidadAlimento') ?? 0;
    }

    public static function getCostThisMonth()
    {
        return static::whereBetween('Fecha', [now()->startOfMonth(), now()])
            ->sum('CostoTotal') ?? 0;
    }

    public static function getConsumptionByType($days = 30)
    {
        return static::with('tipoAlimento')
            ->select('IDTipoAlimento', DB::raw('SUM(CantidadAlimento) as total_consumption'))
            ->whereBetween('Fecha', [now()->subDays($days), now()])
            ->groupBy('IDTipoAlimento')
            ->orderByDesc('total_consumption')
            ->get();
    }

    public static function getDailyConsumption($days = 7)
    {
        return static::select('Fecha as date', DB::raw('SUM(CantidadAlimento) as total'))
            ->whereBetween('Fecha', [now()->subDays($days - 1), now()])
            ->groupBy('Fecha')
            ->orderBy('Fecha')
            ->get();
    }

    public static function getConsumptionByLot($days = 30)
    {
        return static::with('lote')
            ->select('IDLote', DB::raw('SUM(CantidadAlimento) as total_consumption'))
            ->whereBetween('Fecha', [now()->subDays($days), now()])
            ->groupBy('IDLote')
            ->orderByDesc('total_consumption')
            ->get();
    }

    public static function getAverageConsumptionPerBird($loteId = null)
    {
        $query = static::query();
        
        if ($loteId) {
            $query->where('IDLote', $loteId);
        }

        $totalConsumption = $query->whereBetween('Fecha', [now()->subDays(30), now()])
            ->sum('CantidadAlimento');

        // Assuming we need to calculate based on bird count in lots
        $totalBirds = Gallina::when($loteId, function($q) use ($loteId) {
            return $q->where('IDLote', $loteId);
        })->count();

        return $totalBirds > 0 ? $totalConsumption / $totalBirds : 0;
    }

    // Accessors & Mutators
    public function getCostoPorKgAttribute()
    {
        return $this->CantidadAlimento > 0 ? $this->CostoTotal / $this->CantidadAlimento : 0;
    }
}
