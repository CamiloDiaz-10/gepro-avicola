<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\HasFincaScope;

class Alimentacion extends Model
{
    use HasFactory, HasFincaScope;

    protected $table = 'alimentacion';
    protected $primaryKey = 'IDAlimentacion';
    
    protected $fillable = [
        'IDLote',
        'IDUsuario',
        'IDTipoAlimento',
        'Fecha',
        'CantidadKg',
        'Observaciones'
    ];

    protected $casts = [
        'Fecha' => 'date',
        'CantidadKg' => 'decimal:2'
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
            ->sum('CantidadKg') ?? 0;
    }

    public static function getConsumptionThisWeek()
    {
        return static::whereBetween('Fecha', [now()->startOfWeek(), now()])
            ->sum('CantidadKg') ?? 0;
    }

    public static function getConsumptionThisMonth()
    {
        return static::whereBetween('Fecha', [now()->startOfMonth(), now()])
            ->sum('CantidadKg') ?? 0;
    }

    public static function getCostThisMonth()
    {
        return static::whereBetween('Fecha', [now()->startOfMonth(), now()])
            ->leftJoin('tipo_alimentos','tipo_alimentos.IDTipoAlimento','=','alimentacion.IDTipoAlimento')
            ->select(DB::raw('SUM(CantidadKg * COALESCE(PrecioPorKg,0)) as total_cost'))
            ->value('total_cost') ?? 0;
    }

    public static function getConsumptionByType($days = 30)
    {
        return static::with('tipoAlimento')
            ->select('IDTipoAlimento', DB::raw('SUM(CantidadKg) as total_consumption'))
            ->whereBetween('Fecha', [now()->subDays($days), now()])
            ->groupBy('IDTipoAlimento')
            ->orderByDesc('total_consumption')
            ->get();
    }

    public static function getDailyConsumption($days = 7)
    {
        return static::select('Fecha as date', DB::raw('SUM(CantidadKg) as total'))
            ->whereBetween('Fecha', [now()->subDays($days - 1), now()])
            ->groupBy('Fecha')
            ->orderBy('Fecha')
            ->get();
    }

    public static function getConsumptionByLot($days = 30)
    {
        return static::with('lote')
            ->select('IDLote', DB::raw('SUM(CantidadKg) as total_consumption'))
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
            ->sum('CantidadKg');

        // Assuming we need to calculate based on bird count in lots
        $totalBirds = Gallina::when($loteId, function($q) use ($loteId) {
            return $q->where('IDLote', $loteId);
        })->count();

        return $totalBirds > 0 ? $totalConsumption / $totalBirds : 0;
    }

    // Accessors & Mutators
    // Costo estimado de este registro basado en precio del tipo de alimento
    public function getCostoEstimadoAttribute()
    {
        $precio = optional($this->tipoAlimento)->PrecioPorKg ?? 0;
        return ($this->CantidadKg ?? 0) * $precio;
    }
}
