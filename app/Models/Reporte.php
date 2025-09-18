<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Reporte extends Model
{
    use HasFactory;

    protected $table = 'reportes';
    protected $primaryKey = 'IDReporte';
    
    protected $fillable = [
        'TipoReporte',
        'NombreReporte',
        'FechaGeneracion',
        'FechaInicio',
        'FechaFin',
        'ParametrosReporte',
        'RutaArchivo',
        'Estado',
        'GeneradoPor',
        'Observaciones'
    ];

    protected $casts = [
        'FechaGeneracion' => 'datetime',
        'FechaInicio' => 'date',
        'FechaFin' => 'date',
        'ParametrosReporte' => 'json'
    ];

    // Relationships
    public function usuario()
    {
        return $this->belongsTo(User::class, 'GeneradoPor', 'IDUsuario');
    }

    // Scopes
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('TipoReporte', $tipo);
    }

    public function scopeGenerados($query)
    {
        return $query->where('Estado', 'Generado');
    }

    public function scopePendientes($query)
    {
        return $query->where('Estado', 'Pendiente');
    }

    public function scopeEnProceso($query)
    {
        return $query->where('Estado', 'En Proceso');
    }

    public function scopeRecientes($query, $days = 30)
    {
        return $query->whereBetween('FechaGeneracion', [now()->subDays($days), now()]);
    }

    public function scopePorUsuario($query, $userId)
    {
        return $query->where('GeneradoPor', $userId);
    }

    // Static methods
    public static function getReportesPorTipo($days = 30)
    {
        return static::select('TipoReporte as type', DB::raw('COUNT(*) as total'))
            ->whereBetween('FechaGeneracion', [now()->subDays($days), now()])
            ->groupBy('TipoReporte')
            ->orderByDesc('total')
            ->get();
    }

    public static function getReportesRecientes($limit = 10)
    {
        return static::with('usuario')
            ->orderBy('FechaGeneracion', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getReportesPendientes()
    {
        return static::pendientes()->count();
    }

    public static function getReportesEnProceso()
    {
        return static::enProceso()->count();
    }

    public static function getReportesGenerados($days = 30)
    {
        return static::generados()
            ->whereBetween('FechaGeneracion', [now()->subDays($days), now()])
            ->count();
    }

    public static function getReportesSummary()
    {
        return [
            'total_generados' => static::getReportesGenerados(),
            'pendientes' => static::getReportesPendientes(),
            'en_proceso' => static::getReportesEnProceso(),
            'por_tipo' => static::getReportesPorTipo(),
            'recientes' => static::getReportesRecientes(5)
        ];
    }

    public static function getTiposReporte()
    {
        return [
            'Produccion' => 'Reporte de Producción de Huevos',
            'Sanidad' => 'Reporte de Sanidad y Tratamientos',
            'Alimentacion' => 'Reporte de Alimentación y Consumo',
            'Mortalidad' => 'Reporte de Mortalidad',
            'Financiero' => 'Reporte Financiero',
            'Inventario' => 'Reporte de Inventario',
            'Reproduccion' => 'Reporte de Reproducción y Celos',
            'General' => 'Reporte General del Sistema'
        ];
    }

    // Accessors & Mutators
    public function getEstadoColorAttribute()
    {
        return match($this->Estado) {
            'Generado' => 'green',
            'En Proceso' => 'blue',
            'Pendiente' => 'yellow',
            'Error' => 'red',
            default => 'gray'
        };
    }

    public function getTipoReporteDescripcionAttribute()
    {
        $tipos = static::getTiposReporte();
        return $tipos[$this->TipoReporte] ?? $this->TipoReporte;
    }

    public function getArchivoExisteAttribute()
    {
        return $this->RutaArchivo && file_exists(storage_path('app/' . $this->RutaArchivo));
    }

    public function getTamanoArchivoAttribute()
    {
        if (!$this->archivo_existe) {
            return null;
        }

        $bytes = filesize(storage_path('app/' . $this->RutaArchivo));
        
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function getDuracionGeneracionAttribute()
    {
        if (!$this->FechaGeneracion || $this->Estado !== 'Generado') {
            return null;
        }

        // This would need to be calculated based on when the report generation started
        // For now, we'll return a placeholder
        return 'N/A';
    }

    // Methods
    public function marcarComoGenerado($rutaArchivo = null)
    {
        $this->update([
            'Estado' => 'Generado',
            'RutaArchivo' => $rutaArchivo,
            'FechaGeneracion' => now()
        ]);
    }

    public function marcarComoEnProceso()
    {
        $this->update([
            'Estado' => 'En Proceso'
        ]);
    }

    public function marcarComoError($observaciones = null)
    {
        $this->update([
            'Estado' => 'Error',
            'Observaciones' => $observaciones
        ]);
    }

    public function getUrlDescarga()
    {
        if (!$this->archivo_existe) {
            return null;
        }

        return route('reportes.download', $this->IDReporte);
    }
}
