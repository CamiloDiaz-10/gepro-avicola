<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MovimientoLote extends Model
{
    use HasFactory;

    protected $table = 'movimiento_lote';
    protected $primaryKey = 'IDMovimiento';
    
    protected $fillable = [
        'IDLote',
        'TipoMovimiento',
        'FechaMovimiento',
        'CantidadAves',
        'PrecioPorAve',
        'ValorTotal',
        'Destino',
        'Observaciones'
    ];

    protected $casts = [
        'FechaMovimiento' => 'date',
        'CantidadAves' => 'integer',
        'PrecioPorAve' => 'decimal:2',
        'ValorTotal' => 'decimal:2'
    ];

    // Relationships
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'IDLote', 'IDLote');
    }

    // Scopes
    public function scopeVentas($query)
    {
        return $query->where('TipoMovimiento', 'Venta');
    }

    public function scopeCompras($query)
    {
        return $query->where('TipoMovimiento', 'Compra');
    }

    public function scopeTraslados($query)
    {
        return $query->where('TipoMovimiento', 'Traslado');
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('FechaMovimiento', now()->toDateString());
    }

    public function scopeUltimaSemana($query)
    {
        return $query->whereBetween('FechaMovimiento', [now()->subWeek(), now()]);
    }

    public function scopeUltimoMes($query)
    {
        return $query->whereBetween('FechaMovimiento', [now()->subMonth(), now()]);
    }

    public function scopePorLote($query, $loteId)
    {
        return $query->where('IDLote', $loteId);
    }

    // Static methods
    public static function getSalesToday()
    {
        return static::ventas()
            ->whereDate('FechaMovimiento', now()->toDateString())
            ->sum('ValorTotal') ?? 0;
    }

    public static function getSalesThisWeek()
    {
        return static::ventas()
            ->whereBetween('FechaMovimiento', [now()->startOfWeek(), now()])
            ->sum('ValorTotal') ?? 0;
    }

    public static function getSalesThisMonth()
    {
        return static::ventas()
            ->whereBetween('FechaMovimiento', [now()->startOfMonth(), now()])
            ->sum('ValorTotal') ?? 0;
    }

    public static function getPurchasesToday()
    {
        return static::compras()
            ->whereDate('FechaMovimiento', now()->toDateString())
            ->sum('ValorTotal') ?? 0;
    }

    public static function getPurchasesThisMonth()
    {
        return static::compras()
            ->whereBetween('FechaMovimiento', [now()->startOfMonth(), now()])
            ->sum('ValorTotal') ?? 0;
    }

    public static function getMovementsByType($days = 30)
    {
        return static::select('TipoMovimiento as type', DB::raw('COUNT(*) as count'), DB::raw('SUM(ValorTotal) as total_value'))
            ->whereBetween('FechaMovimiento', [now()->subDays($days), now()])
            ->groupBy('TipoMovimiento')
            ->get();
    }

    public static function getDailySales($days = 7)
    {
        return static::ventas()
            ->select('FechaMovimiento as date', DB::raw('SUM(ValorTotal) as total'))
            ->whereBetween('FechaMovimiento', [now()->subDays($days - 1), now()])
            ->groupBy('FechaMovimiento')
            ->orderBy('FechaMovimiento')
            ->get();
    }

    public static function getTopDestinations($days = 30)
    {
        return static::ventas()
            ->select('Destino as destination', DB::raw('COUNT(*) as sales_count'), DB::raw('SUM(ValorTotal) as total_value'))
            ->whereBetween('FechaMovimiento', [now()->subDays($days), now()])
            ->whereNotNull('Destino')
            ->groupBy('Destino')
            ->orderByDesc('total_value')
            ->limit(10)
            ->get();
    }

    public static function getAveragePrice($tipoMovimiento = null, $days = 30)
    {
        $query = static::query();
        
        if ($tipoMovimiento) {
            $query->where('TipoMovimiento', $tipoMovimiento);
        }

        return $query->whereBetween('FechaMovimiento', [now()->subDays($days), now()])
            ->where('CantidadAves', '>', 0)
            ->selectRaw('AVG(PrecioPorAve) as average_price')
            ->first()
            ->average_price ?? 0;
    }

    public static function getFinancialSummary($days = 30)
    {
        return [
            'total_sales' => static::ventas()->whereBetween('FechaMovimiento', [now()->subDays($days), now()])->sum('ValorTotal'),
            'total_purchases' => static::compras()->whereBetween('FechaMovimiento', [now()->subDays($days), now()])->sum('ValorTotal'),
            'net_income' => static::ventas()->whereBetween('FechaMovimiento', [now()->subDays($days), now()])->sum('ValorTotal') - 
                           static::compras()->whereBetween('FechaMovimiento', [now()->subDays($days), now()])->sum('ValorTotal'),
            'birds_sold' => static::ventas()->whereBetween('FechaMovimiento', [now()->subDays($days), now()])->sum('CantidadAves'),
            'birds_purchased' => static::compras()->whereBetween('FechaMovimiento', [now()->subDays($days), now()])->sum('CantidadAves'),
            'average_sale_price' => static::getAveragePrice('Venta', $days),
            'average_purchase_price' => static::getAveragePrice('Compra', $days)
        ];
    }

    public static function getRecentMovements($limit = 10)
    {
        return static::with('lote')
            ->orderBy('FechaMovimiento', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    // Accessors & Mutators
    public function getTipoColorAttribute()
    {
        return match($this->TipoMovimiento) {
            'Venta' => 'green',
            'Compra' => 'blue',
            'Traslado' => 'yellow',
            default => 'gray'
        };
    }

    public function getGananciaPorAveAttribute()
    {
        // This would need comparison with purchase price or cost basis
        // For now, just return the sale price
        return $this->TipoMovimiento === 'Venta' ? $this->PrecioPorAve : 0;
    }
}
