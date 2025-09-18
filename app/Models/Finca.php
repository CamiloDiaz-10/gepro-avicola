<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finca extends Model
{
    protected $table = 'fincas';
    protected $primaryKey = 'IDFinca';

    protected $fillable = [
        'name',
        'location',
        'capacity',
        'user_id',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }



    public function birds()
    {
        return $this->hasMany(Bird::class, 'finca_id', 'IDFinca');
    }

    public function eggProductions()
    {
        return $this->hasMany(EggProduction::class, 'finca_id', 'IDFinca');
    }


}
