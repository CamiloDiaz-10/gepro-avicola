<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'finca_id',
        'feed_type',
        'quantity',
        'unit',
        'supplier',
        'expiry_date',
        'purchase_date',
        'purchase_price',
        'minimum_threshold',
    ];

    public function finca()
    {
        return $this->belongsTo(Finca::class);
    }

    // Métodos para estadísticas
    public static function getLowStockItems()
    {
        return self::whereRaw('quantity <= minimum_threshold')
            ->get();
    }

    public static function getTotalInventoryValue()
    {
        return self::sum(\DB::raw('quantity * purchase_price'));
    }

    public static function getInventoryByType()
    {
        return self::groupBy('feed_type')
            ->selectRaw('feed_type, SUM(quantity) as total_quantity')
            ->get();
    }

    public static function getExpiringItems($days = 30)
    {
        return self::where('expiry_date', '<=', now()->addDays($days))
            ->orderBy('expiry_date')
            ->get();
    }
}