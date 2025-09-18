<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bird extends Model
{
    use HasFactory;

    protected $table = 'gallinas';
    protected $primaryKey = 'IDGallina';

    protected $fillable = [
        'IDLote',
        'IDTipoGallina',
        'NumeroIdentificacion',
        'FechaNacimiento',
        'Estado',
        'NotasSalud'
    ];

    protected $casts = [
        'FechaNacimiento' => 'date'
    ];

    public function finca()
    {
        return $this->belongsTo(Finca::class);
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'IDLote', 'IDLote');
    }

    public function tipoGallina()
    {
        return $this->belongsTo(TipoGallina::class, 'IDTipoGallina', 'IDTipoGallina');
    }

    public function eggProductions()
    {
        return $this->hasMany(EggProduction::class, 'IDGallina', 'IDGallina');
    }

    // Statistics methods
    public static function getTotalBirdsCount()
    {
        return self::count();
    }

    public static function getBirdsByStatus()
    {
        return self::groupBy('Estado')
            ->selectRaw('Estado as status, count(*) as total')
            ->get();
    }

    public static function getRecentAcquisitions($days = 7)
    {
        return self::where('created_at', '>=', now()->subDays($days))
            ->count();
    }
}