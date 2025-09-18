<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sanidad extends Model
{
    use HasFactory;

    protected $table = 'sanidad';
    protected $primaryKey = 'IDSanidad';
    
    protected $fillable = [
        'IDLote',
        'TipoTratamiento',
        'NombreTratamiento',
        'FechaTratamiento',
        'FechaProximaAplicacion',
        'Dosis',
        'Veterinario',
        'Costo',
        'Estado',
        'Observaciones'
    ];

    protected $casts = [
        'FechaTratamiento' => 'date',
        'FechaProximaAplicacion' => 'date',
        'Costo' => 'decimal:2'
    ];

    // Relationships
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'IDLote', 'IDLote');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('Estado', 'Pendiente');
    }

    public function scopeCompletados($query)
    {
        return $query->where('Estado', 'Completado');
    }

    public function scopeVencidos($query)
    {
        return $query->where('FechaProximaAplicacion', '<', now())
            ->where('Estado', '!=', 'Completado');
    }

    public function scopeProximosVencer($query, $days = 7)
    {
        return $query->whereBetween('FechaProximaAplicacion', [now(), now()->addDays($days)])
            ->where('Estado', '!=', 'Completado');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('TipoTratamiento', $tipo);
    }

    public function scopePorLote($query, $loteId)
    {
        return $query->where('IDLote', $loteId);
    }

    public function scopeUltimoMes($query)
    {
        return $query->whereBetween('FechaTratamiento', [now()->subMonth(), now()]);
    }

    // Static methods
    public static function getTreatmentsByType($days = 30)
    {
        return static::select('TipoTratamiento as treatment', DB::raw('COUNT(*) as total'))
            ->whereBetween('FechaTratamiento', [now()->subDays($days), now()])
            ->groupBy('TipoTratamiento')
            ->orderByDesc('total')
            ->get();
    }

    public static function getPendingTreatments()
    {
        return static::pendientes()->count();
    }

    public static function getOverdueTreatments()
    {
        return static::vencidos()->count();
    }

    public static function getUpcomingTreatments($days = 7)
    {
        return static::proximosVencer($days)->count();
    }

    public static function getVaccinationCount($days = 30)
    {
        return static::where('TipoTratamiento', 'Vacuna')
            ->whereBetween('FechaTratamiento', [now()->subDays($days), now()])
            ->count();
    }

    public static function getTreatmentCosts($days = 30)
    {
        return static::whereBetween('FechaTratamiento', [now()->subDays($days), now()])
            ->sum('Costo') ?? 0;
    }

    public static function getHealthSummary()
    {
        return [
            'pending_treatments' => static::getPendingTreatments(),
            'overdue_treatments' => static::getOverdueTreatments(),
            'upcoming_treatments' => static::getUpcomingTreatments(),
            'monthly_vaccinations' => static::getVaccinationCount(),
            'monthly_costs' => static::getTreatmentCosts()
        ];
    }

    public static function getTreatmentSchedule($loteId = null)
    {
        $query = static::with('lote')
            ->where('Estado', '!=', 'Completado')
            ->orderBy('FechaProximaAplicacion');

        if ($loteId) {
            $query->where('IDLote', $loteId);
        }

        return $query->get();
    }

    // Accessors & Mutators
    public function getEstadoColorAttribute()
    {
        return match($this->Estado) {
            'Completado' => 'green',
            'En Proceso' => 'blue',
            'Pendiente' => 'yellow',
            'Vencido' => 'red',
            default => 'gray'
        };
    }

    public function getEsVencidoAttribute()
    {
        return $this->FechaProximaAplicacion && 
               $this->FechaProximaAplicacion < now() && 
               $this->Estado !== 'Completado';
    }

    public function getDiasParaVencerAttribute()
    {
        if (!$this->FechaProximaAplicacion || $this->Estado === 'Completado') {
            return null;
        }

        return now()->diffInDays($this->FechaProximaAplicacion, false);
    }
}
