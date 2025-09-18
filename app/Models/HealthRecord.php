<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'bird_id',
        'finca_id',
        'condition',
        'treatment',
        'diagnosis_date',
        'treatment_date',
        'notes',
        'status',
    ];

    public function bird()
    {
        return $this->belongsTo(Bird::class);
    }

    public function finca()
    {
        return $this->belongsTo(Finca::class);
    }

    // Métodos para estadísticas
    public static function getActiveHealthIssues()
    {
        return self::where('status', 'active')
            ->count();
    }

    public static function getHealthConditionsSummary()
    {
        return self::where('status', 'active')
            ->groupBy('condition')
            ->selectRaw('condition, count(*) as total')
            ->get();
    }

    public static function getRecentTreatments($days = 7)
    {
        return self::whereBetween('treatment_date', [now()->subDays($days), now()])
            ->count();
    }
}