<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Bird extends Model
{
    use HasFactory;

    protected $table = 'gallinas';
    protected $primaryKey = 'IDGallina';

    protected $fillable = [
        'IDLote',
        'IDTipoGallina',
        'FechaNacimiento',
        'Peso',
        'Estado',
        'UrlImagen',
        'qr_token',
        'qr_image_path'
    ];

    protected $casts = [
        'FechaNacimiento' => 'date',
        'Peso' => 'decimal:2'
    ];

    protected static function booted()
    {
        static::creating(function (Bird $bird) {
            if (empty($bird->qr_token)) {
                $bird->qr_token = (string) Str::uuid();
            }
        });
    }

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
        return $this->hasMany(ProduccionHuevos::class, 'IDGallina', 'IDGallina');
    }

    public function celos()
    {
        return $this->hasMany(Celo::class, 'IDGallina', 'IDGallina');
    }

    // Scopes
    public function scopeVivas($query)
    {
        return $query->where('Estado', 'Viva');
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

    // Statistics methods
    public static function getTotalBirdsCount()
    {
        return static::count();
    }

    public static function getBirdsByStatus()
    {
        return static::selectRaw('Estado as status, COUNT(*) as total')
            ->groupBy('Estado')
            ->get();
    }

    public static function getRecentAcquisitions($days = 7)
    {
        return static::where('created_at', '>=', now()->subDays($days))
            ->count();
    }

    public static function getCountByType()
    {
        return static::with('tipoGallina')
            ->selectRaw('IDTipoGallina, COUNT(*) as total')
            ->groupBy('IDTipoGallina')
            ->get();
    }
}