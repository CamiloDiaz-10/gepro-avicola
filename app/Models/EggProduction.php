<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EggProduction extends Model
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
        'PesoPromedio' => 'decimal:2',
        'PorcentajePostura' => 'decimal:2'
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'IDLote', 'IDLote');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'IDUsuario', 'IDUsuario');
    }

    // Métodos para estadísticas
    public static function getTotalEggsToday()
    {
        return self::whereDate('Fecha', today())
            ->sum('CantidadHuevos') ?? 0;
    }

    public static function getWeeklyProduction()
    {
        return self::whereBetween('Fecha', [now()->subDays(6), now()])
            ->selectRaw('DATE(Fecha) as date, SUM(CantidadHuevos) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public static function getProductionByQuality()
    {
        // Como no hay campo de calidad, devolvemos por turno
        return self::whereDate('Fecha', today())
            ->groupBy('Turno')
            ->selectRaw('COALESCE(Turno, "Sin turno") as quality_grade, SUM(CantidadHuevos) as total')
            ->get();
    }
}