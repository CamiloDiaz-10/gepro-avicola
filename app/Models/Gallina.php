<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallina extends Model
{
    use HasFactory;

    protected $table = 'gallinas';
    protected $primaryKey = 'IDGallina';
    
    protected $fillable = [
        'IDLote',
        'IDTipoGallina',
        'FechaNacimiento',
        'Peso',
        'Estado'
    ];

    protected $casts = [
        'FechaNacimiento' => 'date',
        'Peso' => 'decimal:2'
    ];

    // Relationships
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'IDLote', 'IDLote');
    }

    public function tipoGallina()
    {
        return $this->belongsTo(TipoGallina::class, 'IDTipoGallina', 'IDTipoGallina');
    }

    public function produccionHuevos()
    {
        return $this->hasManyThrough(
            ProduccionHuevos::class,
            Lote::class,
            'IDLote', // Foreign key on lotes table
            'IDLote', // Foreign key on produccion_huevos table
            'IDLote', // Local key on gallinas table
            'IDLote'  // Local key on lotes table
        );
    }

    public function celos()
    {
        return $this->hasMany(Celo::class, 'IDGallina', 'IDGallina');
    }

    // Scopes
    public function scopeVivas($query)
    {
        return $query->where('Estado', 'Activa');
    }

    public function scopeActivas($query)
    {
        return $query->where('Estado', 'Activa');
    }

    public function scopePorTipo($query, $tipoId)
    {
        return $query->where('IDTipoGallina', $tipoId);
    }

    public function scopePorLote($query, $loteId)
    {
        return $query->where('IDLote', $loteId);
    }

    // Accessors & Mutators
    public function getEdadEnDiasAttribute()
    {
        return $this->FechaNacimiento ? now()->diffInDays($this->FechaNacimiento) : 0;
    }

    public function getEdadEnSemanasAttribute()
    {
        return floor($this->edad_en_dias / 7);
    }

    public function getEsAdultaAttribute()
    {
        return $this->edad_en_dias >= 150; // 5 meses aproximadamente
    }

    // Static methods
    public static function getTotalCount()
    {
        return static::count();
    }

    public static function getCountByStatus()
    {
        return static::selectRaw('Estado as status, COUNT(*) as total')
            ->groupBy('Estado')
            ->get();
    }

    public static function getCountByType()
    {
        return static::with('tipoGallina')
            ->selectRaw('IDTipoGallina, COUNT(*) as total')
            ->groupBy('IDTipoGallina')
            ->get();
    }

    public static function getRecentAcquisitions($days = 7)
    {
        return static::where('created_at', '>=', now()->subDays($days))->count();
    }
}
