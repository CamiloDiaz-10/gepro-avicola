<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\HasFincaScope;

class Sanidad extends Model
{
    use HasFactory, HasFincaScope;

    protected $table = 'sanidad';
    protected $primaryKey = 'IDSanidad';
    
    protected $fillable = [
        'IDLote',
        'IDUsuario',
        'Fecha',
        'Producto',
        'TipoTratamiento',
        'Dosis',
        'Observaciones',
    ];

    protected $casts = [
        'Fecha' => 'date',
    ];

    // Relationships
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'IDLote', 'IDLote');
    }

    // Scopes mínimos útiles
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('TipoTratamiento', $tipo);
    }

    public function scopePorLote($query, $loteId)
    {
        return $query->where('IDLote', $loteId);
    }

    public function scopeUltimosDias($query, $days = 30)
    {
        return $query->whereBetween('Fecha', [now()->subDays($days), now()]);
    }

    // Static methods
    public static function getTreatmentsByType($days = 30)
    {
        return static::select('TipoTratamiento as treatment', DB::raw('COUNT(*) as total'))
            ->whereBetween('Fecha', [now()->subDays($days), now()])
            ->groupBy('TipoTratamiento')
            ->orderByDesc('total')
            ->get();
    }

    // Accessors & Mutators
    // Accessors & Mutators mínimos (no definidos porque la tabla actual no maneja estado/fechas próximas)
}
