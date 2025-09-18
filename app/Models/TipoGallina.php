<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoGallina extends Model
{
    use HasFactory;

    protected $table = 'tipo_gallinas';
    protected $primaryKey = 'IDTipoGallina';
    
    protected $fillable = [
        'NombreTipo',
        'Descripcion',
        'ProduccionPromedio',
        'PesoPromedio'
    ];

    protected $casts = [
        'ProduccionPromedio' => 'decimal:2',
        'PesoPromedio' => 'decimal:2'
    ];

    // Relationships
    public function gallinas()
    {
        return $this->hasMany(Gallina::class, 'IDTipoGallina', 'IDTipoGallina');
    }

    // Accessors & Mutators
    public function getCantidadGallinasAttribute()
    {
        return $this->gallinas()->count();
    }

    // Static methods
    public static function getTiposConConteo()
    {
        return static::withCount('gallinas')->get();
    }
}
