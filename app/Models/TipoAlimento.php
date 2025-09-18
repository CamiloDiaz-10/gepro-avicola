<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAlimento extends Model
{
    use HasFactory;

    protected $table = 'tipo_alimentos';
    protected $primaryKey = 'IDTipoAlimento';
    
    protected $fillable = [
        'NombreAlimento',
        'Descripcion',
        'NivelProteina',
        'NivelEnergia',
        'PrecioPorKg',
        'Stock',
        'StockMinimo',
        'Proveedor'
    ];

    protected $casts = [
        'NivelProteina' => 'decimal:2',
        'NivelEnergia' => 'decimal:2',
        'PrecioPorKg' => 'decimal:2',
        'Stock' => 'decimal:2',
        'StockMinimo' => 'decimal:2'
    ];

    // Relationships
    public function alimentaciones()
    {
        return $this->hasMany(Alimentacion::class, 'IDTipoAlimento', 'IDTipoAlimento');
    }

    // Scopes
    public function scopeStockBajo($query)
    {
        return $query->whereRaw('Stock <= StockMinimo');
    }

    public function scopeDisponible($query)
    {
        return $query->where('Stock', '>', 0);
    }

    public function scopePorProveedor($query, $proveedor)
    {
        return $query->where('Proveedor', $proveedor);
    }

    // Accessors & Mutators
    public function getStockStatusAttribute()
    {
        if ($this->Stock <= 0) {
            return 'agotado';
        } elseif ($this->Stock <= $this->StockMinimo) {
            return 'bajo';
        } elseif ($this->Stock <= ($this->StockMinimo * 2)) {
            return 'medio';
        } else {
            return 'alto';
        }
    }

    public function getStockColorAttribute()
    {
        return match($this->stock_status) {
            'agotado' => 'red',
            'bajo' => 'orange',
            'medio' => 'yellow',
            'alto' => 'green',
            default => 'gray'
        };
    }

    public function getConsumoMensualAttribute()
    {
        return $this->alimentaciones()
            ->whereBetween('Fecha', [now()->subMonth(), now()])
            ->sum('CantidadAlimento');
    }

    // Static methods
    public static function getLowStockFeeds()
    {
        return static::stockBajo()->get();
    }

    public static function getLowStockCount()
    {
        return static::stockBajo()->count();
    }

    public static function getTotalStockValue()
    {
        return static::selectRaw('SUM(Stock * PrecioPorKg) as total_value')
            ->first()
            ->total_value ?? 0;
    }

    public static function getStockSummary()
    {
        return [
            'total_types' => static::count(),
            'low_stock' => static::stockBajo()->count(),
            'out_of_stock' => static::where('Stock', '<=', 0)->count(),
            'total_value' => static::getTotalStockValue()
        ];
    }
}
